<?php 
	define("ROOT_ACCESS_ID", 1);
	session_start();
	include ('controllers/utility.php');
	include ('models/sqldb.php');
	include ('models/administrator.php');
	$admin = NULL;
	$loggedIn = FALSE;
	if (isset($_SESSION['adminID']))
	{
		$admin = Administrator::getAdminByID($_SESSION['adminID']);
		if (empty($admin))
		{
			header("Location: logout.php");
			exit();
		}
		$loggedIn = TRUE;
	}
	resetTimeout();
	?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<meta name="original-source" content="http://commons.wikimedia.org/wiki/File:CampbellsModif.png">
<meta name="original-source" content="http://upload.wikimedia.org/wikipedia/commons/a/a4/Old_Woman_in_Suzdal_-_Russia.JPG">
<link rel="stylesheet" href="style/bryant.css" type="text/css"/>
<script type="text/javascript" 
			src="scripts/js/jquery-1.4.4.min.js"></script>
<head>
<title>Bryant Community Center</title>
</head>
	<body>
		<div id="header">
		<a href="./"><img src="/style/images/orangeCANsmall.jpg" /></a>
		<?php
			if ($loggedIn)
			{
echo <<<LOGGED_IN_AS1
					<h1>Bryant Food Distribution Task Selection</h1>
					<h2>Choose which task to manage</h2>
					<p>Logged in as: {$admin->getUsername()}</p>
					<a href="logout.php" class="compact-button default">Logout</a>
					<hr/>
				</div>
LOGGED_IN_AS1;
				showErrors();
echo <<<LOGGED_IN_AS2
				</div>
				<div id="taskWrap">
					<div class="task">
						<a href="addressEntry.php?clean=1">
						<h3>Enter New Client Information</h3>
						<img src="style/images/addClient.jpg" alt="Click this area to add new client information" />
						<p>Click here to add a new client to the database</p></a>
					</div>
					<div class="task">
						<a href="search.php?clean=1">
						<h3>Manage Food Distribution</h3>
						<img src="style/images/foodDist.png" alt="Click this area to check the date of a client's last visit" />
					<p>Click here to check for a client's last distribution.</p></a><!--'-->
					</div><!-- /.task -->
LOGGED_IN_AS2;
				if ($admin->getAccessID() == ROOT_ACCESS_ID)
				{
echo <<<LOGGED_IN_AS3
					<div class="task">
						<a href="administration.php">
						<h3>Administrative business</h3>
						<p>Manage administrator accounts, form options, and monthly reports.</p></a>
					</div>
LOGGED_IN_AS3;
				}
					echo "</div><!-- /.taskWrap -->	";	
			}
			else
			{
				echo "
				<h1>Bryant Community Center Login</h1>
				<hr/></div>";
				$attemptedUser = isset($_SESSION['attemptedUser'])? $_SESSION['attemptedUser'] : '';
				showErrors();
echo <<<LOGIN_FORM
				<form action="controllers/login.php" method="post">
					<label for="bccUser">Username:</label><input type="text" id="bccUser" name="bccUser" value="{$attemptedUser}" /><br />
					<label for="bccPassword">Password:</label><input type="password" id="bccPassword" name="bccPassword" /><br />
					<input type="submit" name="login" value="Login" />
				</form>
						
LOGIN_FORM;
			}
			?>
			</body>
</html>

