<?php 
	
	/**
	 * This file opens the twitter auth dialog for the twitter app.
	 * When the user interacts with the dialog, the location will
	 * be redirected to this file by twitter (with these $_get:
	 * ?oauth_token=blah1&oauth_verifier=blah2).
	 * This twitter dialog file uses the codebird php oauth
	 * framework for twitter.
	 * By default, there are some vars stored in the $_session...
	 * $auth_url = $cb->oauth_authorize();
	 * fetches the auth url from the twitter app settings.
	 */
	
	include_once '../../config.php';
	include_once 'codebird.php';
	
	$CONSUMER_KEY = $twitter_consumer_key;
	$CONSUMER_KEY_SECRET = $twitter_consumer_secret;
	$CALLBACK_URL = $twitter_callback_url;
	
	// consumerkey and -secret from the twitter app config
	Codebird::setConsumerKey( $CONSUMER_KEY, $CONSUMER_KEY_SECRET ); // static, see 'Using multiple Codebird instances'

	$auth_url = $cb = Codebird::getInstance();
	
	session_start();

	if (! isset($_GET['oauth_verifier'])) {
		// gets a request token
		$reply = $cb->oauth_requestToken(array(
			//'oauth_callback' => 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
			'oauth_callback' => $CALLBACK_URL . ( isset( $_GET[ 'popup' ] ) ? '?popup=' . $_GET[ 'popup' ] : '' )
		));

		// stores it
		$cb->setToken($reply->oauth_token, $reply->oauth_token_secret);
		$_SESSION['oauth_token'] = $reply->oauth_token;
		$_SESSION['oauth_token_secret'] = $reply->oauth_token_secret;

		// gets the authorize screen URL
		$auth_url = $cb->oauth_authorize();
		
		header('Location: ' . $auth_url);
		die();

	} elseif (! isset($_SESSION['oauth_verified'])) {
		// gets the access token
		$cb->setToken($_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
		$reply = $cb->oauth_accessToken(array(
			'oauth_verifier' => $_GET['oauth_verifier']
		));
		// store the authenticated token, which may be different from the request token (!)
		$_SESSION['oauth_token'] = $reply->oauth_token;
		$_SESSION['oauth_token_secret'] = $reply->oauth_token_secret;
		$cb->setToken($_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
		$_SESSION['oauth_verified'] = true;
	}
	
?>