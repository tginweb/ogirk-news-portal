

(function($) {

    SM_Elementor.FieldWidget.Base = SM_Elementor.components.base.extend({

        initialize : function()
        {
            var self = this;

            self.dispatcher = self.params.dispatcher;

            self.bindChange();
        }

    });

    SM_Elementor.FieldWidget.Base.prototype.setValue = function() {

    }

    SM_Elementor.FieldWidget.Base.prototype.getValue = function() {

    }

    SM_Elementor.FieldWidget.Base.prototype.bindChange = function() {

    }


    SM_Elementor.FieldWidget.links = SM_Elementor.FieldWidget.Base.extend({

        bindChange : function() {

            var self = this;

            if (self.params.linkable && self.params.linkable=='yes') return;

            var items = self.$el.find('.control-item');

            if (self.params.trigger_hover && self.params.trigger_hover=='yes')
            {
                var event = 'hover';
            }
            else
            {
                var event = 'click';
            }

            self.$el.find('.control-item').on(event, function () {

                var item = $(this);

                if (self.params.multiple=='yes')
                {
                    if (item.get(0).hasAttribute('data-selected'))
                        item.removeAttr('data-selected');
                    else
                        item.attr('data-selected', '');
                }
                else
                {
                    items.removeAttr('data-selected');

                    item.attr('data-selected', '');
                }

                self.dispatcher.trigger('filter_change', [this])
            });

        },

        getValue : function() {

            var self = this;

            var result = [];

            var selected_items = self.$el.find('.control-item[data-selected]');

            selected_items.each(function () {

                result.push($(this).data('value'));
            });

            return result;
        }

    });

    SM_Elementor.FieldWidget.select = SM_Elementor.FieldWidget.Base.extend({

        bindChange : function() {

            var self = this;

            self.$el.find('select').change(function () {

                self.dispatcher.trigger('filter_change', [this])
            });

        },

        getValue : function() {

            return this.$el.find('select').val();
        }

    });


    var sm_query = SM_Elementor.components.base.extend({

        $el                  : null,
        settings             : {},
        settings_protected   : '',
        query                : {},
        query_protected      : '',
        filters              : {},
        filters_controllers  : {},
        filters_state        : {},
        filters_widths       : [],
        current_page         : 1,
        user_action          : 'next_prev',
        is_ajax_running      : false,

        initialize : function()
        {
            var self = this;

            if (self.$el.hasClass('sm-processed')) return;

            self.cid                 = self.params.cid;
            self.settings            = self.params.settings;
            self.settings_protected  = self.params.settings_protected;
            self.query               = self.params.query;
            self.query_protected     = self.params.query_protected;

            self.$elInner            = self.$el.find('q-inner');

            self.bindPagination();

            if (self.settings.filters_display=='yes')
            {
                self.filters = self.params.filters;

                self.bindFilters();
            }

            self.bindLightbox();

            self.initView();
        }

    });


    sm_query.prototype.initView = function () {

    }

    sm_query.prototype.bindLightbox = function () {

        if ($().fancybox === undefined) {
            return;
        }

        var self = this;

        if (self.settings['lightbox_link_wrap']=='yes')
        {

            self.$el.on( "click", ".lightbox-link-wrapper", function(e, p) {

                var t = $(e.target);

                if ((!p || p!=='own') && (t.prop("tagName")!='A') && t.closest('a').length==0)
                {
                    $(this).find('a.m-lightbox-link').trigger('click', ['own']);
                }

            });
        }

        /* ----------------- Lightbox Support ------------------ */

        self.$el.fancybox({
            selector: 'a.m-lightbox-link', // the selector for portfolio item
            loop: true,
            buttons: [
                "zoom",
                "share",
                "slideShow",
                "fullScreen",
                //"download",
                "thumbs",
                "close"
            ],
            baseClass: 'outside-element'
        });

    }

    sm_query.prototype.bindFilters = function() {

        var self = this;

        $.each(self.filters, function (i, filter_params) {

            $filter = self.$el.find('[data-filter-name="' + filter_params.name + '"]');

            if ($filter.length)
            {
                var widget_constructor;

                if (widget_constructor = SM_Elementor.FieldWidget[filter_params.widget])
                {
                    filter_params.dispatcher = self.$el;

                    var widget = new widget_constructor($filter, filter_params);

                    widget.initialize();

                    self.filters_controllers[filter_params.name] = widget;
                }
            }
        });

        self.$el.on('filter_change', function () { self.onFilterChange.apply(self); });
    };

    sm_query.prototype.onFilterChange = function() {

        var self = this;

        this.current_page = 1;

        self.doAjaxRequest('filter');
    }

    sm_query.prototype.bindPagination = function() {

        var self = this;

        if (self.settings['pagination_ajax'] !== 'yes') return;

        self.$el.on('click', 'a.q-page-nav', function(e) {

            e.preventDefault();

            self.handlePageNavigation($(this));
        });

        self.$el.on('click', 'a.q-load-more', function (e) {

            e.preventDefault();

            self.handleLoadMore($(this));

        });

    };


    sm_query.prototype.handleLoadMore = function ($target) {

        var self = this;

        if (self.$el.is('.q-processing')) return;

        if (this.is_ajax_running === true)
            return;

        var userAction = 'load_more';

        this.current_page++;

        this.doAjaxRequest(userAction);
    }

    sm_query.prototype.handlePageNavigation = function ($target) {

        var user_action = 'next';

        var self = this;

        var paged = $target.data('page');

        if ($target.is('.q-current-page') || self.$el.is('.q-processing'))
            return;

        if (this.is_ajax_running === true)
            return;

        if (paged == 'prev') {

            if (this.current_page == 1)
                return;

            this.current_page--;

            user_action = 'prev';
        }
        else if (paged == 'next') {

            if (this.current_page >= this.maxpages)
                return;

            this.current_page++;

            user_action = 'next';
        }
        else {

            this.current_page = paged;

            user_action = 'load_page';
        }

        this.doAjaxRequest(user_action);

    }

    sm_query.prototype.getFiltersValues = function () {

        var self = this;

        var filters_value = {};

        $.each(self.filters_controllers, function (filter_name, filter_controller) {

            var filter_value = filter_controller.getValue();

            if (filter_value)
            {
                filters_value[filter_name] = filter_value;
            }
        });

        return filters_value;
    }

    sm_query.prototype.getRequestData = function (user_action, data) {

        var requestData = {
            'cid'                : this.cid,
            'query_view'         : this.settings.query_view,
            'settings_protected' : this.settings_protected,
            'query_protected'    : this.query_protected,
            'filters'            : this.getFiltersValues(),
            'current_page'       : this.current_page,
            'user_action'        : user_action
        };

        if (data)
            $.extend(requestData, data);

        return requestData;
    }

    sm_query.prototype.doAjaxRequest = function (user_action, data) {

        var self = this;

        var requestData = self.getRequestData(user_action, data);

        self.ajaxRequestStart(user_action);

        this.ajaxAction('sm_elementor_module_query_request', requestData, function (response) {

            self.processAjaxResponse(response.data, user_action);

            self.ajaxRequestEnd(user_action);

        });

    }

    sm_query.prototype.processAjaxResponse = function (response, user_action) {

        var self = this;

        var $innerContent = $(response.inner_content);

        if ('load_more' === user_action)
        {
            self.$el.find('.q-inner').append($innerContent);
        }
        else
        {
            self.$el.find('.q-inner').html($innerContent);
        }

        self.$el.find('.q-pagination').replaceWith(response.pagination);
    }

    sm_query.prototype.ajaxRequestStart = function (user_action) {

        var self = this;

        self.is_ajax_running = true;

        self.$el.addClass('q-processing');

        if (user_action == 'next' || user_action == 'prev' || user_action == 'filter' || user_action == 'load_page')
        {
            self.$el.append('<div class="q-loader-gif"></div>');

            self.$elInner.addClass('q-loading');
        }
    }

    sm_query.prototype.ajaxRequestEnd = function (user_action) {

        var self = this;

        self.is_ajax_running = false;

        self.$el.removeClass('q-processing');

        $('.q-loader-gif').remove();

        self.$elInner.removeClass('q-loading');

        //self._ensureBlockObjectsAreVisible(self.$el, user_action);
    }

    SM_Elementor.components.sm_query = sm_query;



    $(document).on('sm/processor/registered', function (e, manager) {


        manager.observeFunction('[data-sm-elementor-query]', function () {

            console.log(SM_Elementor.components['sm_query_'+this.params.settings.query_view]);

            if (this.params.settings.query_view && SM_Elementor.components['sm_query_'+this.params.settings.query_view])
            {
                var view = new SM_Elementor.components['sm_query_'+this.params.settings.query_view](this.$el, this.params);
            }
            else
            {
                var view = new SM_Elementor.components.sm_query(this.$el, this.params);
            }

            view.initialize();
        });

    });

})(jQuery);


