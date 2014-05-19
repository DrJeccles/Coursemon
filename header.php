<div class="header">
	<div class="user">
		<?php if(isset($_SESSION['username']))
		{echo("Welcome $username!<br />
		<a href=\"account.php\">Account</a> | <a href=\"php/logout.php\">logout</a>");}?>
	</div>
	<a href="browse.php"><img src="img/logo.png" alt="CourseMon Home" class="logo" /></a>
</div>