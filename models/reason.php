<?php
  
  class Reason
  {
    private $reasonID;
    private $reasonDesc;
    
    //If a reason is retrieved from the database, flag it as such
    private $createdFromDB = true;
    
    //This flag keeps track of whether or not this object is inconsistent
    //with the database and needs to be written back to the database on
    //destruction
    private $dirty = false;
    
    //No setter for reasonID because that should be handled internally
    public function getReasonID()
    {
      return $this->reasonID;
    }
    
    public function getReasonDesc()
    {
      return $this->reasonDesc;
    }
    
    public function setReasonDesc($val)
    {
      $this->dirty = true;
      $this->reasonDesc = $val;
      return $this->reasonDesc;
    }
        
    public function __destruct()
    {
      if($this->dirty)
      {
        $this->save();
      }
    }
    
    //Creates a new reason
    public static function create()
    {
      $reason = new Reason();
      $reason->createdFromDB = FALSE;
      return $reason;
    }
    
    //Deletes a reason from the database
    public function delete()
    {
      // Ensure DB Connection
      SQLDB::connect("bcc_food_client");
      
      $query = "DELETE FROM bcc_food_client.reasons WHERE reason_id = '{$this->reasonID}'";
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
      $reasonDescParam = mysql_real_escape_string($this->reasonDesc);
      $query = "";
      
      //If this reason already existed in the database, update it
      if($this->createdFromDB)
      {
        $query = "UPDATE bcc_food_client.reasons SET ";
        $query .= "reason_desc='{$reasonDescParam}' ";
        $query .= "WHERE reason_id = '{$this->reasonID}'";
      }
      //If the reason was freshly created, insert it into the database.
      else
      {
        $query = "INSERT INTO bcc_food_client.reasons (reason_desc) ";
        $query .= "VALUES ('{$reasonDescParam}')";
      }
      $result = mysql_query($query);
      
      if ($result !== FALSE)
      {
        //If the update or insert was successful, this object is now consistent with the database
        //so if it's deleted we don't need to update the database
        $this->dirty = FALSE;
        
        if(!$this->createdFromDB)
        {
          $this->reasonID = mysql_insert_id();
        }
      }
      return $result;
    }
    
    
    //Creates a new reason given a row from the bcc_food_client.reasons table
    private static function createFromSQLRow($row)
    {
      $reason = new Reason();
      $reason->reasonID = $row["reason_id"];
      $reason->reasonDesc = $row["reason_desc"];
      $reason->createdFromDB = true;
      $reason->dirty = false;
      return $reason;
    }
    
    //Returns a reason object given a reason ID, or null if none found
    public static function getReasonByID($reasonID)
    {
      SQLDB::connect("bcc_food_client");
      
      $reasonID = mysql_real_escape_string($reasonID);
      
      $query = "SELECT reason_id, reason_desc ";
      $query .= "FROM bcc_food_client.reasons ";
      $query .= "WHERE reason_id = '{$reasonID}'";
      
      $result = mysql_query($query);
      
      $reason = NULL;
      if ($row = mysql_fetch_array($result))
      {
        $reason = Reason::createFromSQLRow($row);
      }
      
      return $reason;
    }
    
		public static function getReasonByDesc($desc)
		{
			SQLDB::connect("bcc_food_client");
			$desc = strToLower(mysql_real_escape_string($desc));
			
			$query = "SELECT reason_id, reason_desc
			FROM bcc_food_client.reasons
			WHERE reason_desc = '{$desc}'";

			$result = mysql_query($query);
			$reason = NULL;
			if ($row = mysql_fetch_array($result))
			{
				$reason  = Reason::createFromSQLRow($row);
			}
			return $reason;
		}
		
    public static function getAllReasons()
    {
      SQLDB::connect("bcc_food_client");
      
      $query = "SELECT reason_id, reason_desc FROM bcc_food_client.reasons";
      
      $result = mysql_query($query);
      
      $reasons = array();
      while ($row = mysql_fetch_array($result))
      {
        $reasons[] = Reason::createFromSQLRow($row);
      }
      
      return $reasons;
    }
		
		public static function getRemovableReasonIDs()
		{
			SQLDB::connect("bcc_food_client");
			$query = "SELECT reason_id 
								FROM bcc_food_client.reasons
								WHERE reason_id NOT IN (SELECT DISTINCT reason_id FROM bcc_food_client.clients)";
			$result = mysql_query($query);
			$IDs = array();
			
			while ($row = mysql_fetch_array($result))
			{
				$IDs[] = $row['reason_id'];
			}
			
			return $IDs;
		}
    
  }
?>