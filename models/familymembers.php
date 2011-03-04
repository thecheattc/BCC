<?php
  
  class FamilyMember
  {
    private $famMemberID;
    private $guardianClientID;
    private $age;
    private $genderID;
    
    //If a family member is retrieved from the database, flag it as such
    private $createdFromDB = true;
    
    //This flag keeps track of whether or not this object is inconsistent
    //with the database and needs to be written back to the database on
    //destruction
    private $dirty = false;
    
    //No setter for family member ID because that should be handled internally
    public function getFamMemberID()
    {
      return $this->famMemberID;
    }
    
    public function getGuardianClientID()
    {
      return $this->guardianClientID;
    }
    
    public function setGuardianClientID($val)
    {
      $this->dirty = true;
      $this->guardianClientID = $val;
      return $this->guardianClientID;
    }
    
    public function getAge()
    {
      return $this->age;
    }
    
    public function setAge($val)
    {
      $this->dirty = true;
      $this->age = $val;
      return $this->age;
    }
    
    public function getGenderID()
    {
      return $this->genderID;
    }
    
    public function setGenderID($val)
    {
      $this->dirty = true;
      $this->genderID = $val;
      return $this->genderID;
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
      $famMember = new FamilyMember();
      $famMember->createdFromDB = FALSE;
      return $famMember;
    }
    
    //Deletes a house from the database
    public function delete()
    {
      // Ensure DB Connection
      SQLDB::connect();
      
      $query = "DELETE FROM bcc_food_client.family_members WHERE fam_member_id = '{$this->famMemberID}'";
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
      $guardianParam = mysql_real_escape_string($this->guardianClientID);
      $ageParam = mysql_real_escape_string($this->age);
      $genderParam = mysql_real_escape_string($this->genderID);
      $query = "";
      
      //If this house already existed in the database, update it
      if($this->createdFromDB)
      {
        $query = "UPDATE bcc_food_client.family_members SET ";
        $query .= "guardian_client_id='{$guardianParam}', ";
        $query .= "age='{$ageParam}', ";
        $query .= "gender_id='{$genderParam}', ";
        $query .= "WHERE fam_member_id = '{$this->famMemberID}'";
      }
      //If the house was freshly created, insert it into the database.
      else
      {
        $query = "INSERT INTO bcc_food_client.family_members (guardian_client_id, age, gender_id) ";
        $query .= "VALUES ('{$guardianParam}', '{$ageParam}', '{$genderParam}')";
      }
      
      
      $result = mysql_query($query);
      
      if ($result !== FALSE)
      {
        //If the update or insert was successful, this object is now consistent with the database
        //so if it's deleted we don't need to update the database
        $this->dirty = FALSE;
        
        if(!$this->createdFromDB)
        {
          $this->famMemberID = mysql_insert_id();
        }
      }
      return $result;
    }
    
    
    //Creates a new house given a row from the bcc_food_client.family_members table
    private static function createFromSQLRow($row)
    {
      $member = new FamilyMember();
      $member->famMemberID = $row["family_member_id"];
      $member->guardianClientID = $row["guardian_client_id"];
      $member->age = $row["age"];
      $member->genderID = $row["gender_id"];
      $member->createdFromDB = true;
      $member->dirty = false;
      return $member;
    }
    
    //Returns a family member object given a family member ID, or null if none found
    public static function getFamilyMemberByID($memberID)
    {
      SQLDB::connect();
      
      $memberID = mysql_real_escape_string($memberID);
      
      $query = "SELECT family_member_id, guardian_client_id, age, gender_id ";
      $query .= "FROM bcc_food_client.family_members ";
      $query .= "WHERE family_member_id = '{$memberID}'";
      
      $result = mysql_query($query);
      
      $member = NULL;
      if ($row = mysql_fetch_array($result))
      {
        $member = FamilyMember::createFromSQLRow($row);
      }
      
      return $member;
    }
    
    //Returns an array of family member objects given a client ID
    public static function getFamilyMembersByClientID($clientID)
    {
      SQLDB::connect();
      
      $clientID = mysql_real_escape_string($clientID);
      
      $query = "SELECT family_member_id, guardian_client_id, age, gender_id ";
      $query .= "FROM bcc_food_client.family_members ";
      $query .= "WHERE guardian_client_id = '{$clientID}'";
      
      $result = mysql_query($query);
      
      $members = array();
      if ($row = mysql_fetch_array($result))
      {
        $members[] = FamilyMember::createFromSQLRow($row);
      }
      
      return $members;
    }
    
  }
