<?php
	session_start();
  include ('utility.php');
	include ('../models/administrator.php');
  include ('../models/visit.php');
  include ('../models/familyMember.php');
  include ('../models/client.php');
  include ('../models/sqldb.php');
	
	$_SESSION['errors'] = array();
	
	$_SESSION["adminUsername"] = $_POST["adminUsername"];
	$_SESSION["accessID"] = $_POST["accessID"];
	
	$adminUsername = processString($_POST["adminUsername"]);
	$password = processPassword($_POST["adminPass1"]);
	$accessID = processString($_POST["accessID"]);
	$passMatch = ($_POST["adminPass1"] === $_POST["adminPass2"]);
	
	$invalidAccessLevel = (Administrator::isValidAccessID($accessID))? FALSE : TRUE;
	$duplicate = Administrator::getAdminByUsername($adminUsername);
	
	if (empty($adminUsername))
	{
		$_SESSION['errors'][] = "The username given is invalid. Ensure it is at most 20 characters.";
	}
	if (isset($duplicate))
	{
		$_SESSION['errors'][] = "The username you have chosen is already taken. Please choose another.";
	}
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
	if (!empty($_SESSION['errors']))
	{
		header("Location: ../manageAdmins.php");
		exit();
	}
	
	$admin = Administrator::create();
	$admin->setUsername($adminUsername);
	$admin->setPassword($password);
	$admin->setAccessID($accessID);
	
	if ($admin->save())
	{
		$savedID = $_SESSION['adminID'];
		$_SESSION = NULL;
		$_SESSION['adminID'] = $savedID;
		$_SESSION['errors'][] = "Administrator successfully created.";
		header('Location: ../manageAdmins.php');
		exit();
	}
	else
	{
		$_SESSION['errors'][] = "There was an error saving the administrator to the database.";
		header('Location: ../manageAdmins.php');
		exit();
	}
