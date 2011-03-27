<?php
  include ('utility.php');
  include ('../models/sqldb.php');
  include ('../models/visit.php');
  include ('../models/client.php');
  
  if(empty($_POST['distType']))
  {
    if (empty($_POST['clientID']))
    {
      header('Location: ../viewHistory.php?recordVisiterror=1');
    }
    else
    {
      header("Location: ../viewHistory.php?recordVisitError=1&client={$_POST['clientID']}");
    }
  }
  else
  {
    $visit = Visit::create();
    $visit->setClientID($_POST['clientID']);
    $visit->setTypeID($_POST['distType']);
    $visit->setDate(date("m-d-Y"));
    if ($visit->save() === FALSE)
    {
      header("Location: ../viewHistory.php?recordVisitError=1&client={$_POST['clientID']}");
    }
    else
    {
      header("Location: ../viewHistory.php?recordVisitSuccess=1&visit={$visit->getVisitID()}&client={$_POST['clientID']}");
    }
  }