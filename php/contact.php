<?php
	//mail include
	require_once("/usr/share/php/Mail.php");
	//captcha stuff
	require_once('../securimage/securimage.php');
	$privatekey = "6LdH6c8SAAAAAOT4XV4yBvfbIZ9n6TexM_1T-nNl";
	//validate session
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
	//get captcha response
	$image = new Securimage();
	if(isset($_POST['captcha_code'])){

    	if ($image->check($_POST['captcha_code']) == true && $_POST["message"])
	{
		//we got data for all inputs, sanitize
		$Pname=$_POST['name'];
		$email=$_POST['email'];
		$Pmess=$_POST['message'];
		$name = strip_tags(stripslashes($mysqli->real_escape_string($Pname)));
		$message = strip_tags(stripslashes($mysqli->real_escape_string($Pmess)));
		
		//send the message
		$from = "CourseMon <coursemon0@gmail.com>";
		$to = "<costes.c@gmail.com>";
		$subject = "CourseMon Message";
		//$body = "Testing...\n1..2..3...\nTesting...";

		$host = "ssl://smtp.gmail.com";
		$port = "465";
		$username = "coursemon0";
		$password = "CoursePass";

		$headers = array ('From' => $from,
		  'To' => $to,
		  'Subject' => $subject);
		$smtp = Mail::factory('smtp',
		  array ('host' => $host,
			'port' => $port,
			'auth' => true,
			'username' => $username,
			'password' => $password));
		
		//send mail
		$body = "From: $name\nEmail: $email\nMessage: $message";
		$mail = $smtp->send($to, $headers, $body);
		
		if (PEAR::isError($mail)) {
		  //echo("<p>" . $mail->getMessage() . "</p>");
		 } else {
		  //echo("Alert sent!");
		 }
		 header("location:../index.php");
	}
	else print("invaild data\nreCAPTCHA said: " . $resp->error . "");

     }
?>