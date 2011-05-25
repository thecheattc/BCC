<?php
    
  //Encapsulates the usage and distribution_type tables
  class Visit
  {
    private $visitID;
    private $clientID;
    private $typeID;
    private $distTypeDesc;
    private $date;
		private $note;
		private $locationID;
		private $locationName;
    
    //If a visit is retrieved from the database, flag it as such
    private $createdFromDB = true;
    
    //This flag keeps track of whether or not this object is inconsistent
    //with the database and needs to be written back to the database on
    //destruction
    private $dirty = false;
    
    //No setter for visitID because that should be handled internally
    public function getVisitID()
    {
      return $this->visitID;
    }
    
    public function getClientID()
    {
      return $this->clientID;
    }
    
    public function setClientID($val)
    {
      $this->dirty = true;
      $this->clientID = $val;
      return $this->clientID;
    }
    
    public function getTypeID()
    {
      return $this->typeID;
    }
    
    public function setTypeID($val)
    {
      $this->dirty = true;
      $this->typeID = $val;
      SQLDB::connect("bcc_food_client");
      $query = "SELECT dist_type_desc FROM bcc_food_client.distribution_type ";
      $query .= "WHERE dist_type_id = {$val}";
      
      $result = mysql_query($query);
      
      if ($row = mysql_fetch_array($result))
      {
        $this->distTypeDesc = $row['dist_type_desc'];
      }
      return $this->typeID;
    }
    
    public function getDistTypeDesc()
    {
      return $this->distTypeDesc;
    }
    
    //No setter for type description, because it should only
    //ever be set by setting the type ID
    
    public function getDate()
    {
      return $this->date;
    }
    
    public function setDate($val)
    {
      $this->dirty = true;
      $this->date = $val;
      return $this->date;
    }
		
		public function getNote()
    {
      return $this->note;
    }
    
    public function setNote($val)
    {
      $this->dirty = true;
      $this->note = $val;
      return $this->note;
    }
		
		public function getLocationID()
    {
      return $this->locationID;
    }
    
    public function setLocationID($val)
    {
      $this->dirty = true;
      $this->locationID = $val;
      SQLDB::connect("bcc_food_client");
      $query = "SELECT location_name FROM bcc_food_client.locations ";
      $query .= "WHERE location_id = {$val}";
      
      $result = mysql_query($query);
      if ($row = mysql_fetch_array($result))
      {
        $this->locationName = $row['location_name'];
      }
      return $this->locationID;
    }
		
		public function getLocationName()
		{
			return $this->locationName;
		}
    
    public function __destruct()
    {
      if($this->dirty)
      {
        $this->save();
      }
    }
    
    //Creates a new Visit
    public static function create()
    {
      $visit = new Visit();
      $visit->createdFromDB = FALSE;
      return $visit;
    }
    
    //Deletes a visit from the database
    public function delete()
    {
      // Ensure DB Connection
      SQLDB::connect("bcc_food_client");
      
      $query = "DELETE FROM bcc_food_client.usage WHERE dist_id = '{$this->visitID}'";
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
      $clientIDParam = mysql_real_escape_string($this->clientID);
      $typeIDParam = mysql_real_escape_string($this->typeID);
      $dateParam = mysql_real_escape_string(normalDateToMySQL($this->date));
			$noteParam = mysql_real_escape_string($this->note);
			$locationIDParam = mysql_real_escape_string($this->locationID);
      $query = "";
      
      //If this visit already existed in the database, update it
      if($this->createdFromDB)
      {
        $query = "UPDATE bcc_food_client.usage SET ";
        $query .= "client_id='{$clientIDParam}', ";
        $query .= "type_id='{$typeIDParam}', ";
        $query .= "date='{$dateParam}', ";
				$query .= "note='{$noteParam}', ";
				$query .= "location_id='{$locationIDParam}' ";
        $query .= "WHERE dist_id = '{$this->visitID}'";
      }
      //If the visit was freshly created, insert it into the database.
      else
      {
        $query = "INSERT INTO bcc_food_client.usage (client_id, type_id, location_id, date, note) ";
        $query .= "VALUES ('{$clientIDParam}', '{$typeIDParam}', '{$locationIDParam}', '{$dateParam}', '{$noteParam}')";
      }
      
      $result = mysql_query($query);
      if ($result !== FALSE)
      {
        //If the update or insert was successful, this object is now consistent with the database
        //so if it's deleted we don't need to update the database
        $this->dirty = FALSE;
        
        if(!$this->createdFromDB)
        {
          $this->visitID = mysql_insert_id();
        }
      }
      return $result;
    }
    
    
    //Creates a new house given a row from the bcc_food_client.usage joined with the distribution_type table
    private static function createFromSQLRow($row)
    {
      $visit = new Visit();
      $visit->visitID = $row["dist_id"];
      $visit->clientID = $row["client_id"];
      $visit->typeID = $row["type_id"];
      $visit->distTypeDesc = $row["dist_type_desc"];
      $visit->date = createMySQLDate($row["date"]);
			$visit->note = $row["note"];
			$visit->locationID = $row["location_id"];
			$visit->locationName = $row["location_name"];
      $visit->createdFromDB = true;
      $visit->dirty = false;
      return $visit;
    }
    
    //Returns an array of visits given a client ID
    public static function getHistoryByClientID($clientID, $since)
    {
      SQLDB::connect("bcc_food_client");
      
      $clientID = mysql_real_escape_string($clientID);
      $since = mysql_real_escape_string(normalDateToMySQL($since));
      
      $query = "SELECT u.dist_id, u.client_id, u.type_id, u.location_id, u.date, u.note, d.dist_type_desc, l.location_name ";
      $query .= "FROM bcc_food_client.usage u JOIN bcc_food_client.distribution_type d ";
      $query .= "ON u.type_id = d.dist_type_id ";
			$query .= "JOIN bcc_food_client.locations l ON l.location_id=u.location_id ";
      $query .= "WHERE client_id = '{$clientID}' AND date >= '{$since}' ";
      $query .= "ORDER BY date DESC";

      $result = mysql_query($query);
			$visits = array();
      while ($row = mysql_fetch_array($result))
      {
        $visits[] = Visit::createFromSQLRow($row);
      }
      
      return $visits;
    }
    
    public static function getVisitByID($visitID)
    {
      SQLDB::connect("bcc_food_client");
      
      $visitID = mysql_real_escape_string($visitID);
      
      $query = "SELECT u.dist_id, u.client_id, u.type_id, u.location_id, u.date, u.note, d.dist_type_desc, l.location_name ";
      $query .= "FROM bcc_food_client.usage u LEFT JOIN bcc_food_client.distribution_type d ";
      $query .= "ON type_id = dist_type_id ";
			$query .= "JOIN bcc_food_client.locations l ON l.location_id=u.location_id ";
      $query .= "WHERE dist_id = '{$visitID}'";
      
      $result = mysql_query($query);
      
      $visit = NULL;
      if ($row = mysql_fetch_array($result))
      {
        $visit = Visit::createFromSQLRow($row);
      }
      return $visit;
    }
    
    //Changes the date and distribution type of the specified distribution
    //to the given date and distribution type
    public static function changeHistoryByID($distID, $newDate, $newDistTypeID, $newNote, $newLocationID)
    {
      SQLDB::connect("bcc_food_client");
      
      $distID = mysql_real_escape_string($distID);
      $newDate = mysql_real_escape_string(normalDateToMySQL($newDate));
      $newDistTypeID = mysql_real_escape_string($newDistTypeID);
			$newNote = mysql_real_escape_string($newNote);
			$newLocationID = mysql_real_escape_string($newLocationID);
      
      $query = "UPDATE bcc_food_client.usage ";
      $query .= "SET type_id = '{$newDistTypeID}', ";
      $query .= "date = '{$newDate}', ";
			$query .= "note = '{$newNote}' ";
			$query .= "location_id = '{$newLocationID}' ";
      $query .= "WHERE dist_id = '{$distID}'";
      
      $result = mysql_query($query);
      
      return $result;
    }
    
    public static function deleteVisitByID($distID)
    {
      SQLDB::connect("bcc_food_client");
      $distID = mysql_real_escape_string($distID);
      $query = "DELETE FROM bcc_food_client.usage WHERE dist_id = '{$distID}'";
      $result = mysql_query($query);
      return $result;
    }
    
    //Returns an array of distribution type ID - distribution description pairs
    public static function getAllDistTypes()
    {
      SQLDB::connect("bcc_food_client");
      
      $query = "SELECT dist_type_id, dist_type_desc ";
      $query .= "FROM bcc_food_client.distribution_type ";
      
      $result = mysql_query($query);
      
      $pairs = array();
      while ($row = mysql_fetch_array($result))
      {
        $pairs[$row['dist_type_id']] = $row['dist_type_desc'];
      }
      return $pairs;
    }
		
		public static function getAllLocations()
    {
      SQLDB::connect("bcc_food_client");
      
      $query = "SELECT location_id, location_name ";
      $query .= "FROM bcc_food_client.locations ";
      
      $result = mysql_query($query);
      
      $pairs = array();
      while ($row = mysql_fetch_array($result))
      {
        $pairs[$row['location_id']] = $row['location_name'];
      }
      return $pairs;
    }
		
		public static function getRemovableLocationIDs()
		{
			SQLDB::connect("bcc_food_client");
			$query = "SELECT location_id 
								FROM bcc_food_client.locations
								WHERE location_id NOT IN (SELECT DISTINCT location_id FROM bcc_food_client.usage)";
			$result = mysql_query($query);
			$IDs = array();
			
			while ($row = mysql_fetch_array($result))
			{
				$IDs[] = $row['location_id'];
			}
			
			return $IDs;
		}
		
		public static function createLocation($locationName)
		{
			SQLDB::connect("bcc_food_client");
			$locationName = mysql_real_escape_string($locationName);
			return mysql_query("INSERT INTO locations (location_name) VALUES ('{$locationName}')");
		}
		
		public static function removeLocation($locationID)
		{
			SQLDB::connect("bcc_food_client");
			$locationID = mysql_real_escape_string($locationID);
			return mysql_query("DELETE FROM bcc_food_client.locations WHERE location_id={$locationID}");
		}
    
  }
  ?>