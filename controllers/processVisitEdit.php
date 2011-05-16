<?php
	session_start();
  include ('utility.php');
  include ('../models/visit.php');
  include ('../models/sqldb.php');
	include ('../models/administrator.php');
	
	$_SESSION['errors'] = array();
	if (!hasAccess())
	{
		$_SESSION['errors'][] = "This operation requires administrative privileges.";
		header("Location: ../");
	}
  
  $badDate = !createNormalDate($_POST['date']);
  if (empty($_GET['visit']) || $badDate || empty($_POST['type']))
  {
    if (!empty($_GET['client']))
    {
			$_SESSION['errors'][] = "There was an error editing the visit.";
      header("Location: ../viewHistory.php?client={$_GET['client']}");
      exit();
    }
    else
    {
			$_SESSION['errors'][] = "There was an error editing the visit.";
      header("Location: ../search.php");
      exit();
    }
  }
  
  $visit = Visit::getVisitByID($_GET['visit']);
  if ($visit === NULL)
  {
    if (!empty($_GET['client']))
    {
			$_SESSION['errors'][] = "There was an error editing the visit.";
      header("Location: ../viewHistory.php?client={$_GET['client']}");
      exit();
    }
    else
    {
			$_SESSION['errors'][] = "There was an error editing the visit.";
      header("Location: ../search.php");
      exit();

    }
  }
  $visit->setDate(createNormalDate($_POST['date']));
  $visit->setTypeID($_POST['type']);

  if ($visit->save() === FALSE)
  {
		$_SESSION['errors'][] = "There was an error saving the visit to the database.";
    header("Location: ../viewHistory.php?client={$visit->getClientID()}");
    exit();
  }
  else
  {
		$_SESSION['errors'][] = "Visit {$_GET['visit']} successfully edited.";
		header("Location:../viewHistory.php?client={$visit->getClientID()}");
    exit();
  }