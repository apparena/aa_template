<?php 
	
	/**
	 * Register a new user.
	 */
	
	include_once '../../config.php';
	
	include_once '../../init.php';
	
	include_once 'check_database.php';
	
	$userdata = array();
	
	if ( isset( $_POST[ 'userData' ] ) ) { $userdata = $_POST[ 'userData' ]; } else { echo json_encode( array( 'error' => 'missing user data' ) ); exit( 0 ); }
	
	
	
	echo json_encode( array( 'success' => 'yay!' ) );
	
?>