<?php




// Connect to database
	
	include_once('recentCAS/CAS.php');
	

	if (isset($_GET['logout'])) {
		setcookie("login", "", 0, "/");
    phpCAS::logout();
    header('Location: https://prod.library.gvsu.edu/status');
	} 


// Send user to CAS
phpCAS::client(CAS_VERSION_2_0,'eis.gvsu.edu',443,'/auth/');
			
$filename = 'caslog.txt';
phpCAS::setDebug($filename);
phpCAS::setVerbose(true);

// initialize phpCAS
			

// no SSL validation for the CAS server
phpCAS::setNoCasServerValidation();


// force CAS authentication
phpCAS::forceAuthentication();

// at this step, the user has been authenticated by the CAS server
// and the user's login name can be read with phpCAS::getUser().

setcookie("login", phpCAS::getUser(), 0, "/");
header('Location: https://prod.library.gvsu.edu/status');

?>

