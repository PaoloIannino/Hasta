<?php

	// Include useful php routines
	@include 'utils.php';

	// Retrieve session info
	if(session_start() === FALSE){
		write_log("new_bid.php: Creating session");
	}
	
	// Then call the log_out routine and redirect to main view
	log_out('php/hasta.php');
?>