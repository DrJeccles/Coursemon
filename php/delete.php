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

	if($_POST["ind"])
	{
		$ind = strval($_POST["ind"]);
		//we have the index to delete, make sure this user owns that field
		$query = "SELECT * FROM $table WHERE ind=$ind";
		$result=$mysqli->query($query);
		$result=$result->fetch_object();
		if(trim($result->user) == trim($username))
		{
			//we're good, this is a valid request
			$query = "DELETE FROM $table WHERE ind = '$ind'";
			$result = $mysqli->query($query);
			//redirect to browse page
			header("location:../browse.php");
		}
		else print("break-in attempt detected, that's not your data!");
	}
	else print"invaild data";
?>