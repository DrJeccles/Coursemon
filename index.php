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
	
	if(isset($_SESSION['username']))
	{
		header("location:browse.php");
		exit();
	}
?>
<html>
	<head>
		<title>Welcome to CourseMon</title>
		<meta name="description" content="CourseMon alows you to create a watch-list of Georgia Tech courses and can alert you when a seat becomes available!" />
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
		<img src="img/logo2.png" style="display:none"/>
		<?php include'header.php';?>
		<div class="container">
			<p class="heading">Welcome to CourseMon</p>
			<p class="body">CourseMon allows to you set up a customized course watch list to keep track of how many seats are 
				open in each class.  You can also have CourseMon send you a text message to alert you if a seat
				opens up in a course.</p>
			<p class="heading2">How it Works</p>
			<p class="body">Once you log in, you can add a course to your watchlist by its CRN number. CourseMon will 
							keep track of your courses and update the number of seats available every three minutes. 
							If you have an alert set for a particular course and there are seats available, CourseMon 
							will text you an alert at the phone number you provided when registering (message and data 
							rates may apply).  If the number of seats goes back down to 0, CourseMon will alert you again
							when seats become available if the course is still in your watchlist.</p>
			<br />
			<p class="body center"><a href="register.php">Register</a> an account and get started!</p>
			<p class="body center">Existing User? Log in below!</p>
				<div class="cont">
				<form action="php/login.php" method="post">
			  <p>
				<label for="user">User Name:</label>
				<input id="user" type="text" name="user" />
			  </p>
			  <p>
				<label for="password">Password:</label>
				<input id="password" type="password" name="password" />
			  </p>
			  <p>
				<input type="submit" name="login" value="Login" />
			  </p>
			</form>
			</div>
		</div>
		<?php include'footer.html';?>
	</body>
</html>