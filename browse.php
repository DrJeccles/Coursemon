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
		<title>CourseMon - Browse</title>
		<meta name="description" content="View and edit your watch-list of courses." />
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
				<p id="list-title">Watchlist</p>
				<table id="watchlist" class="watchlist">
					<tr>
						<th>Alert</th>
						<th>CRN</th>
						<th>Semester</th>
						<th>Name</th>
						<th>Capacity</th>
						<th>Actual</th>
						<th>Remaining</th>
					</tr>
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
						
						//get class data
						$query = "SELECT * FROM course WHERE user = '$username'";
						$result = $mysqli->query($query);
						while($obj = $result->fetch_object())
						{
							print("<tr>");
							print("<td>");
							if($obj->alert)
								print("Yes");
							else
								print("No");
							print("</td><td>");
							print($obj->crn);
							print("</td><td>");
							if($obj->sem == 0)
								print("Fall");
							elseif($obj->sem == 1)
								print("Summer");
							print("</td><td>");
							print($obj->title);
							print("</td><td>");
							print($obj->cap);
							print("</td><td>");
							print($obj->act);
							print("</td><td>");
							print($obj->rem);
							print("</td><td>");
							print("<form action=\"php/delete.php\" method=\"POST\"><input type=\"submit\" name=\"Delete\" value=\"delete\" /><input type=\"hidden\" name=\"ind\" value=\"$obj->ind\" /></form>");
							print("</td>");
							print("</tr>");
						}				
					?>
				</table>
				<div class="cont">
				<p id="add_title">Add Course Watch</p>
				<form action="php/add.php" method="post">
					<label for="crn">CRN:</label>
					<input id="crn" type="text" name="crn" />
					<br />
					<label for="sem">Semester:</label>
					<select name="sem">
					  <option value="0">Fall 2014</option>
					  <option value="1">Summer 2014</option>
					</select>
					<br />
					<label for="title">Display Name:</label>
					<input id="title" type="text" name="title" />
					<br />
					<label for="alert">Text Alert:</label>
					<input type="hidden" name="alert" value="0" />
					<input type="checkbox" name="alert" value="1" />
					<input type="hidden" name="username" value="<?php echo($username);?>" />
				  <p id="add_butt">
					<input type="submit" name="Submit" value="submit" />
				  </p>
				</form>
			</div>
		</div>	
		<?php include'footer.html';?>
	</body>
</html>