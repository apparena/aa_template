/**
 * This module has to be configurable:
 * - where to put the login stuff
 * - which login template to use where
 * So there shall be maybe 2 or 3 templates,
 * maybe one for the menubar, one for a sidebar
 * and an inline login or even a popup...
 * If behavior is weird, turn on debug info in init-param:
 * newSettings.debug = true.
 * 
 * @author: guntram pollock
 * @date: 06.05.2013
 */
define(
	'auth', // module name
	[ 'jquery', 'gapi', 'facebook' ], // required for this module
	function ( $, gapi, FB ) { // the required item passed in as an object once the required dependency has been loaded
		
		var TAG = 'modules.auth',
		    auth = {
			
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
						
						// a styled console log msg -> "modules.auth.login >> some message"
						console.log( '%c ' + TAG + '.' + msg + ' ', style );
						
					} else {
						
						/*
						 * if msg is an object we want to log it isolated,
						 * because it will be expandable (e.g. in the
						 * chrome console)
						 */
						console.log( TAG );
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
			
			/**
			 * Overwrite settings if set and
			 * initialize the ui elements.
			 * @param {Object} newSettings The settings which will override the default settings.
			 */
			init: function ( newSettings ) {
				
				// just copy the function scope for use in child functions here in the init (which have different "this"-scopes!)
				var that = this;
				
				/*
				 * !!IMPORTANT!!
				 * I had to put the g+ callback function
				 * to the window object because the
				 * stupid gapi will not know the auth module
				 * for some reason. Maybe this will get fixed
				 * or we will be able to access the module
				 * via the g+ api sometime...
				 */
				window.gplusCallback = function ( authResult ) {
					
					// the auto-rendered g+ sign in btn is surrounded by an extra div which is out of place...
					$( '#gplus_login' ).parent().css( 'top', '12px' );
					//$( '#gplus_login' ).parent().attr( 'title', 'login mit google plus' ); // does not work...
					
					if (authResult['access_token']) {
						
						aa.userdata = {};
						
						// Successfully authorized
						// Hide the sign-in button now that the user is authorized, for example:
						// document.getElementById('signinButton').setAttribute('style', 'display: none');
						// Well, thanx for the good idea google, but you can also do it with jquery and/or bootstrap ;)
						// $( '#gplus_login' ).fadeOut( 200 );
						// $( '#gplus_login' ).addClass( 'hide' );
						
						that.log( 'g+ callback >> success' ); // use the outer "this"-scope to console.log stuff if debug is on
						that.log( authResult );
						
						// google recommends hiding the sign in button when there is a valid access token in the authResult
						
						gapi.auth.setToken(authResult); // Store the returned token.
						
						// use the oauth client to get authorization data
						gapi.client.load( 'oauth2', 'v2', function() {
							
							gapi.client.oauth2.userinfo.get().execute( function( response ) {
								
								// Shows user email
								that.log( response );
								aa.userdata = $.extend( aa.userdata, response );
								// use the plus client to get user data
								gapi.client.load( 'plus', 'v1', function() {
									
									// this corresponds to the FB.api( '/me', ... -call
									gapi.client.plus.people.get({ 'userId' : 'me' }).execute( function( response ) {
										
										// Shows other profile information
										that.log( response );
										
										aa.userdata = $.extend( aa.userdata, response );
										
										if ( aa.gplusFirstStart == true ) {
											
											/*
											 * do not directly login the user via g+ if he is logged in with this browser, 
											 * gplus initializes directly and if the user is logged in it calls this function. 
											 * also called by the user clicking the g+ login btn.
											 */
											aa.gplusFirstStart = false;
											
										} else {
											
											that.login( aa.userdata, 'gplus' );
											
										}
										
									}); // end get "me" call
									
								}); // end plus v1 call
								
							}); // end gapi userinfo call
							
						}); // end gapi oauth2 call
						
					} else if ( authResult['error'] ) {
						
						// There was an error.
						// Possible error codes:
						//   "access_denied" - User denied access to your app
						//   "immediate_failed" - Could not automatically log in the user
						that.log( 'g+ callback >> error', true );
						that.log( authResult, true );
						
					}
					
				};
				
				if ( typeof( newSettings ) == 'object' ) {
					
					$.extend( true, this.settings, newSettings );
					
				} else {
					
					this.log( 'init >> no valid settings object found! use one like that:', true );
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
				
				var templates = this.settings.placement.templates,
				    elements  = this.settings.placement.toElements;
				
				// load the templates to the elements.
				// note that the indices have to be 0, 1, 2,... in templates and elements!
				// each template is mapped to the element of the same index.
				for ( var index in templates ) {
					
					if ( typeof( elements[ index] ) == 'undefined' ) {
						
						this.log( '!! init >> the index "' + index + '" is missing in the toElements array and will be skipped', true );
						this.log( this.settings.placement, true );
						continue; // skip this step of the loop
						
					}
					
					try {
						
						$( elements[ index ] ).load( 'modules/auth/templates/' + templates[ index ] + '.phtml' );
						
					} catch( e ) {
						
						this.log( '! init >> an error occured', true );
						this.log( e, true );
						
					}
					
				}
				
				// just to make sure FB has parsed stuff... (i know it wont if we dont call this ;) )
				FB.XFBML.parse();
				
			},
			
			/**
			 * Just a little test function displaying
			 * the config and showing a red error msg...
			 */
			test: function () {
				
				this.settings.debug = true;
				this.log( 'test >> auth module test function' );
				this.log( 'test >> current config:' );
				this.log( this.settings );
				this.log( 'test >> ERROR TEST', true );
				this.settings.debug = false;
				
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
					
					this.log( 'login >> mode not set, so "email" will be used' );
					
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
						that.finalLogin( response );
						
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
			
			/**
			 * This will be explicitly called by the
			 * twitter callback file "twitter_auth_callback.php".
			 * This file is set in the config.php of this project.
			 * (Do not mind the url set in the twitter app settings?!)
			 * @param {Object} response Some user data received from twitter.
			 */
			twitter_popup_callback: function ( response ) {
				
				var responseString = response;
				
				try {
					response = $.parseJSON( response );
				} catch ( e ) {}
				
				if ( this.settings.debug ) {
					
					// display the object in a little bit more readable way in html...
					$( '#responselog' ).append(
						'<br />==========================================' +
						'<h2>timestamp: ' + new Date() + '</h2>' +
						'<h2>twitter popup response:</h2>' +
						responseString.replace( /",/g, '",<br />').replace( /{/g, '{<br />' ).replace( /}/g, '<br />}' ) +
						'<br />'
					);
					
				}
				
				this.log( 'twitter_callback >> twitter login callback fetched response' );
				this.log( response );
				
				try {
					popup_window.close();
				} catch ( e ) {}
				
				popup_window = null;
				
				if ( typeof( response ) == 'string' ) {
					
					if ( response == 'false' ) {
						
						this.log( 'twitter_callback >> oh my! the user canceled twitter login...' );
						
					} else {
						
						aa.userdata = $.extend( aa.userdata, response );
						
						this.login( aa.userdata, 'twitter' );
						
					}
					
				} else {
					
					if ( typeof( response ) == 'object' ) {
						
						aa.userdata = $.extend( aa.userdata, response );
						
						this.login( aa.userdata, 'twitter' );
						
					} else {
						
						that.log( 'twitter_callback >> something went wrong', true );
						that.log( response );
						
					}
					
				}
				
			},
			
			/**
			 * Check the FB login status and
			 * fetch user data if the user is
			 * logged in with this browser.
			 * If the user is not logged in,
			 * he will be prompted to do so.
			 */
			facebook_popup: function () {
				
				var doLogin = false,
				    that    = this;
				
				FB.getLoginStatus( function( response ) {
					
					if ( response.status === 'connected' ) {
						
					    // the user is logged in and has authenticated your
					    // app, and response.authResponse supplies
					    // the user's ID, a valid access token, a signed
					    // request, and the time the access token 
					    // and signed request each expire
					    var uid         = response.authResponse.userID,
					        accessToken = response.authResponse.accessToken;
					    
					    FB.api( '/me', function ( response ) {
							
							//console.log('Good to see you, ' + response.name + '.');
							
							aa.userdata = $.extend( aa.userdata, response.authResponse );
							
							that.login( aa.userdata, 'fb' );
							
						});
					    
					} else if ( response.status === 'not_authorized' ) {
						
					    // the user is logged in to Facebook, 
					    // but has not authenticated your app
						doLogin = true;
						
					} else {
						
					    // the user isn't logged in to Facebook.
						doLogin = true;
						
					}
					
					if ( doLogin == true ) {
						
						doLogin = false;
						
						FB.login( function ( response ) {
							
							if ( response.authResponse ) {
								
								//console.log('Welcome!  Fetching your information.... ');
								
								FB.api( '/me', function ( response ) {
									
									//console.log('Good to see you, ' + response.name + '.');
									
									aa.userdata = $.extend( aa.userdata, response );
									
									that.login( aa.userdata, 'fb' );
									
								});
								
							 } else {
								 
								 //console.log('User cancelled login or did not fully authorize.');
								 
								 
							 }
							 
						}); // end login call
						
					}
					
				}); // end getLoginStatus call
				
			},
			
			/**
			 * Do the final login stuff,
			 * such as changing the "login"
			 * item in the menu to
			 * "profile" with a logout fct,
			 * or saving / updating the
			 * user profile in the db...
			 */
			finalLogin: function ( userdata ) {
				
				// re init everything
				this.init({
					placement: {
						// "template[0]" will be mapped to "toElement[0]" and so on...
						templates:  [ 'auth_navbar_profile' ],
						toElements: [ '#menu_login' ]
					},
					debug: this.settings.debug
				});
				
			},
			
			/**
			 * Open a twitter bootstrap modal for the user to
			 * register using a very simple form.
			 * (Only email, username and password
			 * are required...
			 * maybe leave out the username?!)
			 */
			register: function () {
				
				var that = this;
				
				// remove old container if it is present
				$( '#register_container' ).remove();
				
				$( 'body' ).append( '<div id="register_container"></div>' );
				
				// load the registration template for the modal
				$( '#register_container' ).load( 'modules/auth/templates/auth_register.phtml', function () {
					
					$( '#registration_modal' ).modal( 'show' );
					
					$( '#register_btn_login' ).on( 'click', function () {
						
						var password_repeat = $( '#register_password_repeat' ).val(),
						    validation      = false,
						    userdata        = {
								email: $( '#register_email' ).val(),
								password: $( '#register_password' ).val(),
								username: $( '#register_username' ).val()
							}; // these fields have to be in the registration template!!
						
						// VERY simple validation here!
						if ( userdata.email.length < 5 ||
							 userdata.email.indexOf( '.' ) <= 0 ||
							 userdata.email.indexOf( '@' ) <= 0 ) {
							
							// definitely invalid email ;)
							$( '#register_email_error' ).fadeIn( 300 );
							
							validation = false;
							
						} else {
							
							$( '#register_email_error' ).fadeOut( 300 );
							
							validation = true;
							
						}
						
						if ( userdata.password.length < 6 ) {
							
							// password is way too short ;)
							$( '#register_password_error' ).fadeIn( 300 );
							
							validation = false;
							
						} else {
							
							$( '#register_password_error' ).fadeOut( 300 );
							
							validation = true;
							
						}
						
						if ( userdata.password.length != password_repeat.length || userdata.password != password_repeat ) {
							
							// passwords do not match
							$( '#register_password_repeat_error' ).fadeIn( 300 );
							
							validation = false;
							
						} else {
							
							$( '#register_password_repeat_error' ).fadeOut( 300 );
							
							validation = true;
							
						}
						
						if ( userdata.username.length < 3 ) {
							
							// username is way too short ;)
							$( '#register_username_error' ).fadeIn( 300 );
							
							validation = false;
							
						} else {
							
							$( '#register_username_error' ).fadeOut( 300 );
							
							validation = true;
							
						}
						
						if ( validation == false ) {
							
							return false;
							
						}
						
						aa.userdata = $.extend( aa.userdata, userdata );
						
						that.login( aa.userdata, 'email' );
						
					});
					
				});
				
			}
			
		};
		
		return auth;
		
	}
);