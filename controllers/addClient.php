<?php
  include ('utility.php');
  include ('../models/house.php');
  include ('../models/client.php');
  include ('../models/sqldb.php');
  
  define("UNEMPLOYED_REASON_ID", 1);
  
  /**** Need to be careful with absolute references to IDs! ****/
  /**** Should we allow multiple reasons? How to deal with family members? ****/
  /**** Can make the error handling nicer once the functionality is there ****/
   
  echo "<PRE>";
  echo "Date: "; var_dump($_POST['date']);
  echo "First: "; var_dump($_POST['cfName']);
  echo "Last: "; var_dump($_POST['clName']);
  echo "Street: "; var_dump($_POST['cAddress']); 	
  echo "City: "; var_dump($_POST['cCity']);
  echo "Zip: "; var_dump($_POST['cZip']);
  echo "Phone: "; var_dump($_POST['cPhone']);
  echo "Age: "; var_dump($_POST['cAge']);
  echo "GenderID: "; var_dump($_POST['gengroup']);
  echo "EthnicityID: "; var_dump($_POST['ethgroup']);
  echo "ReasonID: "; var_dump($_POST['reasongroup']);
  echo "UnempDate: "; var_dump($_POST['uDate']);
  
  $date = processDate($_POST['date']);
  $first = processString($_POST['cfName']);
  $last = processString($_POST['clName']);
  $address = processString($_POST['cAddress']);
  $city = processString($_POST['cCity']);
  $zip = processString($_POST['cZip']);
  $phone = processString($_POST['cPhone']);
  $age = processString($_POST['cAge']);
  $genderID = processString($_POST['gengroup']);
  $ethnicityID = processString($_POST['ethgroup']);
  $reasonID = processString($_POST['reasongroup']);
  $unempDate = NULL;
  
  if ($reasonID == UNEMPLOYED_REASON_ID)
  {
    $unempDate = processDate($_POST['uDate']);
  }
  
  echo "Date: "; var_dump($date);
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
  echo "</PRE>";
  
  //If any part of the address is listed, all parts must be.
  $badAddr = FALSE;
  $count = 0;
  if (isset($address)){ $count++;}
  if (isset($city)){ $count++;}
  if (isset($zip)){ $count++;}
  if ($count > 0 && $count < 3){ $badAddr = TRUE;}
  
  //If the address is incomplete or any field besides the phone_number or unemployment date is empty 
  //(unemployment date has to be present when the reason is "lost job"), redirect with an error
  if ($badAddr || empty($date) || empty($first) || empty($last) ||
      empty($city) || empty($zip) || empty($age) ||
      empty($genderID) || empty($ethnicityID) || empty($reasonID) ||
      ($reasonID == UNEMPLOYED_REASON_ID && empty($unempDate)))
  {
    echo "Bad input";
    header('Location: ../dataEntry.php?error');
  }
  else
  {
    $error = false;
    //If the address is given, create an entry in the database for their house.
    $house = NULL;
    if ($count === 3)
    {
      $house = House::create();
      $house->setAddress($address);
      $house->setCity($city);
      $house->setZip($zip);
      
      //If the insert failed then presumably the house already exists
      if ($house->save() === FALSE)
      {
        echo "House save failed";
        $house = House::searchByAddress($address, $city, $zip);
        if ($house === NULL)
        {
          //Couldn't find that house, so the insert failed for some other reason. Raise an error
          echo "Search by address failed";
          $error = TRUE;
        }
      }
    }
    if ($error)
    {
      header('Location: ../dataEntry.php?error');
    }
    else
    {
      $client = Client::create();
      $client->setFirstName($first);
      $client->setLastName($last);
      $client->setApplicationDate($date);
      $client->setAge($age);
      $client->setPhoneNumber($phone);
      $client->setEthnicityID($ethnicityID);
      $client->setGenderID($genderID);
      $client->setReasonID($reasonID);
      $client->setUnemploymentDate($unempDate);
      
      if ($house === NULL)
      {
        $client->setHouseID(NULL);
      }
      else
      {
        $client->setHouseID($house->getHouseID());
      }
      
      if (!$client->save())
      {
        echo "Client save failed";
        header('Location: ../dataEntry.php?error');
      }
    }
  }
