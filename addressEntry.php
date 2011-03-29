<?php
  session_start();
  include('models/sqldb.php');
  include('controllers/utility.php');
  include('models/visit.php');
  include('models/client.php');
  include('models/house.php');
  
  echo "<PRE>";
  var_dump($_SESSION);
  echo "</PRE>";

  if (isset($_GET['clean']))
  {
    $_SESSION = NULL;
    session_destroy();
  }
  
  $_SESSION['fromConfirm'] = NULL;
  if (!empty($_GET['client']))
  {
    $_SESSION['edit'] = TRUE;
    $client = Client::getClientByID($_GET['client']);
    if ($client === NULL)
    {
      session_destroy();
      header("Location: search.php?error=1");
    }
    else
    {
      if ($client->getHouseID() !== NULL)
      {
        $house = House::getHouseByID($client->getHouseID());
        if ($house === NULL)
        {
          session_destroy();
          header("Location: search.php?error=1");
        }
        
        $_SESSION['clientID'] = $client->getClientID();
        if ($house)
        {
          $_SESSION['houseID'] = $house->getHouseID();
          $_SESSION['streetNumber'] = $house->getStreetNumber();
          $_SESSION['streetName'] = $house->getStreetName();
          $_SESSION['streetType'] = $house->getStreetType();
          $_SESSION['city'] = $house->getCity();
          $_SESSION['zip'] = $house->getZip();
        }
        else
        {
          $_SESSION['houseID'] = NULL;
          $_SESSION['streetNumber'] = NULL;
          $_SESSION['streetName'] = NULL;
          $_SESSION['streetType'] = NULL;
          $_SESSION['city'] = NULL;
          $_SESSION['zip'] = NULL;
        }
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
	<title>Bryant Food Distribution Client Address Entry Screen</title>
</head>
	<body>
		<div id="header">
    <?php 
      if (isset($_SESSION['edit']))
      {
        echo "<h1>Edit Client Address</h1>
        <h2>Enter the information to change this client's address</h2>";
      }
      else
      {
        echo " <h1>Add a New Client</h1>
        <h2>Enter the information for a new client's address</h2>";
      }
      ?>
			<hr/>
			<ul>
				<li><a href="selectTask.php">Select a Task</a></li>
				<li><a href="search.php">Search for a Client</a></li>
        <?php
          if (isset($_SESSION['edit']))
          {
            echo '<li><a href="addressEntry.php?clean=1">Add a new client</a></li>';
          }
        ?>
			</ul>
		</div><!-- /header -->
    
    <?php
      if (!empty($_SESSION['errors']))
      {
        echo "\n<div id='newClient'>\n\t<ul>\n";
        foreach($_SESSION['errors'] as $error)
        {
          echo "\t\t<li>" . $error . "</li>\n";
        }
        echo "\t</ul>\n</div>";
        $_SESSION['errors'] = NULL;
      }
        
      if ($_SESSION['haveSearched'] === TRUE)
      {
        echo "<div id='newClient'>\n\t<p>Select the address you would like for this client.</p>\n";
        echo "<form method='post' action='clientEntry.php'>\n<table>\n";
        foreach($_SESSION['matches'] as $match)
        {
          echo '<tr>
                  <td>' . $match["streetNumber"] . " " . $match["streetName"] . " " . $match["streetType"] . " "
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
        echo '<tr>
        <td>Use what was entered: ' . $_SESSION['streetNumber'] . ' ' . $_SESSION['streetName'] . ' ' . $_SESSION['streetType'] . ' '
        . $_SESSION['city'] . ' ' . $_SESSION['zip'] . '</td>
        <td><input name="houseID" type="radio" value="new" ';
        if ($_SESSION['houseID'] === "new")
        {
          echo "checked";
        }
        echo '/></td>
        </tr>
        <tr>
          <td><input type="submit" value="Continue" /></td>
        </tr>
        <tr>
          <td><a href="addressEntry.php?clean=1">Go back</a>
        </tr>
        </table>
        </form>
        </div>';
      }
      else
      {
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
      echo '<tr>
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