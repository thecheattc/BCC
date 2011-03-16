<?php
  include ('utility.php');
  include ('../models/sqldb.php');
  include ('../models/visit.php');
  include ('../models/client.php');
  
  if(empty($_POST['distType']))
  {
    if (empty($_POST['clientID']))
    {
      header('Location: ../viewHistory.php?error=1');
    }
    else
    {
      header("Location: ../viewHistory.php?error=1&client={$_POST['clientID']}");
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
      header("Location: ../viewHistory.php?error=1&client={$_POST['clientID']}");
    }
    else
    {
      header("Location: ../viewHistory.php?visit={$visit->getVisitID()}&client={$_POST['clientID']}");
    }
  }