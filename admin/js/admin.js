/*jslint regexp: true, nomen: true, undef: true, sloppy: true, eqeq: true, vars: true, white: true, plusplus: true, maxerr: 50, indent: 4 */
/*global d4plib_admin_data, gdbbx_admin_data*/

;(function($, window, document, undefined) {
    window.wp = window.wp || {};
    window.wp.gdbbx = window.wp.gdbbx || {};

    window.wp.gdbbx.admin = {
        init: function() {
            $("#gdbbx-form-settings").areYouSure();

            $(".d4p-setting-expandable_list a.button-primary").click(function(e) {
                e.preventDefault();

                var list = $(this).closest(".d4p-setting-expandable_list"),
                    next = $(".d4p-next-id", list),
                    next_id = next.val(),
                    el = $(".list-element-0", list).clone();

                $("input", el).each(function() {
                    var id = $(this).attr("id").replace("_0_", "_" + next_id + "_"),
                        name = $(this).attr("name").replace("[0]", "[" + next_id + "]");

                    $(this).attr("id", id).attr("name", name);
                });

                el.attr("class", "list-element-" + next_id).fadeIn();
                $(this).before(el);

                next_id++;
                next.val(next_id);
            });
        },
        more: function() {
            $(document).on("change", ".gdbbx-content-editor-topic-selection input", function() {
                var type = $(this).val();

                $(".gdbbx-content-editor-topic").removeClass("gdbbx-select-type-show");
                $(".gdbbx-content-editor-topic-" + type).addClass("gdbbx-select-type-show");
            });

            $(document).on("change", ".gdbbx-content-editor-reply-selection input", function() {
                var type = $(this).val();

                $(".gdbbx-content-editor-reply").removeClass("gdbbx-select-type-show");
                $(".gdbbx-content-editor-reply-" + type).addClass("gdbbx-select-type-show");
            });

            $(document).on("click", ".gdbbx-feature-more-control .d4p-bulk-ctrl", function() {
                var on = $(this).hasClass("button-secondary");

                if (on) {
                    $(this).removeClass("button-secondary").addClass("button-primary");
                    $(".gdbbx-inner-ctrl-options").hide();
                    $(".gdbbx-feature-submit").show();
                } else {
                    $(this).removeClass("button-primary").addClass("button-secondary");
                    $(".gdbbx-inner-ctrl-options").show();
                    $(".gdbbx-feature-submit").hide();
                }
            });
        },
        bulk: function() {
            $(document).on("click", ".gdbbx-features-bulk-control .d4p-bulk-ctrl", function() {
                var on = $(this).hasClass("button-secondary");

                if (on) {
                    $(this).removeClass("button-secondary").addClass("button-primary");
                    $("input.feature-status, .gdbbx-inner-ctrl-options").hide();
                } else {
                    $(this).removeClass("button-primary").addClass("button-secondary");
                    $("input.feature-status, .gdbbx-inner-ctrl-options").show();
                }
            });

            $(document).on("click", ".gdbbx-inner-ctrl-options a", function(e) {
                e.preventDefault();

                var on = $(this).attr("href").substr(1);

                if (on === 'checkall') {
                    $(".d4p-options-panel .feature-status").prop("checked", true);
                } else if (on === 'uncheckall') {
                    $(".d4p-options-panel .feature-status").prop("checked", false);
                }
            });
        },
        bbcodes: function() {
            $(document).on("change", ".gdbbx-grid-bbcodes thead th input, .gdbbx-grid-bbcodes tfoot th input", function() {
                var name = $(this).data("column"), checked = $(this).is(":checked");

                $(".gdbbx-grid-bbcodes tbody td.column-" + name + " input").prop("checked", checked);
            });
        }
    };

    wp.gdbbx.admin.init();

    $(document).ready(function() {
        if (gdbbx_admin_data.page === "features") {
            if (gdbbx_admin_data.panel === "" || gdbbx_admin_data.panel === "index") {
                wp.gdbbx.admin.bulk();
            } else {
                wp.gdbbx.admin.more();
            }
        }

        if (gdbbx_admin_data.page === "bbcodes") {
            wp.gdbbx.admin.bbcodes();
        }
    });
})(jQuery, window, document);
