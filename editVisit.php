<?php
  include ('models/visit.php');
  include ('models/client.php');
  include ('models/sqldb.php');
  include ('controllers/utility.php');
  
  if (empty($_GET['visit']))
  {
    header("Location: search.php?editVisitError=1");
  }
  else
  {
    $visit = Visit::getVisitByID($_GET['visit']);
    $client = NULL;
    if ($visit !== NULL)
    {
      $client = Client::getClientByID($visit->getClientID());
    }
    if ($visit === NULL || $client === NULL)
    {
      header("Location: search.php?editVisitError=1");
    }
    $types = Visit::getAllDistTypes();
  }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Visit history for <?php echo $client->getfirstName() . " " . $client->getlastName(); ?></title>
  <link rel="stylesheet" href="style/bryant.css" type="text/css" media="all" />
  <script src="scripts/js/jquery-1.4.1.min.js"></script>
  <script src="scripts/js/jquery.simplemodal-1.4.1.js" type="text/javascript" language="javascript" charset="utf-8"></script>
  <script type="text/javascript" src="scripts/js/jquery-ui-1.8.10.custom/js/jquery-ui-1.8.10.custom.min.js"></script>
</head>
<body>
  <div id="header">
    <h3>Edit <?php echo $client->getFirstName() . " " . $client->getLastName() ?>&rsquo;s visit</h1>
    <hr />
  </div>
<form method="post" action="controllers/processVisitEdit.php?visit=<?php echo $_GET['visit']; ?>&client=<?php echo $visit->getClientID(); ?>">
    <label for="date">Date: </label>
    <input name="date" id="date" value="<?php echo $visit->getDate(); ?>" />
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
    </select>
    <input type="submit" value="Edit visit" />
  </form>
</body>
</html>
