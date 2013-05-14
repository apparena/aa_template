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
	
	$response = array();
	
	if ( isset( $_POST[ 'userData' ] ) ) { $userdata = $_POST[ 'userData' ]; } else { echo json_encode( array( 'error' => 'missing user data' ) ); exit( 0 ); }
	if ( isset( $_POST[ 'mode' ] ) ) { $mode = $_POST[ 'mode' ]; } else { echo json_encode( array( 'error' => 'missing mode' ) ); exit( 0 ); }
	
	switch( $mode ) {
		
		/* check for a valid login in the session */
		case 'check':
			
			/* no unsetting here... */
			
			if ( isset( $_SESSION[ 'userlogin_' . $aa_inst_id ] ) &&
				 isset( $_SESSION[ 'userlogin_' . $aa_inst_id ][ 'userinstance' ] ) &&
				 isset( $_SESSION[ 'userlogin_' . $aa_inst_id ][ 'userdata' ] ) ) {
				
				$response[ 'userdata' ] = $_SESSION[ 'userlogin_' . $aa_inst_id ][ 'userdata' ];
				$response[ 'userinstance' ] = $_SESSION[ 'userlogin_' . $aa_inst_id ][ 'userinstance' ];
			 	$response[ 'success' ] = true;
			 	
			 	echo json_encode( $response );
			 	exit( 0 );
				
			} else {
				
				echo json_encode( array( 'error' => 'no login detected' ) );
				exit( 0 );
				
			}
			
			break;
			
		case 'fb':
			
			/* only unset for a new login! */
			unset( $_SESSION[ 'userlogin_' . $aa_inst_id ] );
			
			
			
			break;
			
		case 'gplus':
			
			unset( $_SESSION[ 'userlogin_' . $aa_inst_id ] );
			
			
			
			break;
			
		case 'twitter':
			
			unset( $_SESSION[ 'userlogin_' . $aa_inst_id ] );
			
			
			
			break;
			
		case 'email':
		default:
			
			unset( $_SESSION[ 'userlogin_' . $aa_inst_id ] );
			
			$userdata[ 'email' ] = mysql_real_escape_string( $userdata[ 'email' ] );
			$userdata[ 'password' ] = mysql_real_escape_string( $userdata[ 'password' ] );
			
			// md5 hash password
			$userdata[ 'password' ] = md5( $userdata[ 'password' ] );
			
			// check if the user already exists in the email table
			$query = "SELECT * FROM `user_data_email` WHERE `email` = '" . $userdata[ 'email' ] . "' AND `password` = '" . $userdata[ 'password' ] . "'";
			
			$result = mysql_query( $query );
			
			if ( $result ) {
				
				if ( mysql_num_rows( $result ) <= 0 ) {
					
					echo json_encode( array( 'error' => 'email does not exist or wrong password' ) );
					exit( 0 );
					
				} else {
					
					$response[ 'userdata' ] = mysql_fetch_assoc( $result );
					unset( $response[ 'userdata' ][ 'password' ] );
					
				}
				
				mysql_free_result( $result );
				
			}
			
			// check if the user has already registered for this instance
			// (should not happen if above succeeds)
			$query = "SELECT * FROM `user_data` WHERE `email` = '" . $userdata[ 'email' ] . "' AND `aa_inst_id` = " . $aa_inst_id;
			
			$result = mysql_query( $query );
			
			if ( $result ) {
				
				if ( mysql_num_rows( $result ) <= 0 ) {
					
					echo json_encode( array( 'error' => 'email exists but user did not yet register for this instance' ) );
					exit( 0 );
					
				} else {
					
					$response[ 'userinstance' ] = mysql_fetch_assoc( $result );
					
				}
				
				mysql_free_result( $result );
				
			}
			
			break;
		
	}
	
	$_SESSION[ 'userlogin_' . $aa_inst_id ] = $response;
	
	$response[ 'success' ] = true;
	
	echo json_encode( $response );
	exit( 0 );
	
?>