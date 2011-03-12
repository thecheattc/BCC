<?php
  include_once('utility.php');
  include_once('../models/house.php');
  include_once('../models/client.php');
  include_once('../models/sqldb.php');
  
  define("UNEMPLOYED_REASON_ID", 1);
  define("HOMELESS_REASON_ID", 6);
  
  /**** Need to be careful with absolute references to IDs! ****/
  /**** Should we allow multiple reasons? How to deal with family members? ****/
  /**** Some homeless have addresses listed. What? ****/
  /**** Should the date of application really be listed on the form, or should it just be implicit? ****/
  /**** Can make the error handling nicer once the functionality is there ****/
  /**** Should they enter a city and zip even if they're homeless, to track where they usually are? ****/
  /**** Add search by address to house class ****/
  /**** Need to make the date for unemployment labeled and optional ****/
  /**** processDate isn't working yet ****/
  
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
  echo "<br /> <br />";
  
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
  
  //If any field besides house_id, phone_number, or unemployment date is empty 
  //(unemployment date has to be present when the reason is "lost job", and
  //address has to be present when the reason is not "homeless"), redirect with an error
  if (empty($date) || empty($first) || empty($last) ||
      empty($city) || empty($zip) || empty($age) ||
      empty($genderID) || empty($ethnicityID) || empty($reasonID) ||
      ($reasonID == UNEMPLOYED_REASON_ID && empty($unempDate)) ||
      ($reasonID != HOMELESS_REASON_ID && empty($address)))
  {
    header('Location: ../dataEntry.php&error');
  }
  else
  {
    $error = false;
    //If they're not listed as homeless, create an entry in the database for their house.
    $house = NULL;
    if ($reasonID != HOMELESS_REASON_ID)
    {
      $house = House::create();
      $house->setAddress($address);
      $house->setCity($city);
      $house->setZip($zip);
      
      //If the insert failed, so presumably the house exists already
      if (!$house->save())
      {
        $house = House::searchByAddress($address, $city, $zip);
        if ($house === NULL)
        {
          //Couldn't find that house, so the insert failed for some other reason. Raise an error
          $error = TRUE;
        }
      }
    }
    if ($error)
    {
      header('Location: ../dataEntry.php&error');
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
      
      if ($reasonID != HOMELESS_REASON_ID)
      {
        $client->setHouseID($house->getHouseID());
      }
      else
      {
        $client->setHouseID(NULL);
      }
      
      if (!$client->save())
      {
        header('Location: ../dataEntry.php&error');
      }
    }
  }
