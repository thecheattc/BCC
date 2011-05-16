<?php
	session_start();
  include ('models/sqldb.php');
  include ('models/visit.php');
	include ('models/administrator.php');
  include ('models/familyMember.php');
  include ('models/client.php');
  include ('models/house.php');
  include ('controllers/utility.php');
	
	if (!hasAccess())
	{
		$_SESSION['errors'] = array();
		$_SESSION['errors'][] = "This operation requires administrative privileges.";
		header("Location: ./");
		exit();
	}
	resetTimeout();
	$adminID = $_SESSION['adminID'];
	$timeout = $_SESSION['timeout'];
	if (isset($_GET['clean']))
  {
    $_SESSION = array();
		$_SESSION['adminID'] = $adminID;
		$_SESSION['timeout'] = $timeout;
  }
	
  $first = isset($_POST['first'])? $_POST['first'] : '';
  $last = isset($_POST['last'])? $_POST['last'] : '';
  $street = isset($_POST['street'])? $_POST['street'] : '';
  $search = FALSE;
  $clients = NULL;
  $houses = NULL;
  
  if(!empty($first) || !empty($last) || !empty($street))
  {
    $search = TRUE;
  }
  
  if ($search)
  {
    $clients = Client::searchByNameAndStreet($first, $last, $street);
    
    $houses = array();
    foreach ($clients as $client)
    {
      if ($client->getHouseID() !== NULL)
      {
        $houses[] = House::getHouseByID($client->getHouseID());
      }
      else
      {
        $houses[] = NULL;
      }
    }
  }
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Client Search</title>
  <link rel="stylesheet" href="style/bryant.css" type="text/css" media="all" />
  <script src="scripts/js/jquery-1.4.1.min.js"></script>
  <script src="scripts/js/jquery.simplemodal-1.4.1.js" type="text/javascript" language="javascript" charset="utf-8"></script>
  <script type="text/javascript" src="scripts/js/jquery-ui-1.8.10.custom/js/jquery-ui-1.8.10.custom.min.js"></script>
</head>
<body>
	<div id="header">
		<?php 
			if ($search === FALSE)
			{
				$heading = "Search for a Client";
				$subheading = "Search by first name, last name, and/or street address";
			}
			else if (!empty($clients))
			{
				$heading = "Search Results";
				$subheading = "";
			}
			else
			{
				$heading = "No results were found";
				$subheading = "";
			}
			showHeader("BCC Search", $heading, $subheading); 
			?>
	</div>
<?php
	showErrors();
	
	if($search == false){
		echo '
		<div id="newClient" class="search">
				<form method="post" action="search.php">
				<fieldset>
					<legend>Search for a client</legend>
					<table>
						<tr>
							<td><label for="first">First name:</label></td>
							<td><input type="text" name="first" value="'.$first.'" /></td>
						</tr>
						<tr>
							<td><label for="last">Last name: </label></td>
							<td><input type="text" size="60" name="last" value="'.$last.'" /></td>
						</tr>
						<tr>
							<td><label for="street">Street address: </label></td>
							<td><input name="street" type="text" size="60" value="'.$street.'" /></td>
						</tr>
						<tr>
							<td><input type="submit" name="searchsubmit" id="searchsubmit" value="Search" /></td>
						</tr>
						<tr>
							<td><br /><a href="addressEntry.php?clean=1">Add a client</a></td>
						</tr>
					</table>
				</fieldset>
				</form>
			</div>';
	}
  else
  {
    if (!empty($clients))
    {
      echo '
        <div id="newClient" class="search">';
      
      echo "<table id='resTable'>\n";
      for ($i=0; $i<count($clients); $i++)
      {
        echo "\t<tr>\n\t\t<td>\n";
        echo "\t<span>{$clients[$i]->getFirstName()} {$clients[$i]->getLastName()}</span></td>";
        if($houses[$i] != NULL)
        {
          echo "<td><span>{$houses[$i]->getStreetNumber()} {$houses[$i]->getStreetName()} 
          {$houses[$i]->getStreetType()} {$houses[$i]->getCity()} {$houses[$i]->getZip()}</span></td>";
        }
				$tempDate = mySQLDateToNormal($clients[$i]->getApplicationDate());
        echo "<td><span>Registered {$tempDate} </span></td>";
        echo "<td><a href='viewHistory.php?client={$clients[$i]->getClientID()}'>View visit history</a></td>";
        echo "<td><a href='addressEntry.php?client={$clients[$i]->getClientID()}'>Edit client information</a></td>";
				echo "<td><a href='controllers/deleteClient.php?client={$clients[$i]->getClientID()}' " ;
				echo "onClick=" . '"' . "return confirm('Are you sure you want to delete this client? ";
				echo "Doing so will remove all information related to the client from the database.')" . '">Delete this client</a>';
        echo "</tr>";
      }
      echo "</table><br/>";
    }
	}
?>
</body>
</html>


