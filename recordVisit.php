<?php
  include ('controllers/utility.php');
  include ('models/sqldb.php');
  include ('models/visit.php');
  include ('models/client.php');
  
  if(empty($_POST['distType']))
  {
    if (empty($_POST['clientID']))
    {
      header('Location: viewHistory.php?error=1');
    }
    else
    {
      header("Location: viewHistory.php?error=1&client={$_POST['clientID']}");
    }
  }
  else
  {
    $visit = Visit::create();
    $visit->setClientID($_POST['clientID']);
    $visit->setTypeID($_POST['distType']);
    $visit->setDate(date("Y-m-d"));
    if ($visit->save() === FALSE)
    {
      header("Location: viewHistory.php?error=1&client={$_POST['clientID']}");
    }
  }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <link rel="stylesheet" href="style/bryant.css" type="text/css"/>
  <link type="text/css" href="scripts/js/jquery-ui-1.8.10.custom/css/ui-lightness/jquery-ui-1.8.10.custom.css" rel="Stylesheet" />	
  <script type="text/javascript" src="scripts/js/jquery-1.4.4.min.js"></script>
  <script src="scripts/js/jquery.simplemodal-1.4.1.js" type="text/javascript" language="javascript" charset="utf-8"></script>
  <script type="text/javascript" src="scripts/js/jquery-ui-1.8.10.custom/js/jquery-ui-1.8.10.custom.min.js"></script>
  <title>Record a visit</title>
</head>
<body>
<?php 
  $message = ($visit->getDistTypeDesc() === "Rejected")? 
  "<p>The rejection was recorded. This visit's ID number is {$visit->getVisitID()}.</p>" :
  "<p>The visit was recorded. This visit's ID number is {$visit->getVisitID()}.</p>";
  
  echo $message;
  
?>
</body>
</html>