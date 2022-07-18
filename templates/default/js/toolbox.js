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

;(function($, window, document, undefined) {
    window.wp = window.wp || {};
    window.wp.gdbbx = window.wp.gdbbx || {};

    window.wp.gdbbx.attachments = {
        is_enhanced: true,
        allow_submit: true,
        extensions: [],
        files: [],
        inner: 1,
        init: function() {
            $("form#new-post").attr("enctype", "multipart/form-data");

            this.is_enhanced = gdbbx_data.attachments.method === 'enhanced' && wp.gdbbx.helper.detect_msie() > 9;
            if (typeof gdbbx_data.attachments.allowed_extensions === 'string') {
                this.extensions = gdbbx_data.attachments.allowed_extensions.split(' ');
            }

            this.actions();

            if (this.is_enhanced) {
                this.enhanced.run();
            } else {
                this.basic.run();
            }

            this.thread.run();
        },
        actions: function() {
            $(document).on("click", ".gdbbx-attachment-insert", function(e) {
                e.preventDefault();

                var id = $(this).attr("href").substr(1),
                    shortcode = '[attachment file="' + id + '"]';

                wp.gdbbx.helper.into_editor(shortcode);
                wp.gdbbx.helper.scroll_to_editor();
            });

            $(document).on("click", ".gdbbx-attachment-confirm", function(e) {
                if (!confirm(gdbbx_data.text.are_you_sure)) {
                    e.preventDefault();
                }
            });

            $(document).on("click", ".gdbbx-attachment-add-file", function(e) {
                e.preventDefault();

                wp.gdbbx.attachments.add(this);
            });
        },
        count: function() {
            return $(".gdbbx-attachments-input").length;
        },
        count_empty: function() {
            return $(".gdbbx-attachments-input:not(.gdbbx-attachment-added)").length;
        },
        add: function(el) {
            if (!gdbbx_data.attachments.limiter || wp.gdbbx.attachments.count() < gdbbx_data.attachments.max_files) {
                if (!gdbbx_data.attachments.auto_new_file || (gdbbx_data.attachments.auto_new_file && wp.gdbbx.attachments.count_empty() === 0)) {
                    if (wp.gdbbx.attachments.is_enhanced) {
                        $(el).before(wp.gdbbx.attachments.enhanced.block());
                    } else {
                        $(el).before(wp.gdbbx.attachments.basic.block());
                    }
                }
            }

            if (!gdbbx_data.attachments.auto_new_file && gdbbx_data.attachments.limiter && wp.gdbbx.attachments.count() === gdbbx_data.attachments.max_files) {
                $(el).hide();
            }
        },
        enhanced: {
            run: function() {
                $(".gdbbx-attachments-form > div").addClass("gdbbx-validation-active");

                if (gdbbx_data.attachments.auto_new_file) {
                    $(".gdbbx-attachments-form").addClass("gdbbx-auto-newfile");
                }

                wp.gdbbx.attachments.enhanced.actions();
            },
            actions: function() {
                $(document).on("click", ".gdbbx-attachment-preview", function() {
                    $(this).closest(".gdbbx-attachments-input").find("input[type=file]").click();
                });

                $(document).on("click", ".gdbbx-attachments-input a.bbp-att-remove", function(e) {
                    e.preventDefault();

                    var block = $(this).closest(".gdbbx-attachments-input"),
                        inner = block.data("id");

                    delete wp.gdbbx.attachments.files[inner];

                    block.fadeOut("slow", function() {
                        if (gdbbx_data.attachments.auto_new_file) {
                            var all = block.parent();

                            if ($(".gdbbx-attachments-input:not(.gdbbx-attachment-added)", all).length === 0) {
                                $(".gdbbx-attachment-add-file", all).before(wp.gdbbx.attachments.enhanced.block());
                            }
                        } else {
                            $(".gdbbx-attachment-add-file").show();
                        }

                        if (wp.gdbbx.attachments.count() === 0) {
                            $(this).after(wp.gdbbx.attachments.enhanced.block());
                        }

                        $(this).remove();

                        wp.gdbbx.attachments.enhanced.check_submit();
                    });
                });

                $(document).on("change", ".gdbbx-attachments-input input[type=file]", wp.gdbbx.attachments.enhanced.attach);

                $(document).on("click", ".gdbbx-attachments-input a.bbp-att-caption", function(e) {
                    e.preventDefault();

                    $(this).prev().find("input").show();
                    $(this).hide();
                });

                $(document).on("click", ".gdbbx-attachments-input a.bbp-att-shortcode", function(e) {
                    e.preventDefault();

                    var shortcode = '[attachment file="' + $(this).data("file") + '"]';

                    wp.gdbbx.helper.into_editor(shortcode);
                    wp.gdbbx.helper.scroll_to_editor();
                });
            },
            attach: function() {
                if (!this.files || this.files.length !== 1) {
                    return;
                }

                var block = $(this).closest(".gdbbx-attachments-input"), img = "", valid = true,
                    valid_dupe = true, valid_size = true, valid_type = true, forbidden = ["js", "php"],
                    txt = "", regex = /^([a-zA-Z0-9\s_\\.\-:\+])+(.jpg|.jpeg|.gif|.png|.bmp)$/;

                var file = this.files[0],
                    size = Math.round(file.size / 1024),
                    ext = wp.gdbbx.helper.file_extension(file.name),
                    hash = file.name + file.size + file.lastModified + file.type;

                block.removeClass("gdbbx-attachment-invalid")
                    .addClass("gdbbx-attachment-added")
                    .data("id", wp.gdbbx.attachments.inner);

                txt = '<div>' + gdbbx_data.attachments.text.file_name + ": <strong>" + file.name + "</strong></div>";
                txt += '<div>' + gdbbx_data.attachments.text.file_size + ": <strong>" + size + " kb</strong>, ";
                txt += gdbbx_data.attachments.text.file_type + ": <strong>" + ext.toUpperCase() + "</strong></div>";

                if ($.inArray(ext, forbidden) > -1) {
                    valid = false;
                    valid_type = false;
                }

                $.each(wp.gdbbx.attachments.files, function(fid, fhash) {
                    if (fhash === hash) {
                        valid = false;
                        valid_dupe = false;

                        return false;
                    }
                });

                if (valid_dupe) {
                    wp.gdbbx.attachments.files[wp.gdbbx.attachments.inner] = hash;
                }

                if (gdbbx_data.attachments.limiter) {
                    if (file.size > gdbbx_data.attachments.max_size) {
                        valid = false;
                        valid_size = false;
                    }

                    if (wp.gdbbx.attachments.extensions.length > 0) {
                        if ($.inArray(ext, wp.gdbbx.attachments.extensions) === -1) {
                            valid = false;
                            valid_type = false;
                        }
                    }
                }

                if (!valid) {
                    txt += "<strong>";
                    txt += gdbbx_data.attachments.text.file_validation;

                    if (!valid_dupe) {
                        txt += " " + gdbbx_data.attachments.text.file_validation_duplicate;
                    }

                    if (!valid_type) {
                        txt += " " + gdbbx_data.attachments.text.file_validation_type;
                    }

                    if (!valid_size) {
                        txt += " " + gdbbx_data.attachments.text.file_validation_size;
                    }

                    txt += "</strong><br/>";

                    block.addClass("gdbbx-attachment-invalid");
                }

                if (valid) {
                    if (gdbbx_data.attachments.set_caption_file) {
                        txt += "<div><label><input name='gdbbx-attachment_caption[]' type='text' style='display: none' placeholder='" + gdbbx_data.attachments.text.file_caption_placeholder + "' /><span class='gdbbx-accessibility-show-for-sr'>" + gdbbx_data.attachments.text.file_caption_placeholder + "</span></label><a data-file='" + file.name + "' class='bbp-att-caption' href='#'>" + gdbbx_data.attachments.text.file_caption + "</a></div>";
                    }

                    if (gdbbx_data.attachments.insert_into_content) {
                        txt += "<div><a class='bbp-att-shortcode' href='#'>" + gdbbx_data.attachments.text.file_shortcode + "</a></div>";
                    }
                }

                txt += "<div><a class='bbp-att-remove' href='#'>" + gdbbx_data.attachments.text.file_remove + "</a></div>";

                block.find(".gdbbx-attachment-control").html(txt);
                block.find(".gdbbx-attachment-control .bbp-att-shortcode").data('file', file.name);
                block.find(".gdbbx-attachment-preview .gdbbx-attached-file").remove();

                if (window.FileReader && regex.test(file.name.toLowerCase())) {
                    var reader = new FileReader();
                    reader.readAsDataURL(file);

                    reader.onloadend = function() {
                        img = '<img class="gdbbx-attached-file" alt="' + file.name + '" src="' + this.result + '" />';
                        block.find(".gdbbx-attachment-preview").prepend(img);
                    };
                } else {
                    img = '<p class="gdbbx-attached-file" title="' + file.name + '">.' + ext.toUpperCase() + '</p>';
                    block.find(".gdbbx-attachment-preview").prepend(img);
                }

                wp.gdbbx.attachments.inner++;

                wp.gdbbx.attachments.add(".gdbbx-attachments-form .gdbbx-attachment-add-file");
                wp.gdbbx.attachments.enhanced.check_submit();
            },
            check_submit: function() {
                var valid = true;

                $(".gdbbx-attachments-form .gdbbx-attachments-input").each(function() {
                    if ($(this).hasClass("gdbbx-attachment-invalid")) {
                        valid = false;
                    }
                });

                $(".gdbbx-attachments-form").closest("form").find(".bbp-submit-wrapper button").attr("disabled", !valid);
            },
            block: function() {
                var block = '<div class="gdbbx-attachments-input gdbbx-validation-active">';
                block += '<div role="button" class="gdbbx-attachment-preview"><span aria-hidden="true">' + gdbbx_data.attachments.text.select_file + '</span></div>';
                block += '<label><input type="file" size="40" name="gdbbx-attachment[]" />';
                block += '<span class="gdbbx-accessibility-show-for-sr">' + gdbbx_data.attachments.text.select_file + '</span></label>';
                block += '<div class="gdbbx-attachment-control"></div>';
                block += '</div>';

                return block;
            }
        },
        basic: {
            run: function() {
            },
            actions: function() {
            },
            block: function() {
                var block = '<div class="gdbbx-attachments-input gdbbx-validation-disabled">';
                block += '<div class="gdbbx-attachment-header"><span aria-hidden="true">' + gdbbx_data.attachments.text.select_file + '</span></div>';
                block += '<label><input type="file" size="40" name="gdbbx-attachment[]" />';
                block += '<span class="gdbbx-accessibility-show-for-sr">' + gdbbx_data.attachments.text.select_file + '</span></label>';
                block += '</div>';

                return block;
            }
        },
        thread: {
            nonce: '',
            topic: 0,
            current: 1,
            run: function() {
                $(document).on("click", ".gdbbx-attachments-thread-pager .__prev", function(e) {
                    e.preventDefault();

                    var form = $(this).closest(".bbp-form");

                    if (wp.gdbbx.attachments.thread.current > 1) {
                        wp.gdbbx.attachments.thread.current--;

                        wp.gdbbx.attachments.thread.show(form);
                    }
                });

                $(document).on("click", ".gdbbx-attachments-thread-pager .__next", function(e) {
                    e.preventDefault();

                    var form = $(this).closest(".bbp-form"),
                        total = $(".gdbbx-attachments-thread-pages", form).data("pages");

                    if (wp.gdbbx.attachments.thread.current < total) {
                        wp.gdbbx.attachments.thread.current++;

                        wp.gdbbx.attachments.thread.show(form);
                    }
                });

                $(document).on("click", ".gdbbx-attachments-thread-control a", function(e) {
                    e.preventDefault();

                    var form = $(this).parent().parent().find(".bbp-form");

                    wp.gdbbx.attachments.thread.nonce = $(this).data("nonce");
                    wp.gdbbx.attachments.thread.topic = $(this).data("topic");

                    $(this).parent().remove();

                    form.show();

                    wp.gdbbx.attachments.thread.fetch(form, 1);
                });
            },
            show: function(form) {
                var pages = $(".gdbbx-attachments-thread-pages", form),
                    pager = $(".gdbbx-attachments-thread-pager", form),
                    total = pages.data("pages"),
                    now = wp.gdbbx.attachments.thread.current,
                    show = $(".gdbbx-attachments-thread-page-" + wp.gdbbx.attachments.thread.current, form);

                pager.removeClass("gdbbx-thread-current-first").removeClass("gdbbx-thread-current-last");

                if (now === 1) {
                    pager.addClass("gdbbx-thread-current-first");
                } else if (now === total) {
                    pager.addClass("gdbbx-thread-current-last");
                }

                $(".__current", pager).html(now);

                if (show.hasClass("gdbbx-attachments-thread-empty")) {
                    wp.gdbbx.attachments.thread.fetch(form, now);
                } else {
                    $(".gdbbx-attachments-thread-page", form).hide();
                    $(".gdbbx-attachments-thread-page-" + now, form).show();
                }
            },
            fetch: function(form, pg) {
                $.ajax({
                    dataType: "html", type: "post", data: {
                        nonce: wp.gdbbx.attachments.thread.nonce,
                        topic: wp.gdbbx.attachments.thread.topic,
                        page: pg
                    },
                    url: gdbbx_data.url + "?action=gdbbx_attachments_thread",
                    success: function(html) {
                        $(".gdbbx-attachments-thread-page", form).hide();
                        $(".gdbbx-attachments-thread-page-" + pg, form).html(html).removeClass("gdbbx-attachments-thread-empty").show();
                        $(".gdbbx-attachments-thread-pager", form).show();
                    }
                });
            }
        }
    };

    $(document).ready(function() {
        wp.gdbbx.attachments.init();
    });
})(jQuery, window, document);

