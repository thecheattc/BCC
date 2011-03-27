<?php

  class House
  {
    private $houseID;
    private $address;
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
      SQLDB::connect();
      
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
      SQLDB::connect();
      
      //Sanitize user-generated input
      $addressParam = mysql_real_escape_string($this->address);
      $cityParam = mysql_real_escape_string($this->city);
      $zipParam = mysql_real_escape_string($this->zip);
      $query = "";
      
      //If this house already existed in the database, update it
      if($this->createdFromDB)
      {
        $query = "UPDATE bcc_food_client.houses SET ";
        $query .= "address='{$addressParam}', ";
        $query .= "city='{$cityParam}', ";
        $query .= "zip='{$zipParam}' ";
        $query .= "WHERE house_id = '{$this->houseID}'";
      }
      //If the house was freshly created, insert it into the database.
      else
      {
        $query = "INSERT INTO bcc_food_client.houses (address, city, zip) ";
        $query .= "VALUES ('{$addressParam}', '{$cityParam}', '{$zipParam}')";
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
      $house->address = $row["address"];
      $house->city = $row["city"];
      $house->zip = $row["zip"];
      $house->createdFromDB = true;
      $house->dirty = false;
      return $house;
    }
    
    //Returns a house object given a house ID, or null if none found
    public static function getHouseByID($houseID)
    {
      SQLDB::connect();
      
      $houseID = mysql_real_escape_string($houseID);
      
      $query = "SELECT house_id, address, city, zip ";
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
    
    //Returns the house that matches the given street, city, and zip.
    public static function searchByAddress($street = '', $city = '', $zip = '')
    {
      SQLDB::connect();
      
      $street = mysql_real_escape_string($street);
      $city = mysql_real_escape_string($city);
      $zip = mysql_real_escape_string($zip);
      
      $query = "SELECT house_id, address, city, zip ";
      $query .= "FROM bcc_food_client.houses ";
      $query .= "WHERE address = '{$street}' AND city = '{$city}' AND zip = '{$zip}'";
      
      
      $result = mysql_query($query);
      $house = NULL;
      
      if ($row = mysql_fetch_array($result))
      {
        $house = House::createFromSQLRow($row);
      }
      
      return $house;
    }
    
  }

?>