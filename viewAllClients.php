<?php 
	session_start();
	include ('controllers/utility.php');
	include ('models/sqldb.php');
	include ('models/administrator.php');
	include ('models/report.php');
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
		<?php showHeader("Bryant Community Center", "List of Clients", "All registered clients including last visit date", TRUE); ?>
		</div>
		<?php showErrors(); ?>	
		<div>
			<?php echo "<pre>"; Report::showAllClients(); echo "</pre>"; ?>
		</div>
	</body>
</html>