;(function($, window, document, undefined) {
    window.wp = window.wp || {};
    window.wp.gdbbx = window.wp.gdbbx || {};

    window.wp.gdbbx.toolbar = {
        init: function() {
            $(".gdbbx-editor-bbcodes").each(function() {
                wp.gdbbx.toolbar.run($(this), $(this));
            });

            $(".gdbbx-newpost-bbcodes").each(function() {
                wp.gdbbx.toolbar.run($(this), $(".bbp-the-content-wrapper"));
            });

            $(".gdbbx-signature.gdbbx-limiter-enabled").each(function() {
                wp.gdbbx.toolbar.limit($(this));
            });
        },
        run: function(toolbar, textarea) {
            $(".gdbbx-buttonbar-button button", toolbar).keypress(function(e) {
                if (e.keyCode === 32 || e.keyCode === 13) {
                    e.preventDefault();

                    wp.gdbbx.toolbar.click(this, textarea);
                }
            });

            $(".gdbbx-buttonbar-button button", toolbar).click(function(e) {
                e.preventDefault();

                wp.gdbbx.toolbar.click(this, textarea);
            });
        },
        click: function(button, textarea) {
            var bbcode = $(button).data("bbcode");

            bbcode = bbcode.replace(/\(/g, "[")
                .replace(/\)/g, "]")
                .replace(/\'/g, '"');

            var wrap = {
                    content: bbcode.indexOf("{content}") > -1,
                    id: bbcode.indexOf("{id}") > -1,
                    url: bbcode.indexOf("{url}") > -1,
                    email: bbcode.indexOf("{email}") > -1
                },
                editor = $("textarea", textarea),
                selected = editor.textrange();

            if (selected.length > 0) {
                if (wrap.content) {
                    bbcode = bbcode.replace("{content}", selected.text);
                } else if (wrap.id) {
                    bbcode = bbcode.replace("{id}", selected.text);
                } else if (wrap.url) {
                    bbcode = bbcode.replace("{url}", selected.text);
                } else if (wrap.email) {
                    bbcode = bbcode.replace("{email}", selected.text);
                }
            }

            editor.textrange("replace", bbcode);
        },
        limit: function(textarea) {
            var args = {
                maxChars: $(textarea).data("chars"),
                maxCharsWarning: $(textarea).data("warning"),
                msgFontSize: "inherit",
                msgFontFamily: "inherit",
                msgFontColor: "inherit"
            };

            $(textarea).jqEasyCounter(args);
        }
    };

    $(document).ready(function() {
        wp.gdbbx.toolbar.init();
    });
})(jQuery, window, document);

/**
 * jquery-textrange
 *
 * A jQuery plugin for getting, setting and replacing the selected text in input fields and textareas.
 * See the [README](https://github.com/dwieeb/jquery-textrange/blob/1.x/README.md) for usage and examples.
 *
 * (c) 2012-2017 Daniel Imhoff <dwieeb@gmail.com> - dwieeb.com
 */

(function(factory) {

    if (typeof define === 'function' && define.amd) {
        define(['jquery'], factory);
    } else if (typeof exports === 'object') {
        factory(require('jquery'));
    } else {
        factory(jQuery);
    }

})(function($) {

    var browserType,

        textrange = {
            get: function(property) {
                return _textrange[browserType].get.apply(this, [property]);
            },

            set: function(start, length) {
                var s = parseInt(start),
                    l = parseInt(length),
                    e;

                if (typeof start === 'undefined') {
                    s = 0;
                } else if (start < 0) {
                    s = this[0].value.length + s;
                }

                if (typeof length !== 'undefined') {
                    if (length >= 0) {
                        e = s + l;
                    } else {
                        e = this[0].value.length + l;
                    }
                }

                _textrange[browserType].set.apply(this, [s, e]);

                return this;
            },

            setcursor: function(position) {
                return this.textrange('set', position, 0);
            },

            replace: function(text) {
                _textrange[browserType].replace.apply(this, [String(text)]);

                return this;
            },

            /**
             * Alias for $().textrange('replace')
             */
            insert: function(text) {
                return this.textrange('replace', text);
            }
        },

        _textrange = {
            xul: {
                get: function(property) {
                    var props = {
                        position: this[0].selectionStart,
                        start: this[0].selectionStart,
                        end: this[0].selectionEnd,
                        length: this[0].selectionEnd - this[0].selectionStart,
                        text: this.val().substring(this[0].selectionStart, this[0].selectionEnd)
                    };

                    return typeof property === 'undefined' ? props : props[property];
                },

                set: function(start, end) {
                    if (typeof end === 'undefined') {
                        end = this[0].value.length;
                    }

                    this[0].selectionStart = start;
                    this[0].selectionEnd = end;
                },

                replace: function(text) {
                    var start = this[0].selectionStart;
                    var end = this[0].selectionEnd;
                    var val = this.val();
                    this.val(val.substring(0, start) + text + val.substring(end, val.length));
                    this[0].selectionStart = start;
                    this[0].selectionEnd = start + text.length;
                }
            },

            msie: {
                get: function(property) {
                    var range = document.selection.createRange(), props;

                    if (typeof range === 'undefined') {
                        props = {
                            position: 0,
                            start: 0,
                            end: this.val().length,
                            length: this.val().length,
                            text: this.val()
                        };

                        return typeof property === 'undefined' ? props : props[property];
                    }

                    var start = 0;
                    var end = 0;
                    var length = this[0].value.length;
                    var lfValue = this[0].value.replace(/\r\n/g, '\n');
                    var rangeText = this[0].createTextRange();
                    var rangeTextEnd = this[0].createTextRange();
                    rangeText.moveToBookmark(range.getBookmark());
                    rangeTextEnd.collapse(false);

                    if (rangeText.compareEndPoints('StartToEnd', rangeTextEnd) === -1) {
                        start = -rangeText.moveStart('character', -length);
                        start += lfValue.slice(0, start).split('\n').length - 1;

                        if (rangeText.compareEndPoints('EndToEnd', rangeTextEnd) === -1) {
                            end = -rangeText.moveEnd('character', -length);
                            end += lfValue.slice(0, end).split('\n').length - 1;
                        } else {
                            end = length;
                        }
                    } else {
                        start = length;
                        end = length;
                    }

                    props = {
                        position: start,
                        start: start,
                        end: end,
                        length: length,
                        text: range.text
                    };

                    return typeof property === 'undefined' ? props : props[property];
                },

                set: function(start, end) {
                    var range = this[0].createTextRange();

                    if (typeof range === 'undefined') {
                        return;
                    }

                    if (typeof end === 'undefined') {
                        end = this[0].value.length;
                    }

                    var ieStart = start - (this[0].value.slice(0, start).split("\r\n").length - 1);
                    var ieEnd = end - (this[0].value.slice(0, end).split("\r\n").length - 1);

                    range.collapse(true);

                    range.moveEnd('character', ieEnd);
                    range.moveStart('character', ieStart);

                    range.select();
                },

                replace: function(text) {
                    document.selection.createRange().text = text;
                }
            }
        };

    $.fn.extend({
        textrange: function(arg) {
            var method = 'get';
            var options = {};

            if (typeof this[0] === 'undefined') {
                return this;
            }

            if (typeof arg === 'string') {
                method = arg;
            } else if (typeof arg === 'object') {
                method = arg.method || method;
                options = arg;
            }

            if (typeof browserType === 'undefined') {
                browserType = 'selectionStart' in this[0] ? 'xul' : document.selection ? 'msie' : 'unknown';
            }

            // I don't know how to support this browser. :c
            if (browserType === 'unknown') {
                return this;
            }

            // Focus on the element before operating upon it.
            if (!options.nofocus && document.activeElement !== this[0]) {
                this[0].focus();
            }

            if (typeof textrange[method] === 'function') {
                return textrange[method].apply(this, Array.prototype.slice.call(arguments, 1));
            } else {
                $.error("Method " + method + " does not exist in jQuery.textrange");
            }
        }
    });
});

/* jQuery jqEasyCharCounter-Extended plugin
 * See: http://github.com/EspadaV8/jqEasyCharCounter-Extended
 * Original examples and documentation at: http://www.jqeasy.com/
 * Version: 1.0 (29/09/2010)
 * No license. Use it however you want. Just keep this notice included.
 * Requires: jQuery v1.3+
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 */
(function($) {

    $.fn.extend({
        jqEasyCounter: function(givenOptions) {
            return this.each(function() {
                var $this = $(this),
                    options = $.extend({
                        maxChars: 100,                  // max number of characters
                        maxCharsWarning: 80,            // max number of characters before warning is shown
                        msgFontSize: '12px',            // css font size for counter
                        msgFontColor: '#000',           // css font color for counter
                        msgFontFamily: 'Arial',         // css font family for counter
                        msgTextAlign: 'right',          // css text-align for counter (left, right, center)
                        msgWarningColor: '#F00',        // css font color for warning
                        msgAppendMethod: 'insertAfter',  // position of counter relative to the input element(insertAfter, insertBefore)
                        msg: 'Characters: ',            // The message to use
                        msgPlacement: 'prepend',        // Prepend/Append the message to the number
                        numFormat: 'CURRENT/MAX'        // Format of the numbers (CURRENT, MAX, REMAINING)
                    }, givenOptions);

                if (options.maxChars <= 0) {
                    return;
                }

                // create counter element
                var jqEasyCounterMsg = $("<div class=\"jqEasyCounterMsg\">&nbsp;</div>");
                var jqEasyCounterMsgStyle = {
                    'font-size': options.msgFontSize,
                    'font-family': options.msgFontFamily,
                    'color': options.msgFontColor,
                    'text-align': options.msgTextAlign,
                    'width': $this.width(),
                    'opacity': 0
                };
                jqEasyCounterMsg.css(jqEasyCounterMsgStyle);
                jqEasyCounterMsg[options.msgAppendMethod]($this);

                $this
                    .bind('keydown keyup keypress', doCount)
                    .bind('focus paste', function() {
                        setTimeout(doCount, 10);
                    })
                    .bind('blur', function() {
                        jqEasyCounterMsg.stop().fadeTo('fast', 0);
                        return false;
                    });

                function doCount() {
                    var val = $this.val(),
                        length = val.length;

                    if (length >= options.maxChars) {
                        val = val.substring(0, options.maxChars);
                    }

                    if (length > options.maxChars) {
                        var originalScrollTopPosition = $this.scrollTop();
                        $this.val(val.substring(0, options.maxChars));
                        $this.scrollTop(originalScrollTopPosition);
                    }

                    if (length >= options.maxCharsWarning) {
                        jqEasyCounterMsg.css({"color": options.msgWarningColor});
                    } else {
                        jqEasyCounterMsg.css({"color": options.msgFontColor});
                    }

                    if (options.msgPlacement === 'prepend') {
                        html = options.msg + options.numFormat;
                    } else {
                        html = options.numFormat + options.msg;
                    }
                    html = html.replace('CURRENT', $this.val().length);
                    html = html.replace('MAX', options.maxChars);
                    html = html.replace('REMAINING', options.maxChars - $this.val().length);

                    jqEasyCounterMsg.html(html);
                    jqEasyCounterMsg.stop().fadeTo('fast', 1);
                }
            });
        }
    });

})(jQuery);