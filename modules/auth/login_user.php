<?php 
	
	/**
	 * This script has to check if the user
	 * is already in the db. If so, it might
	 * have to update the given credentials
	 * into the user's data.
	 * If it does not find the user, it has
	 * to save him to the db.
	 */
	
	include_once '../../config.php';
	
	$userdata = array();
	$mode = 'email';
	
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
			
			
			
			break;
		
	}
	
	echo json_encode( array( 'success' => 'yay!' ) );
	
?>