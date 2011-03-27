<?php
  include ('utility.php');
  include ('../models/visit.php');
  include ('../models/sqldb.php');
  
  
  if (empty($_GET['visit']) || empty($_POST['date']) || empty($_POST['type']))
  {
    if (!empty($_GET['client']))
    {
      header("Location: ../viewHistory.php?client={$_GET['client']}&editVisitError=1");
    }
    else
    {
      header("Location: ../search.php?editVisitError=1");
    }
  }
  else
  {
    $visit = Visit::getVisitByID($_GET['visit']);
    if ($visit === NULL)
    {
      if (!empty($_GET['client']))
      {
        header("Location: ../viewHistory.php?client={$_GET['client']}&editVisitError=1");
      }
      else
      {
      header("Location: ../search.php?editVisitError=1");
      }
    }
    $visit->setDate($_POST['date']);
    $visit->setTypeID($_POST['type']);

    if ($visit->save() === FALSE)
    {
      header("Location: ../viewHistory.php?client={$visit->getClientID()}&editVisitError=1");
    }
    else
    {
      header("Location:../viewHistory.php?client={$visit->getClientID()}&visit={$_GET['visit']}&editVisitSuccess=1");
    }
  }