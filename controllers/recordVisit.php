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
	}
  
  if(empty($_POST['distType']))
  {
    if (empty($_POST['clientID']))
    {
			$_SESSION['errors'][] = "There was an error recording the visit.";
      header('Location: ../viewHistory.php');
      exit();
    }
    else
    {
			$_SESSION['errors'][] = "There was an error recording the visit.";
      header("Location: ../viewHistory.php?client={$_POST['clientID']}");
      exit();
    }
  }
  else
  {
    $visit = Visit::create();
    $visit->setClientID($_POST['clientID']);
    $visit->setTypeID($_POST['distType']);
    $visit->setDate(createNormalDate(date("m-d-Y")));
    if ($visit->save() === FALSE)
    {
			$_SESSION['errors'][] = "There was an error saving the visit to the database.";
      header("Location: ../viewHistory.php?client={$_POST['clientID']}");
      exit();
    }
    else
    {
			$_SESSION['errors'][] = "Visit {$visit->getVisitID()} successfully recorded.";
      header("Location: ../viewHistory.php?client={$_POST['clientID']}");
      exit();
    }
  }