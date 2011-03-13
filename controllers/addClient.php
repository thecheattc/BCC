<?php
  include ('utility.php');
  include ('../models/house.php');
  include ('../models/client.php');
  include ('../models/sqldb.php');
  
  define("UNEMPLOYED_REASON_ID", 1);
  
  /**** Need to be careful with absolute references to IDs! ****/
  /**** Should we allow multiple reasons? How to deal with family members? ****/
  /**** Can make the error handling nicer once the functionality is there ****/
  
  $data = unserialize(base64_decode($_POST['formData']));  
  
  echo "<PRE>";
  echo "Date: "; var_dump($data['appDate']);
  echo "First: "; var_dump($data['firstName']);
  echo "Last: "; var_dump($data['lastName']);
  echo "Street: "; var_dump($data['address']);
  echo "City: "; var_dump($data['city']);
  echo "Zip: "; var_dump($data['zip']);
  echo "Phone: "; var_dump($data['phone']);
  echo "Age: "; var_dump($data['age']);
  echo "GenderID: "; var_dump($data['gender']);
  echo "EthnicityID: "; var_dump($data['ethnic']);
  echo "ReasonID: "; var_dump($data['reason']);
  echo "UnempDate: "; var_dump($data['udate']);
  echo "<br /> <br />";
  
  $date = processDate($data['appDate']);
  $first = processString($data['firstName']);
  $last = processString($data['lastName']);
  $address = processString($data['address']);
  $city = processString($data['city']);
  $zip = processString($data['zip']);
  $phone = processString($data['phone']);
  $age = processString($data['age']);
  $genderID = processString($data['gender']);
  $ethnicityID = processString($data['ethnic']);
  $reasonID = processString($data['reason']);
  $unempDate = NULL;
  
  if ($reasonID == UNEMPLOYED_REASON_ID)
  {
    $unempDate = processDate($data['udate']);
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
