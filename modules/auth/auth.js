/**
 * This module has to be configurable:
 * - where to put the login stuff
 * - which login template to use where
 * So there shall be maybe 2 or 3 templates,
 * maybe one for the menubar, one for a sidebar
 * and an inline login or even a popup...
 * If behavior is weird, turn on debug info in init-param:
 * newSettings.debug = true.
 */
define(
	'auth', // module name
	[ 'jquery', 'gapi', 'facebook' ], // required for this module
	function ( $, gapi, FB ) { // the required item passed in as an object once the required dependency has been loaded
		
		var TAG = 'modules.auth';
		
		var auth = {
			
			/**
			 * Log something to the console.
			 * Only works if settings.debug is set to true.
			 * @param {String} msg The message to display.
			 * @param {Boolean} error If the message shall be displayed in the usual style or in a red error style.
			 */
			log: function ( msg, error ) {
				
				if ( this.settings.debug ) {
					
					var style = 'background: #eee; color: #ff0000';
					
					if ( typeof( error ) == 'undefined' || error == false ) {
						
						style = '';
						
					}
					
					if ( typeof( msg ) == 'undefined' || msg.length <= 0 ) {
						
						msg = 'missing msg';
						
					}
					
					if ( typeof( msg ) != 'object' ) {
						
						// a styled console log msg
						console.log( '%c ' + TAG + ' >> ' + msg + ' ', style );
						
					} else {
						
						console.log( TAG + ' >>' );
						console.log( msg );
						
					}
					
				}
				
			},
			
			// some defaults
			settings: {
				
				placement: {
					// "template[0]" will be mapped to "toElement[0]" and so on...
					templates: [ 'auth_navbar_item' ], // template(s) from modules/auth/templates
					toElements: [ '#placeItHere' ] // element selector(s) to which the template(s) shall be rendered to
				},
				
				debug: false // console.log information about what is going on
				
			},
			
			// overwrite settings if set and show the ui element
			init: function ( newSettings ) {
				
				window.gplusCallback = function ( authResult ) {
					
					// the auto-rendered g+ sign in btn is surrounded by an extra div which is out of place...
					$( '#gplus_login' ).parent().css( 'top', '12px' );
					$( '#gplus_login' ).parent().attr( 'title', 'login mit google plus' );
					
					if (authResult['access_token']) {
						
						aa.userdata = {};
						
						// Successfully authorized
						// Hide the sign-in button now that the user is authorized, for example:
						//document.getElementById('signinButton').setAttribute('style', 'display: none');
						console.log( 'success' );
						console.log(authResult);
						
						// google recommends hiding the sign in button when there is a valid access token in the authResult
						
						gapi.auth.setToken(authResult); // Store the returned token.
						
						// use the oauth client to get authorization data
						gapi.client.load( 'oauth2', 'v2', function() {
							gapi.client.oauth2.userinfo.get().execute( function( response ) {
								// Shows user email
								console.log( response );
								$.extend( aa.userdata, response );
								// use the plus client to get user data
								gapi.client.load( 'plus', 'v1', function() {
									gapi.client.plus.people.get({ 'userId' : 'me' }).execute( function( response ) {
										
										// Shows other profile information
										console.log( response );
										$.extend( aa.userdata, response );
										if ( aa.gplusFirstStart == true ) {
											aa.gplusFirstStart = false; // do not directly login the user via g+ if he is logged in in this browser, gplus initializes directly and if the user is logged in it calls this function. also called by the user clicking the g+ login btn.
										} else {
											this.finalLogin( aa.userdata );
										}
										
									});
								});
							});
						});
						
					} else if (authResult['error']) {
						// There was an error.
						// Possible error codes:
						//   "access_denied" - User denied access to your app
						//   "immediate_failed" - Could not automatically log in the user
						// console.log('There was an error: ' + authResult['error']);
						console.log( 'error' );
						console.log(authResult);
						
					}
					
				};
				
				if ( typeof( newSettings ) == 'object' ) {
					
					$.extend( true, this.settings, newSettings );
					
				} else {
					
					this.log( 'init >> no settings object found! use one like that:', true );
					this.log( this.settings );
					
					return false;
					
				}
				
				if ( typeof( this.settings.placement.templates ) == 'undefined' || typeof( this.settings.placement.toElements ) == 'undefined' ) {
					
					this.log( 'init >> the templates array or the elements array is empty (undefined)', true );
					this.log( this.settings.placement );
					return false;
					
				}
				
				if ( this.settings.placement.templates.length <= 0 || this.settings.placement.toElements.length <= 0 ) {
					
					this.log( 'init >> the templates array or the elements array is of zero length', true );
					this.log( this.settings.placement );
					return false;
					
				}
				
				if ( this.settings.placement.templates.length != this.settings.placement.toElements.length ) {
					
					this.log( 'init >> the chosen templates are not of the same number as the elements where they have to be rendered to', true );
					this.log( this.settings.placement );
					return false;
					
				}
				
				var templates = this.settings.placement.templates;
				var elements = this.settings.placement.toElements;
				
				// load the templates to the elements.
				// note that the indices have to be 0, 1, 2,... in templates and elements!
				// each template is mapped to the element of the same index.
				for ( var index in templates ) {
					
					try {
						
						$( elements[ index ] ).load( 'modules/auth/templates/' + templates[ index ] + '.phtml' );
						
					} catch( e ) {
						
						this.log( e, true );
						
					}
					
				}
				
/*
 * This was just for testing!
 * FB initialization is done in main.js in the requirejs callback.
 *
				// init fb and render fb-buttons and other xfbml stuff
				window.fbAsyncInit = function () {
					
					FB.init({
						appId: '169227223237688', // App ID
						status: true, // check login status
						cookie: true, // enable cookies to allow the server to access the session
						xfbml:  true  // parse XFBML
					});
					
					FB.XFBML.parse();
					
				};
*/
				
			},
			
			// functions are also possible :)
			test: function () {
				
				this.log( 'test >> auth module test function' );
				
				this.log( 'test >> current config:' );
				
				this.log( this.settings );
				
				this.log( 'test >> ERROR TEST', true );
				
			},
			
			/**
			 * No matter coming from where (email, fb, g+, twitter),
			 * call the login ajax script from here.
			 * @param {Object} userData An object wrapping the user's data, e.g. an email/password-pair or the g+-id.
			 * @param {String} mode The mode which will be distinguished by the login ajax script.
			 */
			login: function ( userData, mode ) {
				
				if ( typeof( mode ) == 'undefined' || mode.length <= 0 ) {
					
					mode = 'email';
					
				}
				
				this.log( 'login >> fetched userdata:', true );
				
				this.log( userData );
				
				var that = this;
				
				$.ajax({
					url: 'modules/auth/login_user.php',
					type: 'POST',
					dataType: 'JSON',
					data: {
						userData: userData,
						mode: mode
					},
					success: function ( response ) {
						
						that.log( 'login >> login callback says: ' + response.success, true );
						
						
						
					}
				});
				
			},
			
			/**
			 * Opens the twitter auth file in a popup.
			 */
			twitter_popup: function () {
				
				// opens the twitter auth sign in popup
				popup_window = window.open( 'modules/auth/twitter_auth.php?popup=true','twitter-login','height=500,width=600' ); // the twitter auth dialog is responsive and will fit itself to an appropriate size
				
			},
			
			twitter_popup_callback: function ( response ) {
				
				// twitter popup sign in
				console.log( 'twitter login callback fetched response' );
				console.log( response );
				
				popup_window.close();
				
				if ( typeof( response ) == 'string' ) {
					
					if ( response == 'false' ) {
						
						console.log( 'oh my! the user canceled twitter login...' );
						
					} else {
						
						this.finalLogin( response );
						
					}
					
				}
				
			},
			
			facebook_popup: function () {
				
				
				
			},
			
			/**
			 * Do the final login stuff,
			 * such as changing "login" to
			 * "profile" with a logout fct,
			 * or saving / updating the
			 * user profile in the db...
			 */
			finalLogin: function ( userdata ) {
				
				//$( '#auth_module_login' ).html( 'profile' );

				this.auth.init({
					placement: {
						// "template[0]" will be mapped to "toElement[0]" and so on...
						templates:  [ 'auth_navbar_profile' ],
						toElements: [ '#menu_login' ]
					},
					debug: true
				});
				
			}
			
		};
		
		return auth;
		
	}
);