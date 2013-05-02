<?php 
	
	
	
?>

<!doctype html>
<html>
<head>
	<title>login test</title>
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
	<link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/css/bootstrap-combined.min.css" rel="stylesheet">
	<script src="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<script type="text/javascript" src="script.js"></script>
</head>
<body>
	<div id="login_buttons">
		<h2>Login</h2>
		<button onclick="twitter();" class="btn btn-primary"><i class="icon-twitter icon-white">&nbsp;</i>twitter inline</button>
		<button onclick="twitter_popup();" class="btn btn-primary"><i class="icon-twitter icon-white">&nbsp;</i>twitter as a popup</button>
		<div id="google_login_button_wrapper" style="margin-top: 10px;">
			<button 
				id="gplus_login"
				class="g-signin"
				data-scope="https://www.googleapis.com/auth/plus.me https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/plus.login"
				data-requestvisibleactions="http://schemas.google.com/AddActivity"
				data-clientId="990596349199.apps.googleusercontent.com"
				data-callback="gplusCallback"
				data-theme="dark"
				data-cookiepolicy="single_host_origin">
			</button>
		</div>
	</div>
	
	<hr />
	
	<div id="social_buttons">
		<h2>Social</h2>
		<table class="table">
			<thead>
				<tr><th>teile einen Link</th><th>folgen</th><th>Hashtag</th><th>Erw�hnung</th></tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<a href="https://twitter.com/share" class="twitter-share-button" data-via="GuntramPollock" data-lang="de">Twittern</a>
					</td>
					<td>
						<a href="https://twitter.com/GuntramPollock" class="twitter-follow-button" data-show-count="false" data-lang="de">@GuntramPollock folgen</a>
					</td>
					<td>
						<a href="https://twitter.com/intent/tweet?button_hashtag=TwitterGeschichten" class="twitter-hashtag-button" data-lang="de" data-related="GuntramPollock">Tweet #TwitterGeschichten</a>
					</td>
					<td>
						<a href="https://twitter.com/intent/tweet?screen_name=GuntramPollock" class="twitter-mention-button" data-lang="de" data-related="GuntramPollock">Tweet to @GuntramPollock</a>
					</td>
				</tr>
			</tbody>
		</table>
		<br />
		<table class="table">
			<thead>
				<tr><th>+1 like/comment/share</th><th>profile badge</th><th>page badge</th><th>comment/share (no +1)</th></tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<div class="g-plusone" data-annotation="inline" data-width="300"></div>
					</td>
					<td>
						<div class="g-plus" data-height="69" data-href="//plus.google.com/109156055769208294930" data-rel="author"></div>
					</td>
					<td>
						<g:plus href="https://plus.google.com/108834261031675170830"></g:plus>
					</td>
					<td>
						<div class="g-plus" data-action="share"></div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<hr />
	<div id="responselog">
	</div>
	<script>
		$(document).ready( function () {
			
			popup_window = null;
			
		});
		
		// need to specify an onload fct in the plusone.js call!
		function gapiAsyncInit () {
			
			// g+ api callback when gapi has been loaded. MUST be specified in the call to plusone.js!
			
		}
	</script>
	<script type="text/javascript">
		<!-- load g+ js api asynchronously (will render buttons) -->
		(function() {var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;po.src = 'https://apis.google.com/js/client:plusone.js?onload=gapiAsyncInit';var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);})();
    </script>
	<script>
		<!-- render the twitter social buttons -->
		!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');
	</script>
</body>
</html>
<script>
	function twitter () {
		
		// inline twitter sign in
		top.location = 'twitter_auth.php';
		
	}
	
	function twitter_popup () {
		
		// opens the twitter auth sign in popup
		popup_window = window.open( 'twitter_auth.php?popup=true','twitter-login','height=500,width=600' ); // the twitter auth dialog is responsive and will fit itself to an appropriate size
		
	}
	
	function twitter_popup_callback( response ) {
		
		// twitter popup sign in
		console.log( 'fetched response' );
		
		$( '#responselog' ).append(
			'<br />==========================================' +
			'<h2>timestamp: ' + new Date() + '</h2>' +
			'<h2>twitter popup response:</h2>' +
			response.replace( /",/g, '",<br />').replace( /{/g, '{<br />' ).replace( /}/g, '<br />}' ) +
			'<br />'
		);
		
		popup_window.close();
		
	}
	
	function gplusCallback(authResult) {
		$( '#gplus_login' ).parent().css( 'top', '12px' );
		if (authResult['access_token']) {
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
				$( '#responselog' ).append(
					'<br />==========================================' +
					'<h2>timestamp: ' + new Date() + '</h2>' +
					'<h2>g+ oauth response:</h2>' +
					JSON.stringify( response ).replace( /",/g, '",<br />').replace( /{/g, '{<br />' ).replace( /}/g, '<br />}' ) +
					'<br />'
				);
			  })
			});
			
			// use the plus client to get user data
			gapi.client.load( 'plus', 'v1', function() {
			  gapi.client.plus.people.get({ 'userId' : 'me' }).execute( function( response ) {
				// Shows other profile information
				console.log( response );
				$( '#responselog' ).append(
					'<h2>g+ plus response:</h2>' +
					JSON.stringify( response ).replace( /",/g, '",<br />').replace( /{/g, '{<br />' ).replace( /}/g, '<br />}' ) +
					'<br />'
				);
			  })
			});
		} else if (authResult['error']) {
			// There was an error.
			// Possible error codes:
			//   "access_denied" - User denied access to your app
			//   "immediate_failed" - Could not automatically log in the user
			// console.log('There was an error: ' + authResult['error']);
			console.log( 'error' );
			console.log(authResult);
			$( '#responselog' ).append(
				'error-response: ' +
				JSON.stringify( authResult ).replace( /",/g, '",<br />').replace( /{/g, '{<br />' ).replace( /}/g, '<br />}' ) +
				'<br />'
			);
		}
	}
	
</script>