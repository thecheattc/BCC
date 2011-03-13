<?php
  
  class Gender
  {
    private $genderID;
    private $genderDesc;
    
    //If an gender is retrieved from the database, flag it as such
    private $createdFromDB = true;
    
    //This flag keeps track of whether or not this object is inconsistent
    //with the database and needs to be written back to the database on
    //destruction
    private $dirty = false;
    
    //No setter for genderID because that should be handled internally
    public function getGenderID()
    {
      return $this->genderID;
    }
    
    public function getGenderDesc()
    {
      return $this->genderDesc;
    }
    
    public function setGenderDesc($val)
    {
      $this->dirty = true;
      $this->genderDesc = $val;
      return $this->genderDesc;
    }
        
    public function __destruct()
    {
      if($this->dirty)
      {
        $this->save();
      }
    }
    
    //Creates a new gender
    public static function create()
    {
      $gender = new Gender();
      $gender->createdFromDB = FALSE;
      return $gender;
    }
    
    //Deletes a gender from the database
    public function delete()
    {
      // Ensure DB Connection
      SQLDB::connect();
      
      $query = "DELETE FROM bcc_food_client.genders WHERE gender_id = '{$this->genderID}'";
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
      $genderDescParam = mysql_real_escape_string($this->genderDesc);
      $query = "";
      
      //If this gender already existed in the database, update it
      if($this->createdFromDB)
      {
        $query = "UPDATE bcc_food_client.genders SET ";
        $query .= "gender_desc='{$genderDescParam}' ";
        $query .= "WHERE gender_id = '{$this->genderID}'";
      }
      //If the gender was freshly created, insert it into the database.
      else
      {
        $query = "INSERT INTO bcc_food_client.genders (gender_desc) ";
        $query .= "VALUES ('{$genderDescParam}')";
      }
      
      
      $result = mysql_query($query);
      
      if ($result !== FALSE)
      {
        //If the update or insert was successful, this object is now consistent with the database
        //so if it's deleted we don't need to update the database
        $this->dirty = FALSE;
        
        if(!$this->createdFromDB)
        {
          $this->genderID = mysql_insert_id();
        }
      }
      return $result;
    }
    
    
    //Creates a new gender given a row from the bcc_food_client.genders table
    private static function createFromSQLRow($row)
    {
      $gender = new Gender();
      $gender->genderID = $row["gender_id"];
      $gender->genderDesc = $row["gender_desc"];
      $gender->createdFromDB = true;
      $gender->dirty = false;
      return $gender;
    }
    
    public static function getAllGenders()
    {
      SQLDB::connect();
      
      $query = "SELECT gender_id, gender_desc ";
      $query .= "FROM bcc_food_client.genders ";
      
      $result = mysql_query($query);
      
      $genders = array();
      while ($row = mysql_fetch_array($result))
      {
        $genders[] = Gender::createFromSQLRow($row);
      }
      
      return $genders;
    }
    
    //Returns a gender object given a gender ID, or null if none found
    public static function getGenderByID($genderID)
    {
      SQLDB::connect();
      
      $genderID = mysql_real_escape_string($genderID);
      
      $query = "SELECT gender_id, gender_desc ";
      $query .= "FROM bcc_food_client.genders ";
      $query .= "WHERE gender_id = '{$genderID}'";
      
      $result = mysql_query($query);
      
      $gender = NULL;
      if ($row = mysql_fetch_array($result))
      {
        $gender = Gender::createFromSQLRow($row);
      }
      
      return $gender;
    }
    
  }
