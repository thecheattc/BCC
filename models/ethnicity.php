<?php
  
  class Ethnicity
  {
    private $ethnicityID;
    private $ethnicityDesc;
    
    //If an ethnicity is retrieved from the database, flag it as such
    private $createdFromDB = true;
    
    //This flag keeps track of whether or not this object is inconsistent
    //with the database and needs to be written back to the database on
    //destruction
    private $dirty = false;
    
    //No setter for ethnicityID because that should be handled internally
    public function getEthnicityID()
    {
      return $this->ethnicityID;
    }
    
    public function getEthnicityDesc()
    {
      return $this->ethnicityDesc;
    }
    
    public function setEthnicityDesc($val)
    {
      $this->dirty = true;
      $this->ethnicityDesc = $val;
      return $this->ethnicityDesc;
    }
        
    public function __destruct()
    {
      if($this->dirty)
      {
        $this->save();
      }
    }
    
    //Creates a new ethnicity
    public static function create()
    {
      $ethnicity = new Ethnicity();
      $ethnicity->createdFromDB = FALSE;
      return $ethnicity;
    }
    
    //Deletes an ethnicity from the database
    public function delete()
    {
      // Ensure DB Connection
      SQLDB::connect("bcc_food_client");
      
      $query = "DELETE FROM bcc_food_client.ethnicities WHERE ethnicity_id = '{$this->ethnicityID}'";
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
      $ethnicityDescParam = mysql_real_escape_string($this->ethnicityDesc);
      $query = "";
      
      //If this ethnicity already existed in the database, update it
      if($this->createdFromDB)
      {
        $query = "UPDATE bcc_food_client.ethnicities SET ";
        $query .= "ethnicity_desc='{$ethnicityDescParam}' ";
        $query .= "WHERE ethnicity_id = '{$this->ethnicityID}'";
      }
      //If the ethnicity was freshly created, insert it into the database.
      else
      {
        $query = "INSERT INTO bcc_food_client.ethnicities (ethnicity_desc) ";
        $query .= "VALUES ('{$ethnicityDescParam}')";
      }
      
      
      $result = mysql_query($query);
      
      if ($result !== FALSE)
      {
        //If the update or insert was successful, this object is now consistent with the database
        //so if it's deleted we don't need to update the database
        $this->dirty = FALSE;
        
        if(!$this->createdFromDB)
        {
          $this->ethnicityID = mysql_insert_id();
        }
      }
      return $result;
    }
    
    
    //Creates a new ethnicity given a row from the bcc_food_client.ethnicities table
    private static function createFromSQLRow($row)
    {
      $ethnicity = new Ethnicity();
      $ethnicity->ethnicityID = $row["ethnicity_id"];
      $ethnicity->ethnicityDesc = $row["ethnicity_desc"];
      $ethnicity->createdFromDB = true;
      $ethnicity->dirty = false;
      return $ethnicity;
    }
    
    //Returns an ethnicity object given an ethnicity ID, or null if none found
    public static function getEthnicityByID($ethnicityID)
    {
      SQLDB::connect("bcc_food_client");
      
      $ethnicityID = mysql_real_escape_string($ethnicityID);
      
      $query = "SELECT ethnicity_id, ethnicity_desc ";
      $query .= "FROM bcc_food_client.ethnicities ";
      $query .= "WHERE ethnicity_id = '{$ethnicityID}'";
      
      $result = mysql_query($query);
      
      $ethnicity = NULL;
      if ($row = mysql_fetch_array($result))
      {
        $ethnicity = Ethnicity::createFromSQLRow($row);
      }
      
      return $ethnicity;
    }
		
		public static function getEthnicityByDesc($desc)
		{
			SQLDB::connect("bcc_food_client");
			$desc = strToLower(mysql_real_escape_string($desc));
			
			$query = "SELECT ethnicity_id, ethnicity_desc
								FROM bcc_food_client.ethnicities
								WHERE ethnicity_desc = '{$desc}'";
			
			$result = mysql_query($query);
			$ethnicity = NULL;
			if ($row = mysql_fetch_array($result))
			{
				$ethnicity  = Ethnicity::createFromSQLRow($row);
			}
			return $ethnicity;
		}
    
    public static function getAllEthnicities()
    {
      SQLDB::connect("bcc_food_client");
      
      $query = "SELECT ethnicity_id, ethnicity_desc FROM bcc_food_client.ethnicities";
      
      $result = mysql_query($query);
      
      $ethnicities = array();
      while ($row = mysql_fetch_array($result))
      {
        $ethnicities[] = Ethnicity::createFromSQLRow($row);
      } 
      return $ethnicities;
    }
		
		public static function getRemovableEthnicityIDs()
		{
			SQLDB::connect("bcc_food_client");
			$query = "SELECT ethnicity_id 
								FROM bcc_food_client.ethnicities
								WHERE ethnicity_id NOT IN (SELECT ethnicity_id FROM bcc_food_client.clients)";
			$result = mysql_query($query);
			$IDs = array();
			
			while ($row = mysql_fetch_array($result))
			{
				$IDs[] = $row['ethnicity_id'];
			}
			return $IDs;
		}
    
  }
?>