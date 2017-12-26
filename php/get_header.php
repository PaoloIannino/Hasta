<?php
	// Include file with useful constants values
	@include 'constants.php';

	// Include useful php routines
	@include 'utils.php';
	
	// Retrieve session info
	if(session_start() === FALSE)
		write_log("Creating new session");
	
	// Get current winner info
	$winner = get_winner(1);
	if(isset($winner)){
		if(isset($_SESSION['s239562_email'])){
			// By default we are not the winner
			$message ='BID EXCEEDED';
			$color = color_red;
			$your_email = $_SESSION['s239562_email'];
			$your_bid = get_bid($your_email, 1);
			
			// If the winner id corresponds to our one, properly update the view
			if($winner['Email'] === $_SESSION['s239562_email']){
				$message = 'YOU ARE THE HIGHEST BIDDER';
				$color = color_green;
			}
		
		// Update the min value requested in the input form
		$min_bid = $winner['Bid'] + 0.01;
	
		// Update different elements of the view
		// some updates are done here only because this procedure is periodically called
		echo '	<script type="text/javascript">
					$(document).ready(function() {
						$("#footer_content").html(\'Logged in as ' . $_SESSION['s239562_email'] . '\');
						$("#number_raise").attr("min","' . $min_bid . '");
						$("#number_raise").attr("placeholder","' . $min_bid . '");
						$("#number_raise").attr("title","Your bids must be greater than the winner one");
						$("#my_bid").attr("style","background-color:' . $color . '");
						$("#my_bid").html("€ ' . $your_bid . '");
						$("#raise_div").attr("style","visibility:visible");
					});
				</script>';
		
		// Print the view
		echo '	<div id="message_div">
					<h1 id="message" style="color:' . $color. '">'
						. $message .
					'</h1>
				</div>
				<div id="bid_info" style="width:28%;margin-left: 10%;">
					<div id="current_bid">
						<h2 id="current_bid_title">
							Winner BID
						</h2>
						<h1 class="bid_value">€ ' 
							. $winner['Bid'] . 
						'</h1>
						<h2 id="current_bid_email">'
							. $winner['Email'] .
						'</h2>
					</div>
				</div>';
		} else {
			// If we are not logged in, print another view
			echo '	<div id="message_div">
						<h1 id="message"">LOGIN TO MAKE YOUR BID</h1>
					</div>
					<div id="bid_info" style="width:100%;margin-left:0%;">
						<div id="current_bid">
							<h2 id="current_bid_title">
								Winner BID
							</h2>
							<h1 class="bid_value">€ ' 
								. $winner['Bid'] . 
							'</h1>
							<h2 id="current_bid_email">'
								. $winner['Email'] .
							'</h2>
						</div>
					</div>';
			
			// Update different elements of the view
			// some updates are done here only because this procedure is periodically called
			echo '	<script type="text/javascript">
					$(document).ready(function() {
						$("#footer_content").html(\'Join us today to run for our wonderful lastest offer: A BRAND NEW MERKAVA 4 ISRAELI TANK!!!\');
						$("#raise_div").attr("style","visibility:hidden");
					});
				</script>';
		}
	} else {
		// If we are unable to retrieve the winner info, print an error message
		$message = 'ERROR RETRIEVING INFO';
		$color = color_red;
		echo '	<div id="message_div">
					<h1 id="message" style="color:' . $color. '">'
						. $message .
					'</h1>
				</div>';
	}
?>