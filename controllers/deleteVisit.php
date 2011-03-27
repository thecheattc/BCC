<?php
  include ('utility.php');
  include ('../models/visit.php');
  include ('../models/client.php');
  include ('../models/sqldb.php');
  
  if (empty($_GET['visit']))
  {
    header("Location: ../search.php?deleteVisitError=1");
  }
  else
  {
    $visit = Visit::getVisitByID($_GET['visit']);
    if ($visit !== NULL)
    {
      if ($visit->delete())
      {
        header("Location: ../viewHistory.php?client={$visit->getClientID()}&deleteVisitSuccess=1");
      }
      else
      {
        header("Location: ../viewHistory.php?client={$visit->getClientID()}&deleteVisitError=1");
      }
    }
    else
    {
      header('Location: ../search.php?deleteVisitError=1');
    }
  }
