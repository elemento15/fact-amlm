<?php 
	$message = preg_replace('/(<\/?p>)+/', ' ', $message);
	echo json_encode(array('success' => false, 'msg' => $message));
	exit
?>