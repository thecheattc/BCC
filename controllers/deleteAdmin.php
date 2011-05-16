<?php
	session_start();
  include ('utility.php');
	include ('../models/administrator.php');
  include ('../models/sqldb.php');
	
	define("ROOT_ACCESS_ID", 1);
	
	$_SESSION['errors'] = array();
	if (!hasAccess(TRUE))
	{
		$_SESSION['errors'][] = "This operation requires administrative privileges.";
		header("Location: ../");
		exit();
	}
  
  if (empty($_GET['id']))
  {
		$_SESSION['errors'][] = "There was an error deleting this administrator.";
    header('Location: ../manageAdmins.php');
    exit();
  }
  
	$admin = Administrator::getAdminByID($_GET['id']);
	if (empty($admin))
	{
		$_SESSION['errors'][] = "The administrator requested was not found.";
    header('Location: ../manageAdmins.php');
    exit();
	}
	
	if ($admin->getAccessID() == ROOT_ACCESS_ID && Administrator::rootAdminCount() < 2)
	{
		$_SESSION['errors'][] = "Deletion unsuccesful. There must be at least one administrator with root access to the system.";
    header('Location: ../manageAdmins.php');
    exit();
	}
	
	if($admin->delete())
	{
		$_SESSION['errors'][] = "Deletion successful.";
		if ($admin->getAdminID() == $_SESSION['adminID'])
		{
			header('Location: ../logout.php');
		}
		header('Location: ../manageAdmins.php');
    exit();
	}
	else
	{
		$_SESSION['errors'][] = "There was an error removing the administrator from the database.";
    header('Location: ../manageAdmins.php');
		exit();
	}
	
	
