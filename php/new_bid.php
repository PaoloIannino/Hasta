<?php

	// Include file with useful constants values
	@include 'utils.php';

	// Include useful php routines
	@include 'constants.php';

	// Retrieve session info
	if(session_start() === FALSE){
		write_log("new_bid.php: Creating session");
	}
	
	// If the MAX_SESSION_TIME is elasped, log out
	if((time() - $_SESSION['s239562_last_access']) > MAX_SESSION_TIME){
		log_out('php/login.php');
	// Otherwise update "last access"
	} else $_SESSION['s239562_last_access'] = time();
	
	// Terminate is the received request is different from POST
	if($_SERVER['REQUEST_METHOD'] !== 'POST'){
		write_log("new_bid.php: Retrieving post data");
		exit();
	}
	
	// Get connection to the db and start the transaction
	$db = get_dblink();
	if(!$db->autocommit(FALSE)){
		write_log("new_bid.php: Disabling autocommit");
		exit();
	}
	
	// Retrieve values needed for the proper update of the user and site state
	$your_email = $_SESSION['s239562_email'];
	$your_old_bid = get_bid($your_email, 1);
	$your_new_bid = floatval($_POST['new_bid']);
	$max_bid = get_max(1);
	$winner = get_winner(1);
 	$winner_email = $winner['Email'];
	$winner_bid = floatval($winner['Bid']);
	
	$message = 'Bid exceeded!!! ';
	$your_bid = $your_old_bid;
	
	if($your_new_bid >= $winner_bid){
		// update html
		$your_bid = $your_new_bid;
		if($winner_email === '' || $winner_email === $your_email){
			// update winner email = your email
			$winner_email = $your_email;
		} else {
			if($your_new_bid < $max_bid){
				// update winner bid = your new bid + 0.01
				$winner_bid = $your_bid + 0.01;
			}
		
			if($your_new_bid === $max_bid){
				// update winner bid = max bid
				$winner_bid = $max_bid;
			}
				
			if($your_new_bid > $max_bid){
				// update winner bid
				$winner_bid = $max_bid + 0.01;
				// update winner email
				$winner_email = $your_email;
			}		
		}
		
		// update your account in db
		update_bid($your_email, 1, $your_new_bid);
		// update winner in db
		update_winner(1, $winner_email, $winner_bid);
		
	}
	
	// End of the transaction
	if(!$db->commit()){
		write_log("new_bid.php: During commit");
		$db->rollback();
		exit();
	}
	
	// Reset autocommit state
	if(!$db->autocommit(TRUE)){
		write_log("new_bid.php: Enabling autocommit");
		exit();
	}
	
	// Based on the current winner id update the view
	if($winner_email === $your_email){
		// update html
		$message = 'You are the highest bidder!!! ';
	}
	
	// Based on the current winner id update the view
	$color = color_green;
	if($your_email !== $winner_email)
		$color = color_red;
	
	// Print view
	echo '	<div id="personal_info">
				<h2 id="current_bid_title">
					Your BID
				</h2>
				<h1 id="my_bid" class="bid_value" style="background-color:' . $color . '">€ '
					. $your_bid .
				'</h1>
			</div>';
?>