<?php
  session_start();
  include('models/sqldb.php');
	include('models/administrator.php');
  include('controllers/utility.php');
  include('models/visit.php');
  include('models/client.php');
  include('models/familyMember.php');
  include('models/house.php');
	
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
  if (isset($_GET['nosearch']))
  {
    $_SESSION['haveSearched'] = NULL;
  }
  
  //If it's an edit and they haven't searched yet, prepopulate the form fields.
  if ((!empty($_GET['client']) || !empty($_SESSION['edit'])) && empty($_SESSION['haveSearched']))
  {
    $client = NULL;
    if (!empty($_SESSION['edit']))
    {
      $client = Client::getClientByID($_SESSION['clientID']);
    }
    else
    {
      $_SESSION['edit'] = TRUE;
      $client = Client::getClientByID($_GET['client']);
    }
    if ($client === NULL)
    {
			$_SESSION = array();
			$_SESSION['adminID'] = $adminID;
			$_SESSION['timeout'] = $timeout;
			$_SESSION['errors'] = array();
			$_SESSION['errors'][] = "The requested client could not be found.";
      header("Location: search.php?clean=1");
      exit();
    }
    
    $_SESSION['clientID'] = $client->getClientID();
    $_SESSION['houseID'] = NULL;
    $_SESSION['streetNumber'] = NULL;
    $_SESSION['streetName'] = NULL;
    $_SESSION['streetType'] = NULL;
    $_SESSION['line2'] = NULL;
    $_SESSION['city'] = NULL;
    $_SESSION['zip'] = NULL;
    
    if ($client->getHouseID() !== NULL)
    {
      $house = House::getHouseByID($client->getHouseID());
      if ($house)
      {
        $_SESSION['houseID'] = $house->getHouseID();
        $_SESSION['streetNumber'] = $house->getStreetNumber();
        $_SESSION['streetName'] = $house->getStreetName();
        $_SESSION['streetType'] = $house->getStreetType();
        $_SESSION['line2'] = $house->getLine2();
        $_SESSION['city'] = $house->getCity();
        $_SESSION['zip'] = $house->getZip();
      }
      else
      {
				$_SESSION = array();
				$_SESSION['adminID'] = $adminID;
				$_SESSION['timeout'] = $timeout;
				$_SESSION['errors'] = array();
				$_SESSION['errors'][] = "The requested client could not be found.";
        header("Location: search.php?clean=1");
        exit();
      }
    }
  }
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta name="original-source" content="http://commons.wikimedia.org/wiki/File:CampbellsModif.png">
	<meta name="original-source" content="http://upload.wikimedia.org/wikipedia/commons/a/a4/Old_Woman_in_Suzdal_-_Russia.JPG">
	<link rel="stylesheet" href="style/bryant.css" type="text/css"/>
	<link type="text/css" href="scripts/js/jquery-ui-1.8.10.custom/css/ui-lightness/jquery-ui-1.8.10.custom.css" rel="Stylesheet" />
