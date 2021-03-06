<?php
	session_start();
	include ('models/visit.php');
  include ('models/client.php');
	include ('models/administrator.php');
  include ('models/familyMember.php');
  include ('models/sqldb.php');
  include ('controllers/utility.php');

	if (!hasAccess())
	{
		$_SESSION['errors'] = array();
		$_SESSION['errors'][] = "This operation requires administrative privileges.";
		header("Location: ./");
		exit();
	}
	resetTimeout();
  
  if (empty($_GET['visit']))
  {
		$_SESSION['errors'] = array();
		$_SESSION['errors'][] = "No visit requested.";
    header("Location: search.php?clean=1");
		exit();
  }
	$visit = Visit::getVisitByID($_GET['visit']);
	$client = NULL;
	if ($visit !== NULL)
	{
		$client = Client::getClientByID($visit->getClientID());
	}
	if ($visit === NULL || $client === NULL)
	{
		$_SESSION['errors'] = array();
		$_SESSION['errors'][] = "There was an error retrieving the requested visit.";
		header("Location: search.php?clean=1");
	}
	$types = Visit::getAllDistTypes();
	$locations = Visit::getAllLocations();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" href="style/bryant.css" type="text/css" media="all" />
	<link type="text/css" href="scripts/js/jquery-ui-1.8.10.custom/css/ui-lightness/jquery-ui-1.8.10.custom.css" rel="Stylesheet" />
	<script type="text/javascript" src="scripts/js/jquery-1.4.4.min.js"></script>
	<script src="scripts/js/jquery.simplemodal-1.4.1.js" type="text/javascript" language="javascript" charset="utf-8"></script>
	<script type="text/javascript" src="scripts/js/jquery-ui-1.8.10.custom/js/jquery-ui-1.8.10.custom.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$('#date').datepicker({ dateFormat: 'mm-dd-yy' });
		});
	</script>
</head>
<body>
  <div id="header">
		<?php showHeader("Edit Visit", "Edit {$client->getFirstName()} {$client->getLastName()}'s visit"); ?> 
  </div>
	<?php showErrors();	?>
<form method="post" action="controllers/processVisitEdit.php?visit=<?php echo $_GET['visit']; ?>&client=<?php echo $visit->getClientID(); ?>">
    <label for="date">Date: </label>
    <input name="date" id="date" value="<?php echo mySQLDateToNormal($visit->getDate()); ?>" /><br />
    <label for="type">Type: </label>
    <select id="type" name="type">
      <?php foreach ($types as $key => $value)
            {
              echo "\t\t";
              echo "<option ";
              if ($value == $visit->getDistTypeDesc())
              {
                echo "selected ";
              }
              echo "value=\"{$key}\">".$value."</option>";
              echo "\n";
            }
      ?>
    </select><br />
		<label for="note">Note for the distribution (optional):</label><br />
		<textarea name="note"><?php echo $visit->getNote(); ?></textarea><br />
		<label for="location">Type: </label>
		<select id="location" name="location">
		<?php foreach ($locations as $key => $value)
			{
				echo "\t\t";
				echo "<option ";
				if ($value == $visit->getLocationName())
				{
					echo "selected ";
				}
				echo "value=\"{$key}\">".$value."</option>";
				echo "\n";
			}
			?>
		</select><br />
    <input type="submit" value="Edit visit" />
  </form>
</body>
</html>
