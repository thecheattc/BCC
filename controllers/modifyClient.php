<?php
  session_start();
  include_once('utility.php');
  include_once('../models/house.php');
  include_once('../models/client.php');
  include_once('../models/sqldb.php');
  include_once('../models/familyMember.php');
  
  define("UNEMPLOYED_REASON_ID", 1);
  define("HOMELESS_REASON_ID", 6);
  define("NOT_ON_FOODSTAMPS", 0);
  define("OTHER_REASON_ID", 7);
  
  $edit = (!empty($_SESSION['edit']))? TRUE : FALSE;
  $house = NULL;
  $client = NULL;
  $newFamilyMembers = array();
  $oldFamilyMembers = array();
  $errors = array();

  if ($edit)
  {
    $client = Client::getClientByID($_SESSION['clientID']);
    if ($client === NULL)
    {
      header("Location: ../search.php");
      exit();
    }
    else
    {
      $oldFamilyMembers = $client->getAllFamilyMembers();
    }
  }
  if (!isset($_SESSION['oldAddressValid']))
  {
    $_SESSION['oldAddressValid'] = FALSE;
  }
  
  $appDate = $_SESSION['appDate'];
  $first = processString($_SESSION['firstName']);
  $last = processString($_SESSION['lastName']);
  if ($_SESSION['houseID'] === "new")
  {
    $streetNumber = processString($_SESSION['streetNumber']);
    $streetName = processString($_SESSION['streetName']);
    $streetType = processString($_SESSION['streetType']);
    $city = processString($_SESSION['city']);
    $zip = processString($_SESSION['zip']);
  }
  else
  {
    $match = NULL;
    foreach($_SESSION['matches'] as $possible)
    {
      if ($possible['houseID'] == $_SESSION['houseID'])
      {
        $match = $possible;
      }
    }
    if ($match)
    {
      $streetNumber = processString($match['streetNumber']);
      $streetName = processString($match['streetName']);
      $streetType = processString($match['streetType']);
      $city = processString($match['city']);
      $zip = processString($match['zip']);
    }
    else
    {
      $errors[] = "There was an error processing the address given.";
      $_SESSION['errors'] = $errors;
      header("Location: clientConfim.php");
      exit();
    }
  }
  $oldAddressValid = processString($_SESSION['oldAddressValid']);
  $phone = processString($_SESSION['number']);
  $age = processString($_SESSION['age']);
  $genderID = processString($_SESSION['gengroup']);
  $ethnicityID = processString($_SESSION['ethgroup']);
  $reasonID = processString($_SESSION['reasongroup']);
  $explanation = processString($_SESSION['explanation']);
  $receivesStamps = processString($_SESSION['receivesStamps']);
  
  for ($i=0; $i<$_SESSION['memberCount']; $i++)
  {
    $_SESSION['familyMembers'][$i]["age"] = processString($_SESSION['familyMembers'][$i]["age"]);
    $_SESSION['familyMembers'][$i]["gender"] = processString($_SESSION['familyMembers'][$i]["gender"]);
    $_SESSION['familyMembers'][$i]["ethnicity"] = processString($_SESSION['familyMembers'][$i]["ethnicity"]);
  }
  
  $wantsStamps = NULL;
  $unempDate = NULL;
  
  if ($reasonID == UNEMPLOYED_REASON_ID)
  {
    $unempDate = $_SESSION['uDate'];
  }
  if ($receivesStamps == NOT_ON_FOODSTAMPS)
  {
    $wantsStamps = processString($_SESSION['wantsStamps']);
  }
  
  if ((!empty($reasonID) && $reasonID != HOMELESS_REASON_ID))
  { 
    $addressRequired = TRUE;
  }
  
  if (empty($_SESSION['houseID']))
  {
    $errors[] = "House selection from search results";
  }
  if ($addressRequired)
  {
    if (empty($streetNumber))
    {
      $errors[] = "Street number";
    }
    if (empty($streetName))
    {
      $errors[] = "Street name";
    }
    if (empty($streetType))
    {
      $errors[] = "Street type";
    }
    if (empty($city))
    {
      $errors[] = "City";
    }
    if (empty($zip))
    {
      $errors[] = "Zip";
    }
  }
  if (empty($appDate))
  {
    $errors[] = "Application date";
  }
  if (empty($first))
  {
    $errors[] = "First name";
  }
  if (empty($last))
  {
    $errors[] = "Last name";
  }
  if (!isset($age))
  {
    $errors[] = "Age";
  }
  if (empty($genderID))
  {
    $errors[] = "Gender";
  }
  if (empty($ethnicityID))
  {
    $errors[] = "Ethnicity";
  }
  if (empty($reasonID))
  {
    $errors[] = "Reason for getting food";
  }
  if ($reasonID == UNEMPLOYED_REASON_ID && empty($unempDate))
  {
    $errors[] = "Unemployment date";
  }
  if ($reasonID == OTHER_REASON_ID && empty($explanation))
  {
    $errors[] = "Explanation of reason";
  }
  if (!isset($receivesStamps))
  {
    $errors[] = "Current food stamp status";
  }
  if ($receivesStamps == NOT_ON_FOODSTAMPS && !isset($wantsStamps))
  {
    $errors[] = "Interest in food stamps";
  }
  for ($i=0; $i<$_SESSION['memberCount']; $i++)
  {
    $j = $i+1;
    if (empty($_SESSION['familyMembers'][$i]["age"]))
    {
      $errors[] = "Child {$j} age";
    }
    if (empty($_SESSION['familyMembers'][$i]["gender"]))
    {
      $errors[] = "Child {$j} gender";
    }
    if (empty($_SESSION['familyMembers'][$i]["ethnicity"]))
    {
      $errors[] = "Child {$j} ethnicity";
    }
  }
  if (!empty($errors))
  {
    $_SESSION['errors'] = $errors;
    header('Location: ../clientConfirm.php');
    exit();
  }

  $house = NULL;
  $houseSaveFail = FALSE;
  //Only bother with a house if an address was given - at this point, if any part is given, all are
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
      $house->setCity($city);
      $house->setZip($zip);
    }
    //If they chose an old house, simply point the client to it.
    else
    {
      $house = House::getHouseByID($_SESSION['houseID']);
    }
    /*
     echo "<pre>";
     var_dump($house);
     echo "</pre>";
     */
    //If the insert failed then presumably the house already exists
    if ($house->save() === FALSE)
    {
      $house = House::searchByAddress($streetNumber, $streetName, $streetType, $city, $zip);
      if ($house === NULL)
      {
        //Couldn't find that house, so the insert failed for some other reason. Raise an error
        $houseSaveFail = TRUE;
      }
    }
    if ($houseSaveFail)
    {
      $errors[] = "There was an error adding the address to the database.";
      $_SESSION['errors'] = $errors;
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
    $errors[] = "There was an error adding the client to the database.";
    $_SESSION['errors'] = $errors;
    header("Location: ../clientConfirm.php");
    exit();
  }
  
  //Now that we can build proper family member objects, convert the session variables to
  //family member objects and stick it in the new family members array
  for ($i=0; $i<$_SESSION['memberCount']; $i++)
  {
    $familyMember = FamilyMember::create();
    if($house !== NULL)
    {
      $familyMember->setHouseID($house->getHouseID());
      $familyMember->setGuardianID(NULL);
    }
    else
    {
      $familyMember->setHouseID(NULL);
      $familyMember->setGuardianID($client->getClientID());
    }
    $familyMember->setAge($_SESSION['familyMembers'][$i]["age"]);
    $familyMember->setGenderID($_SESSION['familyMembers'][$i]["gender"]);
    $familyMember->setEthnicityID($_SESSION['familyMembers'][$i]["ethnicity"]);
    $newFamilyMembers[] = $familyMember;
  }
  /*
   echo "<PRE>";
   var_dump($newFamilyMembers);
   echo "</PRE>";
   */
  Client::deleteHouseIfNotReferenced($oldHouseID);
  session_destroy();
  foreach($newFamilyMembers as $familyMember)
  {
    $familyMember->save();
  }
  foreach($oldFamilyMembers as $familyMember)
  {
    $familyMember->delete();
  }
  if ($edit)
  { 
    header("Location: ../search.php?success=1&clientEdit=1");
  }
  else
  {
    header("Location: ../search.php?success=1&clientEdit=0");
  }
