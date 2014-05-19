<?php
	require_once("../PHPMailer-master/PHPMailerAutoload.php");
	
	//database creation command
	//CREATE TABLE course(ind INT(16) NOT NULL PRIMARY KEY AUTO_INCREMENT,user VARCHAR(20) NOT NULL, crn VARCHAR(10) NOT NULL, title VARCHAR(150), cap SMALLINT, act SMALLINT, rem SMALLINT, alert BIT(1) NOT NULL, alerted BIT(1) default='0');
    //CREATE TABLE members(ind INT(16) NOT NULL PRIMARY KEY AUTO_INCREMENT,username VARCHAR(100) NOT NULL, password VARCHAR(65) NOT NULL, phone VARCHAR(15), carrier INT);	
	//NOTES
	//This works to go through the course table, lookup the data for each crn, and update table.
	//Doesn't take into accound that there could be multiple entries for the same crn (TODO?)
	print("Loading background\n");
	//setup database connection
	$username="root";
	$password="MyPass";
	$database="course";
	$table="course";
	//log in
	$mysqli = new mysqli("localhost", $username, $password, $database);
	/* check connection */
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}
	
	
	
	$old_num = "";
	while(1){
		//main loop
		$time = microtime();
		print("updating data, time = $time\n");
		//get course numbers to lookup from table
		$result = $mysqli->query("SELECT DISTINCT crn FROM $table WHERE sem = 0 ORDER BY crn");
	
		if($result)
		{
			//if we have results iterate through and get data
			if(($result->num_rows > 0))
			{
				print("Updating $result->num_rows courses.\n");
				//go through each result row
				while ($row = $result->fetch_object()) 
				{
				//get crn from row
					$crn = $row->crn;
					//print("CRN: $crn\n");
					//get seats for this class
					$res = get_seats("https://oscar.gatech.edu/pls/bprod/bwckschd.p_disp_detail_sched?term_in=201408&crn_in=$crn");
					//print("$res->rem seats remaining");
					//update table with seats remaining
					//print("updating table\n");
					if($res->rem == 0)
					{
						$query = "UPDATE course SET cap = '$res->cap', act='$res->act', rem = '$res->rem', alerted = 0 WHERE crn = '$row->crn'";
					}
					else
					{
						$query = "UPDATE course SET cap = '$res->cap', act='$res->act', rem = '$res->rem' WHERE crn = '$row->crn'";
					}
					//print("Query: $query\n");
					$mysqli->query($query);
					//wait a second so oscar doesn't get mad
					sleep(1);
				}
			}
		}
		$result = $mysqli->query("SELECT DISTINCT crn FROM $table WHERE sem=1 ORDER BY crn");
	
		if($result)
		{
			//if we have results iterate through and get data
			if(($result->num_rows > 0))
			{
				print("Updating $result->num_rows courses.\n");
				//go through each result row
				while ($row = $result->fetch_object()) 
				{
				//get crn from row
					$crn = $row->crn;
					//print("CRN: $crn\n");
					//get seats for this class
					$res = get_seats("https://oscar.gatech.edu/pls/bprod/bwckschd.p_disp_detail_sched?term_in=201405&crn_in=$crn");
					//print("$res->rem seats remaining");
					//update table with seats remaining
					//print("updating table\n");
					if($res->rem == 0)
					{
						$query = "UPDATE course SET cap = '$res->cap', act='$res->act', rem = '$res->rem', alerted = 0 WHERE crn = '$row->crn'";
					}
					else
					{
						$query = "UPDATE course SET cap = '$res->cap', act='$res->act', rem = '$res->rem' WHERE crn = '$row->crn'";
					}
					//print("Query: $query\n");
					$mysqli->query($query);
					//wait a second so OSCAR doesn't get mad
					sleep(1);
				}
			}
		}
		//check to see if we need to send alerts
		$result = $mysqli->query("SELECT * FROM course");
		if($result)
		{
			if($result->num_rows > 0)
			{
				while($row = $result->fetch_object())
				{
					if(($row->rem > 0) & (strval($row->alert) == 1) &(strval($row->alerted) == 0))
					{
						print("\tSending Alert\n");
						//there are seats available, send the alert
						send_alert($mysqli, $row);
					}
				}
			}
		}
		//pause excecution for awile (3 mins)
		sleep(60);
	}
	
	function get_seats($url){

		if( !($data = file_get_contents($url)) ) return false;
		//find first occurance of the word seats
		$sub = 0; //characters to subtract b/c of single digit results
		$spos = strpos($data, "Seats");
		$rem = substr($data, $spos + 18 + 22, 3);
		//test to see if it's only two digits
		if(!is_numeric($rem))
		{$rem = substr($rem, 0, 2);$sub++;}
		//test to see if it's only one digit
		if(!is_numeric($rem))
		{$rem = substr($rem, 0, 1);$sub++;}
		$res->cap = strval($rem);
		//print("Capacity: $res->cap\n");
		$rem = substr($data, $spos + 18 + 31 + 22 - $sub, 3);
		//test to see if it's only two digits
		if(!is_numeric($rem))
		{$rem = substr($rem, 0, 2);$sub++;}
		//test to see if it's only one digit
		if(!is_numeric($rem))
		{$rem = substr($rem, 0, 1);$sub++;}
		$res->act = strval($rem);
		//print("Actual: $res->act\n");
		$rem = substr($data, $spos + 18 + 31 + 31 + 22 - $sub, 3);
		//test to see if it's only two digits
		if(!is_numeric($rem))
		{$rem = substr($rem, 0, 2);$sub++;}
		//test to see if it's only one digit
		if(!is_numeric($rem))
		{$rem = substr($rem, 0, 1);$sub++;}
		$res->rem = strval($rem);
		//print("Remaining: $res->rem\n");
		return $res;
	}
	
	function send_alert($mysqli, $row)
	{
		//get user contact info
		$user = $row->user;
		print("Sending alert to $user\n");
		$carrier = $row->carrier;
		$ind = $row->ind;
		$result = $mysqli->query("SELECT * FROM members WHERE username = '$row->user'");
		$result = $result->fetch_object();
		$phone = $result->phone;
		if($phone != "")
		{
			//has a phone number, can send alert
			//get provider email
			$result = $mysqli->query("SELECT * FROM carriers WHERE ind = '$result->carrier'");
			$result = $result->fetch_object();
			$c_address = $result->address;
			$from = "CourseMon <coursemon0@gmail.com>";
			$to = "$phone@$c_address";
			$subject = "";
			//$body = "Testing...\n1..2..3...\nTesting...";

			$host = "smtp.gmail.com";
			$port = "587";
			$username = "coursemon0";
			$password = "CoursePass";

			//$headers = array ('From' => $from,
			//  'To' => $to,
			//  'Subject' => $subject);
			//$smtp = Mail::factory('smtp',
			//  array ('host' => $host,
			//	'port' => $port,
			//	'auth' => true,
			//	'username' => $username,
			//	'password' => $password));
			$mail = new PHPMailer;

$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = $host;  // Specify main and backup server
$mail->Port = 587;
$mail->Timeout = 400;
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = $username;                            // SMTP username
$mail->Password = $password;                           // SMTP password
$mail->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted;
$mail->SMTPKeepAlive = true;
$mail->From = 'coursemon0@gmail.com';
$mail->addAddress($to);  // Add a recipient
			
			//build title
			if($row->title != "")
				$title=$row->title." CRN".$row->crn;
			else
				$title="CRN".$row->crn;
			//print debug
			//send mail
			//$mail = $smtp->send($to, $headers, "$row->rem Seats Available in $title!");
			
$mail->Body    = "$row->rem Seats Are Available in $title!";


			if(!$mail->send())  {
			  echo 'Message could not be sent.';
   				echo 'Mailer Error: ' . $mail->ErrorInfo;
   				exit;
			 } else {
			  print("Alert sent to $user at $to!\n");
			  //set the alerted flag
			  $result = $mysqli->query("UPDATE course SET alerted='1' WHERE ind = '$row->ind'");
			  $result = $mysqli->query("SELECT * FROM stats WHERE name='alerts_sent'");
			  $result=$result->fetch_object();
			  $new = strval($result->value) + 1;
			  $result=$mysqli->query("UPDATE stats SET value = $new WHERE name='alerts_sent'");
			 }
		}
	}
?>
