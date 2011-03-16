<?php

  include('models/sqldb.php');
  include('models/visit.php');
  include('models/client.php');
  include('models/gender.php');
  include('controllers/utility.php');
  
  date_default_timezone_set('America/New_York');
  
  if (empty($_GET['client']))
  {
    header('Location: /selectTask.php');
  }
  else
  {
    $client = Client::getClientByID($_GET['client']);
  }
  $firstName = '';
  $lastName = '';
  $visits = NULL;
  $gender = NULL;
  $distTypes = NULL;
  
  if (empty($client))
  {
    header('Location: /selectTask.php');
  }
  else
  {
    //Default date to search for is since this month
    $since = date("Y-m");
    $since = $since . "-01";
    if (!empty($_POST['since']))
    {
      if($_POST['since'] == 1)//This year
      {
        $since = date("Y");
        $since = $since . "-01-01";
      }
      elseif($_POST['since'] == 2) // Forever
      {
        $since = '';
      }
    }
    $firstName = $client->getFirstName();
    $lastName = $client->getLastName();
    $visits = $client->getVisitHistory($since);
    $pronoun = (Gender::getGenderByID($client->getGenderID())->getGenderDesc() == "Male")? "him" : "her";
    $distTypes = Visit::getAllDistTypes();
  }
  
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Visit history for <?php echo $firstName . " " . $lastName; ?></title>
  <link rel="stylesheet" href="style/bryant.css" type="text/css" media="all" />
  <script src="scripts/js/jquery-1.4.1.min.js"></script>
  <script src="scripts/js/jquery.simplemodal-1.4.1.js" type="text/javascript" language="javascript" charset="utf-8"></script>
  <script type="text/javascript" src="scripts/js/jquery-ui-1.8.10.custom/js/jquery-ui-1.8.10.custom.min.js"></script>
</head>
<body>
  <div id="header">
    <h3>Visit history for <?php echo $client->getFirstName() . " " . $client->getLastName() ?></h1>
    <?php if (!empty($_GET['visit'])){ echo "<h4>Visit {$_GET['visit']} has been recorded.</h4>";} ?>
    <hr />
  </div>
  <form method="post" action="viewHistory.php?client=<?php echo $_GET['client']; ?>">
    <label for="since">View visit history for:</label>
    <select id="since" name="since">
      <option value="0" selected>This month</option>
      <option value="1">This year</option>
      <option value="2">Forever</option>
    </select>
    <input type="submit" value="Search visits" />
  </form>
  <table>
    <tr>
      <td>Date</td>
      <td>Type of distribution</td>
    </tr>

<?php
  foreach($visits as $visit)
  {
    echo "\t<tr>\n";
    echo "\t\t<td>{$visit->getDate()}</td>\n";
    echo "\t\t<td>{$visit->getDistTypeDesc()}</td>\n";
    echo "\t\t<td><a href='editVisit.php?visit={$visit->getVisitID()}'>Edit</a></td>\n";
    echo "\t\t<td><a href='controllers/deleteVisit.php?visit={$visit->getVisitID()}'>Delete</a></td>\n";
    echo "\t<tr>\n";
  }
  ?>
  </table>
  <p>If <?php echo $firstName;?> tried to receive food today, record the type of distribution, 
or record that you could not give <?php echo $pronoun; ?> food. </p>
  <form method="post" action="controllers/recordVisit.php">
    <select id="distType" name="distType">
    <option value="0" selected>Select a distribution type</option>
<?php
  foreach ($distTypes as $key => $value)
  {
    echo "<option value='{$key}'>{$value}</option>\n";
  }
  ?>
    </select>
    <input type="hidden" name="clientID" id="clientID" value="<?php echo $_GET['client']; ?>" />
    <input type="submit" value="Submit" />
  </form>
</body>
</html>


