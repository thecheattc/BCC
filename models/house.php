<?php
  
  class House
  {
    private $houseID;
    private $streetNumber;
    private $streetName;
    private $streetType;
    private $line2;
    private $city;
    private $zip;
    
    //If a house is retrieved from the database, flag it as such
    private $createdFromDB = true;
    
    //This flag keeps track of whether or not this object is inconsistent
    //with the database and needs to be written back to the database on
    //destruction
    private $dirty = false;
    
    //No setter for houseID because that should be handled internally
    public function getHouseID()
    {
      return $this->houseID;
    }
    
    public function getAddress()
    {
      return $this->address;
    }
    
    public function setAddress($val)
    {
      $this->dirty = true;
      $this->address = $val;
      return $this->address;
    }
   
    public function getStreetNumber()
    {
      return $this->streetNumber;
    }
    
    public function setStreetNumber($val)
    {
      $this->dirty = true;
      $this->streetNumber = $val;
      return $this->streetNumber;
    }
    
    public function getStreetName()
    {
      return $this->streetName;
    }
    
    public function setStreetName($val)
    {
      $this->dirty = true;
      $this->streetName = $val;
      return $this->streetName;
    }
    
    public function getStreetType()
    {
      return $this->streetType;
    }
    
    public function setStreetType($val)
    {
      $this->dirty = true;
      $this->streetType = $val;
      return $this->streetType;
    }
    
    public function getLine2()
    {
      return $this->line2;
    }
    
    public function setLine2($val)
    {
      $this->dirty = true;
      $this->line2 = $val;
      return $this->line2;
    }
    
    public function getCity()
    {
      return $this->city;
    }
    
    public function setCity($val)
    {
      $this->dirty = true;
      $this->city = $val;
      return $this->city;
    }
    
    public function getZip()
    {
      return $this->zip;
    }
    
    public function setZip($val)
    {
      $this->dirty = true;
      $this->zip = $val;
      return $this->zip;
    }
    
    public function __destruct()
    {
      if($this->dirty)
      {
        $this->save();
      }
    }
    
    //Creates a new house
    public static function create()
    {
      $house = new House();
      $house->createdFromDB = FALSE;
      return $house;
    }
    
    //Deletes a house from the database
    public function delete()
    {
      // Ensure DB Connection
      SQLDB::connect("bcc_food_client");
      
      $query = "DELETE FROM bcc_food_client.houses WHERE house_id = '{$this->houseID}'";
      $result = mysql_query($query);
      $this->discard();
      
      return $result;
    }
    
    // Call this to ignore changes made so that no change in database
    // is reflected.  This should be the last thing done with a discarded object.
    public function discard()
    {
      $this->dirty = false;
    }
    
    //Updates the database to reflect this object's data
    public function save()
    {
      //Ensure connection to the database
      SQLDB::connect("bcc_food_client");
      
      //Sanitize user-generated input
      $streetNumberParam = mysql_real_escape_string($this->streetNumber);
      $streetNameParam = mysql_real_escape_string($this->streetName);
      $streetTypeParam = (mysql_real_escape_string($this->streetType) === '')? "NULL" : "'" . mysql_real_escape_string($this->streetType) . "'";
      $line2Param = (mysql_real_escape_string($this->line2) === '')? "NULL" : "'" . mysql_real_escape_string($this->line2) . "'";
      $cityParam = mysql_real_escape_string($this->city);
      $zipParam = mysql_real_escape_string($this->zip);
      $query = "";
      
      //If this house already existed in the database, update it
      if($this->createdFromDB)
      {
        $query = "UPDATE bcc_food_client.houses SET ";
        $query .= "street_number='{$streetNumberParam}', ";
        $query .= "street_name='{$streetNameParam}', ";
        $query .= "street_type=" . $streetTypeParam . ", ";
        $query .= "line2=" . $line2Param . ", ";
        $query .= "city='{$cityParam}', ";
        $query .= "zip='{$zipParam}' ";
        $query .= "WHERE house_id = '{$this->houseID}'";
      }
      //If the house was freshly created, insert it into the database.
      else
      {
        $query = "INSERT INTO bcc_food_client.houses (street_number, street_name, street_type, line2, city, zip) ";
        $query .= "VALUES ('{$streetNumberParam}', '{$streetNameParam}', ";
        $query .= $streetTypeParam . ", " . $line2Param . ", '{$cityParam}', '{$zipParam}')";
      }
      $result = mysql_query($query);
        if ($result !== FALSE)
      {
        //If the update or insert was successful, this object is now consistent with the database
        //so if it's deleted we don't need to update the database
        $this->dirty = FALSE;
        
        if(!$this->createdFromDB)
        {
          $this->houseID = mysql_insert_id();
        }
      }
      return $result;
    }
    
    
    //Creates a new house given a row from the bcc_food_client.houses table
    private static function createFromSQLRow($row)
    {
      $house = new House();
      $house->houseID = $row["house_id"];
      $house->streetNumber = $row["street_number"];
      $house->streetName = $row["street_name"];
      $house->streetType = $row["street_type"];
      $house->line2 = $row["line2"];
      $house->city = $row["city"];
      $house->zip = $row["zip"];
      $house->createdFromDB = true;
      $house->dirty = false;
      return $house;
    }
    
    //Returns a house object given a house ID, or null if none found
    public static function getHouseByID($houseID)
    {
      SQLDB::connect("bcc_food_client");
      
      $houseID = mysql_real_escape_string($houseID);
      
      $query = "SELECT house_id, street_number, street_name, street_type, line2, city, zip ";
      $query .= "FROM bcc_food_client.houses ";
      $query .= "WHERE house_id = '{$houseID}'";
      
      $result = mysql_query($query);
      
      $house = NULL;
      if ($row = mysql_fetch_array($result))
      {
        $house = House::createFromSQLRow($row);
      }
      
      return $house;
    }
    
    //Returns a house given all fields
    public static function searchByAddress($streetNumber, $streetName, $streetType, $line2, $city, $zip)
    {
      SQLDB::connect("bcc_food_client");
      
      $streetNumber = mysql_real_escape_string(processString($streetNumber, TRUE));
      $streetName = mysql_real_escape_string(processString($streetName));
      $streetType = mysql_real_escape_string(processString($streetType));
      $line2 = mysql_real_escape_string(processString($line2));
      $city = mysql_real_escape_string(processString($city));
      $zip = mysql_real_escape_string(processString($zip));
      
      $query = "SELECT house_id, street_number, street_name, street_type, line2, city, zip ";
      $query .= "FROM bcc_food_client.houses ";
      $query .= "WHERE street_number = '{$streetNumber}' AND street_name = '{$streetName}' AND ";
      $query .= "street_type = '{$streetType}' AND line2 = '{$line2}' AND city = '{$city}' AND zip = '{$zip}'";
      
      $result = mysql_query($query);
      $house = NULL;
      
      if ($row = mysql_fetch_array($result))
      {
        $house = House::createFromSQLRow($row);
      }
      
      return $house;
    }
    
    
    //Returns an array of houses matching the given streetNumber and streetName.
    public static function searchAddresses($streetNumber = '', $streetName = '')
    {
      SQLDB::connect("bcc_food_client");
      
      $streetNumber = mysql_real_escape_string(processString($streetNumber, TRUE));
      $streetName = mysql_real_escape_string(processString($streetName));
      
      $query = "SELECT house_id, street_number, street_name, street_type, line2, city, zip ";
      $query .= "FROM bcc_food_client.houses ";
      $query .= "WHERE street_number LIKE '{$streetNumber}' OR street_name LIKE '{$streetName}'";
      
      $result = mysql_query($query);
      $houses = array();
      
      while ($row = mysql_fetch_array($result))
      {
        $house = array();
        $house['houseID'] = $row['house_id'];
        $house['streetNumber'] = $row['street_number'];
        $house['streetName'] = $row['street_name'];
        $house['streetType'] = $row['street_type'];
        $house['line2'] = $row['line2'];
        $house['city'] = $row['city'];
        $house['zip'] = $row['zip'];
        $houses[] = $house;
      }
      
      return $houses;
    }
    
  }

?>