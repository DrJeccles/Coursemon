<?php
	//Login functions
	
	session_start();
	
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
	
	//escape posted input for saftey
	// username and password sent from form
	$username=$_POST['user'];
	$password=$_POST['password'];
	$clean_username = strip_tags(stripslashes($mysqli->real_escape_string($username)));
	$clean_password = sha1(strip_tags(stripslashes($mysqli->real_escape_string($password))));
	//see if the user is real
	$query="SELECT * FROM members WHERE username='$clean_username' and password='$clean_password'";
	$result = $mysqli->query($query);
	if($result->num_rows == 1)
	{
		//user exists, successful log in
		$fingerprint = md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);
		$_SESSION['last_active'] = time();
		$_SESSION['fingerprint'] = $fingerprint;
		$_SESSION["username"] = $username;
		//redirect to browse page
		$arr = array('message' => 'Login Successful');
		echo(json_encode($arr));
		header("location:../browse.php");
	}
	else
	{
		$arr = array('message' => 'Login Unsuccessful');
		echo(json_encode($arr));
		header("location:../index.php");
	}
?>