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
		
		
		//TODO:save twitter credentials to db!!
		/*
		 * the obj looks like this:
		 * stdClass Object ( [id] => 1130718230 [id_str] => 1130718230 [name] => Guntram Pollock [screen_name] => GuntramPollock [location] => [description] => [url] => [entities] => stdClass Object ( [description] => stdClass Object ( [urls] => Array ( ) ) ) [protected] => [followers_count] => 0 [friends_count] => 4 [listed_count] => 0 [created_at] => Tue Jan 29 10:55:01 +0000 2013 [favourites_count] => 0 [utc_offset] => [time_zone] => [geo_enabled] => [verified] => [statuses_count] => 9 [lang] => de [status] => stdClass Object ( [created_at] => Mon Apr 29 10:48:23 +0000 2013 [id] => 328823070557097984 [id_str] => 328823070557097984 [text] => @GuntramPollock wtf!! [source] => Tweet Button [truncated] => [in_reply_to_status_id] => [in_reply_to_status_id_str] => [in_reply_to_user_id] => 1130718230 [in_reply_to_user_id_str] => 1130718230 [in_reply_to_screen_name] => GuntramPollock [geo] => [coordinates] => [place] => [contributors] => [retweet_count] => 0 [favorite_count] => 0 [entities] => stdClass Object ( [hashtags] => Array ( ) [symbols] => Array ( ) [urls] => Array ( ) [user_mentions] => Array ( [0] => stdClass Object ( [screen_name] => GuntramPollock [name] => Guntram Pollock [id] => 1130718230 [id_str] => 1130718230 [indices] => Array ( [0] => 0 [1] => 15 ) ) ) ) [favorited] => [retweeted] => [lang] => nl ) [contributors_enabled] => [is_translator] => [profile_background_color] => C0DEED [profile_background_image_url] => http://a0.twimg.com/images/themes/theme1/bg.png [profile_background_image_url_https] => https://si0.twimg.com/images/themes/theme1/bg.png [profile_background_tile] => [profile_image_url] => http://a0.twimg.com/sticky/default_profile_images/default_profile_5_normal.png [profile_image_url_https] => https://si0.twimg.com/sticky/default_profile_images/default_profile_5_normal.png [profile_link_color] => 0084B4 [profile_sidebar_border_color] => C0DEED [profile_sidebar_fill_color] => DDEEF6 [profile_text_color] => 333333 [profile_use_background_image] => 1 [default_profile] => 1 [default_profile_image] => 1 [following] => [follow_request_sent] => [notifications] => [httpstatus] => 200 )
		 */
		
		
		if ( isset( $_GET[ 'popup' ] ) && $_GET[ 'popup' ] == 'true' ) {
			
// exit( 0 );

			$reply = mysql_real_escape_string( $reply );
			
			// $user only contains the id and the screen name (and the token stuff and an http response code)...
			// $reply contains the user's data
			echo '<script>window.opener.aa.auth.twitter_popup_callback(\'' . json_encode( $reply ) . '\');</script>';
			
		}
		
	} else {
		
		// the user did not accept the app auth dialog
		
		echo 'you did not login';
		echo '<script>window.opener.twitter_popup_callback(\'false\');</script>';
		
		//echo '<script>window.opener.twitter_popup_callback(\'false\');</script>'; // if the callback is in the window scope of the opening page
		echo '<script>window.opener.aa.auth.twitter_popup_callback(\'false\');</script>';
		
	}
	
?>