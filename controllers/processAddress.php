<?php
  session_start();
  include ('utility.php');
  include ('../models/house.php');
  include ('../models/sqldb.php');
	include ('../models/administrator.php');
	
	$_SESSION['errors'] = array();
	if (!hasAccess())
	{
		$_SESSION['errors'][] = "This operation requires administrative privileges.";
		header("Location: ../");
		exit();
	}
  
  if (empty($_SESSION['haveSearchedHouse']))
  {
    $_SESSION['streetNumber'] = $_POST['streetNumber'];
    $_SESSION['streetName'] = $_POST['streetName'];
    $_SESSION['streetType'] = $_POST['streetType'];
    $_SESSION['line2'] = $_POST['line2'];
    $_SESSION['city'] = $_POST['city'];
    $_SESSION['zip'] = $_POST['zip'];
    $_SESSION['oldAddressValid'] = $_POST['oldAddressValid'];
    $houses = House::searchAddresses($_POST['streetNumber'], $_POST['streetName']);
    $_SESSION['houseMatches'] = $houses;
    $_SESSION['haveSearchedHouse'] = TRUE;
    header("Location: ../addressEntry.php");
		exit();
   }
	if (empty($_POST['houseID']))
	{
		$_SESSION['errors'][] = "Please choose an address from the list.";
		header("Location: ../addressEntry.php");
		exit();
	}
	if ($_POST['houseID'] !== "new")
	{
		$house = House::getHouseByID($_POST['houseID']);
		if ($house === NULL)
		{
			$_SESSION['errors'][] = "The selected address couldn't be retrieved from the database.";
			$_SESSION['houseID'] = NULL;
			header("Location: ../addressEntry.php");
			exit();
		}
		$_SESSION['houseID'] = $house->getHouseID();
		$_SESSION['streetNumber'] = $house->getStreetNumber();
		$_SESSION['streetName'] = $house->getStreetName();
		$_SESSION['streetType'] = $house->getStreetType();
		$_SESSION['line2'] = $house->getLine2();
		$_SESSION['city'] = $_POST['city'];
		$_SESSION['zip'] = $_POST['zip'];
		header("Location: ../spouseEntry.php");
		exit();
	}
	else
	{
		$_SESSION['houseID'] = $_POST['houseID'];
		header("Location: ../spouseEntry.php");
		exit();
	}
  
