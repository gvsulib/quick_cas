<?php


if (!isset($_COOKIE["login"])) {
        setcookie("login", "", 0, "/");
        $_COOKIE["login"] = "";
}


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
	
	if (isset($_REQUEST['logout'])) {
		setcookie("login", "", -3600, "/");
		phpCAS::logout();
	}
	
	

	


	
	$result = $db->query("INSERT INTO cas VALUES ('', '$user_location', '$referrer', '$now')");

	if(!$result) {
		echo 'There was an error writing referrer to the database.';
		die;
	} else {
		//echo "making it to CAS section";
			
			// Send user to CAS
			include_once('CAS.php');
			$filename = 'caslog.txt';
			phpCAS::setDebug($filename);

			// initialize phpCAS
			phpCAS::client(CAS_VERSION_2_0,'eis.gvsu.edu',443,'/auth/');

			// no SSL validation for the CAS server
			phpCAS::setNoCasServerValidation();

			// force CAS authentication
			phpCAS::forceAuthentication();

			$username = phpCAS::getUser();
			setcookie("login", $username, 0, "/");
			$_COOKIE['login'] = $username;

	}
	



// at this step, the user has been authenticated by the CAS server
// and the user's login name can be read with phpCAS::getUser().



if(isset($_COOKIE['login']) && !empty($_COOKIE['login'])) {

	
	
	echo "login:" . $_COOKIE['login'];

}

?>