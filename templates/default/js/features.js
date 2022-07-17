;/*jslint regexp: true, nomen: true, undef: true, sloppy: true, eqeq: true, vars: true, white: true, plusplus: true, maxerr: 50, indent: 4 */
/*global bbpc_data, tinymce, tinyMCE */(function($, window, document, undefined) {
	window.wp      = window.wp || {};
	window.wp.bbpc = window.wp.bbpc || {};

	window.wp.bbpc.helper = {
		detect_msie: function() {
			var ua   = window.navigator.userAgent,
				msie = ua.indexOf( 'MSIE ' );

			if (msie > 0) {
				return parseInt( ua.substring( msie + 5, ua.indexOf( '.', msie ) ), 10 );
			}

			var trident = ua.indexOf( 'Trident/' );

			if (trident > 0) {
				var rv = ua.indexOf( 'rv:' );

				return parseInt( ua.substring( rv + 3, ua.indexOf( '.', rv ) ), 10 );
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

			return $.trim( t.toString() );
		},
		file_extension: function(name) {
			return name.substr( name.lastIndexOf( "." ) + 1 ).toLowerCase();
		},
		is_tinymce: function() {
			var id = $( "#bbp_topic_content" ).length > 0 ? "bbp_topic_content" : "bbp_reply_content";

			return bbpc_data.wp_editor && ! $( "#" + id ).is( ":visible" );
		},
		into_editor: function(text) {
			var id = $( "#bbp_topic_content" ).length > 0 ? "bbp_topic_content" : "bbp_reply_content";

			if (wp.bbpc.helper.is_tinymce()) {
				text += "<br/><br/>";

				tinymce.get( id ).execCommand( "mceInsertContent", false, text );
			} else {
				var txtr = $( "#" + id ),
					cntn = txtr.val();

				if ($.trim( cntn ) !== "") {
					text = "\n\n" + text;
				}

				text += "\n\n";

				txtr.val( cntn + text );
			}
		},
		scroll_to_editor: function() {
			if (wp.bbpc.helper.detect_msie() > 8) {
				$( "html, body" ).animate( {scrollTop: $( "#new-post" ).offset().top}, 1000 );
			} else {
				document.location.href = "#new-post";
			}

			$( ".bbp-the-content-wrapper textarea" ).focus();
		}
	};

	window.wp.bbpc.toolbox = {
		bbcodes: {
			init: function() {
				$( ".bbpc-bbcode-spoiler" ).each(
					function() {
						var hover = $( this ).data( "hover" ),
						normal    = $( this ).data( "color" );

						$( this ).hover(
							function() {
								$( this ).css( "background", hover );
							},
							function() {
								$( this ).css( "background", normal );
							}
						);
					}
				);
			}
		},
		quotes: {
			init: function() {
				$( document ).on(
					"click",
					".bbpc-link-quote",
					function(e) {
						e.preventDefault();

						if ($( "#bbp_reply_content" ).length > 0) {
							var qout = wp.bbpc.helper.get_selection(), id = $( this ).data( "id" ),
							eol      = wp.bbpc.helper.is_tinymce() ? "</br></br>" : "\n",
							quote_id = "#bbpc-quote-wrapper-" + id, is_selection = true;

							if (qout === "") {
								is_selection = false;
								qout         = $( quote_id ).html();
							}

							qout = qout.replace( /&nbsp;/g, " " );
							qout = qout.replace( /<\s*p[^>]*>/g, "" );
							qout = qout.replace( /<\s*\/\s*(p|br)\s*>|<\s*br\s*>/g, eol );
							qout = qout.trim();

							qout = $( "<div>" ).html( qout ).html();

							if (bbpc_data.quote.method === "bbcode") {
								var code = bbpc_data.quote.bbcode === "postquote" && ! is_selection ? "postquote" : "quote";

								if (code === "postquote") {
									qout = "[" + code + " quote=" + id + "]";
								} else {
									qout = "[" + code + " quote=" + id + "]" + qout + "[/" + code + "]";
								}
							} else {
								var author = bbpc_data.quote.wrote.replace( '%s', $( this ).data( "author" ) ),
								title      = '<div class="bbpc-quote-title"><a href="' + $( this ).data( "url" ) + '">' + author + ':</a></div>';

								qout = '<blockquote class="bbpc-bbcode-quote">' + title + qout + '</blockquote>';
							}

							wp.bbpc.helper.into_editor( qout );
							wp.bbpc.helper.scroll_to_editor();
						}
					}
				);
			}
		},
		canned_replies: {
			init: function() {
				$( ".bbpc-canned-replies .bbpc-canned-replies-show" ).click(
					function(e) {
						e.preventDefault();

						var container = $( this ).closest( ".bbpc-canned-replies" );

						$( this ).hide();
						$( ".bbpc-canned-replies-hide", container ).show();
						$( ".bbpc-canned-replies-list", container ).slideDown();
					}
				);

				$( ".bbpc-canned-replies .bbpc-canned-replies-hide" ).click(
					function(e) {
						e.preventDefault();

						var container = $( this ).closest( ".bbpc-canned-replies" );

						$( this ).hide();
						$( ".bbpc-canned-replies-show", container ).show();
						$( ".bbpc-canned-replies-list", container ).slideUp();
					}
				);

				$( ".bbpc-canned-replies .bbpc-canned-reply-insert" ).click(
					function(e) {
						e.preventDefault();

						var container = $( this ).closest( ".bbpc-canned-reply" ),
						content       = $( ".bbpc-canned-reply-content", container ).html();

						wp.bbpc.helper.into_editor( content );

						if (bbpc_data.canned_replies.auto_close_on_insert) {
							var wrapper = $( this ).closest( ".bbpc-canned-replies" );

							$( ".bbpc-canned-replies-hide", wrapper ).click();
						}
					}
				);
			}
		},
		fitvids: {
			init: function() {
				$( ".bbp-topic-content, .bbp-reply-content" ).fitVids();
			}
		},
		report: {
			sending_report: false,
			init: function() {
				$( ".bbpc-link-report" ).click(
					function(e) {
						e.preventDefault();

						if ( ! wp.bbpc.toolbox.report.sending_report) {
							if (bbpc_data.report.mode === "form") {
								wp.bbpc.toolbox.report.form( $( this ) );
							} else {
								wp.bbpc.toolbox.report.button( $( this ) );
							}
						}
					}
				);
			},
			button: function(el) {
				var id = el.data( "id" ), nonce = el.data( "nonce" );

				if (bbpc_data.report.mode === "confirm") {
					if (confirm( bbpc_data.report.confirm ) === false) {
						return;
					}
				}

				var call = {
					post: id,
					nonce: nonce
				};

				wp.bbpc.toolbox.report.sending_report = true;

				$.ajax(
					{
						dataType: "html", type: "post", data: call,
						url: bbpc_data.url + "?action=bbpc_report_post",
						success: function(html) {
							wp.bbpc.toolbox.report.sending_report = false;

							$( ".bbpc-link-report-" + call.post ).replaceWith( "<span>" + bbpc_data.report.after + "</span>" );
						}
					}
				);
			},
			form: function(el) {
				var id      = el.data( "id" ), nonce = el.data( "nonce" ),
					content = el.closest( "#bbpress-forums" ).find( ".post-" + id + " .bbp-reply-content, .post-" + id + " .bbp-topic-content" );

				if (content.length === 1) {
					if (content.find( ".bbpc-report-wrapper" ).length === 0) {
						$( ".bbpc-report-wrapper" ).remove();

						var form = $( ".bbpc-report-template > div" )
							.clone()
							.addClass( "bbpc-report-wrapper" );

						form.find( "button" )
							.data( "id", id )
							.data( "nonce", nonce );

						content.append( form );

						form.find( "input" ).focus();

						wp.bbpc.toolbox.report.handle( content.find( ".bbpc-report-wrapper" ) );
					}

					if (bbpc_data.report.scroll) {
						var offset = 0;

						if ($( "#wpadminbar" ).length > 0) {
							offset = $( "#wpadminbar" ).height();
						}

						$( "html, body" ).animate(
							{
								scrollTop: content.find( ".bbpc-report-wrapper" ).offset().top - offset
							},
							500
						);
					}
				}
			},
			handle: function(el) {
				$( "button.bbpc-report-cancel", el ).click(
					function() {
						$( ".bbpc-report-wrapper" ).remove();
					}
				);

				$( "button.bbpc-report-send", el ).click(
					function() {
						var text = $( "input", el ).val();

						if (text.length < bbpc_data.report.min) {
							alert( bbpc_data.report.alert );
						} else {
							var call = {
								report: text,
								post: $( this ).data( "id" ),
								nonce: $( this ).data( "nonce" )
							};

							$( ".bbpc-report-form", el ).hide();
							$( ".bbpc-report-sending", el ).show();

							wp.bbpc.toolbox.report.sending_report = true;

							$.ajax(
								{
									dataType: "html", type: "post", data: call,
									url: bbpc_data.url + "?action=bbpc_report_post",
									success: function(html) {
										wp.bbpc.toolbox.report.sending_report = false;

										$( ".bbpc-report-sending", el ).hide();
										$( ".bbpc-report-sent", el ).show();

										$( ".bbpc-link-report-" + call.post ).replaceWith( "<span>" + bbpc_data.report.after + "</span>" );
									}
								}
							);
						}
					}
				);
			}
		},
		thanks: {
			init: function() {
				$( ".bbpc-link-thanks, .bbpc-link-unthanks" ).click(
					function(e) {
						e.preventDefault();

						wp.bbpc.toolbox.thanks.handle( this );
					}
				);
			},
			handle: function(el) {
				var call      = {
					nonce: $( el ).data( "thanks-nonce" ),
					say: $( el ).data( "thanks-action" ),
					id: $( el ).data( "thanks-id" )
				}, button     = $( el ),
					is_thanks = button.hasClass( "bbpc-link-thanks" );

				$.ajax(
					{
						dataType: "html", type: "post", data: call,
						url: bbpc_data.url + "?action=bbpc_say_thanks",
						success: function(html) {
							var thanks = $( html ).fadeIn( 600 );

							$( ".bbpc-thanks-post-" + call.id ).fadeOut( 400 ).replaceWith( thanks );

							if (is_thanks) {
								if (bbpc_data.thanks.removal) {
									button.removeClass( "bbpc-link-thanks" )
									.addClass( "bbpc-link-unthanks" )
									.data( "thanks-action", "unthanks" )
									.html( bbpc_data.thanks.unthanks );
								} else {
									button.replaceWith( "<span>" + bbpc_data.thanks.saved + "</span>" );
								}
							} else {
								button.removeClass( "bbpc-link-unthanks" )
								.addClass( "bbpc-link-thanks" )
								.data( "thanks-action", "thanks" )
								.html( bbpc_data.thanks.thanks );
							}
						}
					}
				);
			}
		},
		scheduler: {
			init: function() {
				var field = $( "#bbpc_schedule_datetime" );

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

					if (bbpc_data.flatpickr_locale !== "") {
						args.locale = bbpc_data.flatpickr_locale;
					}

					field.flatpickr( args );

					$( document ).on(
						"change",
						"#bbpc_schedule_when",
						function() {
							var when = $( this ).val(), field = $( this ).parent().parent().next();

							if (when === 'future') {
								field.show();
							} else {
								field.hide();
							}
						}
					);
				}
			}
		},
		misc: {
			privacy: function() {
				$( ".bbpc-private-reply-hidden" ).each(
					function() {
						$( this ).hide().prev( ".bbp-reply-header" ).hide();
					}
				);
			}
		},
		run: function() {
			if (typeof bbpc_data !== "undefined") {
				wp.bbpc.toolbox.misc.privacy();

				if (bbpc_data.load.indexOf( 'scheduler' ) > -1) {
					wp.bbpc.toolbox.scheduler.init();
				}

				if (bbpc_data.load.indexOf( 'quote' ) > -1) {
					wp.bbpc.toolbox.quotes.init();
				}

				if (bbpc_data.load.indexOf( 'bbcodes' ) > -1) {
					wp.bbpc.toolbox.bbcodes.init();
				}

				if (bbpc_data.load.indexOf( 'report' ) > -1) {
					wp.bbpc.toolbox.report.init();
				}

				if (bbpc_data.load.indexOf( 'thanks' ) > -1) {
					wp.bbpc.toolbox.thanks.init();
				}

				if (bbpc_data.load.indexOf( 'canned_replies' ) > -1) {
					wp.bbpc.toolbox.canned_replies.init();
				}

				if (bbpc_data.load.indexOf( 'fitvids' ) > -1 && $.fn.fitVids) {
					wp.bbpc.toolbox.fitvids.init();
				}
			}
		}
	};

	$( document ).ready(
		function() {
			wp.bbpc.toolbox.run();

			if (typeof EnlighterJS === 'object') {
				EnlighterJS.init( 'pre.bbpc-bbcode-scode' );
			}
		}
	);
})( jQuery, window, document );
