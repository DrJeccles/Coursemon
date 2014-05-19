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
	//$username = $_SESSION['username'];
?>
<html>
	<head>
		<title>CourseMon - Register</title>
		<meta name="description" content="Register an account on CourseMon and get started today!" />
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
			<p class="heading center">Create an Account</p>
				<div class="cont" style="min-width: 320px">
				<FORM action="php/register.php" METHOD=POST>
					<?php
						//$err = $_GET['err'];
						//if($err)
							//print("<p class=\"error\">$err</p>");
					?>
					<div class="left">
					User Name: <input name="regname" type="text" size"20"></input><br />
					Password:<input name="regpass1" type="password" size"20"></input><br />
					Retype Password:<input name="regpass2" type="password" size"20"></input><br />
					Phone Number (In the form xxx-xxx-xxxx, only if you wish to recieve text alerts):<input name="phone" type="text" size"20"></input><br />
					Cell Carrier:
					<select name="carrier">
					  <option value="1">Verizon</option>
					  <option value="2">AT&#038;T</option>
					  <option value="3">T-Mobile</option>
					  <option value="4">Sprint PCS</option>
					  <option value="5">Virgin Mobile</option>
					  <option value="6">US Cellular</option>
					  <option value="7">Nextel</option>
					  <option value="8">Boost</option>
					  <option value="9">Alltel</option>
					  <option value="10">MetroPCS</option>
					</select>
					</div>
					<br />
					<?php
					  require_once('securimage/securimage.php');
					  $publickey = "6LdH6c8SAAAAAED3bhxMhaZlPs_okDt87Ytq67-W"; // you got this from the signup page
					  echo Securimage::getCaptchaHtml();
					?>
					<br />
					<input type="submit" value="Submit!"></input>
					</FORM>
			</div>
		</div>
		<?php include'footer.html';?>
	</body>
</html>