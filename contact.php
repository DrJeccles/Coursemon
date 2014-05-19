<html>
	<head>
		<title>CourseMon - Contact</title>
		<meta name="description" content="Have something to say? Use this page to tell me what's on your mind!" />
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
			<p class="heading">Have Something to Say?</p>
			<p class="body">Use the contact form below to tell me what's on your mind!</p>
			<br />
			<p class="heading2 center">Contact</p>
			<div class="cont" style="min-width: 320px">
				<FORM action="php/contact.php" METHOD=POST>
					<div class="left">
					From: <input name="name" type="text" size"20"></input><br />
					Email (so I can reply): <input name="email" type="text" size"20"></input><br />
					Message: <textarea name="message" cols="35" rows="5"></textarea><br />
					<?php
					  require_once('securimage/securimage.php');
					  $publickey = "6LdH6c8SAAAAAED3bhxMhaZlPs_okDt87Ytq67-W"; // you got this from the signup page
					  echo Securimage::getCaptchaHtml();
					?>
					</div>
					<br />
				<input type="submit" value="Submit!"></input>
				</FORM>
			</div>
		</div>
		<?php include'footer.html';?>
	</body>
</html>