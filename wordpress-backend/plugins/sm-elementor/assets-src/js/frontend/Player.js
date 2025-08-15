
(function($) {

    $(document).on('sm/processor/registered', function (e, manager) {

        manager.observeComponent('[data-sm-elementor-player]', SM_Elementor.components.base.extend({

            player_inited : false,

            player : '',

            initialize : function() {

                var com = this;

                this.params = $.extend({

                    lazyload : false

                }, this.params);



                if (!this.params.lazyload)
                {
                    this.init_player();

                }




                /*
                var player_elm = this.$el.find('.player');

                var player = new Plyr(player_elm);

                player.on('ready', function () {

                    if (com.params.autoplay)
                        player.play();

                });

                this.$el.find('.m-content').click(function () {

                    var source = {};

                    var module_media_info = $(this).closest('.module').data('media-info');

                    source.type = module_media_info.type;

                    source.sources = [{
                        src: module_media_info.src,
                        provider: module_media_info.provider
                    }];

                    player.source = source;

                    $(this).closest('.modules').find('.module').removeClass('active');
                    $(this).closest('.module').addClass('active');


                    return false;
                });
                */

                this.$el.data('com', this);
            },

            init_player : function (init_params) {


                if (this.player_inited) return;

                var self = this;

                var params = $.extend({}, self.params, init_params || {});

                self.player = new Plyr(self.$el.find('.player-handle'));

                self.player.on('ready', function () {

                    if (params.autoplay) self.play();

                });

                this.player_inited = true;
            },

            play : function () {

                this.init_player();

                this.player.play();
            },

            stop : function () {

                if (!this.player_inited) return;

                this.player.stop();
            }

        }));


    });


})(jQuery);


