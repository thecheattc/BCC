<?php
  session_start();
  include ('utility.php');
  include ('../models/client.php');
  include ('../models/sqldb.php');
	include ('../models/administrator.php');
	
	$_SESSION['errors'] = array();
	if (!hasAccess())
	{
		$_SESSION['errors'][] = "This operation requires administrative privileges.";
		header("Location: ../");
		exit();
	}
  
  if (empty($_SESSION['haveSearchedSpouse']))
  {
    $_SESSION['spouseFirst'] = $_POST['spouseFirst'];
    $_SESSION['spouseLast'] = $_POST['spouseLast'];
    $spouses = Client::searchSpouses($_POST['spouseFirst'], $_POST['spouseLast']);
    $_SESSION['spouseMatches'] = $spouses;
    $_SESSION['haveSearchedSpouse'] = TRUE;
    header("Location: ../spouseEntry.php");
		exit();
	}
	if (empty($_POST['spouseID']))
	{
		$_SESSION['errors'][] = "Please choose an option from the list.";
		header("Location: ../spouseEntry.php");
		exit();
	}
	if ($_POST['spouseID'] !== "single")
	{
		$spouse = Client::getClientbyID($_POST['spouseID']);
		if ($spouse === NULL)
		{
			$_SESSION['errors'][] = "The selected spouse couldn't be retrieved from the database.";
			$_SESSION['spouseID'] = NULL;
			header("Location: ../spouseEntry.php");
			exit();
		}
		$_SESSION['spouseID'] = $spouse->getClientID();
		$_SESSION['spouseFirst'] = $spouse->getFirstName();
		$_SESSION['spouseLast'] = $spouse->getLastName();
		header("Location: ../clientEntry.php");
		exit();
	}
	else
	{
		$_SESSION['spouseID'] = $_POST['spouseID'];
		header("Location: ../clientEntry.php");
		exit();
	}
  
