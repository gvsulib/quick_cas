<?php
session_start();

// Hash the user's IP address and referrer and save to db
// Connect to database
	include('config.php');

	$db = new mysqli($db_host, $db_user, $db_pass, $db_database);
	
	if ($db->connect_errno) {
    	printf("Connect failed: %s\n", $db->connect_error);
    	exit();
	} 
		

	$now = time();
	$user_location = sha1($_SERVER['REMOTE_ADDR']);
	$referrer = $_SESSION['location'];

	if(isset($referrer)) {
		$result = $db->query("INSERT INTO cas VALUES ('', '$user_location', '$referrer', '$now')");

		if(!$result) {
			echo 'There was an error';
			die;
		} else {
			// Send user to CAS
			include_once('CAS.php');
			$filename = 'caslog.txt';
			phpCAS::setDebug($filename);

			// initialize phpCAS
			phpCAS::client(CAS_VERSION_2_0,'cas.gvsu.edu',443,'/auth/');

			// no SSL validation for the CAS server
			phpCAS::setNoCasServerValidation();

			// force CAS authentication
			phpCAS::forceAuthentication();

			$_SESSION['username'] = phpCAS::getUser();

		}
	}



// at this step, the user has been authenticated by the CAS server
// and the user's login name can be read with phpCAS::getUser().



if(isset($_SESSION['username']) && !empty($_SESSION['username'])) {

	$hashed_ip = sha1($_SERVER['REMOTE_ADDR']);

	$loggedin_result = $db->query("SELECT referrer FROM cas WHERE user_location='$hashed_ip' ORDER BY timestamp DESC LIMIT 1");

	if($loggedin_result) {

		$obj = $loggedin_result->fetch_object();
		$record_id = $obj->referrer_id;
		$destination = $obj->referrer;

		// Remove entry from the database. We don't need it anymore!
		$remove_record = $db->query("DELETE FROM cas WHERE referrer_id='$record_id'");

		if($remove_record) {
			// Send the user on their way
			header('Location: ' . $destination);
		}


	} else {

		echo "No way, Jose.";

	}

}

