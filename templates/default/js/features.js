/*jslint regexp: true, nomen: true, undef: true, sloppy: true, eqeq: true, vars: true, white: true, plusplus: true, maxerr: 50, indent: 4 */
/*global gdbbx_data, tinymce, tinyMCE */

;(function($, window, document, undefined) {
    window.wp = window.wp || {};
    window.wp.gdbbx = window.wp.gdbbx || {};

    window.wp.gdbbx.helper = {
        detect_msie: function() {
            var ua = window.navigator.userAgent,
                msie = ua.indexOf('MSIE ');

            if (msie > 0) {
                return parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
            }

            var trident = ua.indexOf('Trident/');

            if (trident > 0) {
                var rv = ua.indexOf('rv:');

                return parseInt(ua.substring(rv + 3, ua.indexOf('.', rv)), 10);
            }

            return 99;
        },
        get_selection: function() {
            var t = "";

            if (window.getSelection) {
                t = window.getSelection();
            } else if (document.getSelection) {
                t = document.getSelection();
            } else if (document.selection) {
                t = document.selection.createRange().text;
            }

            return $.trim(t.toString());
        },
        file_extension: function(name) {
            return name.substr(name.lastIndexOf(".") + 1).toLowerCase();
        },
        is_tinymce: function() {
            var id = $("#bbp_topic_content").length > 0 ? "bbp_topic_content" : "bbp_reply_content";

            return gdbbx_data.wp_editor && !$("#" + id).is(":visible");
        },
        into_editor: function(text) {
            var id = $("#bbp_topic_content").length > 0 ? "bbp_topic_content" : "bbp_reply_content";

            if (wp.gdbbx.helper.is_tinymce()) {
                text += "<br/><br/>";

                tinymce.get(id).execCommand("mceInsertContent", false, text);
            } else {
                var txtr = $("#" + id),
                    cntn = txtr.val();

                if ($.trim(cntn) !== "") {
                    text = "\n\n" + text;
                }

                text += "\n\n";

                txtr.val(cntn + text);
            }
        },
        scroll_to_editor: function() {
            if (wp.gdbbx.helper.detect_msie() > 8) {
                $("html, body").animate({scrollTop: $("#new-post").offset().top}, 1000);
            } else {
                document.location.href = "#new-post";
            }

            $(".bbp-the-content-wrapper textarea").focus();
        }
    };

    window.wp.gdbbx.toolbox = {
        bbcodes: {
            init: function() {
                $(".gdbbx-bbcode-spoiler").each(function() {
                    var hover = $(this).data("hover"),
                        normal = $(this).data("color");

                    $(this).hover(
                        function() {
                            $(this).css("background", hover);
                        },
                        function() {
                            $(this).css("background", normal);
                        }
                    );
                });
            }
        },
        quotes: {
            init: function() {
                $(document).on("click", ".gdbbx-link-quote", function(e) {
                    e.preventDefault();

                    if ($("#bbp_reply_content").length > 0) {
                        var qout = wp.gdbbx.helper.get_selection(), id = $(this).data("id"),
                            eol = wp.gdbbx.helper.is_tinymce() ? "</br></br>" : "\n",
                            quote_id = "#gdbbx-quote-wrapper-" + id, is_selection = true;

                        if (qout === "") {
                            is_selection = false;
                            qout = $(quote_id).html();
                        }

                        qout = qout.replace(/&nbsp;/g, " ");
                        qout = qout.replace(/<\s*p[^>]*>/g, "");
                        qout = qout.replace(/<\s*\/\s*(p|br)\s*>|<\s*br\s*>/g, eol);
                        qout = qout.trim();

                        qout = $("<div>").html(qout).html();

                        if (gdbbx_data.quote.method === "bbcode") {
                            var code = gdbbx_data.quote.bbcode === "postquote" && !is_selection ? "postquote" : "quote";

                            if (code === "postquote") {
                                qout = "[" + code + " quote=" + id + "]";
                            } else {
                                qout = "[" + code + " quote=" + id + "]" + qout + "[/" + code + "]";
                            }
                        } else {
                            var author = gdbbx_data.quote.wrote.replace('%s', $(this).data("author")),
                                title = '<div class="gdbbx-quote-title"><a href="' + $(this).data("url") + '">' + author + ':</a></div>';

                            qout = '<blockquote class="gdbbx-bbcode-quote">' + title + qout + '</blockquote>';
                        }

                        wp.gdbbx.helper.into_editor(qout);
                        wp.gdbbx.helper.scroll_to_editor();
                    }
                });
            }
        },
        canned_replies: {
            init: function() {
                $(".gdbbx-canned-replies .gdbbx-canned-replies-show").click(function(e) {
                    e.preventDefault();

                    var container = $(this).closest(".gdbbx-canned-replies");

                    $(this).hide();
                    $(".gdbbx-canned-replies-hide", container).show();
                    $(".gdbbx-canned-replies-list", container).slideDown();
                });

                $(".gdbbx-canned-replies .gdbbx-canned-replies-hide").click(function(e) {
                    e.preventDefault();

                    var container = $(this).closest(".gdbbx-canned-replies");

                    $(this).hide();
                    $(".gdbbx-canned-replies-show", container).show();
                    $(".gdbbx-canned-replies-list", container).slideUp();
                });

                $(".gdbbx-canned-replies .gdbbx-canned-reply-insert").click(function(e) {
                    e.preventDefault();

                    var container = $(this).closest(".gdbbx-canned-reply"),
                        content = $(".gdbbx-canned-reply-content", container).html();

                    wp.gdbbx.helper.into_editor(content);

                    if (gdbbx_data.canned_replies.auto_close_on_insert) {
                        var wrapper = $(this).closest(".gdbbx-canned-replies");

                        $(".gdbbx-canned-replies-hide", wrapper).click();
                    }
                });
            }
        },
        fitvids: {
            init: function() {
                $(".bbp-topic-content, .bbp-reply-content").fitVids();
            }
        },
        report: {
            sending_report: false,
            init: function() {
                $(".gdbbx-link-report").click(function(e) {
                    e.preventDefault();

                    if (!wp.gdbbx.toolbox.report.sending_report) {
                        if (gdbbx_data.report.mode === "form") {
                            wp.gdbbx.toolbox.report.form($(this));
                        } else {
                            wp.gdbbx.toolbox.report.button($(this));
                        }
                    }
                });
            },
            button: function(el) {
                var id = el.data("id"), nonce = el.data("nonce");

                if (gdbbx_data.report.mode === "confirm") {
                    if (confirm(gdbbx_data.report.confirm) === false) {
                        return;
                    }
                }

                var call = {
                    post: id,
                    nonce: nonce
                };

                wp.gdbbx.toolbox.report.sending_report = true;

                $.ajax({
                    dataType: "html", type: "post", data: call,
                    url: gdbbx_data.url + "?action=gdbbx_report_post",
                    success: function(html) {
                        wp.gdbbx.toolbox.report.sending_report = false;

                        $(".gdbbx-link-report-" + call.post).replaceWith("<span>" + gdbbx_data.report.after + "</span>");
                    }
                });
            },
            form: function(el) {
                var id = el.data("id"), nonce = el.data("nonce"),
                    content = el.closest("#bbpress-forums").find(".post-" + id + " .bbp-reply-content, .post-" + id + " .bbp-topic-content");

                if (content.length === 1) {
                    if (content.find(".gdbbx-report-wrapper").length === 0) {
                        $(".gdbbx-report-wrapper").remove();

                        var form = $(".gdbbx-report-template > div")
                            .clone()
                            .addClass("gdbbx-report-wrapper");

                        form.find("button")
                            .data("id", id)
                            .data("nonce", nonce);

                        content.append(form);

                        form.find("input").focus();

                        wp.gdbbx.toolbox.report.handle(content.find(".gdbbx-report-wrapper"));
                    }

                    if (gdbbx_data.report.scroll) {
                        var offset = 0;

                        if ($("#wpadminbar").length > 0) {
                            offset = $("#wpadminbar").height();
                        }

                        $("html, body").animate({
                            scrollTop: content.find(".gdbbx-report-wrapper").offset().top - offset
                        }, 500);
                    }
                }
            },
            handle: function(el) {
                $("button.gdbbx-report-cancel", el).click(function() {
                    $(".gdbbx-report-wrapper").remove();
                });

                $("button.gdbbx-report-send", el).click(function() {
                    var text = $("input", el).val();

                    if (text.length < gdbbx_data.report.min) {
                        alert(gdbbx_data.report.alert);
                    } else {
                        var call = {
                            report: text,
                            post: $(this).data("id"),
                            nonce: $(this).data("nonce")
                        };

                        $(".gdbbx-report-form", el).hide();
                        $(".gdbbx-report-sending", el).show();

                        wp.gdbbx.toolbox.report.sending_report = true;

                        $.ajax({
                            dataType: "html", type: "post", data: call,
                            url: gdbbx_data.url + "?action=gdbbx_report_post",
                            success: function(html) {
                                wp.gdbbx.toolbox.report.sending_report = false;

                                $(".gdbbx-report-sending", el).hide();
                                $(".gdbbx-report-sent", el).show();

                                $(".gdbbx-link-report-" + call.post).replaceWith("<span>" + gdbbx_data.report.after + "</span>");
                            }
                        });
                    }
                });
            }
        },
        thanks: {
            init: function() {
                $(".gdbbx-link-thanks, .gdbbx-link-unthanks").click(function(e) {
                    e.preventDefault();

                    wp.gdbbx.toolbox.thanks.handle(this);
                });
            },
            handle: function(el) {
                var call = {
                        nonce: $(el).data("thanks-nonce"),
                        say: $(el).data("thanks-action"),
                        id: $(el).data("thanks-id")
                    }, button = $(el),
                    is_thanks = button.hasClass("gdbbx-link-thanks");

                $.ajax({
                    dataType: "html", type: "post", data: call,
                    url: gdbbx_data.url + "?action=gdbbx_say_thanks",
                    success: function(html) {
                        var thanks = $(html).fadeIn(600);

                        $(".gdbbx-thanks-post-" + call.id).fadeOut(400).replaceWith(thanks);

                        if (is_thanks) {
                            if (gdbbx_data.thanks.removal) {
                                button.removeClass("gdbbx-link-thanks")
                                    .addClass("gdbbx-link-unthanks")
                                    .data("thanks-action", "unthanks")
                                    .html(gdbbx_data.thanks.unthanks);
                            } else {
                                button.replaceWith("<span>" + gdbbx_data.thanks.saved + "</span>");
                            }
                        } else {
                            button.removeClass("gdbbx-link-unthanks")
                                .addClass("gdbbx-link-thanks")
                                .data("thanks-action", "thanks")
                                .html(gdbbx_data.thanks.thanks);
                        }
                    }
                });
            }
        },
        scheduler: {
            init: function() {
                var field = $("#gdbbx_schedule_datetime");

                if (field.length === 1) {
                    var current = field.val(), args = {
                        enableTime: true,
                        enableSeconds: true,
                        altInput: true,
                        minDate: Date.now(),
                        minTime: Date.now(),
                        defaultDate: current.length === 0 ? Date.now() : current,
                        dateFormat: "Y-m-d H:i:S"
                    };

                    if (gdbbx_data.flatpickr_locale !== "") {
                        args.locale = gdbbx_data.flatpickr_locale;
                    }

                    field.flatpickr(args);

                    $(document).on("change", "#gdbbx_schedule_when", function() {
                        var when = $(this).val(), field = $(this).parent().parent().next();

                        if (when === 'future') {
                            field.show();
                        } else {
                            field.hide();
                        }
                    });
                }
            }
        },
        misc: {
            privacy: function() {
                $(".gdbbx-private-reply-hidden").each(function() {
                    $(this).hide().prev(".bbp-reply-header").hide();
                });
            }
        },
        run: function() {
            if (typeof gdbbx_data !== "undefined") {
                wp.gdbbx.toolbox.misc.privacy();

                if (gdbbx_data.load.indexOf('scheduler') > -1) {
                    wp.gdbbx.toolbox.scheduler.init();
                }

                if (gdbbx_data.load.indexOf('quote') > -1) {
                    wp.gdbbx.toolbox.quotes.init();
                }

                if (gdbbx_data.load.indexOf('bbcodes') > -1) {
                    wp.gdbbx.toolbox.bbcodes.init();
                }

                if (gdbbx_data.load.indexOf('report') > -1) {
                    wp.gdbbx.toolbox.report.init();
                }

                if (gdbbx_data.load.indexOf('thanks') > -1) {
                    wp.gdbbx.toolbox.thanks.init();
                }

                if (gdbbx_data.load.indexOf('canned_replies') > -1) {
                    wp.gdbbx.toolbox.canned_replies.init();
                }

                if (gdbbx_data.load.indexOf('fitvids') > -1 && $.fn.fitVids) {
                    wp.gdbbx.toolbox.fitvids.init();
                }
            }
        }
    };

    $(document).ready(function() {
        wp.gdbbx.toolbox.run();

        if (typeof EnlighterJS === 'object') {
            EnlighterJS.init('pre.gdbbx-bbcode-scode');
        }
    });
})(jQuery, window, document);
