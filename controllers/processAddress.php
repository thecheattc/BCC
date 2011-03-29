<?php
  session_start();
  include ('utility.php');
  include ('../models/house.php');
  include ('../models/sqldb.php');
  
  //For processing the next step, there are three options:
  //Session clientID is "new". For this, create a new address.
  //Session clientID is something other than new, and oldAddressValid is false. For this, edit the house in the DB.
  //Session clientID is something other than new, and oldAddressValid is true. For this, create a new address.
  /*
  echo "<PRE>";
  var_dump($_SESSION);
  echo "\n\n\n";
  var_dump($_POST);
  echo "</PRE>";
   */
  
  if (empty($_SESSION['haveSearched']))
  {
    $_SESSION['streetNumber'] = $_POST['streetNumber'];
    $_SESSION['streetName'] = $_POST['streetName'];
    $_SESSION['streetType'] = $_POST['streetType'];
    $_SESSION['city'] = $_POST['city'];
    $_SESSION['zip'] = $_POST['zip'];
    $_SESSION['oldAddressValid'] = $_POST['oldAddressValid'];
    $houses = House::searchAddresses($_POST['streetNumber'], $_POST['streetName']);
    if ($houses === NULL)
    {
      $houses = array();
    }
    foreach ($houses as $sessHouse)
    {
      
    }
    $_SESSION['matches'] = $houses;
    $_SESSION['haveSearched'] = TRUE;
    header("Location: ../addressEntry.php");
   }
  else
  {
    if (empty($_POST['houseID']))
    {
      if (empty($_SESSION['errors']))
      {
        $_SESSION['errors'] = array();
      }
      $_SESSION['errors'][] = "Please choose an address from the list.";
      header("Location: addressEntry.php");
    }
    else
    {
      if ($_POST['houseID'] !== "new")
      {
        $house = House::getHouseByID($_POST['houseID']);
        if ($house === NULL)
        {
          if (empty($_SESSION['errors']))
          {
            $_SESSION['errors'] = array();
          }
          $_SESSION['errors'][] = "The selected address couldn't be retrieved from the database.";
          $_SESSION['houseID'] = NULL;
          header("Location: addressEntry.php");
        }
        else
        {
          $_SESSION['houseID'] = $house->getHouseID();
          $_SESSION['streetNumber'] = $house->getStreetNumber();
          $_SESSION['streetName'] = $house->getStreetName();
          $_SESSION['streetType'] = $house->getStreetType();
          $_SESSION['city'] = $_POST['city'];
          $_SESSION['zip'] = $_POST['zip'];
          header("Location: clientEntry.php");
        }
      }
      else
      {
        $_SESSION['houseID'] = $_POST['houseID'];
        header("Location: clientEntry.php");
      }
    }
    
  }
  
