<?php
  session_start();
  include_once('utility.php');
  include_once('../models/house.php');
  include_once('../models/client.php');
  include_once('../models/sqldb.php');
  include_once('../models/familyMember.php');
  
  define("UNEMPLOYED_REASON_ID", 1);
  define("NOT_ON_FOODSTAMPS", 0);
      
  $edit = (!empty($_SESSION['edit']))? TRUE : FALSE;
  $house = NULL;
  $client = NULL;
  $newFamilyMembers = array();
  $oldFamilyMembers = array();
  if ($edit)
  {
    $client = Client::getClientByID($_SESSION['clientID']);
    if ($client === NULL)
    {
      header("Location: ../search.php");
    }
    else
    {
      $oldFamilyMembers = $client->getAllFamilyMembers();
    }
  }
  
  $appDate = $_SESSION['appDate'];
  $first = processString($_SESSION['firstName']);
  $last = processString($_SESSION['lastName']);
  $streetNumber = processString($_SESSION['streetNumber']);
  $streetName = processString($_SESSION['streetName']);
  $streetType = processString($_SESSION['streetType']);
  $city = processString($_SESSION['city']);
  $zip = processString($_SESSION['zip']);
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
  
  //If any part of the address is listed, all parts must be.
  $badAddr = FALSE;
  $count = 0;
  if (!empty($streetNumber)){ $count++;}
  if (!empty($streetName)){ $count++;}
  if (!empty($streetType)){ $count++;}
  if (!empty($city)){ $count++;}
  if (!empty($zip)){ $count++;}
  if ($count > 0 && $count < 5){ $badAddr = TRUE;}
  
  $errors = array();
  if (empty($_SESSION['houseID']))
  {
    $errors[] = "House selection from search results";
  }
  if ($badAddr)
  {
    $errors[] = "Street number";
    $errors[] = "Street name";
    $errors[] = "Street address";
    $errors[] = "City";
    $errors[] = "Zip";
  }
  if (isset($_SESSION['edit']) && !isset($oldAddressValid))
  {
    $errors[] = "Validity of old address";
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
  }
  else
  {
    $house = NULL;
    $houseSaveFail = FALSE;
    //Only bother with a house if an address was given
    if ($count === 5)
    {
      //If the houseID is new, or the houseID is something other than new and the old adderss is still valid, make a new house.
      //Otherwise just grab the house and edit it.
      if ($_SESSION['houseID'] === "new")
      {
        $house = House::create();
      }
      else
      {
        if ($_SESSION['oldAddressValid'])
        {
          $house = House::create();
        }
        else
        {
          $house = House::getHouseByID($_SESSION['houseID']);
        }
      }
      $house->setStreetNumber($streetNumber);
      $house->setStreetName($streetName);
      $house->setStreetType($streetType);
      $house->setCity($city);
      $house->setZip($zip);
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
    }
    if ($houseSaveFail)
    {
      $errors[] = "There was an error adding the address to the database.";
      $_SESSION['errors'] = $errors;
      header('Location: ../clientConfirm.php');
    }
    else
    {
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
      $client->setWantsStamps($wantsStamps);
      
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
        echo "<PRE>";
        var_dump($_SESSION);
        echo "</PRE>";
        //header("Location: ../clientConfirm.php");
      }
      else
      {
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
      }
    }
  }
