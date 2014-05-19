<?php

	//captcha stuff
	//require_once('../securimage/securimage.php');
	$privatekey = "6LdH6c8SAAAAAOT4XV4yBvfbIZ9n6TexM_1T-nNl";
	//validate session
	//session_start();
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
	//$image = new Securimage();
	//if(isset($_POST['captcha_code'])){

    	//if ($image->check($_POST['captcha_code']) == true)
	//{
		if($_POST["regname"] && $_POST["regpass1"] && $_POST["regpass2"])
		{
			//we got data for all inputs, sanitize
			$Pname=$_POST['regname'];
			$Ppass1=$_POST['regpass1'];
			$Ppass2=$_POST['regpass2'];
			$Pphone=$_POST['phone'];
			$carrier=strip_tags(stripslashes($mysqli->real_escape_string($_POST['carrier'])));
			$username = strip_tags(stripslashes($mysqli->real_escape_string($Pname)));
			$pass1 = strip_tags(stripslashes($mysqli->real_escape_string($Ppass1)));
			$pass2 = strip_tags(stripslashes($mysqli->real_escape_string($Ppass2)));
			$phone = ereg_replace("[^0-9]", "", strip_tags(stripslashes($mysqli->real_escape_string($Pphone))));
			if($phone == "")
				$carrier = 0;
			//see if passwords match
			if(strcmp('pass1','pass2'))
			{
				//hash to get final password
				$password = sha1(strip_tags(stripslashes($mysqli->real_escape_string($pass1))));
				//see if the user name is unique				
				$query="SELECT * FROM members WHERE username = '$username'";
				$result = $mysqli->query($query);
				if($result->num_rows == 0)
				{
					//unique user name, proceed
					//add user to database
					
					$query = "INSERT INTO members VALUES('','$username','$password','$phone','$carrier')";
					$result = $mysqli->query($query);
					//log user in
					$fingerprint = md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);
					$_SESSION['last_active'] = time();
					$_SESSION['fingerprint'] = $fingerprint;
					$_SESSION["username"] = $username;
					//redirect to browse page
					//header("location:../browse.php?new=1");
					$arr = array('message' => 'Account Created');
					echo(json_encode($arr));
				}
				else {
				//header("location:../register.php?err=Username Already Taken");
					$arr = array('message' => 'Username Already Taken');
					echo(json_encode($arr));
				}
			}
			//else header("location:../register.php?err=Passwords Don't Match");
			else {$arr = array('message' => 'Passwords Don't Match');
				echo(json_encode($arr));
			}
		}
		//else header("location:../register.php?err=Invalid Data");
		else {$arr = array('message' => 'Invalid Data');
			echo(json_encode($arr));
		}
	//}
	//else header("location:../register.php?err=Captcha Failed");
	//else {$arr = array('message' => 'Captcha Failed');
	//	echo(json_encode($arr));
	//}
    //}
?>