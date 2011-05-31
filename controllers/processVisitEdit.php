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
  $date = createNormalDate($_POST['date']);
	$cleanNote = processString($_POST['note']);
	$cleanType = processString($_POST['type']);
	$cleanLocation = processString($_POST['location']);
  if (empty($_GET['visit']) || empty($date) || empty($cleanType) || empty($cleanLocation) || 
			(!empty($_POST['note']) && empty($cleanNote)))
  {
		$error = "There was an error editing the visit. If you're attaching a note, ";
		$error .= "remember that only numbers, letters, spaces, periods, and commas are allowed.";
		$_SESSION['errors'][] = $error;
    if (!empty($_GET['client']))
    {
      header("Location: ../viewHistory.php?client={$_GET['client']}");
      exit();
    }
    else
    {
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
  $visit->setDate($date);
  $visit->setTypeID($cleanType);
	$visit->setNote($cleanNote);
	$visit->setLocationID($cleanLocation);

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