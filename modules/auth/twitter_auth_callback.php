<?php 
	
	include_once "codebird.php";
	
	$CONSUMER_KEY = '2WnBPqfOf0vaGPJsMFG6fw';
	$CONSUMER_KEY_SECRET = 'mygq0bS2LQfUn6jLiujZ8VWOqXCBKtxHtXgnGnrMc';
	
	Codebird::setConsumerKey( $CONSUMER_KEY, $CONSUMER_KEY_SECRET );
	
	$cb = Codebird::getInstance();
	
	session_start();
	
	if ( isset( $_GET[ 'oauth_token' ] ) && isset( $_GET[ 'oauth_verifier' ] ) ) {
		
		// the user accepted the app auth dialog and we just returned from twitter...
		$_SESSION['oauth_token'] = $_GET[ 'oauth_token' ];
		$_SESSION['oauth_verifier'] = $_GET[ 'oauth_verifier' ];
		
		$cb->setToken($_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
		
		$user = $cb->oauth_accessToken( array( 'oauth_verifier' => $_GET[ 'oauth_verifier' ] ) );
		
		//$test = $cb->account_verifyCredentials( array( 'request_token' =>  ) )
		
		var_dump( $user );
		
		if ( isset( $_GET[ 'popup' ] ) && $_GET[ 'popup' ] == 'true' ) {
/*
			echo '<script>window.opener.document.getElementById("responselog").innerHtml=
				window.opener.document.getElementById("responselog").innerHtml() +
				"<br />==========================================" +
				"<h2>timestamp: " + new Date() + "</h2>" +
				"<h2>twitter popup response:</h2>" +
				' . json_encode( $user ) . '.replace( /",/g, "",<br />").replace( /{/g, "{<br />" ).replace( /}/g, "<br />}" ) +
				"<br />";</script>';
			echo '<script>window.close();</script>';
*/
			//echo '<script>console.log(window.opener.twitter_popup_callback);</script>';
			echo '<script>window.opener.twitter_popup_callback(\'' . json_encode( $user ) . '\');</script>';
		}
		
	} else {
		
		// the user did not accept the app auth dialog
		
		echo 'you did not login';
		echo '<script>window.opener.twitter_popup_callback(\'false\');</script>';
		
	}
	
?>