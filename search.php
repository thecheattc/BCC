<?php

  include('models/sqldb.php');
  include('models/visit.php');
  include('models/client.php');
  include('models/house.php');
  include('controllers/utility.php');
  
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
    $clients = Client::searchByNameAndStreet(processString($first), processString($last), processString($street));
    
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
	<?php
    if($search == false){
      echo '
        <div id="header">
          <h1> Search for a Client</h1>
          <h3>Search by first name, last name, and/or street address</h3>
          <hr />
          <ul>
          <li><a href="selectTask.php">Select a Task</a></li>
          <li><a href="dataEntry.php?clean=1">Add a new Client</a></li>
        </ul>
      </div>';
      if (isset($_GET['deleteError']))
      {
        echo "<h4>There was an error deleting the client.</h4>";
      }
      if (isset($_GET['deleteSuccess']))
      {
        echo "<h4>Client deletion successful.</h4>";
      }
      if (isset($_GET['deleteVisitError']))
      {
        echo "<h4>There was an error deleting the visit.</h4>";  
      }
      echo '<div id="newClient" class="search">
          
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
                <td><br /><a href="dataEntry.php">Add a client</a></td>
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
        <div id="header">
          <h1> Search Results</h1>
          <h2>Record a visit, edit client information, or search again</h2>
        <hr />
        <ul>
          <li><a href="selectTask.php">Select a Task</a></li>
          <li><a href="dataEntry.php">Add a new Client</a></li>
        </ul>
        </div>
        <div id="newClient" class="search">
          <h3>Your results:</h3>';
      
      echo "<table id='resTable'>\n";
      for ($i=0; $i<count($clients); $i++)
      {
        echo "\t<tr>\n\t\t<td>\n";
        echo "\t<span>{$clients[$i]->getFirstName()} {$clients[$i]->getLastName()}</span></td>";
        if($houses[$i] != NULL)
        {
          echo "<td><span>{$houses[$i]->getAddress()} {$houses[$i]->getCity()} {$houses[$i]->getZip()}</span></td>";
        }
        echo "<td><span>Registered {$clients[$i]->getApplicationDate()} </span></td>";
        echo "<td><a href='viewHistory.php?client={$clients[$i]->getClientID()}'>View visit history</a></td>";
        echo "<td><a href='dataEntry.php?client={$clients[$i]->getClientID()}'>Edit client information</a></td>";
        echo "</tr>";
      }
      echo "</table><br/>";
      echo '<form action="search.php"><input type="submit" name="searchAgain" value="Try another search" / >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="selectTask.php">Return to the task selection page</a>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <a href="dataEntry.php">Add a client</a>
      </form>';
      echo '</div>';
    }
    else
    {
  	echo '
  	<div id="header">
    	<h1> Search Results</h1>
    	<h2>Record a visit, edit client information, or search again</h2>
   		<hr />
   		<ul>
			<li><a href="selectTask.php">Select a Task</a></li>
			<li><a href="dataEntry.php">Add a new Client</a></li>
		</ul>
  	</div>
	<div id="newClient" class="search">  
	<div id="noResults">
  	<h3 style="color:red;">There are no results for your search</h3>
  	<h4>You can search again or add this client to the database</h4>
  	<form action="search.php"><a href="dataEntry.php">Add a client</a><br/><br/>
  	<a href="selectTask.php">Return to the task selection page</a><br/><br/>
  	<input type="submit" name="searchAgain" value="Try another search" / ></form>
  	</div>
  	</div>';
    }
  }
?>
</body>
</html>


