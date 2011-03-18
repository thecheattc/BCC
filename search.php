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
  if(!empty($first) || !empty($last) || !empty($street))
  {
    $search = TRUE;
  }
  
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
  /****** Style sheet is wrong (using the newclient style for the form's div), 
   but it's just there to give an idea of what it should look like *****/ 
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
    <h1> Search for a Client</h1>
    <h3>Search by first name, last name, and/or street address</h3>
    <?php if ($_GET['error'] == 1)
          {
            echo "<h4>Unfortunately, there was an error processing your request.</h5>";
          }
      ?>  
    <hr />
  </div>
  <div id="newClient">
    <form method="post" action="search.php">
    <fieldset>
      <legend>Search for a client</legend>
      <table>
        <tr>
          <td><label for="first">First name:</label></td>
          <td><input type="text" name="first" value="<?php echo $first ?>" /></td>
        </tr>
        <tr>
          <td><label for="last">Last name: </label></td>
          <td><input type="text" size="60" name="last" value="<?php echo $last ?>" /></td>
        </tr>
        <tr>
          <td><label for="street">Street address: </label></td>
          <td><input name="street" type="text" size="60" value="<?php echo $street ?>" /></td>
        </tr>
        <tr>
          <td><input type="submit" name="searchsubmit" id="searchsubmit" value="Search" /></td>
        </tr>
      </table>
    </fieldset>
    </form>
  </div>
  <br/>
<?php
  if ($search)
  {
    echo "<h3>Search results:</h3>\n";
  }
  for ($i=0; $i<count($clients); $i++)
  {
    echo "<li>\n";
    echo "\t<span>{$clients[$i]->getFirstName()} {$clients[$i]->getLastName()}</span>\n";
    if($houses[$i] != NULL)
    {
      echo "\t<span>{$houses[$i]->getAddress()} {$houses[$i]->getCity()} {$houses[$i]->getZip()}</span>\n";
    }
    echo "\t";
    echo "<a href='viewHistory.php?client={$clients[$i]->getClientID()}'>View visit history</a>";
    echo "\n";
    echo "\t";
    echo "<a href='editClient.php?client={$clients[$i]->getClientID()}'>Edit client information</a>";
    echo "\n</li>";
  }
  echo "<br /><a href='dataEntry.php'>Add a client</a>\n";
?>
</body>
</html>


