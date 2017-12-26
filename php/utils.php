<?php
	// Include file with useful constants values
	@include 'constants.php';

	// *** L O G   O U T   R O U T I N E *** //
	function log_out($dest){	
		// We need to destroy the session, its data and the coookie
		// NB "session_unset()" is deprecated
					
		// Destroy "_SESSION" array by overwriting it with an empty array
		$_SESSION = array();
					
		// First we check if php sessions are using cookies
		if(ini_get("session.use_cookies")){
			// Then we explicitly mark the cookie as unvalid by setting
			// its "lease time" to a negative value (in the past)
			// all other cookie's paramereters are maintaned equal to previous
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - YEAR, $params["path"],
			$params["domain"], $params["secure"], $params["httponly"]);
		}
					
		// Then we destroy the session on server
		session_destroy();
		
		// Finally the view passed as parameter is loaded
		echo '	<script type="text/javascript">
					$("#changeable").fadeOut(\'slow\', function() {
						$("#changeable").load("' . $dest .'", function(response, status, xhr) {
							if(status == "error")
								ReportError(xhr.status + " - " + xhr.statusText);	

						$("#changeable").fadeIn(\'slow\');
					})});
				</script>';

		exit();
	}
	
	// *** E R R O R   H A N D L I N G   R O U T I N E S *** // 
	
	// Append "date+hour+message" to the "day+month+year" file in "log" directory
	function write_log($message){
		$cwd = @getcwd();
		if(@strstr($cwd, 'php') === FALSE) $log_dir = $cwd . '/log';
		else $log_dir = @str_replace('php', 'log', $cwd);
		
		try{
			// Create the "log" directory, if it exits the error is suppressed
			@mkdir($log_dir);
			
			// build line and append it to file
			@file_put_contents($log_dir . '/' . @date('d_m_y') . '_LOG', 
					@date('c') . "\t" . $message . "\n", 
					FILE_APPEND | LOCK_EX);
		} catch (Exception $exception){
			// Just to avoid printing on client
			// if write_log fails we have no means to report errors 
			// For the same reason a '@' is put in front of functions calls to
			// avoid error messages
		}
	}
	
	function err_report($message){
		// Need to print the whole page since this function may be called
		// before all the rest due to "session_manager.php"
		echo '<!DOCTYPE html><html><head>
				<meta charset="UTF-8">
				<title>Hasta</title>
				</head>
				<body>
					<div id="header">
						<a href="/hasta/">Hasta</a>
					</div>
					<div id="main">
						<h1>Error Page</h1>
						<div id="error_header">
							Ops, an error occurred
							<div id="error">'
							. $message
							. '</div>
						</div>
				</body>
				</html>';
									
		write_log("ERROR\t" . $message);
		exit();
	}
	
	// *** I N J E C T I O N   R O U T I N E S *** //
	
	function sanitize_string($string){
		// Remove all HTML/PHP tags from input string
		$result = strip_tags($string);
		
		// Encode all characters not included in ASCII [32; 127] and the '&' character
		return filter_var($result, FILTER_SANITIZE_STRING, array(FILTER_FLAG_ENCODE_LOW,  FILTER_FLAG_ENCODE_HIGH, FILTER_FLAG_ENCODE_AMP));
	}
	
	// *** D B   R O U T I N E S *** //
	
	// Function returning connection object for hasta_db
	// It implements the Singleton pattern
	function get_dblink(){
		static $db_link = NULL;	
		if(!isset($db_link)) {
			$db_link = new mysqli(db_location, db_username, db_password, db_name);
			if($db_link->connect_error){
				write_log("ERROR\t" . "log_in: Connecting to db - " . $db_link->connect_error);
				return FALSE;
			}
		}
		
		return $db_link;		
	}
	
	// Return TRUE on login success, FALSE otherwise
	function log_in($email, $password){
		// Get connection to hasta_db
		$db = get_dblink();
		
		// Inpute sanitation and validation
		// NB Same procedures must be applied on input for users' registration
		$email = sanitize_string($email);
		$password = sanitize_string($password);
		if($email === NULL || $password === NULL)
			return FALSE;
		
		// Password must have at least: one digit and one alphabetic character
		$pattern = '/.*([0-9].*[a-zA-Z])|([a-zA-Z].*[0-9]).*/';
		if(preg_match($pattern, $password) != 1)
			return FALSE;
		
		// Email validation
		if(filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE)
			return FALSE;
		
		// Password encryption
		$password = substr(hash('sha512', $password . $email), 0, 32);
		
		// SQL injection avoidance
		$email = $db->real_escape_string($email);
		$password = $db->real_escape_string($password);
		
		// Query execution, result is stored
		$result =  $db->query("select count(*) as Found from user where Email = '$email' and Pwd = '$password'");
		
		// Result check
		if($result === FALSE){
			write_log("ERROR\t" . "log_in: Executing select - " . $db->error);
			return FALSE;
		}
		
		$result = intval($result->fetch_array(MYSQLI_ASSOC)['Found']);
		if($result !== 1) return FALSE;
		return TRUE;
	}
	
	// Return TRUE on signin success, FALSE otherwise
	function sign_in($email, $password){
		// Get connection to hasta_db
		$db = get_dblink();
		
		// Inpute sanitation and validation
		// NB Same procedures must be applied on input for users' registration
		$email = sanitize_string($email);
		$password = sanitize_string($password);
		if($email === NULL || $password === NULL)
			return FALSE;
		
		// Password must have at least: one digit and one alphabetic character
		$pattern = '/.*([0-9].*[a-zA-Z])|([a-zA-Z].*[0-9]).*/';
		if(preg_match($pattern, $password) != 1)
			return FALSE;
		
		// Email validation
		if(filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE)
			return FALSE;
		
		// Password encryption
		$password = substr(hash('sha512', $password . $email), 0, 32);
				
		// SQL injection avoidance
		$email = $db->real_escape_string($email);
		$password = $db->real_escape_string($password);
		
		// Query execution, result is stored
		$result =  $db->query("insert into user (Email, Pwd) values ('$email', '$password')");
				
		// Result check
		if($result === FALSE){
			write_log("ERROR\t" . "sign_in: Executing select - " . $db->error);
			return FALSE;
		}
				
		return TRUE;
	}
		
	// Return product info as an associative array (attribute_name => attribute_value) on success
	// FALSE otherwise
	function get_product($product_id){	
		// Get connection to hasta_db
		$db = get_dblink();
			
		// Inpute sanitation and validation
		// NB Same procedures must be applied on input for users' registration
		$product_id= sanitize_string($product_id);
		if($product_id === NULL)
			return FALSE;
		
		// SQL injection avoidance
		$product_id = $db->real_escape_string($product_id);
		
		// Query execution, result is stored
		$result =  $db->query("select Name, Image, Description from product where ProductId = '$product_id'");
	
		// Result check
		if($result === FALSE){
			write_log("ERROR\t" . "get_product: Executing select - " . $db->error);
			return FALSE;
		}
				
		return $result->fetch_assoc();
	}

	// Return "winner" info as an associative array (attribute_name => attribute_value) on success
	// FALSE otherwise
	function get_winner($product_id){
		// Get connection to hasta_db
		$db = get_dblink();
		
		// Inpute sanitation and validation
		// NB Same procedures must be applied on input for users' registration
		$product_id= sanitize_string($product_id);
		if($product_id === NULL)
			return FALSE;
		
		// SQL injection avoidance
		$product_id = $db->real_escape_string($product_id);
		
		// Query execution, result is stored
		$result =  $db->query("select Email, Bid from winner where ProductId = '$product_id' for update");
		
		// Result check
		if($result === FALSE){
			write_log("ERROR\t" . "get_winner: Executing select - " . $db->error);
			return FALSE;
		}
			
		return $result->fetch_assoc();
	}
	
	// Update of record regarding the "winner" in hasta_db.winner
	// TRUE on success, FALSE otherwise
	function update_winner($product_id, $email, $new_bid){
		// Get connection to hasta_db
		$db = get_dblink();
		
		// Inpute sanitation and escaping
		// NB Same procedures must be applied on input for users' registration
		$product_id = sanitize_string($product_id);
		$email = sanitize_string($email);
		if($product_id === NULL || $email === NULL || !is_float($new_bid)){
			write_log("ERROR\t" . "get_bid: Validating input");
			return FALSE;
		}
		
		// Email validation
		if(filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE){
			write_log("ERROR\t" . "get_bid: Validating input");
			return FALSE;
		}
		
		// SQL injection avoidance
		$product_id = $db->real_escape_string($product_id);
		$email = $db->real_escape_string($email);
		$new_bid = $db->real_escape_string($new_bid);
		
		// Query execution, result is stored
		$result =  $db->query("update winner set Email = '$email', Bid = $new_bid where ProductId = '$product_id'");
		
		// Result check
		if($result === FALSE){
			write_log("ERROR\t" . "get_bid: Executing update - " . $db->error);
			return FALSE;
		}
		
		return TRUE;
	}
	
	// Return current user's bid on success
	// FALSE otherwise
	function get_bid($email, $product_id){
		// Get connection to hasta_db
		$db = get_dblink();
		
		// Inpute sanitation and escaping
		// NB Same procedures must be applied on input for users' registration
		$product_id = sanitize_string($product_id);
		$email = sanitize_string($email);
		if($product_id === NULL || $email === NULL){
			write_log("ERROR\t" . "get_bid: Validating input");
			return FALSE;
		}
		
		// Email validation 
		if(filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE){
			write_log("ERROR\t" . "get_bid: Validating input");
			return FALSE;
		}
		
		// SQL injection avoidance
		$product_id = $db->real_escape_string($product_id);
		$email = $db->real_escape_string($email);
		
		// Query execution, result is stored
		$result =  $db->query("select Bid from bids where Email = '$email' and ProductId = '$product_id' for update");
		
		// Result check
		if($result === FALSE){
			write_log("ERROR\t" . "get_bid: Executing select - " . $db->error);
			return FALSE;
		}
		
		$result = floatval($result->fetch_array(MYSQLI_ASSOC)['Bid']);
		return $result;
	}
	
	// Update of record regarding the current_user in hasta_db.bids
	// TRUE on success, FALSE otherwise
	function update_bid($email, $product_id, $new_bid){
		// Get connection to hasta_db
		$db = get_dblink();
		
		// Inpute sanitation and escaping
		// NB Same procedures must be applied on input for users' registration
		$product_id = sanitize_string($product_id);
		$email = sanitize_string($email);
		if($product_id === NULL || $email === NULL || !is_float($new_bid)){
			write_log("ERROR\t" . "get_bid: Validating input");
			return FALSE;
		}
		
		// Email validation
		if(filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE){
			write_log("ERROR\t" . "get_bid: Validating input");
			return FALSE;
		}
		
		// SQL injection avoidance
		$product_id = $db->real_escape_string($product_id);
		$email = $db->real_escape_string($email);
		$new_bid = $db->real_escape_string($new_bid);
		
		// Query execution, result is stored
		$result =  $db->query("insert into bids (Email, ProductId, Bid) values ('$email', '$product_id', $new_bid) on duplicate key update Bid = $new_bid");
		
		// Result check
		if($result === FALSE){
			write_log("ERROR\t" . "get_bid: Executing insert - " . $db->error);
			return FALSE;
		}
		
		return TRUE;
	}
	
	// Return max bid in hasta_db.bids on success
	// FALSE otherwise
	function get_max($product_id){
		// Get connection to hasta_db
		$db = get_dblink();
		
		// Inpute sanitation and escaping
		// NB Same procedures must be applied on input for users' registration
		$product_id = sanitize_string($product_id);
		if($product_id === NULL){
			write_log("ERROR\t" . "get_bid: Validating input");
			return FALSE;
		}
		
		// SQL injection avoidance
		$product_id = $db->real_escape_string($product_id);
		
		// Query execution, result is stored
		$result =  $db->query("select max(Bid) as MaxBid from bids where ProductId = '$product_id' for update");
		
		// Result check
		if($result === FALSE){
			write_log("ERROR\t" . "get_bid: Executing select - " . $db->error);
			return FALSE;
		}
		
		$result = floatval($result->fetch_array(MYSQLI_ASSOC)['MaxBid']);
		return $result;
	}	
?>