<?php


if (!isset($_COOKIE["login"])) {
        setcookie("login", "", 0, "/");
        $_COOKIE["login"] = "";
}



// Connect to database
	
	include_once('phpCAS/CAS.php');


	if (isset($_REQUEST['logout'])) {
		setcookie("login", "", -3600, "/");
		phpCAS::logout();
	} else {
		//echo "making it to CAS section";
			
			// Send user to CAS
			phpCAS::client(CAS_VERSION_2_0,'eis-test.gvsu.edu',443,'/auth/');
			$filename = 'caslog.txt';
			phpCAS::setDebug($filename);

			// initialize phpCAS
			

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

?>

<html>
  <head>
    <title>phpCAS simple client</title>
  </head>
  <body>
    <h1>Successfull Authentication!</h1>
    
    <p>the user's login is <b><?php echo phpCAS::getUser(); ?></b>.</p>
    <p>phpCAS version is <b><?php echo phpCAS::getVersion(); ?></b>.</p>
    <p><a href="?logout=">Logout</a></p>
  </body>
</html>