<?php
  session_start();
	date_default_timezone_set('America/New York');
  include_once('utility.php');
	include_once('../models/administrator.php');
  include_once('../models/house.php');
  include_once('../models/client.php');
  include_once('../models/sqldb.php');
  include_once('../models/familyMember.php');
  include('../models/visit.php');
	
	define("EARLIEST_APPDATE", "01-01-2000");
	
	$_SESSION['errors'] = array();
	if (!hasAccess())
	{
		$_SESSION['errors'][] = "This operation requires administrative privileges.";
		header("Location: ../");
		exit();
	}
  
  define("UNEMPLOYED_REASON_ID", 1);
  define("HOMELESS_REASON_ID", 6);
  define("NOT_ON_FOODSTAMPS", 0);
  define("OTHER_REASON_ID", 7);
  
  $edit = (!empty($_SESSION['edit']))? TRUE : FALSE;
  $house = NULL;
  $client = NULL;
  $newFamilyMembers = array();
  $oldFamilyMembers = FamilyMember::getAllFamilyMembersForClient($_SESSION['clientID'], $_SESSION['spouseID'], $_SESSION['houseID']);

  if ($edit)
  {
    $client = Client::getClientByID($_SESSION['clientID']);
    if (empty($client))
    {
			$_SESSION['errors'][] = "The requested client could not be found.";
      header("Location: ../search.php");
      exit();
    }
  }
  if (!isset($_SESSION['oldAddressValid']))
  {
    $_SESSION['oldAddressValid'] = FALSE;
  }
  
  $appDate = createNormalDate($_SESSION['appDate']);
  $first = processString($_SESSION['firstName']);
  $last = processString($_SESSION['lastName']);
  if ($_SESSION['houseID'] === "new")
  {
    $streetNumber = processString($_SESSION['streetNumber']);
		$streetNumberGiven = !empty($_SESSION['streetNumber']);
    $streetName = processString($_SESSION['streetName']);
		$streetNameGiven = !empty($_SESSION['streetName']);
    $streetType = processString($_SESSION['streetType']);
		$streetTypeGiven = !empty($_SESSION['streetType']);
    $line2 = processString($_SESSION['line2']);
		$line2Given = !empty($_SESSION['line2']);
    $city = processString($_SESSION['city']);		
		$cityGiven = !empty($_SESSION['city']);
    $zip = processString($_SESSION['zip']);
		$zipGiven = !empty($_SESSION['zip']);
  }
  else
  {
    $match = NULL;
    foreach($_SESSION['houseMatches'] as $possible)
    {
      if ($possible['houseID'] == $_SESSION['houseID'])
      {
        $match = $possible;
      }
    }
    if ($match)
    {
      $streetNumber = processString($match['streetNumber']);
			$streetNumberGiven = !empty($match['streetNumber']);
      $streetName = processString($match['streetName']);
			$streetNameGiven = !empty($match['streetName']);
      $streetType = processString($match['streetType']);
			$streetTypeGiven = !empty($match['streetType']);
      $line2 = processString($match['line2']);
			$line2Given = !empty($match['line2']);
      $city = processString($match['city']);
			$cityGiven = !empty($match['city']);
      $zip = processString($match['zip']);
			$zipGiven = !empty($match['zip']);
    }
    else
    {
      $_SESSION['errors'][] = "There was an error processing the address given.";;
      header("Location: ../clientConfirm.php");
      exit();
    }
  }
	$spouseID = (processString($_SESSION['spouseID']) == "single")? NULL : $_SESSION['spouseID'];
  $oldAddressValid = processString($_SESSION['oldAddressValid']);
  $phone = processPhone($_SESSION['number']);
  $age = intval($_SESSION['age']);
  $genderID = processString($_SESSION['gengroup']);
  $ethnicityID = processString($_SESSION['ethgroup']);
  $reasonID = processString($_SESSION['reasongroup']);
  $explanation = processString($_SESSION['explanation']);
  $receivesStamps = processString($_SESSION['receivesStamps']);
  
	$sessionFamily = array();
  for ($i=0; $i<$_SESSION['memberCount']; $i++)
  {
		$sessionFamily[] = array(
													"age" => processString($_SESSION['familyMembers'][$i]["age"]),
													"gender" => processString($_SESSION['familyMembers'][$i]["gender"]),
													"ethnicity" => processString($_SESSION['familyMembers'][$i]["ethnicity"])
													);
  }
  
  $wantsStamps = NULL;
  $unempDate = NULL;
  $addressError = FALSE;
  if ($reasonID == UNEMPLOYED_REASON_ID)
  {
    $unempDate = createNormalDate($_SESSION['uDate']);
  }
  if ($receivesStamps == NOT_ON_FOODSTAMPS)
  {
    $wantsStamps = processString($_SESSION['wantsStamps']);
  }
  
  $addressRequired = ((!empty($reasonID) && $reasonID != HOMELESS_REASON_ID));
  if (empty($_SESSION['houseID']))
  {
    $_SESSION['errors'][] = "House selection from search results";
  }
	if (($streetNumberGiven || $addressRequired) && empty($streetNumber))
	{
		$_SESSION['errors'][] = "Street number";
	}
	if (($streetNameGiven || $addressRequired) && empty($streetName))
	{
		$_SESSION['errors'][] = "Street name";
	}
	if (($streetTypeGiven || $addressRequired) && empty($streetType))
	{
		$_SESSION['errors'][] = "Street type";
	}
	if ($line2Given && empty($line2))
	{
		$_SESSION['errors'][] = "Address line 2";
	}
	if (($cityGiven || $addressRequired) && empty($city))
	{
		$_SESSION['errors'][] = "City";
	}
	if (($zipGiven || $addressRequired) && empty($zip))
	{
		$_SESSION['errors'][] = "Zip";
	}
  if (empty($appDate))
  {
    $_SESSION['errors'][] = "Application date";
  }
  if (empty($first))
  {
    $_SESSION['errors'][] = "First name";
  }
  if (empty($last))
  {
    $_SESSION['errors'][] = "Last name";
  }
	if (empty($phone))
	{
		$_SESSION['errors'][] = "Phone number";
	}
  if (empty($age))
  {
    $_SESSION['errors'][] = "Age";
  }
  if (empty($genderID))
  {
    $_SESSION['errors'][] = "Gender";
  }
  if (empty($ethnicityID))
  {
    $_SESSION['errors'][] = "Ethnicity";
  }
  if (empty($reasonID))
  {
    $_SESSION['errors'][] = "Reason for getting food";
  }
  if ($reasonID == UNEMPLOYED_REASON_ID && empty($unempDate))
  {
    $_SESSION['errors'][] = "Unemployment date";
  }
  if ($reasonID == OTHER_REASON_ID && empty($explanation))
  {
    $_SESSION['errors'][] = "Explanation of reason";
  }
  if (!isset($receivesStamps))
  {
    $_SESSION['errors'][] = "Current food stamp status";
  }
  if ($receivesStamps == NOT_ON_FOODSTAMPS && !isset($wantsStamps))
  {
    $_SESSION['errors'][] = "Interest in food stamps";
  }
  for ($i=0; $i<$_SESSION['memberCount']; $i++)
  {
    $j = $i+1;
    if (empty($sessionFamily[$i]["age"]))
    {
      $_SESSION['errors'][] = "Child {$j} age";
    }
    if (empty($sessionFamily[$i]["gender"]))
    {
      $_SESSION['errors'][] = "Child {$j} gender";
    }
    if (empty($sessionFamily[$i]["ethnicity"]))
    {
      $_SESSION['errors'][] = "Child {$j} ethnicity";
    }
  }
  if (!empty($_SESSION['errors']))
  {
    header('Location: ../clientConfirm.php');
    exit();
  }
	
  $house = NULL;
  $houseSaveFail = FALSE;
  //Only bother with a house if an address was given - at this point, if any part is given, all (except line2) are
  if (!empty($streetNumber))
  {
    if ($_SESSION['houseID'] === "new")
    {
      //If it's an edit and the old is invalid, grab the old house for reuse if it ever existed
      if ($edit && empty($_SESSION['oldAddressValid']) && $client->getHouseID())
      {
        $house = House::getHouseByID($client->getHouseID());
      }
      //If they chose a new house and the old address is valid, or the houseID was null, create a new house.
      else
      {
        $house = House::create();
      }
      $house->setStreetNumber($streetNumber);
      $house->setStreetName($streetName);
      $house->setStreetType($streetType);
      $house->setLine2($line2);
      $house->setCity($city);
      $house->setZip($zip);
    }
    //If they chose an old house, simply point the client to it.
    else
    {
      $house = House::getHouseByID($_SESSION['houseID']);
    }
    //If the insert failed then presumably the house already exists
    if ($house->save() === FALSE)
    {
      $house = House::searchByAddress($streetNumber, $streetName, $streetType, $line2, $city, $zip);
      if ($house === NULL)
      {
        //Couldn't find that house, so the insert failed for some other reason. Raise an error
        $houseSaveFail = TRUE;
      }
    }
    if ($houseSaveFail)
    {
			$_SESSION['errors'][] = "There was an error adding the address to the database.";
      header('Location: ../clientConfirm.php');
      exit();
    }
  }

  //oldHouseID keeps track of the user's previous houseID, if any,
  //so it's possible to delete it if it's not referenced anymore.
  $oldHouseID = '';
  if ($edit)
  {
    $oldHouseID = $client->getHouseID();
  }
  else
  {
    $client = Client::create();
  }
	$client->setSpouseID($spouseID);
  $client->setFirstName($first);
  $client->setLastName($last);
  $client->setApplicationDate($appDate);
  $client->setAge($age);
  $client->setPhoneNumber($phone);
  $client->setEthnicityID($ethnicityID);
  $client->setGenderID($genderID);
  $client->setReasonID($reasonID);
  $client->setExplanation($explanation);
  $client->setUnemploymentDate($unempDate);
  $client->setReceivesStamps($receivesStamps);
	
  if ($client->getReceivesStamps())
  {
    $client->setWantsStamps(NULL);
  }
  else
  {
    $client->setWantsStamps($wantsStamps);
  }
  
  if ($house === NULL)
  {
    $client->setHouseID(NULL);
  }
  else
  {
    $client->setHouseID($house->getHouseID());
  }
  //Client save failed
  if (!$client->save())
  {
    $client->discard();
    $_SESSION['errors'][] = "There was an error adding the client to the database.";
    header("Location: ../clientConfirm.php");
    exit();
  }
  if ($client->getSpouseID() === NULL)
	{
		$client->ensureClientNotListedAsSpouse();
	}
  //Now that we can build proper family member objects, convert the session variables to
  //family member objects and stick it in the new family members array
  for ($i=0; $i<$_SESSION['memberCount']; $i++)
  {
    $familyMember = FamilyMember::create();
    if($reasonID != HOMELESS_REASON_ID)
    {
      $familyMember->setHouseID($house->getHouseID());
      $familyMember->setGuardianID(NULL);
    }
    else
    {
      $familyMember->setHouseID(NULL);
      $familyMember->setGuardianID($client->getClientID());
    }
    $familyMember->setAge($sessionFamily[$i]["age"]);
    $familyMember->setGenderID($sessionFamily[$i]["gender"]);
    $familyMember->setEthnicityID($sessionFamily[$i]["ethnicity"]);
    $newFamilyMembers[] = $familyMember;
  }
  Client::deleteHouseIfNotReferenced($oldHouseID);
  $adminID = $_SESSION['adminID'];
	$timeout = $_SESSION['timeout'];
	$_SESSION = NULL;
	$_SESSION['adminID'] = $adminID;
	$_SESSION['timeout'] = $timeout;
  foreach($newFamilyMembers as $familyMember)
  {
    $familyMember->save();
  }
  foreach($oldFamilyMembers as $familyMember)
  {
    $familyMember->delete();
  }
	$action = ($edit)? "edited" : "created";
	$_SESSION['errors'][] = "Client {$client->getClientID()} successfully {$action}.";
	header("Location: ../search.php");


