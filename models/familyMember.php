<?php
  
  class FamilyMember
  {
    //Ordinarily, a family member is tied to a house.
    //In the case that the person registered with Bryant is homeless,
    //the family member is tied to the person. Therefore, the
    //houseID or the guardianID can be null, but not both.
    private $famMemberID;
    private $guardianID;
    private $houseID;
    private $ethnicityID;
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
    
    public function getGuardianID()
    {
      return $this->guardianID;
    }
    
    public function setGuardianID($val)
    {
      $this->dirty = true;
      $this->guardianID = $val;
      return $this->guardianID;
    }
    
    public function getHouseID()
    {
      return $this->houseID;
    }
    
    public function setHouseID($val)
    {
      $this->dirty = true;
      $this->houseID = $val;
      return $this->houseID;
    }
    
    public function getEthnicityID()
    {
      return $this->ethnicityID;
    }
    
    public function setEthnicityID($val)
    {
      $this->dirty = true;
      $this->ethnicityID = $val;
      return $this->ethnicityID;
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
      $houseIDParam = NULL;
      if (mysql_real_escape_string($this->houseID) === '')
      {
        $houseIDParam = "NULL";
      }
      else
      {
        $houseIDParam = "'" . mysql_real_escape_string($this->houseID) . "'";
      }
      $guardianIDParam = NULL;
      if (mysql_real_escape_string($this->guardianID) === '')
      {
        $guardianIDParam = "NULL";
      }
      else
      {
        $guardianIDParam = "'" . mysql_real_escape_string($this->guardianID) . "'";
      }
      $ethnicityIDParam = mysql_real_escape_string($this->ethnicityID);
      $ageParam = mysql_real_escape_string(processString($this->age));
      $genderParam = mysql_real_escape_string($this->genderID);
      $query = "";
      
      //If this house already existed in the database, update it
      if($this->createdFromDB)
      {
        $query = "UPDATE bcc_food_client.family_members SET ";
        $query .= "member_house_id=" . $houseIDParam . ", ";
        $query .= "guardian_id=" . $guardianIDParam . ", ";
        $query .= "ethnicity_id='{$ethnicityIDParam}', ";
        $query .= "age='{$ageParam}', ";
        $query .= "gender_id='{$genderParam}', ";
        $query .= "WHERE fam_member_id = '{$this->famMemberID}'";
      }
      //If the house was freshly created, insert it into the database.
      else
      {
        $query = "INSERT INTO bcc_food_client.family_members (member_house_id, guardian_id, ethnicity_id, age, gender_id) ";
        $query .= "VALUES (" . $houseIDParam . ", " .$guardianIDParam . ", '{$ethnicityIDParam}', '{$ageParam}', '{$genderParam}')";
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
      $member->famMemberID = $row["fam_member_id"];
      $member->houseID = $row["member_house_id"];
      $member->guardianID = $row["guardian_id"];
      $member->ethnicityID = $row["ethnicity_id"];
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
      
      $query = "SELECT fam_member_id, member_house_id, guardian_id, ethnicity_id, age, gender_id ";
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
    
    //Returns an array of family member objects given an ID - if the $byHouse flag is set,
    //the family members are retrieved if their member_house_id matches the given id, otherwise
    //they're retrieved if their guardian_id matches the given id.
    public static function getAllFamilyMembersForClient($ID, $byHouse)
    {
      SQLDB::connect();
      
      $ID = mysql_real_escape_string($ID);
      
      $query = "SELECT fam_member_id, member_house_id, guardian_id, ethnicity_id, age, gender_id ";
      $query .= "FROM bcc_food_client.family_members ";
      if ($byHouse)
      {
        $query .= "WHERE member_house_id = '{$ID}'";
      }
      else
      {
        $query .= "WHERE guardian_id = '{$ID}'";
      }
      $result = mysql_query($query);
      
      $members = array();
      while ($row = mysql_fetch_array($result))
      {
        $members[] = FamilyMember::createFromSQLRow($row);
      }
      
      return $members;
    }
    
    //Deletes all family members that are not tied to a house or a client
    public static function cleanFamilyMembers()
    {
      SQLDB::connect();
      
      $query = "DELETE FROM bcc_food_client.family_members WHERE member_house_id = NULL AND guardian_id = NULL";
      $result = mysql_query($query);
      return $result;
    }
    
  }
