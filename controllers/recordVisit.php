<?php
	session_start();
  include ('utility.php');
  include ('../models/sqldb.php');
	include ('../models/administrator.php');
  include ('../models/visit.php');
  include ('../models/client.php');
  include ('../models/familyMember.php');
	
	$_SESSION['errors'] = array();
	
	if (!hasAccess())
	{
		$_SESSION['errors'][] = "This operation requires administrative privileges.";
		header("Location: ../");
		exit();
	}
  $cleanNote = processString($_POST['note']);
  if(empty($_POST['distType']) || empty($_POST['distLocation']) || 
		 (!empty($_POST['note']) && empty($cleanNote)))
  {
		$error = "There was an error recording the visit. If you're attaching a note, ";
		$error .= "remember that only numbers, letters, spaces, periods, and commas are allowed.";
		$_SESSION['errors'][] = $error;
    if (empty($_POST['clientID']))
    {
      header('Location: ../viewHistory.php');
      exit();
    }
    else
    {
		 header("Location: ../viewHistory.php?client={$_POST['clientID']}");
		 exit();
    }
  }
	$visit = Visit::create();
	$visit->setClientID($_POST['clientID']);
	$visit->setTypeID($_POST['distType']);
	$visit->setLocationID($_POST['distLocation']);
	$visit->setDate(createNormalDate(date("m-d-Y")));
	$visit->setNote($cleanNote);
	if ($visit->save() === FALSE)
	{
		$_SESSION['errors'][] = "There was an error saving the visit to the database.";
		header("Location: ../viewHistory.php?client={$_POST['clientID']}");
		exit();
	}
	$_SESSION['errors'][] = "Visit {$visit->getVisitID()} successfully recorded.";
	header("Location: ../viewHistory.php?client={$_POST['clientID']}");
	exit();

