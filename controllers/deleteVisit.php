<?php
  include ('utility.php');
  include ('../models/visit.php');
  include ('../models/client.php');
  include ('../models/sqldb.php');
  
  if (empty($_GET['visit']))
  {
    if (empty($_GET['client']))
    {
      header('Location: ../search.php?error=1');
    }
    else
    {
      header("Location: ../viewHistory.php?client={$_GET['client']}&error=1");
    }
  }
  else
  {
    $visit = Visit::getVisitByID($_GET['visit']);
    if ($visit !== NULL)
    {
      Visit::deleteVisitByID($_GET['visit']);
      header("Location: ../viewHistory.php?client={$visit->getClientID()}&error=0");
    }
    else
    {
      if (empty($_GET['client']))
      {
        header('Location: ../search.php?error=1');
      }
      else
      {
        header("Location: ../viewHistory.php?client={$_GET['client']}&error=1");
      }
    }
  }
