<?php 
require_once dirname( __FILE__ ) . '/../../init.php';
require_once dirname( __FILE__ ) . '/config.php';
require_once dirname( __FILE__ ) . '/SendMail.php';

/*
$receiver = array();
$receiver['email'] 	= $_POST['receiver_email'];
$receiver['name']	= $_POST['receiver_name'];
*/

$user_data = array();
if ( isset( $_POST[ 'user_data' ] ) ) {
	$user_data = $_POST[ 'user_data' ];
} else {
	echo json_encode( array( 'error' => 'missing user data' ) );
	exit( 0 );
}

$template = '';
if ( isset( $user_data[ 'template' ] ) && strlen( $user_data[ 'template' ] ) > 0 ) {
	$template = $user_data[ 'template' ];
	unset( $user_data[ 'template' ] );
} else {
	echo json_encode( array( 'error' => 'missing template key. use a key from an App-Arena HTML-item. provide the placeholders {{name}} and {{userdata}} in it.' ) );
	exit( 0 );
}

$customer = array();

/* Use App-Manager variables to send out the email */
if ( isset( $aa['config']['wizard_email']['value'] ) )
	$customer['email'] = $aa['config']['wizard_email']['value'];

if ( isset( $aa['config']['wizard_company_name']['value'] ) )
	$customer['name'] = $aa['config']['wizard_company_name']['value'];

if ( isset( $aa['config']['contactform_email_subject']['value'] ) )
	$email['subject'] = $aa['config']['contactform_email_subject']['value'];

// if ( isset( $aa['config']['contactform_email_body']['value'] ) )
// 	$email['body'] = $aa['config']['contactform_email_body']['value'];

if ( isset( $aa['config'][ $template ]['value'] ) )
	$email['body'] = $aa['config'][ $template ]['value'];

if ( !isset( $email[ 'body' ] ) || strlen( $email[ 'body' ] ) <= 0 ) {
	echo json_encode( array( 'error' => 'template not found: \"' . $template . '\"' ) );
	exit( 0 );
}

// Init newsletter object and send email
$mail = new SendMail( $smtp_config, $_GET['aa_inst_id'], $customer, $user_data, $template );
$ret = $mail->send_email( $email );

/*
if ( is_array( $ret ) ) {
	echo json_encode( array( 'success' => 'mail was sent', 'mail' => $ret ) );
} else {
	echo json_encode( array( 'error' => 'mail not sent', 'message' => $ret ) );
}
*/

if ( $ret !== true ) {
	echo json_encode( array( 'error' => 'mail not sent' ) );
} else {
	echo json_encode( array( 'success' => 'mail was sent' ) );
}

/*if($ret == true) {
   var_dump($ret);
} else {
	echo "Newsletter wurde nicht verschickt.";
  var_dump($ret);
}*/

?>
