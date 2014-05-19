<?php
	include 'update.php';
	
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

	if($_POST["crn"] && $_POST["username"])
	{
		//we got data for all inputs, sanitize
		$Pcrn=$_POST['crn'];
		$sem=$_POST['sem'];
		$Ptitle=$_POST['title'];
		$alert=strval($_POST['alert']);
		$Puser=$_POST['username'];
		
		$username = strip_tags(stripslashes($mysqli->real_escape_string($Puser)));
		//make sure this is only numbers
		$crn = ereg_replace("[^0-9]", "", strip_tags(stripslashes($mysqli->real_escape_string($Pcrn))));
		$title = strip_tags(stripslashes($mysqli->real_escape_string($Ptitle)));		
		
		//add course to database
		$query = "INSERT INTO course VALUES('','$username','$crn','$title',0,0,0,$alert,0,'$sem')";
		$result = $mysqli->query($query);
		//update values
		update($mysqli, $crn,$sem);
		//redirect to browse page
		header("location:../browse.php");
	}
	else print"invaild data";
?>