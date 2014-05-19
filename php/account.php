<?php
	$timeout = 60 * 30; // In seconds, i.e. 30 minutes.
	$fingerprint = md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);
	
	session_start();
	
	if (    (isset($_SESSION['last_active']) && $_SESSION['last_active']<(time()-$timeout))
		 || (isset($_SESSION['fingerprint']) && $_SESSION['fingerprint']!=$fingerprint)
		 || isset($_GET['logout'])
		) {
		setcookie(session_name(), '', time()-3600, '/');
		session_destroy();
	}
	session_regenerate_id(); 
	$_SESSION['last_active'] = time();
	$_SESSION['fingerprint'] = $fingerprint;
	// User authenticated at this point (i.e. $_SESSION['user'] can be trusted).
	$username = $_SESSION['username'];
	
	if(!isset($_SESSION['username']))
	{
		header("location:index.php");
		exit();
	}
	
	//setup database connection
	$DBusername="root";
	$password="MyPass";
	$database="course";
	$table="course";
	
	//log in
	$mysqli = new mysqli("localhost", $DBusername, $password, $database);
	/* check connection */
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}
	//get form data response
	//we got data for all inputs, sanitize
	$Pphone=$_POST['phone'];
	$carrier=strip_tags(stripslashes($mysqli->real_escape_string($_POST['carrier'])));
	$phone = ereg_replace("[^0-9]", "", strip_tags(stripslashes($mysqli->real_escape_string($Pphone))));
	if($phone == "")
		$carrier = 0;
	$query = "UPDATE members SET phone = '$phone', carrier='$carrier' WHERE username='$username'";
	$result = $mysqli->query($query);
	if(!$result)
	{
		printf("Error: %s\n", $mysqli->error);
    }
	header("location:../account.php");		
?>