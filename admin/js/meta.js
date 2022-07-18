/*jslint regexp: true, nomen: true, undef: true, sloppy: true, eqeq: true, vars: true, white: true, plusplus: true, maxerr: 50, indent: 4 */
/*global gdbbx_meta_data, ajaxurl*/

;(function($, window, document, undefined) {
    window.wp = window.wp || {};
    window.wp.gdbbx = window.wp.gdbbx || {};

    window.wp.gdbbx.meta = {
        storage: {
            action: "",
            nonce: "",
            post: "",
            id: ""
        },
        init: function() {
            wp.gdbbx.meta.media.init();
            wp.gdbbx.meta.dialogs.metabox();

            $(document).on("click", ".gdbbx-edit-attachment-attach", function(e){
                e.preventDefault();

                wp.gdbbx.meta.storage.nonce = $(this).data("nonce");
                wp.gdbbx.meta.storage.post = $(this).data("post");

                wp.gdbbx.meta.media.open(window.wp.gdbbx.meta.attach, true);
            });

            $(document).on("click", ".gdbbx-edit-attachment-delete", function(e){
                e.preventDefault();

                wp.gdbbx.meta.storage.action = "delete";
                wp.gdbbx.meta.storage.nonce = $(this).data("nonce");
                wp.gdbbx.meta.storage.post = $(this).data("post");
                wp.gdbbx.meta.storage.id = $(this).data("id");

                $("#gdbbx-dialog-attachment-delete").wpdialog("open");
            });

            $(document).on("click", ".gdbbx-edit-attachment-detach", function(e){
                e.preventDefault();

                wp.gdbbx.meta.storage.action = "detach";
                wp.gdbbx.meta.storage.nonce = $(this).data("nonce");
                wp.gdbbx.meta.storage.post = $(this).data("post");
                wp.gdbbx.meta.storage.id = $(this).data("id");

                $("#gdbbx-dialog-attachment-detach").wpdialog("open");
            });

            $(".d4plib-metabox-wrapper .gdbbx-override").change(function(){
                var sel = $(this).val(),
                    target = $(this).parent().next();

                if (sel === "yes") {
                    target.slideDown();
                } else {
                    target.slideUp();
                }
            });
        },
        media: {
            handler: null,
            init: function() {
                if (wp && wp.media) {
                    if (typeof wp.media.frames.gdbbx_attachment_frame === "undefined") {
                        wp.media.frames.gdbbx_attachment_frame = wp.media({
                            title: gdbbx_meta_data.string_media_dialog_title,
                            className: "media-frame gdbbx-attachment-frame",
                            frame: "post",
                            multiple: true,
                            button: {
                                text: gdbbx_meta_data.string_media_dialog_button
                            }
                        });

                        wp.media.frames.gdbbx_attachment_frame.on("insert", function() {
                            var files = wp.media.frames.gdbbx_attachment_frame.state().get("selection").toJSON();

                            if (wp.gdbbx.meta.media.handler) {
                                wp.gdbbx.meta.media.handler(files);
                            }
                        });
                    }
                }
            },
            open: function(handler, hide_menu) {
                wp.gdbbx.meta.media.handler = handler;

                wp.media.frames.gdbbx_attachment_frame.open();

                $(".gdbbx-attachment-frame .media-frame-title h1").html(gdbbx_meta_data.string_media_dialog_title);
                $(".gdbbx-attachment-frame .media-frame-toolbar .media-toolbar-primary .media-button-insert").html(gdbbx_meta_data.string_media_dialog_button);

                if (hide_menu) {
                    $(".gdbbx-attachment-frame").addClass("hide-menu");
                }
            }
        },
        attach: function(objs) {
            var i, send = {
                nonce: wp.gdbbx.meta.storage.nonce,
                post: wp.gdbbx.meta.storage.post,
                id: []
            };

            for (i = 0; i < objs.length; i++) {
                send.id.push(objs[i].id);
            }

            $("#gdbbx-meta-files ul").append("<li class='please-wait'>" + gdbbx_meta_data.dialog_content_pleasewait + "</li>");

            $.ajax({
                dataType: "html", type: "post", data: send,
                url: ajaxurl + "?action=gdbbx_attachment_attach",
                success: function(html) {
                    $("#gdbbx-meta-files .please-wait").replaceWith(html);
                }
            });
        },
        do: function() {
            var item = $(".gdbbx-attachment-id-" + wp.gdbbx.meta.storage.id),
                send = {
                    nonce: wp.gdbbx.meta.storage.nonce,
                    post: wp.gdbbx.meta.storage.post,
                    id: wp.gdbbx.meta.storage.id
                };

            $("span", item).hide();
            item.append("<strong>" + gdbbx_meta_data.dialog_content_pleasewait + "</strong>");

            $.ajax({
                    dataType: "json", type: "post", data: send,
                url: ajaxurl + "?action=gdbbx_attachment_" + wp.gdbbx.meta.storage.action,
                success: function(json) {
                    if (json.status === "ok") {
                        item.fadeOut("slow", function() {
                            $(this).remove();
                        });
                    } else {
                        $("strong", item).html(gdbbx_meta_data.dialog_content_failed);
                    }
                }
            });
        },
        dialogs: {
            classes: function(extra) {
                var cls = "wp-dialog d4p-dialog gdbbx-modal-dialog";

                if (extra !== "") {
                    cls+= " " + extra;
                }

                return cls;
            },
            defaults: function() {
                return {
                    width: 480,
                    height: "auto",
                    minHeight: 24,
                    autoOpen: false,
                    resizable: false,
                    modal: true,
                    closeOnEscape: false,
                    zIndex: 300000,
                    open: function() {
                        $(".gdbbx-button-focus").focus();
                    }
                };
            },
            icons: function(id) {
                $(id).next().find(".ui-dialog-buttonset button").each(function(){
                    var icon = $(this).data("icon");

                    if (icon !== "") {
                        $(this).find("span.ui-button-text").prepend(gdbbx_meta_data["button_icon_" + icon]);
                    }
                });
            },
            metabox: function() {
                var dlg_delete = $.extend({}, wp.gdbbx.meta.dialogs.defaults(), {
                    dialogClass: wp.gdbbx.meta.dialogs.classes("gdbbx-dialog-hidex"),
                    buttons: [
                        {
                            id: "gdbbx-delete-del-delete",
                            class: "gdbbx-dialog-button-delete",
                            text: gdbbx_meta_data.dialog_button_delete,
                            data: { icon: "delete" },
                            click: function() {
                                $("#gdbbx-dialog-attachment-delete").wpdialog("close");

                                wp.gdbbx.meta.do();
                            }
                        },
                        {
                            id: "gdbbx-delete-del-cancel",
                            class: "gdbbx-dialog-button-cancel gdbbx-button-focus",
                            text: gdbbx_meta_data.dialog_button_cancel,
                            data: { icon: "cancel" },
                            click: function() {
                                $("#gdbbx-dialog-attachment-delete").wpdialog("close");
                            }
                        }
                    ]
                }), dlg_detach = $.extend({}, wp.gdbbx.meta.dialogs.defaults(), {
                    dialogClass: wp.gdbbx.meta.dialogs.classes("gdbbx-dialog-hidex"),
                    buttons: [
                        {
                            id: "gdbbx-delete-del-detach",
                            class: "gdbbx-dialog-button-detach",
                            text: gdbbx_meta_data.dialog_button_detach,
                            data: { icon: "detach" },
                            click: function() {
                                $("#gdbbx-dialog-attachment-detach").wpdialog("close");

                                wp.gdbbx.meta.do();
                            }
                        },
                        {
                            id: "gdbbx-delete-del-cancel",
                            class: "gdbbx-dialog-button-cancel gdbbx-button-focus",
                            text: gdbbx_meta_data.dialog_button_cancel,
                            data: { icon: "cancel" },
                            click: function() {
                                $("#gdbbx-dialog-attachment-detach").wpdialog("close");
                            }
                        }
                    ]
                });

                $("#gdbbx-dialog-attachment-delete").wpdialog(dlg_delete);
                $("#gdbbx-dialog-attachment-detach").wpdialog(dlg_detach);

                wp.gdbbx.meta.dialogs.icons("#gdbbx-dialog-attachment-delete");
                wp.gdbbx.meta.dialogs.icons("#gdbbx-dialog-attachment-detach");
            }
        }
    };

    wp.gdbbx.meta.init();
})(jQuery, window, document);
