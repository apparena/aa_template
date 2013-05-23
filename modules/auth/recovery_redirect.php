<?php 
	
	$aa_inst_id = 0;
	$key = '';
	$user_id = 0;
	$row = array();
	
	if ( isset( $_GET[ 'aa_inst_id' ] ) ) { $aa_inst_id = intval( $_GET[ 'aa_inst_id' ] ); } else { echo json_encode( array( 'error' => 'missing instance id' ) ); exit( 0 ); }
	if ( isset( $_GET[ 'activationkey' ] ) ) { $key = mysql_real_escape_string( $_GET[ 'activationkey' ] ); } else { echo json_encode( array( 'error' => 'missing activation key' ) ); exit( 0 ); }
	if ( strlen( $key ) <= 0 ) { echo json_encode( array( 'error' => 'something went wrong with your activation key' ) ); exit( 0 ); }
	
	include_once '../../config.php';
	include_once '../../init.php';
	include_once 'check_database.php';
	
	$query = "SELECT * FROM `user_log` WHERE `aa_inst_id` = " . $aa_inst_id . " AND `action` = 'user_password_recover' AND `data` LIKE '%" . $key . "%'";
	$result = mysql_query( $query );
	
	if ( $result ) {
		if ( mysql_num_rows( $result ) > 0 ) {
			$row = mysql_fetch_assoc( $result );
			$user_id = $row[ 'user_id' ];
		}
		mysql_free_result( $result );
	}
	
	$row[ 'data' ] = json_decode( $row[ 'data' ] );
	
	if ( $user_id <= 0 ) { echo json_encode( array( 'error' => 'something went wrong with the password recovery - please contact the support team' ) ); exit( 0 ); }
	
	// activate the new password
	$query = "UPDATE `user_data_email` SET `password` = '" . $row[ 'data' ][ 'password' ] . "' WHERE `email` = '" . $row[ 'data' ][ 'email' ] . "'";
	mysql_query( $query );
	
	// log this action
	$query = "INSERT INTO `user_log` SET `aa_inst_id` = " . $aa_inst_id . ", `action` = 'user_password_recover_activate', `data` = 'the user clicked on the activation link', `user_id` = " . $user_id;
	mysql_query( $query );
	
?>

<div class="alert alert-success">
	Dein Passwort wurde geändert. Logge dich ab sofort mit dem neuen Passwort ein.
</div>