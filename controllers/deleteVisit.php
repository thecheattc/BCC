<?php
	session_start();
  include ('utility.php');
	include ('../models/administrator.php');
  include ('../models/visit.php');
  include ('../models/client.php');
  include ('../models/sqldb.php');
	
	$_SESSION['errors'] = array();
	if (!hasAccess())
	{
		$_SESSION['errors'][] = "This operation requires administrative privileges.";
		header("Location: ../");
		exit();
	}
  
  if (empty($_GET['visit']))
  {
		$_SESSION['errors'][] = "No visit was specified to delete.";
    header("Location: ../search.php");
		exit();
  }
	$visit = Visit::getVisitByID($_GET['visit']);
	if (isset($visit))
	{
		if ($visit->delete())
		{
			$_SESSION['errors'][] = "Visit {$visit->getVisitID()} successfully deleted.";
			header("Location: ../viewHistory.php?client={$visit->getClientID()}");
		}
		else
		{
			$_SESSION['errors'][] = "There was an error deleting visit {$visit->getVisitID()}.";
			header("Location: ../viewHistory.php?client={$visit->getClientID()}");
		}
	}
	else
	{
		$_SESSION['errors'][] = "The requested visit could not be found.";
		header('Location: ../search.php?deleteVisitError=1');
	}
