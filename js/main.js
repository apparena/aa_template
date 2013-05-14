/* Preparation of the require config object */
require.config({
    baseUrl:'js',
    //urlArgs: "bust=" +  (new Date()).getTime(), // Be sure to comment this line before deploying app to live stage
    waitSeconds: 30, // increase load timeout for modules, default is something like 7-10 secs...
    paths:{
        //jquery:    '//cdnjs.cloudflare.com/ajax/libs/jquery/1.8.3/jquery.min',
    	jquery:    '//cdnjs.cloudflare.com/ajax/libs/jquery/1.8.3/jquery',
        bootstrap: '//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.2.2/bootstrap.min',
        facebook:  '//connect.facebook.net/de_DE/all',
        script:    'script',
        auth:      '../modules/auth/auth', // no need to shim...
        gapi:      '//apis.google.com/js/client:plusone' // maybe check if we can use the ready callback, usually: "https://apis.google.com/js/client:plusone.js?onload=myCallback"
    },
    shim:{ // load required non AMD modules here...
        jquery:{
            exports:'$'
        },
        bootstrap:{
            deps:[ 'jquery' ]
        },
        facebook:{
            exports:'FB'
        },
        script:{
            deps:[ 'jquery', 'facebook' ]
        },
        gapi: {
			exports: 'gapi'
		}
    }
});

// the main.js uses REQUIRE instead of define to set up the scripts (aliases) our app needs to run
// (the aliases are mapped in the require.config() above).
require([
    'jquery',
    'facebook',
    'bootstrap',
    'script',
    'gapi',
    'auth'
], function (
	$,
	FB,
	bootstrap,
	script,
	gapi,
	auth
) {

    FB.init({
        appId:aa.inst.fb_app_id, // App ID
        channelUrl:aa.inst.fb_canvas_url + '/channel.php', // Channel File
        status:true, // check login status
        cookie:true, // enable cookies to allow the server to access the session
        xfbml:true, // parse XFBML
        oauth:true,
        frictionlessRequests:true
    });
    FB.Canvas.setAutoGrow();
    /* Hide Fangate, if user clicks the like button */
    FB.Event.subscribe('edge.create', function(response) {
        $('#fangate').hide();
    });

    aa_tmpl_load("index.phtml");
    $('#terms-link').click(function () {
        aa_tmpl_load('terms.phtml');
    });
    
    /*
     * This is just used by the twitter callback file, which
     * has no access to require.
     * 
     * If you need to use the auth object anywhere, use the
     * require() method (like in the templates where the
     * module object is used) if you can!
     */
    aa.auth = auth;
    
    
    /*
     * Use the auth module to initialize the authorization panel as a menu item.
     * Each template will be shown in the according toElements array
     * item of the same index number. So if we use the auth_navbar_item template as the first
     * element in the templates array, the plugin will use this
     * template to render it to the first element (which contains a
     * common selector) of the toElements array.
     * The template is then loaded from the file:
     * /modules/auth/templates/auth_navbar_item.phtml.
     * 
     * The second parameter tells the init function to check if the user is
     * currently logged in on first init. If so, the profile will be shown.
     */
    auth.init({
		placement: {
			// "template[0]" will be mapped to "toElement[0]" and so on...
			templates:  [ 'auth_navbar_item' ],
			toElements: [ '#menu_login' ]
		},
		debug: true
	}, true );

});