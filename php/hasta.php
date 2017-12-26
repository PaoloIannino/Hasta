<?php 
	// Include file with useful constants values
	@include 'utils.php';

	// Include useful php routines
	@include 'constants.php';
	
	// Retrieve session info
	if(session_start() === FALSE)
		write_log("Creating new session");

	if(isset($_SESSION['s239562_authenticated'])){
		// If we are logged in but the elasped time exceeded MAX_SESSION_TIME,
		// log out and redirect to main view
		if((time() - $_SESSION['s239562_last_access']) > MAX_SESSION_TIME){
			log_out('php/hasta.php');
		// Otherwise, update last access
		} else $_SESSION['s239562_last_access'] = time();
	}
	
?>
<div id="home">
	<div id="winner_div">
	</div>
<script type="text/javascript">
		// This function only update the main view by calling an Ajax load of "get_header.php"
		function RefreshWinner() {
			$("#winner_div").load("php/get_header.php", function(response, status, xhr) {
					if(status == "error")
						ReportError(xhr.status + " - " + xhr.statusText);
		})};	

		// Execute the function a first time and then periodically
		var refresh = RefreshWinner();
		setInterval(RefreshWinner, 2000);
</script>
<?php
	// Print this part of the view only if we are logged in and our session is still valid
	if(isset($_SESSION['s239562_authenticated'])){
		if((time() - $_SESSION['s239562_last_access']) <= MAX_SESSION_TIME){
			// Retrieve info for printing our state and the input form
			$your_email = $_SESSION['s239562_email'];
			$your_bid = get_bid($your_email, 1);
			$winner = get_winner(1);
			$winner_email = $winner['Email'];
			
			$color = color_green;
			if($your_email !== $winner_email)
				$color = color_red;
		
			// Print the remaining part of the view
			echo '<div id="raise_div">
					<div id="personal_bid">
					<div id="personal_div">
						<h2 id="current_bid_title">
							Your BID
						</h2>
						<h1 id="my_bid" class="bid_value" style="background-color:' . $color . '">€ '
							. $your_bid .
						'</h1>
					</div>
					<form id="raise_form" method="post">
 						<input type="number" id="number_raise" name="new_bid" class="input_box" min="1.00" step="0.01" required>
 						<input type="submit" id="submit_raise" class="input_button" value="RAISE">
					</form>
					</div>
					</div>
				<script type="text/javascript">
					// Since MS Internet Explorer does not support HTML5 input type="number" validation, 
					// another validation mechanism is provided. 
					// This is valid only for MS Internet Explorer, MS Edge supports HTML5 properly.
					var msie = msieversion();
					if(msie) {
						$("#raise_form").attr("novalidate", "");
					}
			
					$("#raise_form").submit(function(event) {
						if(msie){
							var value = parseFloat($("#number_raise").val());
							var min = parseFloat($("#number_raise").attr("min"));
							if(isNaN(value) || isNaN(min) || value < min){
								if(isNaN(value)){
									alert("Please enter a number");
								} else if(value <= min){
									alert("Your bid must be greater than minimum one");
								}

								event.preventDefault();
							} else {
								$("#personal_div").load("php/new_bid.php", $("form").serializeArray(), function(response, status, xhr) {
									if(status == "error")
										ReportError("ERROR\t: Loading raise " + xhr.status + " - " + xhr.statusText);
								});

								event.preventDefault();
							}
						} else {
							$("#personal_div").load("php/new_bid.php", $("form").serializeArray(), function(response, status, xhr) {
								if(status == "error")
									ReportError("ERROR\t: Loading raise " + xhr.status + " - " + xhr.statusText);
							});

							event.preventDefault();
						}

						$("#raise_form").trigger(\'reset\');
					});
				</script>';
		}
	}
	
?>
</div>
