<?php
	session_start();
  include ('utility.php');
	include ('../models/administrator.php');
  include ('../models/visit.php');
  include ('../models/familyMember.php');
  include ('../models/client.php');
  include ('../models/sqldb.php');
	
	$_SESSION['errors'] = array();
	if (!hasAccess(TRUE))
	{
		$_SESSION['errors'][] = "This operation requires administrative privileges.";
		header("Location: ../");
		exit();
	}
  
  if (empty($_GET['client']))
  {
		$_SESSION['errors'][] = "No client was specified for deletion.";
    header('Location: ../search.php');
    exit();
  }
  
  $client = Client::getClientByID($_GET['client']);
  if ($client === NULL)
  {
		$_SESSION['errors'][] = "The requested client could not be found.";
    header('Location: ../search.php');
    exit();
  }
  
  if ($client->delete())
  {
		FamilyMember::cleanFamilyMembers();
		$_SESSION['errors'][] = "The client was successfully deleted.";
		header('Location: ../search.php');
  }
	else
	{
		$_SESSION['errors'][] = "There was an error deleting the requested client.";
		header('Location: ../search.php');
	}
	
