<?php 
	
	$aa_inst_id = 0;
	
	if ( isset( $_GET[ 'aa_inst_id' ] ) ) { $aa_inst_id = $_GET[ 'aa_inst_id' ]; } else { echo json_encode( array( 'error' => 'missing instance id' ) ); exit( 0 ); }
	
	include_once '../../init.php';
	
	unset( $_SESSION[ 'userlogin_' . $aa_inst_id ] );
	
	echo json_encode( array( 'success' => true ) );
	
?>