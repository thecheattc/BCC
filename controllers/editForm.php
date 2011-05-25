<?php
	session_start();
  include ('utility.php');
	include ('../models/administrator.php');
	include ('../models/ethnicity.php');
	include ('../models/reason.php');
	include ('../models/gender.php');
  include ('../models/sqldb.php');
	include ('../models/visit.php');
	
	define("ROOT_ACCESS_ID", 1);
	$_SESSION['errors'] = array();
	
	if (!hasAccess(TRUE))
	{
		$_SESSION['errors'][] = "This operation requires administrative privileges.";
		header("Location: ../");
		exit();
	}
	if (!isset($_POST['action']))
	{
		$_SESSION['errors'][] = "There was an error processing your request.";
		header("Location: ../editForms.php");
		exit();
	}
	
	if ($_POST['action'] === 'addEthnicity')
	{
		addEthnicity();
	}
	elseif ($_POST['action'] === 'removeEthnicity')
	{
		removeEthnicity();
	}
	elseif ($_POST['action'] === 'addReason')
	{
		addReason();
	}
	elseif ($_POST['action'] === 'removeReason')
	{
		removeReason();
	}
	elseif ($_POST['action'] === 'addGender')
	{
		addGender();
	}
	elseif ($_POST['action'] === 'removeGender')
	{
		removeGender();
	}
	elseif ($_POST['action'] === 'addLocation')
	{
		addLocation();
	}
	elseif ($_POST['action'] === 'removeLocation')
	{
		removeLocation();
	}
	
	function addEthnicity()
	{
		$_SESSION['newEthnicity'] = isset($_POST['newEthnicity'])? stripslashes($_POST['newEthnicity']) : '';
		$ethnicityName = processString($_POST['newEthnicity'], FALSE, FALSE);
		if (empty($ethnicityName))
		{
			$_SESSION['errors'][] = "Please enter an ethnicity. Only letters, numbers, periods, and commas are allowed.";
			header('Location: ../editForms.php');
			exit();
		}
		$duplicate = Ethnicity::getEthnicityByDesc($ethnicityName);
		if (!empty($duplicate))
		{
			$_SESSION['errors'][] = "The given ethnicity already exists.";
			header('Location: ../editForms.php');
			exit();
		}
		$ethnicity = Ethnicity::create();
		$ethnicity->setEthnicityDesc($ethnicityName);
		if ($ethnicity->save())
		{
			$_SESSION['errors'][] = "Ethnicity successfully added";
			$_SESSION['newEthnicity'] = '';
			header('Location: ../editForms.php');
			exit();
		}
		else
		{
			$_SESSION['errors'][] = "There was an error adding the ethnicity.";
			header('Location: ../editForms.php');
			exit();
		}
	}
	
	function removeEthnicity()
	{
		$_SESSION['ethnicityID'] = isset($_POST['ethnicityID'])? stripslashes($_POST['ethnicityID']) : '';
		$ethnicityID = processString($_POST['ethnicityID']);
		if (empty($ethnicityID))
		{
			$_SESSION['errors'][] = "Please select an ethnicity.";
			header('Location: ../editForms.php');
			exit();
		}
		$ethnicity = Ethnicity::getEthnicityByID($ethnicityID);
		if (empty($ethnicity))
		{
			$_SESSION['errors'][] = "The selected ethnicity could not be found.";
			header('Location: ../editForms.php');
			exit();
		}
		$removableEthnicityIDs = Ethnicity::getRemovableEthnicityIDs();
		if (!in_array($ethnicity->getEthnicityID(), $removableEthnicityIDs))
		{
			$_SESSION['errors'][] = "The selected ethnicity is in use and cannot be deleted.";
			header('Location: ../editForms.php');
			exit();
		}
		if ($ethnicity->delete())
		{
			$_SESSION['errors'][] = "The ethnicity was successfully deleted.";
			header('Location: ../editForms.php');
			exit();
		}
		else
		{
			$_SESSION['errors'][] = "There was an error deleting the selected ethnicity.";
			header('Location: ../editForms.php');
			exit();
		}
	}
	
	function addReason()
	{
		$_SESSION['newReason'] = isset($_POST['newReason'])? stripslashes($_POST['newReason']) : '';
		$reasonName = processString($_POST['newReason'], FALSE, FALSE);
		if (empty($reasonName))
		{
			$_SESSION['errors'][] = "Please enter a reason. Only letters, numbers, periods, and commas are allowed.";
			header('Location: ../editForms.php');
			exit();
		}
		$duplicate = Reason::getReasonByDesc($reasonName);
		if (!empty($duplicate))
		{
			$_SESSION['errors'][] = "The given reason already exists.";
			header('Location: ../editForms.php');
			exit();
		}
		$reason = Reason::create();
		$reason->setReasonDesc($reasonName);
		if ($reason->save())
		{
			$_SESSION['errors'][] = "Reason successfully added";
			$_SESSION['newReason'] = '';
			header('Location: ../editForms.php');
			exit();
		}
		else
		{
			$_SESSION['errors'][] = "There was an error adding the reason.";
			header('Location: ../editForms.php');
			exit();
		}
	}
	
	function removeReason()
	{
		$_SESSION['reasonID'] = isset($_POST['reasonID'])? stripslashes($_POST['reasonID']) : '';
		$reasonID = processString($_POST['reasonID']);
		if (empty($reasonID))
		{
			$_SESSION['errors'][] = "Please select a reason.";
			header('Location: ../editForms.php');
			exit();
		}
		$reason = Reason::getReasonByID($reasonID);
		if (empty($reason))
		{
			$_SESSION['errors'][] = "The selected reason could not be found.";
			header('Location: ../editForms.php');
			exit();
		}
		$removableReasonIDs = Reason::getRemovableReasonIDs();
		if (!in_array($reason->getReasonID(), $removableReasonIDs))
		{
			$_SESSION['errors'][] = "The selected reason is in use and cannot be deleted.";
			header('Location: ../editForms.php');
			exit();
		}
		if ($reason->delete())
		{
			$_SESSION['errors'][] = "The reason was successfully deleted.";
			header('Location: ../editForms.php');
			exit();
		}
		else
		{
			$_SESSION['errors'][] = "There was an error deleting the selected reason.";
			header('Location: ../editForms.php');
			exit();
		}
	}
	
	function addGender()
	{
		$_SESSION['newGender'] = isset($_POST['newGender'])? stripslashes($_POST['newGender']) : '';
		$genderName = processString($_POST['newGender'], FALSE, FALSE);
		if (empty($genderName))
		{
			$_SESSION['errors'][] = "Please enter a gender. Only letters, numbers, periods, and commas are allowed.";
			header('Location: ../editForms.php');
			exit();
		}
		$duplicate = Gender::getGenderByDesc($genderName);
		if (!empty($duplicate))
		{
			$_SESSION['errors'][] = "The given gender already exists.";
			header('Location: ../editForms.php');
			exit();
		}
		$gender = Gender::create();
		$gender->setGenderDesc($genderName);
		if ($gender->save())
		{
			$_SESSION['errors'][] = "Gender successfully added";
			$_SESSION['newGender'] = '';
			header('Location: ../editForms.php');
			exit();
		}
		else
		{
			$_SESSION['errors'][] = "There was an error adding the gender.";
			header('Location: ../editForms.php');
			exit();
		}
	}
	
	function removeGender()
	{
		$genderID = processString($_POST['genderID']);
		if (empty($genderID))
		{
			$_SESSION['errors'][] = "Please select a gender.";
			header('Location: ../editForms.php');
			exit();
		}
		$gender = Gender::getGenderByID($genderID);
		if (empty($gender))
		{
			$_SESSION['errors'][] = "The selected gender could not be found.";
			header('Location: ../editForms.php');
			exit();
		}
		$removableGenderIDs = Gender::getRemovableGenderIDs();
		if (!in_array($gender->getGenderID(), $removableGenderIDs))
		{
			$_SESSION['errors'][] = "The selected gender is in use and cannot be deleted.";
			header('Location: ../editForms.php');
			exit();
		}
		if ($gender->delete())
		{
			$_SESSION['errors'][] = "The gender was successfully deleted.";
			header('Location: ../editForms.php');
			exit();
		}
		else
		{
			$_SESSION['errors'][] = "There was an error deleting the selected gender.";
			header('Location: ../editForms.php');
			exit();
		}
	}
	
	function addLocation()
	{
		$_SESSION['newLocation'] = isset($_POST['newLocation'])? stripslashes($_POST['newLocation']) : '';
		$locationName = processString($_POST['newLocation'], FALSE, FALSE);
		if (empty($locationName))
		{
			$_SESSION['errors'][] = "Please enter a location. Only letters, numbers, periods, and commas are allowed.";
			header('Location: ../editForms.php');
			exit();
		}
		$locations = Visit::getAllLocations();
		if (in_array($locationName, $locations))
		{
			$_SESSION['errors'][] = "The given location already exists.";
			header('Location: ../editForms.php');
			exit();
		}
		if (Visit::createLocation($locationName))
		{
			$_SESSION['errors'][] = "Location successfully added";
			$_SESSION['newLocation'] = '';
			header('Location: ../editForms.php');
			exit();
		}
		else
		{
			$_SESSION['errors'][] = "There was an error adding the location.";
			header('Location: ../editForms.php');
			exit();
		}
	}
	
	function removeLocation()
	{
		$locationID = processString($_POST['locationID']);
		if (empty($locationID))
		{
			$_SESSION['errors'][] = "Please select a location.";
			header('Location: ../editForms.php');
			exit();
		}
		$removableLocationIDs = Visit::getRemovableLocationIDs();
		if (!in_array($locationID, $removableLocationIDs))
		{
			$_SESSION['errors'][] = "The selected location is in use and cannot be deleted.";
			header('Location: ../editForms.php');
			exit();
		}
		if (Visit::removeLocation($locationID))
		{
			$_SESSION['errors'][] = "The location was successfully deleted.";
			header('Location: ../editForms.php');
			exit();
		}
		else
		{
			$_SESSION['errors'][] = "There was an error deleting the selected location.";
			header('Location: ../editForms.php');
			exit();
		}
	}




