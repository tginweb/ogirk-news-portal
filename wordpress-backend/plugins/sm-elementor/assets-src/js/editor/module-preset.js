(function($) {

    var PresetEditorModule = window.SM_Elementor.classes.EditorModule.extend( {

        globalModels: {},

        panelWidgets: null,

        templatesAreSaved: true,

        altPressed : false,

        onElementorInit: function () {

            this.defineViews();

            this.initEvents();
        },

        defineViews: function () {

            var WidgetView = elementor.modules.elements.views.Widget;

            this.views.GlobalWidgetView = WidgetView.extend( {

                globalModel: null,

                firstEdit: true,

                className: function() {
                    return WidgetView.prototype.className.apply( this, arguments ) + ' elementor-global-widget elementor-global-' + this.model.get( 'templateID' );
                },

                initialize: function() {
                    var self = this,
                        previewSettings = self.model.get( 'previewSettings' ),
                        globalModel = self.getGlobalModel();

                    if ( previewSettings ) {
                        globalModel.set( 'settingsLoadedStatus', 'loaded' ).trigger( 'settings:loaded' );

                        var settingsModel = globalModel.get( 'settings' );

                        settingsModel.handleRepeaterData( previewSettings );

                        settingsModel.set( previewSettings, { silent: true } );
                    } else {
                        var globalSettingsLoadedStatus = globalModel.get( 'settingsLoadedStatus' );

                        if ( ! globalSettingsLoadedStatus ) {
                            globalModel.set( 'settingsLoadedStatus', 'pending' );

                            elementorPro.modules.globalWidget.requestGlobalModelSettings( globalModel );
                        }

                        if ( 'loaded' !== globalSettingsLoadedStatus ) {
                            self.$el.addClass( 'elementor-loading' );
                        }

                        globalModel.on( 'settings:loaded', function() {
                            self.$el.removeClass( 'elementor-loading' );

                            self.render();
                        } );
                    }

                    WidgetView.prototype.initialize.apply( self, arguments );
                },

                getGlobalModel: function() {
                    if ( ! this.globalModel ) {
                        this.globalModel = elementorPro.modules.globalWidget.getGlobalModels( this.model.get( 'templateID' ) );
                    }

                    return this.globalModel;
                },

                getEditModel: function() {
                    return this.getGlobalModel();
                },

                getHTMLContent: function( html ) {
                    if ( 'loaded' === this.getGlobalModel().get( 'settingsLoadedStatus' ) ) {
                        return WidgetView.prototype.getHTMLContent.call( this, html );
                    }

                    return '';
                },

                serializeModel: function() {
                    var globalModel = this.getGlobalModel();

                    return globalModel.toJSON.apply( globalModel, _.rest( arguments ) );
                },

                edit: function() {
                    elementor.getPanelView().setPage( 'globalWidget', 'Global Editing', { editedView: this } );
                },


                onEditRequest: function() {
                    //alert('editRe');

                    var self = this;

                    if (this.firstEdit && !self.altPressed)
                    {
                        this.firstEdit = false;

                        var globalModel = this.getGlobalModel();

                        var template_id = this.model.get( 'templateID' );

                        if (ElementorProConfig.preset_widgets[template_id])
                        {
                            if (!ElementorProConfig.preset_widgets[template_id].requested)
                            {
                                ElementorProConfig.preset_widgets[template_id].requested = true;

                                elementorPro.modules.globalWidget.requestGlobalModelSettings( globalModel, function() {

                                    setTimeout(function () { self.unlinkPreset();}, 100);

                                });
                            }
                            else
                            {
                                setTimeout(function () { self.unlinkPreset();}, 100);
                            }

                            return;
                        }

                    }

                    elementor.getPanelView().setPage( 'globalWidget', 'Global Editing', { editedView: this } );
                },

                unlinkPreset: function() {

                    var self = this;

                    var globalModel = this.getGlobalModel();

                    var template_id = this.model.get( 'templateID' );

                    //elementorPro.modules.globalWidget.requestGlobalModelSettings( globalModel, function() {

                    elementor.history.history.startItem( {
                        title: globalModel.getTitle(),
                        type: elementorPro.translate( 'unlink_widget' )
                    } );

                    var new_settings = elementor.helpers.cloneObject( globalModel.get( 'settings' ).attributes );

                    new_settings.sm_preset = template_id;

                    var newModel = new elementor.modules.elements.models.Element( {
                        elType: 'widget',
                        widgetType: globalModel.get( 'widgetType' ),
                        id: elementor.helpers.getUniqueID(),
                        settings: new_settings,
                        defaultEditSettings: elementor.helpers.cloneObject( globalModel.get( 'editSettings' ).attributes )
                    } );

                    self._parent.addChildModel( newModel, { at: self.model.collection.indexOf( self.model ) } );

                    var newWidget = self._parent.children.findByModelCid( newModel.cid );

                    setTimeout(function () {

                        self.model.destroy();

                        if ( elementor.history ) {
                            elementor.history.history.endItem();
                        }

                        if ( newWidget.edit ) {
                            newWidget.edit();
                        }

                        newModel.trigger( 'request:edit' );

                    }, 300);

                    //});

                },

                unlink: function() {
                    var globalModel = this.getGlobalModel();

                    elementor.history.history.startItem( {
                        title: globalModel.getTitle(),
                        type: elementorPro.translate( 'unlink_widget' )
                    } );

                    var newModel = new elementor.modules.elements.models.Element( {
                        elType: 'widget',
                        widgetType: globalModel.get( 'widgetType' ),
                        id: elementor.helpers.getUniqueID(),
                        settings: elementor.helpers.cloneObject( globalModel.get( 'settings' ).attributes ),
                        defaultEditSettings: elementor.helpers.cloneObject( globalModel.get( 'editSettings' ).attributes )
                    } );

                    this._parent.addChildModel( newModel, { at: this.model.collection.indexOf( this.model ) } );

                    var newWidget = this._parent.children.findByModelCid( newModel.cid );

                    this.model.destroy();

                    if ( elementor.history ) {
                        elementor.history.history.endItem();
                    }

                    if ( newWidget.edit ) {
                        newWidget.edit();
                    }

                    newModel.trigger( 'request:edit' );
                }


            } );

        },

        initEvents: function () {

            var module = this;

            elementor.channels.editor.on( 'smElementor:ApplyPreset', this.onPresetApply);

            elementor.hooks.addFilter( 'element/view', function( DefaultView, model ) {
                if ( model.get( 'templateID' ) ) {

                    return module.views.GlobalWidgetView;
                }

                return DefaultView;
            }, 1000 );

            this.listenKeydown();
        },

        listenKeydown: function () {

            var self = this;

            $(window).keydown(function(e) {
                if(e.keyCode == 18) { self.altPressed = true;  }
            });

            $(window).keyup(function(e) {
                self.altPressed = false;
            });
        },

        onElementorFrontendInit: function() {


        },

        onPresetApply: function() {

            var current_settings = elementor.panel.currentView.content.currentView.model.get('settings').attributes;
            var current_edit_settings = elementor.panel.currentView.content.currentView.model.get('editSettings').attributes;

            var preset_id = current_settings.sm_preset;
            var preset_model = elementorPro.modules.globalWidget.getGlobalModels( preset_id );

            var cb = function() {

                var preset_settings = preset_model.get( 'settings' ).attributes;
                var preset_controls = preset_model.get( 'settings' ).controls;
                var preset_edit_settings = preset_model.get( 'editSettings' ).attributes;

                var new_settings = {};
                var new_edit_settings = {};

                $.each(preset_settings, function(setting_key, setting_preset_val) {

                    var setting_from_current = null;

                    var setting_current_val = current_settings[setting_key];

                    var setting_control;


                    if (setting_control = preset_controls[setting_key])
                    {
                        if (setting_control.tab == 'content')
                        {
                            setting_from_current = true;
                        }
                        else if (setting_key == 'sm_preset')
                        {
                            setting_from_current = true;
                        }
                    }

                    if (!setting_from_current)
                        new_settings[setting_key] = setting_preset_val;
                    else
                        new_settings[setting_key] = setting_current_val;
                });

                var element = elementor.getPanelView().getCurrentPageView().getOption('editedElementView').model;

                var element_parent = elementor.getPanelView().getCurrentPageView().getOption('editedElementView')._parent;

                elementor.history.history.startItem( {
                    title: preset_model.getTitle(),
                    type: 'Change preset'
                } );

                var newModel = new elementor.modules.elements.models.Element( {
                    elType: 'widget',
                    widgetType: new_settings.widgetType,
                    id: elementor.helpers.getUniqueID(),
                    settings: new_settings,
                    defaultEditSettings: new_edit_settings
                } );

                element_parent.addChildModel( newModel, { at: element.collection.indexOf( element ) } );

                var newWidget = element_parent.children.findByModelCid( newModel.cid );


                if ( elementor.history ) {
                    elementor.history.history.endItem();
                }

                if ( newWidget.edit ) {
                    newWidget.edit();
                }

                newModel.trigger( 'request:edit' );


                setTimeout(function () {

                    element.destroy();


                }, 300);

            }


            var globalSettingsLoadedStatus = preset_model.get( 'settingsLoadedStatus' );

            if ( ! globalSettingsLoadedStatus )
            {
                preset_model.set( 'settingsLoadedStatus', 'pending' );

                elementorPro.modules.globalWidget.requestGlobalModelSettings( preset_model, function () {

                    cb.call();

                } );
            }
            else
            {
                cb.call();
            }

        }

    });

    window.SM_Elementor.modules.PresetEditorModule = new PresetEditorModule();


})(jQuery);
