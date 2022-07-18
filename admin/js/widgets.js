/*jslint regexp: true, nomen: true, undef: true, sloppy: true, eqeq: true, vars: true, white: true, plusplus: true, maxerr: 50, indent: 4 */

;(function($, window, document, undefined) {
    window.wp = window.wp || {};
    window.wp.gdbbx = window.wp.gdbbx || {};

    window.wp.gdbbx.widgets = {
        views: function() {
            $(document).on("click", "a.gdbbx-tab-topics-views", function() {
                wp.gdbbx.widgets.run($(this).closest(".d4plib-widget").find(".gdbbx-views-list"), ".gdbbx-views-ul");
            });

            $(document).on("click", "a.gdbbx-tab-topics-stats", function() {
                wp.gdbbx.widgets.run($(this).closest(".d4plib-widget").find(".gdbbx-stats-list"), ".gdbbx-stats-ul");
            });
        },
        run: function(el, key) {
            if (!el.hasClass("gdbbx-active")) {
                $(key, el).sortable();

                $(el).addClass("gdbbx-active");
            }
        }
    };

    wp.gdbbx.widgets.views();
})(jQuery, window, document);
