<?php
  session_start();
  include_once('utility.php');
  include_once('../models/house.php');
  include_once('../models/client.php');
  include_once('../models/sqldb.php');
  include_once('../models/familyMember.php');
  
  define("UNEMPLOYED_REASON_ID", 1);
  define("NOT_ON_FOODSTAMPS", 0);
    
  /**** If two people live at the same house and one changes their address, both people 
   **** will reflect this change. This is fine since the paperwork is only filled out once a year ****/
  
  //Grab all the kids related to a client.
  //If a house is given, tie the array of kids in session to the house
  //Else tie the array of kids in session to the client
  //Insert all the new kids
  //Run through and delete all the old
      
  $edit = (!empty($_SESSION['edit']))? TRUE : FALSE;
  $house = NULL;
  $client = NULL;
  $newFamilyMembers = array();
  $oldFamilyMembers = array();
  if ($edit)
  {
    $client = Client::getClientByID($_SESSION['client']);
    if ($client === NULL)
    {
      header("Location: ../search.php");
    }
    else
    {
      $oldFamilyMembers = $client->getAllFamilyMembers();
      if ($client->getHouseID() !== NULL)
      {
        $house = House::getHouseByID($client->getHouseID());
      }
    }
  }
  /*
  echo "<PRE>";
  echo "Date: "; var_dump($_SESSION['appDate']);
  echo "First: "; var_dump($_SESSION['firstName']);
  echo "Last: "; var_dump($_SESSION['lastName']);
  echo "Street: "; var_dump($_SESSION['address']); 	
  echo "City: "; var_dump($_SESSION['city']);
  echo "Zip: "; var_dump($_SESSION['zip']);
  echo "Phone: "; var_dump($_SESSION['number']);
  echo "Age: "; var_dump($_SESSION['age']);
  echo "GenderID: "; var_dump($_SESSION['gengroup']);
  echo "EthnicityID: "; var_dump($_SESSION['ethgroup']);
  echo "ReasonID: "; var_dump($_SESSION['reasongroup']);
  echo "Reason explanation: "; var_dump($_SESSION['explanation']);
  echo "UnempDate: "; var_dump($_SESSION['uDate']);
  echo "Receives stamps: "; var_dump($_SESSION['receivesStamps']);
  echo "Wants stamps: "; var_dump($_SESSION['wantsStamps']);
  echo "\n";
   */
  
  $appDate = $_SESSION['appDate'];
  $first = processString($_SESSION['firstName']);
  $last = processString($_SESSION['lastName']);
  $address = processString($_SESSION['address']);
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
  /*
  echo "Date: "; var_dump($appDate);
  echo "First: "; var_dump($first);
  echo "Last: "; var_dump($last);
  echo "Street: "; var_dump($address);
  echo "City: "; var_dump($city);
  echo "Zip: "; var_dump($zip);
  echo "Phone: "; var_dump($phone);
  echo "Age: "; var_dump($age);
  echo "GenderID: "; var_dump($genderID);
  echo "EthnicityID: "; var_dump($ethnicityID);
  echo "ReasonID: "; var_dump($reasonID);
  echo "UnempDate: "; var_dump($unempDate);
  echo "Receives stamps: "; var_dump($receivesStamps);
  echo "Wants stamps: "; var_dump($wantsStamps);
  echo "</PRE>";
  */
  
  //If any part of the address is listed, all parts must be.
  $badAddr = FALSE;
  $count = 0;
  if (!empty($address)){ $count++;}
  if (!empty($city)){ $count++;}
  if (!empty($zip)){ $count++;}
  if ($count > 0 && $count < 3){ $badAddr = TRUE;}
  
  $errors = array();
  if ($badAddr)
  {
    $errors[] = "Address";
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
  if (!isset($genderID))
  {
    $errors[] = "Gender";
  }
  if (!isset($ethnicityID))
  {
    $errors[] = "Ethnicity";
  }
  if (!isset($reasonID))
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
    header('Location: ../dataEntry.php');
  }
  else
  {
    $houseSaveFail = false;
    //If the address is given, create an entry in the database for their house.
    if ($count === 3)
    {
      //If it's a new client, the client previously had no address, or 
      //they moved to a new house but the old house still has people registered with BCC in it,
      //create a house
      if (!$edit || $client->getHouseID() === NULL || $oldAddressValid)
      {
        $house = House::create();
      }
      $house->setAddress($address);
      $house->setCity($city);
      $house->setZip($zip);
      
      //If the insert failed then presumably the house already exists
      if ($house->save() === FALSE)
      {
        $house = House::searchByAddress($address, $city, $zip);
        if ($house === NULL)
        {
          //Couldn't find that house, so the insert failed for some other reason. Raise an error
          $houseSaveFail = TRUE;
        }
      }
    }
    //If it's an edit and no address was given, set the house (and in doing so, the client's house id) to NULL.
    else if ($edit && $count === 0)
    {
      $house = NULL;
    }
    //House insertion failed
    if ($houseSaveFail)
    {
      header('Location: ../dataEntry.php');
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
        header("Location: ../dataEntry.php");
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
          if (!$familyMember->delete())
          {
            echo "wut wut in the butt";
          }
        }
        if ($edit)
        {          
          header("Location: ../dataEntry.php?success=1&edit=1");
        }
        else
        {
          header("Location: ../dataEntry.php?success=1&edit=0");
        }
      }
    }
  }
