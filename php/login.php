<?php

	// Include file with useful constants values
	@include 'utils.php';
	
	// Include useful php routines
	@include 'constants.php';

	// On login error $error is filled to print error message on screen
	$error='';
	
	// $email stores the input value for the "email"
	$email='';
	
	// $password stores the input value for teh "password"
	$password='';

	// Suggestion displayed on mouse over "email" input field
	$email_title='Enter a valid email address';

	// Do not allow the user leave the page without confirm
	echo '<script type="text/javascript"><!--
				//Refresh redirects you to home
 				window.onbeforeunload = function() {return true};
				//--></script>';
				
	// Create a new session
	if(session_start() === FALSE)
		write_log("Creating new session");

	// Session management if the user is already logged in 
	if(isset($_SESSION['s239562_authenticated'])){
		if((time() - $_SESSION['s239562_last_access']) > MAX_SESSION_TIME){
			// If the session is invalid, log out
			log_out('php/login.php');
		} else {
			// If the session is still valid, print a message and update "last access" time
			// be aware of the "exit()" the rest of this page will not be printed
			$_SESSION['s239562_last_access'] = time();
			echo '<div class="disabled"><h1>Already logged in as ' . $_SESSION['s239562_email'] . '</h1></div>';
			exit();
		}
	}

	// If the server received a post request
	if($_SERVER['REQUEST_METHOD'] === "POST"){
		if(isset($_POST['email']) && isset($_POST['password'])){
			// Store the passed paramenters  
			$email = $_POST['email'];
			$password = $_POST['password'];
			if(log_in($email, $password)){ 
				// If the login is successuful, update session data and redirect to main view
				$_SESSION['s239562_authenticated'] = TRUE;
				$_SESSION['s239562_last_access'] = time();
				$_SESSION['s239562_email'] = $email;
			
				echo '	<script type="text/javascript">//<!--
							$("#changeable").load("php/hasta.php", function(response, status, xhr) {
								if(status == "error")
									ReportError(xhr.status + " - " + xhr.statusText);
						});
						//--></script>';
			// Otherwise, print an error in the current view
			} else $error = "Failed";
		}
	}

	// The following is printed each time the view is loaded unless the user is alredy logged in
	echo '	<div id="login_message_div" class="page_header_div">
				<h1 class="page_header">Login
					<div class="access_failure">'	. $error . '</div>
				</h1>
			</div>
			<div id="login_form_div" class="form_div">
				<form method="post" id="login_form">
					<h3> Email </h3>
					<input class="input_box email_box" type="email" name="email" placeholder="Your e-mail address" 
							pattern=".*@.*\..*" value="' . $email . '" title="' . $email_title .'" required><br>
					<h3> Password </h3>
					<input class="input_box" type="password" id="pwd" name="password" placeholder="Your password" required><br>
					<div id="submit_button">
						<input type="submit" name="submit" value="LOGIN" class="input_button">
					</div>
					<script type="text/javascript">//<!--
						$("#login_form").submit(function(event) {
							$("#changeable").load("php/login.php", $("form").serializeArray(), function(response, status, xhr) {
								if(status == "error")
									ReportError(xhr.status + " - " + xhr.statusText);
							});
								
							event.preventDefault();
							$("#login_form").trigger(\'reset\');
						});
					//--></script>
				</form>
			</div>';
?>


		
		
		