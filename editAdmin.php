<?php 
	session_start();
	include ('controllers/utility.php');
	include ('models/sqldb.php');
	include ('models/administrator.php');
	
	if (!hasAccess(TRUE))
	{
		$_SESSION['errors'] = array();
		$_SESSION['errors'][] = "This operation requires administrative privileges";
		header("Location: ./");
		exit();
	}
	resetTimeout();
	if (!isset($_GET['id']))
	{
		$_SESSION['errors'] = array();
		$_SESSION['errors'][] = "No administrator selected.";
		header("Location: ./manageAdmins.php");
		exit();
	}
	$admin = Administrator::getAdminByID($_GET['id']);
	if (empty($admin))
	{
		$_SESSION['errors'] = array();
		$_SESSION['errors'][] = "The requested administrator was not found.";
		header("Location: ./manageAdmins.php");
		exit();
	}
	$accessLevels = Administrator::getAllAccessLevels();
	$accessLevelID = (empty($_SESSION['eaccessID']))? $admin->getAccessID() : $_SESSION['eaccessID'];
	?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<link rel="stylesheet" href="style/bryant.css" type="text/css"/>
<script type="text/javascript" src="scripts/js/jquery-1.4.4.min.js"></script>
<head>
</head>
	<body>
		<div id="header">
			<?php showHeader("BCC Administration", "Edit {$admin->getUsername()}'s account", "", TRUE); ?>
		</div>
		<?php showErrors();	?>
		<div>
			<form id="editAdmin" method="post" action="controllers/editAdmin.php?id=<?php echo $admin->getAdminID(); ?>">
				<label for="eadminPass1">Password:</label><input type="password" name="eadminPass1" /><br />
				<label for="adminPass2">Confirm password:</label><input type="password" name="eadminPass2" /><br />
				<label for="eaccessID">Access Level:</label><select name="eaccessID" />
					<?php
						for ($i=0; $i<count($accessLevels); $i++)
						{
							echo "<option value=\"{$accessLevels[$i]['id']}\" ";
							if ($accessLevels[$i]['id'] == $accessLevelID)
							{
								echo "selected ";
							}
							echo "/>{$accessLevels[$i]['name']}</option>\n";
						}
						?>
				</select><br />
				<input type="submit" value="Submit" />
			</form>
		</div>
	</body>
</html>

