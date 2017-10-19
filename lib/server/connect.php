<?php  
//-----------------------------------------------------------------------------
// Handle Incoming Messages
//-----------------------------------------------------------------------------
	require '_In.class.php';

	// Read Data
	$uid = $_GET["uid"];
	$data = json_decode($_GET["data"]);
	$in = new In($uid, $data);
?>