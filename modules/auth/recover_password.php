<?php 
	
	$user_data = array();
	if ( isset( $_POST[ 'userdata' ] ) ) { $user_data = $_POST[ 'userdata' ]; } else { echo json_encode( array( 'error' => 'missing user data' ) ); exit( 0 ); }
	if ( !isset( $_GET['aa_inst_id'] ) ) { echo json_encode( array( 'error' => 'missing instance id' ) ); exit( 0 ); }
	
	require_once dirname( __FILE__ ) . '/SendMail.php';
	
	include_once '../../init.php';
	
	//SMTP Setup
	$smtp_config = array(
		"host" => "smtp.mandrillapp.com",
		"user" => "s.buckpesch@iconsultants.eu",
		"pass" => "cdf57ac6-93bc-4c3f-b7da-5675ddac3db7",
		"port" => "587"
	);
	
	// this will be the sender data
	$customer = array();
	$customer['email'] = 'support@app-arena.com';
	$customer['name']  = 'App-Arena Support';
	
	$email = array();
	$email['subject'] = 'Passwortwiederherstellung';
	$email['body']    = 'Hallo!<br /><br />' .
						'Dein Account wurde zum Wiederherstellen des Passworts markiert.<br />' .
						'Solltest du diese E-Mail nicht angefordert haben kannst du sie ignorieren.<br />' .
						'Falls du dich wieder an dein Passwort erinnerst kannst du dich auch weiterhin damit einloggen und diese E-Mail ignorieren.<br /><br />' .
						'Wenn du dein Passwort wirklich zurücksetzen möchtest dann klicke auf diesen Link:<br /><br />' .
						'{{link}}<br /><br />' .
						'und verwende ab sofort folgendes Passwort:<br /><br />' .
						'{{password}}<br /><br />' .
						'Viele Grüsse,<br />dein App-Arena Support Team';
	
	$mail = new SendMail( $smtp_config, $_GET['aa_inst_id'], $customer, $user_data );
	
	// generate new password
	$password = $mail->generatePassword();
	
	// generate a key for activation
	$key = $mail->generatePassword();
	$key = md5( $key );
	
	// generate activation redirect link
	$currentPath = $aa[ 'instance' ][ 'fb_canvas_url' ];
	$link = $currentPath . 'modules/auth/recovery_redirect.php?aa_inst_id=' . $_GET['aa_inst_id'] . '&activationkey=' . $key;
	
	$ret = $mail->send_email( $email, $link, $key, $password );
	
	if ( $ret !== FALSE ) {
		echo json_encode( array( 'success' => 'mail was sent', 'message' => $ret ) );
	} else {
		echo json_encode( array( 'error' => 'mail not sent', 'message' => $ret ) );
	}
	
?>