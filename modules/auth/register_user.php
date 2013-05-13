<?php 
	
	/**
	 * Register a new user.
	 */
	
	$aa_inst_id = 0;
	
	if ( isset( $_GET[ 'aa_inst_id' ] ) ) { $aa_inst_id = intval( $_GET[ 'aa_inst_id' ] ); } else { echo json_encode( array( 'error' => 'missing instance id' ) ); exit( 0 ); }
	
	include_once '../../config.php';
	include_once '../../init.php';
	include_once 'check_database.php';
	
	$userdata = array();
	$mode = '';
	
	if ( isset( $_POST[ 'userData' ] ) ) { $userdata = $_POST[ 'userData' ]; } else { echo json_encode( array( 'error' => 'missing user data' ) ); exit( 0 ); }
	if ( isset( $_POST[ 'mode' ] ) ) { $mode = $_POST[ 'mode' ]; } else { echo json_encode( array( 'error' => 'missing mode' ) ); exit( 0 ); }
	
	switch( $mode ) {
		
		case 'fb':
			
			
			
			break;
			
		case 'gplus':
			
			
			
			break;
			
		case 'twitter':
			
			
			
			break;
			
		case 'email':
		default:
			
			$userdata[ 'email' ] = mysql_real_escape_string( $userdata[ 'email' ] );
			$userdata[ 'password' ] = mysql_real_escape_string( $userdata[ 'password' ] );
			$userdata[ 'gender' ] = mysql_real_escape_string( $userdata[ 'gender' ] );
			
			// md5 hash password
			$userdata[ 'password' ] = md5( $userdata[ 'password' ] );
			
			// check if the user already exists in the email table
			$query = "SELECT * FROM `user_data_email` WHERE `email` = '" . $userdata[ 'email' ] . "'";
			
			$result = mysql_query( $query );
			
			if ( $result ) {
				
				if ( mysql_num_rows( $result ) > 0 ) {
					
					echo json_encode( array( 'error' => 'email already exists' ) );
					exit( 0 );
					
				}
				
				mysql_free_result( $result );
				
			}
			
			// check if the user has already registered for this instance
			// (should not happen if above succeeds)
			$query = "SELECT * FROM `user_data` WHERE `email` = '" . $userdata[ 'email' ] . "' AND `aa_inst_id` = " . $aa_inst_id;
			
			$result = mysql_query( $query );
			
			if ( $result ) {
				
				if ( mysql_num_rows( $result ) > 0 ) {
					
					echo json_encode( array( 'error' => 'user already exists' ) );
					exit( 0 );
					
				}
				
				mysql_free_result( $result );
				
			}
			
			// insert the user
			$query = "INSERT INTO `user_data_email` SET `email` = '" . $userdata[ 'email' ] . "', `password` = '" . $userdata[ 'password' ] . "', `gender` = '" . $userdata[ 'gender' ] . "'";
			mysql_query( $query );
			
			$query = "INSERT INTO `user_data` SET `email` = '" . $userdata[ 'email' ] . "', `aa_inst_id` = " . $aa_inst_id . ", `ip` = '" . get_client_ip() . "'";
			mysql_query( $query );
			
			break;
		
	}
	
	echo json_encode( array( 'success' => 'yay!' ) );
	exit( 0 );
	
	function get_client_ip()
	{
		// Get client ip address
		if (isset($_SERVER["REMOTE_ADDR"]))
			$client_ip = $_SERVER["REMOTE_ADDR"];
		else if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
			$client_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		else if (isset($_SERVER["HTTP_CLIENT_IP"]))
			$client_ip = $_SERVER["HTTP_CLIENT_IP"];
		
		return $client_ip;
	}
	
?>