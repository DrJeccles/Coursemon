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
?>
<html>
	<head>
		<title>CourseMon - Account</title>
		<meta name="description" content="View and edit your account information." />
		<link rel="stylesheet" type="text/css" href="style.css" />
		<link rel="icon" type="image/png" href="img/favicon.png" />
		<script type="text/javascript">

		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-30724762-1']);
		  _gaq.push(['_trackPageview']);

		  (function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();

		</script>
	</head>
	<body>
			<?php include'header.php';?>
			
			<div class="container">
				
			<div class="cont">
					<p id="list-title">Account</p>
				<FORM action="php/account.php" METHOD=POST>
					<?php
						//$err = $_GET['err'];
						//if($err)
							//print("<p class=\"error\">$err</p>");
					?>
					<div class="left">
					<?php
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
						
						//get user data
						$query = "SELECT * FROM (SELECT members.username, members.phone, carriers.name AS car_name, carriers.ind AS car_ind FROM members LEFT JOIN carriers ON members.carrier=carriers.ind) as result WHERE result.username = '$username'";
						$result = $mysqli->query($query);
						$result = $result->fetch_object();
						for($i=1;$i<11;$i++)
						{
							$carrier_ind[$i] = "false";
						}
						$carrier_ind[strval($result->car_ind)] = "selected = \"true\"";
						//print info
						print("User Name: $username<br />");
						print("Phone: <input name=\"phone\" type=\"text\" size\"12\" value=\"$result->phone\"></input><br />");
						print("Cell Carrier: <select name=\"carrier\">
					  <option value=\"1\" $carrier_ind[1]>Verizon</option>
					  <option value=\"2\" $carrier_ind[2]>AT&#038;T</option>
					  <option value=\"3\" $carrier_ind[3]>T-Mobile</option>
					  <option value=\"4\" $carrier_ind[4]>Sprint PCS</option>
					  <option value=\"5\" $carrier_ind[5]>Virgin Mobile</option>
					  <option value=\"6\" $carrier_ind[6]>US Cellular</option>
					  <option value=\"7\" $carrier_ind[7]>Nextel</option>
					  <option value=\"8\" $carrier_ind[8]>Boost</option>
					  <option value=\"9\" $carrier_ind[9]>Alltel</option>
					  <option value=\"10\" $carrier_ind[10]>MetroPCS</option>
					</select>");
					?>
					
					</div>
					<br />
					<input type="submit" value="Update"></input>
					</FORM>
			</div>
		</div>	
		<?php include'footer.html';?>
	</body>
</html>