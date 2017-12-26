<?php

	// *** H T T P S   R E D I R E C T *** //
	if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on' ) {
		$redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		var_dump('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		header('HTTP/1.1 301 Moved Permanently');
		header('Location: ' . $redirect);
		exit();
	}
	
	// Include file with useful constants values
	@include 'php/constants.php';
	
	// Include useful php routines
	@include 'php/utils.php';
	
	// Execute the code needed for session managing
	@include 'php/session_manager.php';
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Hasta</title>
<link rel="stylesheet" href="css/style.css">
<script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="js/utils.js"></script>
</head>
<body>
	<div id="header">
		<a id="header_content">Hasta</a>
	</div>
	<div id="main">
		<div id="changeable">
			<script type="text/javascript"><!-- // The script tests if cookies are enabled, if not prints an error message (see next script)
				if(!navigator.cookieEnabled)
					$("#changeable").html('<div class="disabled"><h1>Cookies are disabled</h1><h2>No surfing on this site is allowed</h2></div>');						
			//--></script>
			<noscript> <!-- If Javascript is disabled a warning message is printed on screen -->
				<div class="disabled">
					<h1>Javascript is disabled</h1>
					<h2>The site will not behave properly</h2>
				</div>
			</noscript>
		</div>
	</div>
	<div id="nav_bar">
		<a id="home_nav" class="nav_bar_button">Home</a>
		<a id="login_nav" class="nav_bar_button">Login</a>
		<a id="signin_nav" class="nav_bar_button">Sign In</a>
		<a id="product_nav" class="nav_bar_button">Product</a>
		<a id="logout_nav" class="nav_bar_button">Logout</a>
	</div>
	<div id="footer">
		<div id="footer_content">Join us today to run for our wonderful lastest offer: A BRAND NEW MERKAVA 4 ISRAELI TANK!!!</div>
	</div>
	<script type="text/javascript"><!--
		// If cookies are disabled any kind of navigation is not allowed (all links to Ajax functions are locked)
		if(navigator.cookieEnabled){
			// When the page is loaded, Ajax load the main view
			$("#changeable").fadeOut('slow', function() {
				$("#changeable").load("php/hasta.php", function(response, status, xhr) {
					if(status == "error")
						ReportError(xhr.status + " - " + xhr.statusText);	

					$("#changeable").fadeIn('slow');
			})});

			// A click on the "Home" loads the main view
			$("#home_nav").click(function() {
				$("#changeable").fadeOut('slow', function() {
					$("#changeable").load("php/hasta.php", function(response, status, xhr) {
						if(status == "error")
							ReportError(xhr.status + " - " + xhr.statusText);	

						$("#changeable").fadeIn('slow');
			})})});

			// A click on the header loads the main view
			$("#header_content").click(function() {
				$("#changeable").fadeOut('slow', function() {
					$("#changeable").load("php/hasta.php", function(response, status, xhr) {
						if(status == "error")
							ReportError(xhr.status + " - " + xhr.statusText);	

						$("#changeable").fadeIn('slow');
			})})});

			// A click on the "Login" button loads the login view
			$("#login_nav").click(function() {
				$("#changeable").fadeOut('slow', function() {
					$("#changeable").load("php/login.php", function(response, status, xhr) {
						if(status == "error")
							ReportError(xhr.status + " - " + xhr.statusText);	

						$("#changeable").fadeIn('slow');
			})})});

			// A click on the "Sign in" button loads the sign in view
			$("#signin_nav").click(function() {
				$("#changeable").fadeOut('slow', function() {
					$("#changeable").load("php/signin.php", function(response, status, xhr) {
						if(status == "error")
							ReportError(xhr.status + " - " + xhr.statusText);	

						$("#changeable").fadeIn('slow');
			})})});

			// A click on the "Product" button loads the product view
			$("#product_nav").click(function() {
				$("#changeable").fadeOut('slow', function() {
					$("#changeable").load("php/get_product.php", function(response, status, xhr) {
					if(status == "error")
						ReportError(xhr.status + " - " + xhr.statusText);	

						$("#changeable").fadeIn('slow');
			})})});

			// A click on the "Logout" button call a page containing only the "log_out" routine, 
			// in the end, such routine load the main view
			$("#logout_nav").click(function() {
				$("#changeable").fadeOut('slow', function() {
					$("#changeable").load("php/logout.php", function(response, status, xhr) {
						if(status == "error")
							ReportError(xhr.status + " - " + xhr.statusText);	

						$("#changeable").fadeIn('slow');
			})})});

			// Since MS Internet Explorer does not support our css "spin_and_zoom" animation, 
			// another animation is provided. 
			// This is valid only for MS Internet Explorer, MS Edge support "spin_and_zoom".
			var msie = msieversion();
			if(msie) {
				$('head').append('<link rel="stylesheet" href="css/IEstyle.css">');
			}
			
		}
//--></script>
</body>
</html>
		

	