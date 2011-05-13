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
	$administrators = Administrator::getAllAdministrators();
	$accessLevels = Administrator::getAllAccessLevels();
	?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<link rel="stylesheet" href="style/bryant.css" type="text/css"/>
<script type="text/javascript" 
			src="scripts/js/jquery-1.4.4.min.js"></script>
<head>
<title>BCC Administration</title>
</head>
	<body>
<?php
	if (!empty($_SESSION['errors']))
	{
		echo "<div class='errors'>
		<ul>\n";
		foreach($_SESSION['errors'] as $error)
		{
			echo "<li>" . $error . "</li>\n";
		}
		echo "\t</ul>\n</div>";
		$_SESSION['errors'] = NULL;
	}
	?>
		<div><!-- Admin list-->
			<h3>Current administrators:</h3>
			<table>
				<tr>
					<th>Username</th>
					<th>Access level</th>
				</tr>
			<?php
				foreach ($administrators as $admin)
				{
					echo "<tr>
									<td>{$admin->getUsername()}</td>
									<td>{$admin->getAccessLevel()}</td>
									<td><a href=\"editAdmin.php?id={$admin->getAdminID()}\">Edit</td>
									<td><a href=\"controllers/deleteAdmin.php?id={$admin->getAdminID()}\">Delete</td>
								</tr>\n";
				}
				?>
			</table>
		</div>
		<h3>Create a new administrator:</h3>
		<div>
			<form id="newAdmin" method="post" action="controllers/createAdmin.php">
				<label for="adminUsername">Username:</label><input type="text" name="adminUsername" value="<?php echo isset($_SESSION['adminUsername'])? $_SESSION['adminUsername'] : ''; ?>"  /><br />
				<label for="adminPass1">Password:</label><input type="password" name="adminPass1" /><br />
				<label for="adminPass2">Confirm password:</label><input type="password" name="adminPass2" /><br />
				<label for="accessID">Access Level:</label><select name="accessID" />
					<?php
						for ($i=0; $i<count($accessLevels); $i++)
						{
							echo "<option value=\"{$accessLevels[$i]['id']}\" ";
							if ((isset($_SESSION['accessID']) && $accessLevels[$i]['id'] == $_SESSION['accessID']) || 
									(!isset($_SESSION['accessID']) && $i == count($accessLevels) - 1 ))
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