</head>
	<body>
		<div id="header">
    <?php 
      if (isset($_SESSION['edit']))
      {
				$heading = "Edit Client Address";
				$subheading = "Enter the information to edit this client's address";
      }
      else
      {
				$heading = "Add a New Client";
				$subheading = "Enter the information for a new client's address";
      }
			showHeader("BCC Client Address Entry", $heading, $subheading);
      ?>
		</div>
    
    <?php
      showErrors();
        
      if ($_SESSION['haveSearched'] === TRUE)
      {
				showClientEntrySteps(2);
        echo "<div>\n\t<p>Select the address you would like for this client.</p>\n";
        echo "<form method='post' action='clientEntry.php'>\n<table>\n";
        foreach($_SESSION['matches'] as $match)
        {
          echo '<tr>
                  <td>' . $match["streetNumber"] . " " . $match["streetName"] . " " . $match["streetType"] . " " . $match["line2"] . " "
                        . $match ["city"] . " " . $match["zip"] . '</td>
                  <td><input name="houseID" type="radio" value="' . $match["houseID"] . '"';
          if ($_SESSION['houseID'] == $match["houseID"])
          {
            echo "checked";
          }
          echo '/></td>
                </tr>';
          echo "\n";
        }
				$homeless =  ((trim($_SESSION['streetNumber']) . trim($_SESSION['streetName']) . trim($_SESSION['streetType']) . 
				trim($_SESSION['line2']) . trim($_SESSION['city']) . trim($_SESSION['zip'])) == '');
				echo '<tr>
				<td>';
				if ($homeless)
				{
					echo "No address; client is homeless</td>";
				}
				else
				{
					echo "Create a new entry using what was entered: " . $_SESSION['streetNumber'] . ' ' . $_SESSION['streetName'] . ' ' . $_SESSION['streetType'] . ' '
					. $_SESSION['line2'] . ' ' . $_SESSION['city'] . ' ' . $_SESSION['zip'] . '</td>' ;
				}
        echo '<td><input name="houseID" type="radio" value="new" ';
        if ($_SESSION['houseID'] === "new")
        {
          echo "checked";
        }
        echo '/></td>';
        echo '
        </tr>
        <tr>
          <td><input type="hidden" name="fromAddress" value="yup" /></td>
        </tr>
        <tr>
					<td><input type="submit" value="Continue" /></td>
        </tr>
        </table>
        </form>
        </div>';
      }
      else
      {
				showClientEntrySteps(1);
        echo ' 
        <div id="newClient">
          <p>Leave all fields blank if the client is homeless.</p>
          <form method="post" action="controllers/processAddress.php">
          <fieldset>
          <table>
            <tr>
              <td><label for="streetNumber">Street number (e.g. N151W23, 811, etc.): </label></td>
              <td><input name="streetNumber" type="text" size="80" value="' . $_SESSION['streetNumber'] . '"/></td>
            </tr>
            <tr>
              <td><label for="streetName">Street name: </label></td>
              <td><input name="streetName" type="text" size="80" value="' .$_SESSION['streetName'] . '"/></td>
            </tr>
            <tr>
              <td><label for="streetType">Street type (ave., dr., etc.): </label></td>
              <td><input name="streetType" type="text" size="80" value="' . $_SESSION['streetType'] . '"/></td>
            </tr>
            <tr>
              <td><label for="line2">Line 2 (apartment #, etc.): </label></td>
              <td><input name="line2" type="text" size="80" value="' . $_SESSION['line2'] . '"/></td>
            </tr>
            <tr>
              <td><label for="city">City: </label></td>
              <td><input name="city" type="text" size="50" value="' . $_SESSION['city'] . '"/></td>
            </tr>
            <tr>
              <td><label for="zip">Zip: </label></td>
              <td><input name="zip" type="text" size="11" maxlength="11" value="' . $_SESSION['zip']. '" /></td>
            </tr>';
        if($_SESSION['edit'])
        {
          echo "\n\t<tr>\n\t\t<td><label for='oldAddressValid'>If your address has changed, ";
          echo "are there still people registered with Bryant at the old address?</label></td>\n";
          echo "\t\t<td>No <input name='oldAddressValid' id='oldAddressValid' type='radio' value='0' ";
          if (isset($_SESSION['oldAddressValid']) && $_SESSION['oldAddressValid'] == 0){ echo "checked"; }
          echo "/> Yes <input name='oldAddressValid' id='oldAddressValid' type='radio' value='1' ";
          if (isset($_SESSION['oldAddressValid']) && $_SESSION['oldAddressValid'] == 1){ echo "checked"; }
          echo "/></td>\n\t</tr>";
        }
      echo '
            <tr>
              <td><input type="submit" name="clientSub" id="clientSub" value="Continue"/></td>
            </tr>
          </table>
          </fieldset>
          </form>
        </div><!-- /newClient -->';
      }
  ?>
</body>
</html>