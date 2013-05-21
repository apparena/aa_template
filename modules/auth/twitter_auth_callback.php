<?php 
	
	include_once '../../config.php';
	
	include_once "codebird.php";
	
	$CONSUMER_KEY = $twitter_consumer_key;
	$CONSUMER_KEY_SECRET = $twitter_consumer_secret;
	
	Codebird::setConsumerKey( $CONSUMER_KEY, $CONSUMER_KEY_SECRET );
	
	$cb = Codebird::getInstance();
	
	session_start();
	
	if ( isset( $_GET[ 'oauth_token' ] ) && isset( $_GET[ 'oauth_verifier' ] ) ) {
		
		// the user accepted the app auth dialog and we just returned from twitter...
		$_SESSION['oauth_token'] = $_GET[ 'oauth_token' ];
		$_SESSION['oauth_verifier'] = $_GET[ 'oauth_verifier' ];
		
		$cb->setToken($_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
		
// echo "check1<br />";
		
		$user = $cb->oauth_accessToken( array( 'oauth_verifier' => $_GET[ 'oauth_verifier' ] ) );
		
		$username = $user->screen_name;
		
// echo "<br />check2<br />";
		
		$cb->setToken( $user->oauth_token, $user->oauth_token_secret );
		
// print_r( $user );
		
// echo "<br />check3<br />";

		$reply = $cb->users_show( array( 'screen_name' => $username ) );

// print_r( $reply );

// echo "<br />check4<br />";
		
		if ( isset( $_GET[ 'popup' ] ) && $_GET[ 'popup' ] == 'true' ) {
			
exit( 0 );
			
			echo '<script>window.opener.aa.auth.twitter_popup_callback(\'' . json_encode( $user ) . '\');</script>';
			
		}
		
	} else {
		
		// the user did not accept the app auth dialog
		
		echo 'you did not login';
		echo '<script>window.opener.twitter_popup_callback(\'false\');</script>';
		
		//echo '<script>window.opener.twitter_popup_callback(\'false\');</script>'; // if the callback is in the window scope of the opening page
		echo '<script>window.opener.aa.auth.twitter_popup_callback(\'false\');</script>';
		
	}
	
?>