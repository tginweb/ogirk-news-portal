
(function($) {

    $(document).on('sm/processor/registered', function (e, manager) {

        manager.observeComponent('[data-sm-elementor-archive-calendar]', SM_Elementor.components.base.extend({

            initialize : function()
            {
                var view = this;

                view.elm_year = view.$el.find('[name="year"]');
                view.elm_month = view.$el.find('[name="month"]');

                view.elm_year.add(view.elm_month).change(function()
                {
                    if ($(this).attr('name')=='year')
                    {
                        view.sync_elm_months();
                    }

                    view.action_load_month_grid(view.elm_year.val(), view.elm_month.val());
                });

                view.$el.find('.el-query-year').click(function()
                {
                    view.action_query(view.elm_year.val());
                    return false;
                });

                view.$el.find('.el-query-month').click(function()
                {
                    view.action_query(view.elm_year.val(), view.elm_month.val());
                    return false;
                });

                view.$el.on( "click", ".el-query-day", function() {
                    return view.action_query(view.elm_year.val(), view.elm_month.val(), $(this).data('day'));
                });

                view.sync_elm_months();
            },

            sync_elm_months : function()
            {
                var selected_month = this.elm_month.val();
                var selected_year_months = this.elm_year.find('option:selected').data('months')  || [];

                this.elm_month.find('option').each(function()
                {
                    selected_year_months.indexOf(parseInt($(this).attr('value'))) >= 0 ? $(this).show() : $(this).hide();
                });

                if (selected_year_months.indexOf(parseInt(selected_month))==-1)
                {
                    this.elm_month.val(Math.max.apply(Math, selected_year_months));
                }
            },

            action_load_month_grid : function(year, month)
            {
                var view = this;

                this.ajaxAction(
                    'sm_elementor_module_archive_calendar_get_month_grid',
                    {
                        'year' : year,
                        'month' : month,
                        'settings_protected' : view.params['settings_protected'],
                        'query_protected' : view.params['query_protected']
                    },
                    function (response)
                    {
                        if (response.data.content)
                        {
                            view.$el.find('.el-month-grid').replaceWith(response.data.content);
                        }
                    }
                );
            },

            get_base_url_value : function()
            {
                return this.params['base_url'].url;
            },

            action_query : function(year, month, day)
            {
                var date_query = {};
                var date_query_values = [];

                if (year)  date_query.year = year;
                if (month) date_query.monthnum = month;
                if (day)   date_query.day = day;

                for (var key in date_query)
                    date_query_values.push(date_query[key]);

                var date_query_str = date_query_values.join('-');

                if (this.params.trigger_selector)
                {
                    var el_trigger = $(this.params.trigger_selector);

                    el_trigger.val(date_query_str);

                    el_trigger.trigger('change');

                    return false;
                }
                else
                {
                    query_url = $.url.addparam(this.get_base_url_value(), {'date_filter' : date_query_str});

                    window.location.href = query_url;
                }
            }

        }));

    });




})(jQuery);


