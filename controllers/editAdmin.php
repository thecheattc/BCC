<?php
	session_start();
  include ('utility.php');
	include ('../models/administrator.php');
  include ('../models/visit.php');
  include ('../models/familyMember.php');
  include ('../models/client.php');
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
		$_SESSION['errors'][] = "There was an error editing this administrator.";
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
	
	
	$_SESSION["eaccessID"] = $_POST["accessID"];
	
	$password = processPassword($_POST["eadminPass1"]);
	$accessID = processString($_POST["eaccessID"]);
	$passMatch = ($_POST["eadminPass1"] === $_POST["eadminPass2"]);
	
	$invalidAccessLevel = (Administrator::isValidAccessID($accessID))? FALSE : TRUE;
	
	if (empty($password))
	{
		$_SESSION['errors'][] = "Please enter a password between 5 and 15 characters long";
	}
	elseif (!$passMatch)
	{
		$_SESSION['errors'][] = "The password does not match the password confirmation";
	}
	if (empty($accessID))
	{
		$_SESSION['errors'][] = "Please select an access level.";
	}
	if ($invalidAccessLevel)
	{
		$_SESSION['errors'][] = "The access level is invalid.";
	}
	else if (Administrator::rootAdminCount() < 2 && $admin->getAccessID() == ROOT_ACCESS_ID &&
					 $accessID != ROOT_ACCESS_ID)
	{
		$_SESSION['errors'][] = "You cannot change this access level. There must be at least one administrator with root access to the system.";
	}
	if (!empty($_SESSION['errors']))
	{
		$_SESSION['errors'][] = "For text input, only letters, numbers, periods, and commas are allowed.";
		header("Location: ../editAdmin.php?id={$admin->getAdminID()}");
		exit();
	}
	
	$admin->setPassword($password);
	$admin->setAccessID($accessID);
	
	if ($admin->save())
	{
		$savedID = $_SESSION['adminID'];
		$_SESSION = NULL;
		$_SESSION['adminID'] = $savedID;
		$_SESSION['errors'][] = "Administrator successfully edited.";
		header('Location: ../manageAdmins.php');
		exit();
	}
	else
	{
		$_SESSION['errors'][] = "There was an error saving the edits to the database.";
		header("Location: ../editAdmin.php?id={$admin->getAdminID()}");
		exit();
	}
