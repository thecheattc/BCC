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
	
	//Set the houseID so the controller will know how to handle it.
  //Only do this when coming from the addressEntry page
  if (isset($_POST['fromAddress']) && !isset($_POST['houseID']))
  {
		$_SESSION['errors'] = array();
    $_SESSION['errors'][] = "Please select an address from the list.";
    header("Location: addressEntry.php");
    exit();
  }
	
	if (isset($_POST['houseID']))
  {
    $_SESSION['houseID'] = $_POST['houseID'];
		//Since they've changed information that would affect who their family members are,
		//ensure we search for them when we come to the client entry page.
		$_SESSION['modifyFamily'] = FALSE;
  }
	
	$adminID = $_SESSION['adminID'];
	$timeout = $_SESSION['timeout'];
  if (isset($_GET['noSpouseSearch']))
  {
    $_SESSION['haveSearchedSpouse'] = NULL;
  }
  
  //If it's an edit and they haven't searched yet, prepopulate the form fields.
  if (!empty($_SESSION['edit']) && empty($_SESSION['haveSearchedSpouse']))
  {
    $client = Client::getClientByID($_SESSION['clientID']);
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
    $spouse = $client->getSpouseAsClient();
		$_SESSION['spouseFirst'] = '';
		$_SESSION['spouseLast'] = '';
		if (!empty($spouse))
		{
			$_SESSION['spouseFirst'] = $spouse->getFirstName();
			$_SESSION['spouseLast'] = $spouse->getLastName();
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
			showHeader("BCC Client Spouse Entry", "Find Client's Spouse Information", "");
      ?>
		</div>
    
    <?php
      if ($_SESSION['haveSearchedSpouse'] === TRUE)
      {
				showClientEntrySteps(4);
				showErrors();
        echo "<div style=\"float: left\">\n\t<p>Select the spouse you would like for this client.</p>\n";
        echo "<form method='post' action='clientEntry.php'>\n<table>\n";
        foreach($_SESSION['spouseMatches'] as $spouseMatch)
        {
          echo '<tr>
                  <td>' . $spouseMatch["spouseFirst"] . " " . $spouseMatch["spouseLast"] . ", " . 
												$spouseMatch["age"] . ", " . $spouseMatch["phoneNumber"] . '</td>
                  <td><input name="spouseID" type="radio" value="' . $spouseMatch["spouseID"] . '"';
          if ($_SESSION['houseID'] == $spouseMatch["spouseID"])
          {
            echo "checked";
          }
          echo '/></td>
                </tr>';
          echo "\n";
        }
				echo "<td>No spouse; client is single or no spouse has registered with BCC yet</td>";
        echo '<td><input name="spouseID" type="radio" value="single" ';
        if ($_SESSION['spouseID'] === "single")
        {
          echo "checked";
        }
        echo '/></td>';
        echo '
        </tr>
        <tr>
          <td><input type="hidden" name="fromSpouse" value="yup" /></td>
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
				showClientEntrySteps(3);
				showErrors();
        echo ' 
        <div id="searchSpouse">
          <p>Leave all fields blank if the client is single or no spouse has registered with BCC yet.</p>
          <form method="post" action="controllers/processSpouse.php">
          <fieldset>
          <table>
            <tr>
              <td><label for="spouseFirst">First name of spouse: </label></td>
              <td><input name="spouseFirst" type="text" size="80" value="' . $_SESSION['spouseFirst'] . '"/></td>
            </tr>
            <tr>
							<td><label for="streetName">Last name of spouse: </label></td>
              <td><input name="spouseLast" type="text" size="80" value="' .$_SESSION['spouseLast'] . '"/></td>
						</tr>
            <tr>
              <td><input type="submit" name="clientSub" id="clientSub" value="Continue"/></td>
            </tr>
          </table>
          </fieldset>
          </form>
        </div>';
      }
  ?>
</body>
</html>