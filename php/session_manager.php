<?php

	// This file explicitly manages sessions
	// The main aim is both to definitely destroy the cookie releated to a session
	// after a certain amount of time has elapsed and to be sure a "new" cookie is
	// really new.
	// In fact, also if it is expired, a cookie sent by the client can be accepted
	// another time by the server and we want an expired cookie to be not reusable
	
	// Without a session nothing for its management is allowed
	if(session_start() === FALSE)
		err_report("Creating new session");
	
	// A flag to
	$new = FALSE;
	
	// "diff" stores the number of seconds elapsed from last access
	$diff = 0;
	if(isset($_SESSION['s239562_last_access']))
		$diff = time() - $_SESSION['s239562_last_access'];
	else $new = TRUE;
	
	if($new || $diff > MAX_SESSION_TIME){
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
	} 
	
	// Then last access time is updated
	$_SESSION['s239562_last_access'] = time();
?>