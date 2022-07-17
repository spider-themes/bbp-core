;/*jslint regexp: true, nomen: true, undef: true, sloppy: true, eqeq: true, vars: true, white: true, plusplus: true, maxerr: 50, indent: 4 */(function($, window, document, undefined) {
	window.wp      = window.wp || {};
	window.wp.bbpc = window.wp.bbpc || {};

	window.wp.bbpc.widgets = {
		views: function() {
			$( document ).on(
				"click",
				"a.bbpc-tab-topics-views",
				function() {
					wp.bbpc.widgets.run( $( this ).closest( ".d4plib-widget" ).find( ".bbpc-views-list" ), ".bbpc-views-ul" );
				}
			);

			$( document ).on(
				"click",
				"a.bbpc-tab-topics-stats",
				function() {
					wp.bbpc.widgets.run( $( this ).closest( ".d4plib-widget" ).find( ".bbpc-stats-list" ), ".bbpc-stats-ul" );
				}
			);
		},
		run: function(el, key) {
			if ( ! el.hasClass( "bbpc-active" )) {
				$( key, el ).sortable();

				$( el ).addClass( "bbpc-active" );
			}
		}
	};

	wp.bbpc.widgets.views();
})( jQuery, window, document );
