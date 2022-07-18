/*jslint regexp: true, nomen: true, undef: true, sloppy: true, eqeq: true, vars: true, white: true, plusplus: true, maxerr: 50, indent: 4 */
/*global gdbbx_data, wp.gdbbx.helper, tinymce, tinyMCE*/

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
