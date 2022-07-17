;/*jslint regexp: true, nomen: true, undef: true, sloppy: true, eqeq: true, vars: true, white: true, plusplus: true, maxerr: 50, indent: 4 */
/*global d4plib_admin_data, bbpc_admin_data*/(function($, window, document, undefined) {
	window.wp      = window.wp || {};
	window.wp.bbpc = window.wp.bbpc || {};

	window.wp.bbpc.admin = {
		init: function() {
			$( "#bbpc-form-settings" ).areYouSure();

			$( ".d4p-setting-expandable_list a.button-primary" ).click(
				function(e) {
					e.preventDefault();

					var list = $( this ).closest( ".d4p-setting-expandable_list" ),
					next     = $( ".d4p-next-id", list ),
					next_id  = next.val(),
					el       = $( ".list-element-0", list ).clone();

					$( "input", el ).each(
						function() {
							var id = $( this ).attr( "id" ).replace( "_0_", "_" + next_id + "_" ),
							name   = $( this ).attr( "name" ).replace( "[0]", "[" + next_id + "]" );

							$( this ).attr( "id", id ).attr( "name", name );
						}
					);

					el.attr( "class", "list-element-" + next_id ).fadeIn();
					$( this ).before( el );

					next_id++;
					next.val( next_id );
				}
			);
		},
		more: function() {
			$( document ).on(
				"change",
				".bbpc-content-editor-topic-selection input",
				function() {
					var type = $( this ).val();

					$( ".bbpc-content-editor-topic" ).removeClass( "bbpc-select-type-show" );
					$( ".bbpc-content-editor-topic-" + type ).addClass( "bbpc-select-type-show" );
				}
			);

			$( document ).on(
				"change",
				".bbpc-content-editor-reply-selection input",
				function() {
					var type = $( this ).val();

					$( ".bbpc-content-editor-reply" ).removeClass( "bbpc-select-type-show" );
					$( ".bbpc-content-editor-reply-" + type ).addClass( "bbpc-select-type-show" );
				}
			);

			$( document ).on(
				"click",
				".bbpc-feature-more-control .d4p-bulk-ctrl",
				function() {
					var on = $( this ).hasClass( "button-secondary" );

					if (on) {
						$( this ).removeClass( "button-secondary" ).addClass( "button-primary" );
						$( ".bbpc-inner-ctrl-options" ).hide();
						$( ".bbpc-feature-submit" ).show();
					} else {
						$( this ).removeClass( "button-primary" ).addClass( "button-secondary" );
						$( ".bbpc-inner-ctrl-options" ).show();
						$( ".bbpc-feature-submit" ).hide();
					}
				}
			);
		},
		bulk: function() {
			$( document ).on(
				"click",
				".bbpc-features-bulk-control .d4p-bulk-ctrl",
				function() {
					var on = $( this ).hasClass( "button-secondary" );

					if (on) {
						$( this ).removeClass( "button-secondary" ).addClass( "button-primary" );
						$( "input.feature-status, .bbpc-inner-ctrl-options" ).hide();
					} else {
						$( this ).removeClass( "button-primary" ).addClass( "button-secondary" );
						$( "input.feature-status, .bbpc-inner-ctrl-options" ).show();
					}
				}
			);

			$( document ).on(
				"click",
				".bbpc-inner-ctrl-options a",
				function(e) {
					e.preventDefault();

					var on = $( this ).attr( "href" ).substr( 1 );

					if (on === 'checkall') {
						$( ".d4p-options-panel .feature-status" ).prop( "checked", true );
					} else if (on === 'uncheckall') {
						$( ".d4p-options-panel .feature-status" ).prop( "checked", false );
					}
				}
			);
		},
		bbcodes: function() {
			$( document ).on(
				"change",
				".bbpc-grid-bbcodes thead th input, .bbpc-grid-bbcodes tfoot th input",
				function() {
					var name = $( this ).data( "column" ), checked = $( this ).is( ":checked" );

					$( ".bbpc-grid-bbcodes tbody td.column-" + name + " input" ).prop( "checked", checked );
				}
			);
		}
	};

	wp.bbpc.admin.init();

	$( document ).ready(
		function() {
			if (bbpc_admin_data.page === "features") {
				if (bbpc_admin_data.panel === "" || bbpc_admin_data.panel === "index") {
					wp.bbpc.admin.bulk();
				} else {
					wp.bbpc.admin.more();
				}
			}

			if (bbpc_admin_data.page === "bbcodes") {
				wp.bbpc.admin.bbcodes();
			}
		}
	);
})( jQuery, window, document );
