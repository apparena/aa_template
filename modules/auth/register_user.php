<?php 
	
	/**
	 * Maybe we dont need an own script for registering...
	 */
	
	include_once '../../config.php';
	
	$userdata = array();
	
	if ( isset( $_POST[ 'userData' ] ) ) { $userdata = $_POST[ 'userData' ]; } else { echo json_encode( array( 'error' => 'missing user data' ) ); exit( 0 ); }
	
	echo json_encode( array( 'success' => 'yay!' ) );
	
?>