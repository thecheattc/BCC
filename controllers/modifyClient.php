<?php
  include ('utility.php');
  include ('../models/house.php');
  include ('../models/client.php');
  include ('../models/sqldb.php');
  
  define("UNEMPLOYED_REASON_ID", 1);
  
  /**** Need to be careful with absolute references to IDs! ****/
  /**** Should we allow multiple reasons? How to deal with family members? ****/
  /**** Can make the error handling nicer once the functionality is there ****/
  
  $edit = (!empty($_GET['edit']) && $_GET['edit'] == 1 && !empty($_GET['client']))? TRUE : FALSE;
  $house = NULL;
  $client = NULL;
  if ($edit)
  {
    $client = Client::getClientByID($_GET['client']);
    if ($client === NULL)
    {
      header("Location: ../search.php");
    }
    else
    {
      if ($client->getHouseID() !== NULL)
      {
        $house = House::getHouseByID($client->getHouseID());
      }
    }
  }
  /*
  echo "<PRE>";
  echo "Date: "; var_dump($_POST['appDate']);
  echo "First: "; var_dump($_POST['firstName']);
  echo "Last: "; var_dump($_POST['lastName']);
  echo "Street: "; var_dump($_POST['address']); 	
  echo "City: "; var_dump($_POST['city']);
  echo "Zip: "; var_dump($_POST['zip']);
  echo "Phone: "; var_dump($_POST['number']);
  echo "Age: "; var_dump($_POST['age']);
  echo "GenderID: "; var_dump($_POST['gengroup']);
  echo "EthnicityID: "; var_dump($_POST['ethgroup']);
  echo "ReasonID: "; var_dump($_POST['reasongroup']);
  echo "UnempDate: "; var_dump($_POST['uDate']);
  echo "\n";
  */
  $date = normalDateToMySQL($_POST['appDate']);
  $first = processString($_POST['firstName']);
  $last = processString($_POST['lastName']);
  $address = processString($_POST['address']);
  $city = processString($_POST['city']);
  $zip = processString($_POST['zip']);
  $phone = processString($_POST['number']);
  $age = processString($_POST['age']);
  $genderID = processString($_POST['gengroup']);
  $ethnicityID = processString($_POST['ethgroup']);
  $reasonID = processString($_POST['reasongroup']);
  $unempDate = NULL;
  
  if ($reasonID == UNEMPLOYED_REASON_ID)
  {
    $unempDate = normalDateToMySQL($_POST['uDate']);
  }
  /*
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
   */
  
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
    //echo "Bad input";
    header('Location: ../dataEntry.php?error=1');
  }
  else
  {
    $error = false;
    //If the address is given, create an entry in the database for their house.
    if ($count === 3)
    {
      //If it's a new client or the client previously had no address, create a house
      if (!$edit || $client->getHouseID() === NULL)
      {
        $house = House::create();
      }
      $house->setAddress($address);
      $house->setCity($city);
      $house->setZip($zip);
      
      //If the insert failed then presumably the house already exists
      if ($house->save() === FALSE)
      {
        //echo "House save failed";
        $house = House::searchByAddress($address, $city, $zip);
        if ($house === NULL)
        {
          //Couldn't find that house, so the insert failed for some other reason. Raise an error
          //echo "Search by address failed";
          $error = TRUE;
        }
      }
    }
    if ($error)
    {
      header('Location: ../dataEntry.php?error=1');
    }
    else
    {
      if (!$edit)
      {
        $client = Client::create();
      }
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
        //echo "Client save failed";
        if ($edit)
        {
          header("Location: ../editClient.php?client={$client->getClientID()}&error=1");
        }
        else
        {
          header("Location: ../dataEntry.php?error=1");
        }
      }
      else
      {
        if ($edit)
        {
          header("Location: ../editClient.php?client={$client->getClientID()}&success=1");
        }
        else
        {
          header("Location: ../dataEntry.php?success=1");
        }
      }
    }
  }
