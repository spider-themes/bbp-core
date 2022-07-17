;/*jslint regexp: true, nomen: true, undef: true, sloppy: true, eqeq: true, vars: true, white: true, plusplus: true, maxerr: 50, indent: 4 */
/*global bbpc_meta_data, ajaxurl*/(function($, window, document, undefined) {
	window.wp      = window.wp || {};
	window.wp.bbpc = window.wp.bbpc || {};

	window.wp.bbpc.meta = {
		storage: {
			action: "",
			nonce: "",
			post: "",
			id: ""
		},
		init: function() {
			wp.bbpc.meta.media.init();
			wp.bbpc.meta.dialogs.metabox();

			$( document ).on(
				"click",
				".bbpc-edit-attachment-attach",
				function(e){
					e.preventDefault();

					wp.bbpc.meta.storage.nonce = $( this ).data( "nonce" );
					wp.bbpc.meta.storage.post  = $( this ).data( "post" );

					wp.bbpc.meta.media.open( window.wp.bbpc.meta.attach, true );
				}
			);

			$( document ).on(
				"click",
				".bbpc-edit-attachment-delete",
				function(e){
					e.preventDefault();

					wp.bbpc.meta.storage.action = "delete";
					wp.bbpc.meta.storage.nonce  = $( this ).data( "nonce" );
					wp.bbpc.meta.storage.post   = $( this ).data( "post" );
					wp.bbpc.meta.storage.id     = $( this ).data( "id" );

					$( "#bbpc-dialog-attachment-delete" ).wpdialog( "open" );
				}
			);

			$( document ).on(
				"click",
				".bbpc-edit-attachment-detach",
				function(e){
					e.preventDefault();

					wp.bbpc.meta.storage.action = "detach";
					wp.bbpc.meta.storage.nonce  = $( this ).data( "nonce" );
					wp.bbpc.meta.storage.post   = $( this ).data( "post" );
					wp.bbpc.meta.storage.id     = $( this ).data( "id" );

					$( "#bbpc-dialog-attachment-detach" ).wpdialog( "open" );
				}
			);

			$( ".d4plib-metabox-wrapper .bbpc-override" ).change(
				function(){
					var sel = $( this ).val(),
					target  = $( this ).parent().next();

					if (sel === "yes") {
						target.slideDown();
					} else {
						target.slideUp();
					}
				}
			);
		},
		media: {
			handler: null,
			init: function() {
				if (wp && wp.media) {
					if (typeof wp.media.frames.bbpc_attachment_frame === "undefined") {
						wp.media.frames.bbpc_attachment_frame = wp.media(
							{
								title: bbpc_meta_data.string_media_dialog_title,
								className: "media-frame bbpc-attachment-frame",
								frame: "post",
								multiple: true,
								button: {
									text: bbpc_meta_data.string_media_dialog_button
								}
							}
						);

						wp.media.frames.bbpc_attachment_frame.on(
							"insert",
							function() {
								var files = wp.media.frames.bbpc_attachment_frame.state().get( "selection" ).toJSON();

								if (wp.bbpc.meta.media.handler) {
									wp.bbpc.meta.media.handler( files );
								}
							}
						);
					}
				}
			},
			open: function(handler, hide_menu) {
				wp.bbpc.meta.media.handler = handler;

				wp.media.frames.bbpc_attachment_frame.open();

				$( ".bbpc-attachment-frame .media-frame-title h1" ).html( bbpc_meta_data.string_media_dialog_title );
				$( ".bbpc-attachment-frame .media-frame-toolbar .media-toolbar-primary .media-button-insert" ).html( bbpc_meta_data.string_media_dialog_button );

				if (hide_menu) {
					$( ".bbpc-attachment-frame" ).addClass( "hide-menu" );
				}
			}
		},
		attach: function(objs) {
			var i, send = {
				nonce: wp.bbpc.meta.storage.nonce,
				post: wp.bbpc.meta.storage.post,
				id: []
			};

			for (i = 0; i < objs.length; i++) {
				send.id.push( objs[i].id );
			}

			$( "#bbpc-meta-files ul" ).append( "<li class='please-wait'>" + bbpc_meta_data.dialog_content_pleasewait + "</li>" );

			$.ajax(
				{
					dataType: "html", type: "post", data: send,
					url: ajaxurl + "?action=bbpc_attachment_attach",
					success: function(html) {
						$( "#bbpc-meta-files .please-wait" ).replaceWith( html );
					}
				}
			);
		},
		do : function() {
			var item = $( ".bbpc-attachment-id-" + wp.bbpc.meta.storage.id ),
				send = {
					nonce: wp.bbpc.meta.storage.nonce,
					post: wp.bbpc.meta.storage.post,
					id: wp.bbpc.meta.storage.id
			};

			$( "span", item ).hide();
			item.append( "<strong>" + bbpc_meta_data.dialog_content_pleasewait + "</strong>" );

			$.ajax(
				{
					dataType: "json", type: "post", data: send,
					url: ajaxurl + "?action=bbpc_attachment_" + wp.bbpc.meta.storage.action,
					success: function(json) {
						if (json.status === "ok") {
							item.fadeOut(
								"slow",
								function() {
									$( this ).remove();
								}
							);
						} else {
							$( "strong", item ).html( bbpc_meta_data.dialog_content_failed );
						}
					}
				}
			);
		},
		dialogs: {
			classes: function(extra) {
				var cls = "wp-dialog d4p-dialog bbpc-modal-dialog";

				if (extra !== "") {
					cls += " " + extra;
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
						$( ".bbpc-button-focus" ).focus();
					}
				};
			},
			icons: function(id) {
				$( id ).next().find( ".ui-dialog-buttonset button" ).each(
					function(){
						var icon = $( this ).data( "icon" );

						if (icon !== "") {
							$( this ).find( "span.ui-button-text" ).prepend( bbpc_meta_data["button_icon_" + icon] );
						}
					}
				);
			},
			metabox: function() {
				var dlg_delete = $.extend(
					{},
					wp.bbpc.meta.dialogs.defaults(),
					{
						dialogClass: wp.bbpc.meta.dialogs.classes( "bbpc-dialog-hidex" ),
						buttons: [
						{
							id: "bbpc-delete-del-delete",
							class: "bbpc-dialog-button-delete",
							text: bbpc_meta_data.dialog_button_delete,
							data: { icon: "delete" },
							click: function() {
								$( "#bbpc-dialog-attachment-delete" ).wpdialog( "close" );

								wp.bbpc.meta.do();
							}
						},
						{
							id: "bbpc-delete-del-cancel",
							class: "bbpc-dialog-button-cancel bbpc-button-focus",
							text: bbpc_meta_data.dialog_button_cancel,
							data: { icon: "cancel" },
							click: function() {
								$( "#bbpc-dialog-attachment-delete" ).wpdialog( "close" );
							}
						}
						]
					}
				), dlg_detach = $.extend(
					{},
					wp.bbpc.meta.dialogs.defaults(),
					{
						dialogClass: wp.bbpc.meta.dialogs.classes( "bbpc-dialog-hidex" ),
						buttons: [
							{
								id: "bbpc-delete-del-detach",
								class: "bbpc-dialog-button-detach",
								text: bbpc_meta_data.dialog_button_detach,
								data: { icon: "detach" },
								click: function() {
									$( "#bbpc-dialog-attachment-detach" ).wpdialog( "close" );

									wp.bbpc.meta.do();
								}
						},
							{
								id: "bbpc-delete-del-cancel",
								class: "bbpc-dialog-button-cancel bbpc-button-focus",
								text: bbpc_meta_data.dialog_button_cancel,
								data: { icon: "cancel" },
								click: function() {
									$( "#bbpc-dialog-attachment-detach" ).wpdialog( "close" );
								}
						}
							]
						}
				);

				$( "#bbpc-dialog-attachment-delete" ).wpdialog( dlg_delete );
				$( "#bbpc-dialog-attachment-detach" ).wpdialog( dlg_detach );

				wp.bbpc.meta.dialogs.icons( "#bbpc-dialog-attachment-delete" );
				wp.bbpc.meta.dialogs.icons( "#bbpc-dialog-attachment-detach" );
			}
		}
	};

	wp.bbpc.meta.init();
})( jQuery, window, document );
