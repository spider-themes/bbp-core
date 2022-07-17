;/*jslint regexp: true, nomen: true, undef: true, sloppy: true, eqeq: true, vars: true, white: true, plusplus: true, maxerr: 50, indent: 4 */
/*global bbpc_data, wp.bbpc.helper, tinymce, tinyMCE*/(function($, window, document, undefined) {
	window.wp      = window.wp || {};
	window.wp.bbpc = window.wp.bbpc || {};

	window.wp.bbpc.attachments = {
		is_enhanced: true,
		allow_submit: true,
		extensions: [],
		files: [],
		inner: 1,
		init: function() {
			$( "form#new-post" ).attr( "enctype", "multipart/form-data" );

			this.is_enhanced = bbpc_data.attachments.method === 'enhanced' && wp.bbpc.helper.detect_msie() > 9;
			if (typeof bbpc_data.attachments.allowed_extensions === 'string') {
				this.extensions = bbpc_data.attachments.allowed_extensions.split( ' ' );
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
			$( document ).on(
				"click",
				".bbpc-attachment-insert",
				function(e) {
					e.preventDefault();

					var id    = $( this ).attr( "href" ).substr( 1 ),
					shortcode = '[attachment file="' + id + '"]';

					wp.bbpc.helper.into_editor( shortcode );
					wp.bbpc.helper.scroll_to_editor();
				}
			);

			$( document ).on(
				"click",
				".bbpc-attachment-confirm",
				function(e) {
					if ( ! confirm( bbpc_data.text.are_you_sure )) {
						e.preventDefault();
					}
				}
			);

			$( document ).on(
				"click",
				".bbpc-attachment-add-file",
				function(e) {
					e.preventDefault();

					wp.bbpc.attachments.add( this );
				}
			);
		},
		count: function() {
			return $( ".bbpc-attachments-input" ).length;
		},
		count_empty: function() {
			return $( ".bbpc-attachments-input:not(.bbpc-attachment-added)" ).length;
		},
		add: function(el) {
			if ( ! bbpc_data.attachments.limiter || wp.bbpc.attachments.count() < bbpc_data.attachments.max_files) {
				if ( ! bbpc_data.attachments.auto_new_file || (bbpc_data.attachments.auto_new_file && wp.bbpc.attachments.count_empty() === 0)) {
					if (wp.bbpc.attachments.is_enhanced) {
						$( el ).before( wp.bbpc.attachments.enhanced.block() );
					} else {
						$( el ).before( wp.bbpc.attachments.basic.block() );
					}
				}
			}

			if ( ! bbpc_data.attachments.auto_new_file && bbpc_data.attachments.limiter && wp.bbpc.attachments.count() === bbpc_data.attachments.max_files) {
				$( el ).hide();
			}
		},
		enhanced: {
			run: function() {
				$( ".bbpc-attachments-form > div" ).addClass( "bbpc-validation-active" );

				if (bbpc_data.attachments.auto_new_file) {
					$( ".bbpc-attachments-form" ).addClass( "bbpc-auto-newfile" );
				}

				wp.bbpc.attachments.enhanced.actions();
			},
			actions: function() {
				$( document ).on(
					"click",
					".bbpc-attachment-preview",
					function() {
						$( this ).closest( ".bbpc-attachments-input" ).find( "input[type=file]" ).click();
					}
				);

				$( document ).on(
					"click",
					".bbpc-attachments-input a.bbp-att-remove",
					function(e) {
						e.preventDefault();

						var block = $( this ).closest( ".bbpc-attachments-input" ),
						inner     = block.data( "id" );

						delete wp.bbpc.attachments.files[inner];

						block.fadeOut(
							"slow",
							function() {
								if (bbpc_data.attachments.auto_new_file) {
									var all = block.parent();

									if ($( ".bbpc-attachments-input:not(.bbpc-attachment-added)", all ).length === 0) {
										$( ".bbpc-attachment-add-file", all ).before( wp.bbpc.attachments.enhanced.block() );
									}
								} else {
									$( ".bbpc-attachment-add-file" ).show();
								}

								if (wp.bbpc.attachments.count() === 0) {
									$( this ).after( wp.bbpc.attachments.enhanced.block() );
								}

								$( this ).remove();

								wp.bbpc.attachments.enhanced.check_submit();
							}
						);
					}
				);

				$( document ).on( "change", ".bbpc-attachments-input input[type=file]", wp.bbpc.attachments.enhanced.attach );

				$( document ).on(
					"click",
					".bbpc-attachments-input a.bbp-att-caption",
					function(e) {
						e.preventDefault();

						$( this ).prev().find( "input" ).show();
						$( this ).hide();
					}
				);

				$( document ).on(
					"click",
					".bbpc-attachments-input a.bbp-att-shortcode",
					function(e) {
						e.preventDefault();

						var shortcode = '[attachment file="' + $( this ).data( "file" ) + '"]';

						wp.bbpc.helper.into_editor( shortcode );
						wp.bbpc.helper.scroll_to_editor();
					}
				);
			},
			attach: function() {
				if ( ! this.files || this.files.length !== 1) {
					return;
				}

				var block      = $( this ).closest( ".bbpc-attachments-input" ), img = "", valid = true,
					valid_dupe = true, valid_size = true, valid_type = true, forbidden = ["js", "php"],
					txt        = "", regex = /^([a-zA-Z0-9\s_\\.\-:\+])+(.jpg|.jpeg|.gif|.png|.bmp)$/;

				var file = this.files[0],
					size = Math.round( file.size / 1024 ),
					ext  = wp.bbpc.helper.file_extension( file.name ),
					hash = file.name + file.size + file.lastModified + file.type;

				block.removeClass( "bbpc-attachment-invalid" )
					.addClass( "bbpc-attachment-added" )
					.data( "id", wp.bbpc.attachments.inner );

				txt  = '<div>' + bbpc_data.attachments.text.file_name + ": <strong>" + file.name + "</strong></div>";
				txt += '<div>' + bbpc_data.attachments.text.file_size + ": <strong>" + size + " kb</strong>, ";
				txt += bbpc_data.attachments.text.file_type + ": <strong>" + ext.toUpperCase() + "</strong></div>";

				if ($.inArray( ext, forbidden ) > -1) {
					valid      = false;
					valid_type = false;
				}

				$.each(
					wp.bbpc.attachments.files,
					function(fid, fhash) {
						if (fhash === hash) {
							valid      = false;
							valid_dupe = false;

							return false;
						}
					}
				);

				if (valid_dupe) {
					wp.bbpc.attachments.files[wp.bbpc.attachments.inner] = hash;
				}

				if (bbpc_data.attachments.limiter) {
					if (file.size > bbpc_data.attachments.max_size) {
						valid      = false;
						valid_size = false;
					}

					if (wp.bbpc.attachments.extensions.length > 0) {
						if ($.inArray( ext, wp.bbpc.attachments.extensions ) === -1) {
							valid      = false;
							valid_type = false;
						}
					}
				}

				if ( ! valid) {
					txt += "<strong>";
					txt += bbpc_data.attachments.text.file_validation;

					if ( ! valid_dupe) {
						txt += " " + bbpc_data.attachments.text.file_validation_duplicate;
					}

					if ( ! valid_type) {
						txt += " " + bbpc_data.attachments.text.file_validation_type;
					}

					if ( ! valid_size) {
						txt += " " + bbpc_data.attachments.text.file_validation_size;
					}

					txt += "</strong><br/>";

					block.addClass( "bbpc-attachment-invalid" );
				}

				if (valid) {
					if (bbpc_data.attachments.set_caption_file) {
						txt += "<div><label><input name='bbpc-attachment_caption[]' type='text' style='display: none' placeholder='" + bbpc_data.attachments.text.file_caption_placeholder + "' /><span class='bbpc-accessibility-show-for-sr'>" + bbpc_data.attachments.text.file_caption_placeholder + "</span></label><a data-file='" + file.name + "' class='bbp-att-caption' href='#'>" + bbpc_data.attachments.text.file_caption + "</a></div>";
					}

					if (bbpc_data.attachments.insert_into_content) {
						txt += "<div><a class='bbp-att-shortcode' href='#'>" + bbpc_data.attachments.text.file_shortcode + "</a></div>";
					}
				}

				txt += "<div><a class='bbp-att-remove' href='#'>" + bbpc_data.attachments.text.file_remove + "</a></div>";

				block.find( ".bbpc-attachment-control" ).html( txt );
				block.find( ".bbpc-attachment-control .bbp-att-shortcode" ).data( 'file', file.name );
				block.find( ".bbpc-attachment-preview .bbpc-attached-file" ).remove();

				if (window.FileReader && regex.test( file.name.toLowerCase() )) {
					var reader = new FileReader();
					reader.readAsDataURL( file );

					reader.onloadend = function() {
						img = '<img class="bbpc-attached-file" alt="' + file.name + '" src="' + this.result + '" />';
						block.find( ".bbpc-attachment-preview" ).prepend( img );
					};
				} else {
					img = '<p class="bbpc-attached-file" title="' + file.name + '">.' + ext.toUpperCase() + '</p>';
					block.find( ".bbpc-attachment-preview" ).prepend( img );
				}

				wp.bbpc.attachments.inner++;

				wp.bbpc.attachments.add( ".bbpc-attachments-form .bbpc-attachment-add-file" );
				wp.bbpc.attachments.enhanced.check_submit();
			},
			check_submit: function() {
				var valid = true;

				$( ".bbpc-attachments-form .bbpc-attachments-input" ).each(
					function() {
						if ($( this ).hasClass( "bbpc-attachment-invalid" )) {
							valid = false;
						}
					}
				);

				$( ".bbpc-attachments-form" ).closest( "form" ).find( ".bbp-submit-wrapper button" ).attr( "disabled", ! valid );
			},
			block: function() {
				var block = '<div class="bbpc-attachments-input bbpc-validation-active">';
				block    += '<div role="button" class="bbpc-attachment-preview"><span aria-hidden="true">' + bbpc_data.attachments.text.select_file + '</span></div>';
				block    += '<label><input type="file" size="40" name="bbpc-attachment[]" />';
				block    += '<span class="bbpc-accessibility-show-for-sr">' + bbpc_data.attachments.text.select_file + '</span></label>';
				block    += '<div class="bbpc-attachment-control"></div>';
				block    += '</div>';

				return block;
			}
		},
		basic: {
			run: function() {
			},
			actions: function() {
			},
			block: function() {
				var block = '<div class="bbpc-attachments-input bbpc-validation-disabled">';
				block    += '<div class="bbpc-attachment-header"><span aria-hidden="true">' + bbpc_data.attachments.text.select_file + '</span></div>';
				block    += '<label><input type="file" size="40" name="bbpc-attachment[]" />';
				block    += '<span class="bbpc-accessibility-show-for-sr">' + bbpc_data.attachments.text.select_file + '</span></label>';
				block    += '</div>';

				return block;
			}
		},
		thread: {
			nonce: '',
			topic: 0,
			current: 1,
			run: function() {
				$( document ).on(
					"click",
					".bbpc-attachments-thread-pager .__prev",
					function(e) {
						e.preventDefault();

						var form = $( this ).closest( ".bbp-form" );

						if (wp.bbpc.attachments.thread.current > 1) {
							wp.bbpc.attachments.thread.current--;

							wp.bbpc.attachments.thread.show( form );
						}
					}
				);

				$( document ).on(
					"click",
					".bbpc-attachments-thread-pager .__next",
					function(e) {
						e.preventDefault();

						var form = $( this ).closest( ".bbp-form" ),
						total    = $( ".bbpc-attachments-thread-pages", form ).data( "pages" );

						if (wp.bbpc.attachments.thread.current < total) {
							wp.bbpc.attachments.thread.current++;

							wp.bbpc.attachments.thread.show( form );
						}
					}
				);

				$( document ).on(
					"click",
					".bbpc-attachments-thread-control a",
					function(e) {
						e.preventDefault();

						var form = $( this ).parent().parent().find( ".bbp-form" );

						wp.bbpc.attachments.thread.nonce = $( this ).data( "nonce" );
						wp.bbpc.attachments.thread.topic = $( this ).data( "topic" );

						$( this ).parent().remove();

						form.show();

						wp.bbpc.attachments.thread.fetch( form, 1 );
					}
				);
			},
			show: function(form) {
				var pages = $( ".bbpc-attachments-thread-pages", form ),
					pager = $( ".bbpc-attachments-thread-pager", form ),
					total = pages.data( "pages" ),
					now   = wp.bbpc.attachments.thread.current,
					show  = $( ".bbpc-attachments-thread-page-" + wp.bbpc.attachments.thread.current, form );

				pager.removeClass( "bbpc-thread-current-first" ).removeClass( "bbpc-thread-current-last" );

				if (now === 1) {
					pager.addClass( "bbpc-thread-current-first" );
				} else if (now === total) {
					pager.addClass( "bbpc-thread-current-last" );
				}

				$( ".__current", pager ).html( now );

				if (show.hasClass( "bbpc-attachments-thread-empty" )) {
					wp.bbpc.attachments.thread.fetch( form, now );
				} else {
					$( ".bbpc-attachments-thread-page", form ).hide();
					$( ".bbpc-attachments-thread-page-" + now, form ).show();
				}
			},
			fetch: function(form, pg) {
				$.ajax(
					{
						dataType: "html", type: "post", data: {
							nonce: wp.bbpc.attachments.thread.nonce,
							topic: wp.bbpc.attachments.thread.topic,
							page: pg
						},
						url: bbpc_data.url + "?action=bbpc_attachments_thread",
						success: function(html) {
							$( ".bbpc-attachments-thread-page", form ).hide();
							$( ".bbpc-attachments-thread-page-" + pg, form ).html( html ).removeClass( "bbpc-attachments-thread-empty" ).show();
							$( ".bbpc-attachments-thread-pager", form ).show();
						}
					}
				);
			}
		}
	};

	$( document ).ready(
		function() {
			wp.bbpc.attachments.init();
		}
	);
})( jQuery, window, document );
