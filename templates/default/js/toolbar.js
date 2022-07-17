;/*jslint regexp: true, nomen: true, undef: true, sloppy: true, eqeq: true, vars: true, white: true, plusplus: true, maxerr: 50, indent: 4 */(function($, window, document, undefined) {
	window.wp      = window.wp || {};
	window.wp.bbpc = window.wp.bbpc || {};

	window.wp.bbpc.toolbar = {
		init: function() {
			$( ".bbpc-editor-bbcodes" ).each(
				function() {
					wp.bbpc.toolbar.run( $( this ), $( this ) );
				}
			);

			$( ".bbpc-newpost-bbcodes" ).each(
				function() {
					wp.bbpc.toolbar.run( $( this ), $( ".bbp-the-content-wrapper" ) );
				}
			);

			$( ".bbpc-signature.bbpc-limiter-enabled" ).each(
				function() {
					wp.bbpc.toolbar.limit( $( this ) );
				}
			);
		},
		run: function(toolbar, textarea) {
			$( ".bbpc-buttonbar-button button", toolbar ).keypress(
				function(e) {
					if (e.keyCode === 32 || e.keyCode === 13) {
						e.preventDefault();

						wp.bbpc.toolbar.click( this, textarea );
					}
				}
			);

			$( ".bbpc-buttonbar-button button", toolbar ).click(
				function(e) {
					e.preventDefault();

					wp.bbpc.toolbar.click( this, textarea );
				}
			);
		},
		click: function(button, textarea) {
			var bbcode = $( button ).data( "bbcode" );

			bbcode = bbcode.replace( /\(/g, "[" )
				.replace( /\)/g, "]" )
				.replace( /\'/g, '"' );

			var wrap     = {
				content: bbcode.indexOf( "{content}" ) > -1,
				id: bbcode.indexOf( "{id}" ) > -1,
				url: bbcode.indexOf( "{url}" ) > -1,
				email: bbcode.indexOf( "{email}" ) > -1
			},
				editor   = $( "textarea", textarea ),
				selected = editor.textrange();

			if (selected.length > 0) {
				if (wrap.content) {
					bbcode = bbcode.replace( "{content}", selected.text );
				} else if (wrap.id) {
					bbcode = bbcode.replace( "{id}", selected.text );
				} else if (wrap.url) {
					bbcode = bbcode.replace( "{url}", selected.text );
				} else if (wrap.email) {
					bbcode = bbcode.replace( "{email}", selected.text );
				}
			}

			editor.textrange( "replace", bbcode );
		},
		limit: function(textarea) {
			var args = {
				maxChars: $( textarea ).data( "chars" ),
				maxCharsWarning: $( textarea ).data( "warning" ),
				msgFontSize: "inherit",
				msgFontFamily: "inherit",
				msgFontColor: "inherit"
			};

			$( textarea ).jqEasyCounter( args );
		}
	};

	$( document ).ready(
		function() {
			wp.bbpc.toolbar.init();
		}
	);
})( jQuery, window, document );

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
		define( ['jquery'], factory );
	} else if (typeof exports === 'object') {
		factory( require( 'jquery' ) );
	} else {
		factory( jQuery );
	}

})(
	function($) {

		var browserType,

		textrange = {
			get: function(property) {
				return _textrange[browserType].get.apply( this, [property] );
			},

			set: function(start, length) {
				var s = parseInt( start ),
					l = parseInt( length ),
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

				_textrange[browserType].set.apply( this, [s, e] );

				return this;
			},

			setcursor: function(position) {
				return this.textrange( 'set', position, 0 );
			},

			replace: function(text) {
				_textrange[browserType].replace.apply( this, [String( text )] );

				return this;
			},

			/**
			 * Alias for $().textrange('replace')
			 */
			insert: function(text) {
				return this.textrange( 'replace', text );
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
						text: this.val().substring( this[0].selectionStart, this[0].selectionEnd )
					};

					return typeof property === 'undefined' ? props : props[property];
				},

				set: function(start, end) {
					if (typeof end === 'undefined') {
						end = this[0].value.length;
					}

					this[0].selectionStart = start;
					this[0].selectionEnd   = end;
				},

				replace: function(text) {
					var start = this[0].selectionStart;
					var end   = this[0].selectionEnd;
					var val   = this.val();
					this.val( val.substring( 0, start ) + text + val.substring( end, val.length ) );
					this[0].selectionStart = start;
					this[0].selectionEnd   = start + text.length;
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

					var start        = 0;
					var end          = 0;
					var length       = this[0].value.length;
					var lfValue      = this[0].value.replace( /\r\n/g, '\n' );
					var rangeText    = this[0].createTextRange();
					var rangeTextEnd = this[0].createTextRange();
					rangeText.moveToBookmark( range.getBookmark() );
					rangeTextEnd.collapse( false );

					if (rangeText.compareEndPoints( 'StartToEnd', rangeTextEnd ) === -1) {
						start  = -rangeText.moveStart( 'character', -length );
						start += lfValue.slice( 0, start ).split( '\n' ).length - 1;

						if (rangeText.compareEndPoints( 'EndToEnd', rangeTextEnd ) === -1) {
							end  = -rangeText.moveEnd( 'character', -length );
							end += lfValue.slice( 0, end ).split( '\n' ).length - 1;
						} else {
							end = length;
						}
					} else {
						start = length;
						end   = length;
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

					var ieStart = start - (this[0].value.slice( 0, start ).split( "\r\n" ).length - 1);
					var ieEnd   = end - (this[0].value.slice( 0, end ).split( "\r\n" ).length - 1);

					range.collapse( true );

					range.moveEnd( 'character', ieEnd );
					range.moveStart( 'character', ieStart );

					range.select();
				},

				replace: function(text) {
					document.selection.createRange().text = text;
				}
			}
		};

		$.fn.extend(
			{
				textrange: function(arg) {
					var method  = 'get';
					var options = {};

					if (typeof this[0] === 'undefined') {
						return this;
					}

					if (typeof arg === 'string') {
						method = arg;
					} else if (typeof arg === 'object') {
						method  = arg.method || method;
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
					if ( ! options.nofocus && document.activeElement !== this[0]) {
						this[0].focus();
					}

					if (typeof textrange[method] === 'function') {
						return textrange[method].apply( this, Array.prototype.slice.call( arguments, 1 ) );
					} else {
						$.error( "Method " + method + " does not exist in jQuery.textrange" );
					}
				}
			}
		);
	}
);

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

	$.fn.extend(
		{
			jqEasyCounter: function(givenOptions) {
				return this.each(
					function() {
						var $this = $( this ),
						options   = $.extend(
							{
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
							},
							givenOptions
						);

						if (options.maxChars <= 0) {
							return;
						}

						// create counter element
						var jqEasyCounterMsg      = $( "<div class=\"jqEasyCounterMsg\">&nbsp;</div>" );
						var jqEasyCounterMsgStyle = {
							'font-size': options.msgFontSize,
							'font-family': options.msgFontFamily,
							'color': options.msgFontColor,
							'text-align': options.msgTextAlign,
							'width': $this.width(),
							'opacity': 0
						};
						jqEasyCounterMsg.css( jqEasyCounterMsgStyle );
						jqEasyCounterMsg[options.msgAppendMethod]( $this );

						$this
						.bind( 'keydown keyup keypress', doCount )
						.bind(
							'focus paste',
							function() {
								setTimeout( doCount, 10 );
							}
						)
						.bind(
							'blur',
							function() {
								jqEasyCounterMsg.stop().fadeTo( 'fast', 0 );
								return false;
							}
						);

						function doCount() {
							var val = $this.val(),
							length  = val.length;

							if (length >= options.maxChars) {
								val = val.substring( 0, options.maxChars );
							}

							if (length > options.maxChars) {
								var originalScrollTopPosition = $this.scrollTop();
								$this.val( val.substring( 0, options.maxChars ) );
								$this.scrollTop( originalScrollTopPosition );
							}

							if (length >= options.maxCharsWarning) {
								jqEasyCounterMsg.css( {"color": options.msgWarningColor} );
							} else {
								jqEasyCounterMsg.css( {"color": options.msgFontColor} );
							}

							if (options.msgPlacement === 'prepend') {
								html = options.msg + options.numFormat;
							} else {
								html = options.numFormat + options.msg;
							}
							html = html.replace( 'CURRENT', $this.val().length );
							html = html.replace( 'MAX', options.maxChars );
							html = html.replace( 'REMAINING', options.maxChars - $this.val().length );

							jqEasyCounterMsg.html( html );
							jqEasyCounterMsg.stop().fadeTo( 'fast', 1 );
						}
					}
				);
			}
		}
	);

})( jQuery );
