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
	
	if ( isset( $_POST[ 'userData' ] ) ) { $userdata = $_POST[ 'userData' ]; } else { if ( !isset( $_POST[ 'mode' ] ) || $_POST[ 'mode' ] != 'check' ) { echo json_encode( array( 'error' => 'missing user data' ) ); exit( 0 ); } }
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
			
			if ( !isset( $userdata[ 'id' ] ) ) { echo json_encode( array( 'error' => 'missing facebook id' ) ); exit( 0 ); }
			
			$savedata = array();
			
			$savedata[ 'display_name' ] = '';
			
			if ( isset( $userdata[ 'name' ] ) ) {
				$savedata[ 'display_name' ] = mysql_real_escape_string( $userdata[ 'name' ] );
				unset( $userdata[ 'name' ] );
			} else {
				if ( isset( $userdata[ 'first_name' ] ) ) {
					$savedata[ 'display_name' ] = mysql_real_escape_string( $userdata[ 'first_name' ] ) .
								( isset( $userdata[ 'last_name' ] ) ? ' ' . mysql_real_escape_string( $userdata[ 'last_name' ] ) : '' );
					unset( $userdata[ 'first_name' ] );
					if ( isset( $userdata[ 'last_name' ] ) ) {
						unset( $userdata[ 'last_name' ] );
					}
				}
			}
			
			$savedata[ 'fb_id' ] = 0;
			
			if ( isset( $userdata[ 'id' ] ) ) {
				$savedata[ 'fb_id' ] = $userdata[ 'id' ];
				unset( $userdata[ 'id' ] );
			}
			
			$savedata[ 'email' ] = '';
			
			if ( isset( $userdata[ 'email' ] ) ) {
				$savedata[ 'email' ] = mysql_real_escape_string( $userdata[ 'email' ] );
				unset( $userdata[ 'email' ] );
			}
			
			$savedata[ 'profile_image_url' ] = '';
			
			if ( isset( $userdata[ 'profile_image_url' ] ) ) {
				$savedata[ 'profile_image_url' ] = 'https://graph.facebook.com/' . $savedata[ 'fb_id' ] . '/picture?type=square';
			}
			
			$savedata[ 'gender' ] = '';
				
			if ( isset( $userdata[ 'gender' ] ) ) {
				$savedata[ 'gender' ] = mysql_real_escape_string( $userdata[ 'gender' ] );
				unset( $userdata[ 'gender' ] );
			}
			
			// put all the rest into the additional data field
			$savedata[ 'data' ] = $userdata;
			
			saveUser( 'user_data_fb', $savedata );
			
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
	
	function saveUser( $table, $data ) {
		
		global $db_raw,
			   $aa,
			   $aa_inst_id,
			   $response;
		
		switch( $table ) {
			
			case 'user_data_fb':
				
				$query = "SELECT * FROM `user_data_fb` WHERE `fb_id` = '" . $data[ 'fb_id' ] . "'";
				$result = mysql_query( $query );
				
				if ( $result ) {
					if ( mysql_num_rows( $result ) <= 0 ) {
						
						$query = "INSERT INTO `user_data_fb` SET `fb_id` = '" . $data[ 'fb_id' ] . "', `email` = '" . $data[ 'email' ] . "', `display_name` = '" . $data[ 'display_name' ] . "', `profile_image_url` = '" . $data[ 'profile_image_url' ] . "', `gender` = '" . $data[ 'gender' ] . "', `data` = '" . mysql_real_escape_string( json_encode( $data[ 'data' ] ) ) . "'";
						mysql_query( $query );
						
						$response[ 'userdata' ] = $data;
						
					} else {
						
						$response[ 'userdata' ] = mysql_fetch_assoc( $result );
						
					}
					
					mysql_free_result( $result );
				}
				
				$query = "SELECT * FROM `user_data` WHERE `fb_id` = '" . $data[ 'fb_id' ] . "' AND `aa_inst_id` = " . $aa_inst_id;
				
				$result = mysql_query( $query );
				
				if ( $result ) {
					
					if ( mysql_num_rows( $result ) <= 0 ) {
						
						$query = "INSERT INTO `user_data` SET `aa_inst_id` = " . $aa_inst_id . ", `fb_id` = '" . $data[ 'fb_id' ] . "', `ip` = '" . get_client_ip() . "'";
						mysql_query( $query );
						
						$query = "SELECT * FROM `user_data` WHERE `fb_id` = '" . $data[ 'fb_id' ] . "' AND `aa_inst_id` = " . $aa_inst_id;
						$result2 = mysql_query( $query );
						if ( $result2 ) {
							if ( mysql_num_rows( $result2 ) > 0 ) {
								$response[ 'userinstance' ] = mysql_fetch_assoc( $result2 );
							}
							mysql_free_result( $result2 );
						}
						
					} else {
						
						$response[ 'userinstance' ] = mysql_fetch_assoc( $result );
						
					}
					
					mysql_free_result( $result );
					
				}
				
				break;
				
			case 'user_data_gplus':
				
				$query = "SELECT * FROM `user_data_gplus` WHERE `gplus_id` = '" . $data[ 'gplus_id' ] . "'";
				$result = mysql_query( $query );
				
				if ( $result ) {
					if ( mysql_num_rows( $result ) <= 0 ) {
						
						$query = "INSERT INTO `user_data_gplus` SET `gplus_id` = '" . $data[ 'gplus_id' ] . "', `email` = '" . $data[ 'email' ] . "', `display_name` = '" . $data[ 'display_name' ] . "', `profile_image_url` = '" . $data[ 'profile_image_url' ] . "', `gender` = '" . $data[ 'gender' ] . "', `data` = '" . json_encode( $data[ 'data' ] ) . "'";
						mysql_query( $query );
						
						$response[ 'userdata' ] = $data;
						
					} else {
						
						$response[ 'userdata' ] = mysql_fetch_assoc( $result );
						
					}
					
					mysql_free_result( $result );
				}
				
				$query = "SELECT * FROM `user_data` WHERE `gplus_id` = '" . $data[ 'gplus_id' ] . "' AND `aa_inst_id` = " . $aa_inst_id;
				
				$result = mysql_query( $query );
				
				if ( $result ) {
					
					if ( mysql_num_rows( $result ) <= 0 ) {
						
						$query = "INSERT INTO `user_data` SET `aa_inst_id` = " . $aa_inst_id . ", `gplus_id` = '" . $data[ 'gplus_id' ] . "', `ip` = '" . get_client_ip() . "'";
						mysql_query( $query );
						
						$query = "SELECT * FROM `user_data` WHERE `gplus_id` = '" . $data[ 'gplus_id' ] . "' AND `aa_inst_id` = " . $aa_inst_id;
						$result2 = mysql_query( $query );
						if ( $result2 ) {
							if ( mysql_num_rows( $result2 ) > 0 ) {
								$response[ 'userinstance' ] = mysql_fetch_assoc( $result2 );
							}
							mysql_free_result( $result2 );
						}
						
					} else {
						
						$response[ 'userinstance' ] = mysql_fetch_assoc( $result );
						
					}
					
					mysql_free_result( $result );
					
				}
				
				break;
				
			case 'user_data_twitter':
				
				$query = "SELECT * FROM `user_data_twitter` WHERE `twitter_id` = '" . $data[ 'twitter_id' ] . "'";
				$result = mysql_query( $query );
				
				if ( $result ) {
					if ( mysql_num_rows( $result ) <= 0 ) {
						
						$query = "INSERT INTO `user_data_twitter` SET `twitter_id` = '" . $data[ 'twitter_id' ] . "', `email` = '" . $data[ 'email' ] . "', `display_name` = '" . $data[ 'display_name' ] . "', `profile_image_url` = '" . $data[ 'profile_image_url' ] . "', `data` = '" . json_encode( $data[ 'data' ] ) . "'";
						mysql_query( $query );
						
						$response[ 'userdata' ] = $data;
						
					} else {
						
						$response[ 'userdata' ] = mysql_fetch_assoc( $result );
						
					}
					
					mysql_free_result( $result );
				}
				
				$query = "SELECT * FROM `user_data` WHERE `twitter_id` = '" . $data[ 'twitter_id' ] . "' AND `aa_inst_id` = " . $aa_inst_id;
				
				$result = mysql_query( $query );
				
				if ( $result ) {
					
					if ( mysql_num_rows( $result ) <= 0 ) {
						
						$query = "INSERT INTO `user_data` SET `aa_inst_id` = " . $aa_inst_id . ", `twitter_id` = '" . $data[ 'twitter_id' ] . "', `ip` = '" . get_client_ip() . "'";
						mysql_query( $query );
						
						$query = "SELECT * FROM `user_data` WHERE `twitter_id` = '" . $data[ 'twitter_id' ] . "' AND `aa_inst_id` = " . $aa_inst_id;
						$result2 = mysql_query( $query );
						if ( $result2 ) {
							if ( mysql_num_rows( $result2 ) > 0 ) {
								$response[ 'userinstance' ] = mysql_fetch_assoc( $result2 );
							}
							mysql_free_result( $result2 );
						}
						
					} else {
						
						$response[ 'userinstance' ] = mysql_fetch_assoc( $result );
						
					}
					
					mysql_free_result( $result );
					
				}
				
				break;
			
		}
		
	}
	
?>