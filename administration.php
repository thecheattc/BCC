<?php 
	session_start();
	include ('controllers/utility.php');
	include ('models/sqldb.php');
	include ('models/administrator.php');
	$admin = NULL;
	$loggedIn = FALSE;
	if (!hasAccess(TRUE))
	{
		$_SESSION['errors'] = array();
		$_SESSION['errors'][] = "This operation requires administrative privileges";
		header("Location: ./");
		exit();
	}
	resetTimeout();
	?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<link rel="stylesheet" href="style/bryant.css" type="text/css"/>
<script type="text/javascript" 
			src="scripts/js/jquery-1.4.4.min.js"></script>
<head>
</head>
	<body>
		<div id="header">
		<?php showHeader("Bryant Community Center", "Food Distribution Administration", "", TRUE); ?>
		</div>
		<?php showErrors(); ?>	
		<div>
			<ul>
				<li><a href="manageAdmins.php">Manage administrators</a></li>
				<li><a href="report.php">View monthly report</a></li>
				<li><a href="viewAllClients.php">View all clients</a></li>
				<li><a href="editForms.php">Edit form options</a></li>
			</ul>
		</div>
	</body>
</html>

