<?php
  
  //Encapsulates the usage and distribution_type tables
  class Visit
  {
    private $visitID;
    private $clientID;
    private $distTypeID;
    private $distTypeDesc;
    private $date;
    
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
    
    public function getDistTypeID()
    {
      return $this->distTypeID;
    }
    
    public function setDistTypeID($val)
    {
      //SET DIST DESC TOO
      $this->dirty = true;
      $this->distTypeID = $val;
      return $this->distTypeID;
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
      SQLDB::connect();
      
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
      SQLDB::connect();
      
      //Sanitize user-generated input
      $clientIDParam = normalize($this->address);
      $distTypeIDParam = normalize($this->city);
      $dateParam = normalize($this->zip);
      $query = "";
      
      //If this visit already existed in the database, update it
      if($this->createdFromDB)
      {
        $query = "UPDATE bcc_food_client.usage SET ";
        $query .= "client_id='{$clientIDParam}', ";
        $query .= "dist_type_id='{$distTypeIDParam}', ";
        $query .= "date='{$dateParam}', ";
        $query .= "WHERE dist_id = '{$this->visitID}'";
      }
      //If the visit was freshly created, insert it into the database.
      else
      {
        $query = "INSERT INTO bcc_food_client.usage (client_id, dist_type_id, date) ";
        $query .= "VALUES ('{$clientID}', '{$distTypeID}', '{$date}')";
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
    
    
    //Creates a new house given a row from the bcc_food_client.usage table
    private static function createFromSQLRow($row)
    {
      $visit = new Visit();
      $visit->visitID = $row["dist_id"];
      $visit->clientID = $row["client_id"];
      $visit->distTypeID = $row["dist_type_id"];
      $visit->distTypeDesc = $row["dist_type_desc"];
      $visit->date = $row["date"];
      $visit->createdFromDB = true;
      $visit->dirty = false;
      return $visit;
    }
    
    //Returns an array of visits given a client ID
    public static function getHistoryByClientID($clientID)
    {
      SQLDB::connect();
      
      $clientID = mysql_real_escape_string($clientID);
      
      $query = "SELECT dist_id, client_id, dist_type_id, date, dist_type_desc ";
      $query .= "FROM bcc_food_client.usage LEFT JOIN bcc_food_client.distribution_type ";
      $query .= "ON type_id = dist_type_id ";
      $query .= "WHERE client_id = '{$clientID}'";
      
      $result = mysql_query($query);
      
      $visits = array();
      if ($row = mysql_fetch_array($result))
      {
        $visits[] = Visit::createFromSQLRow($row);
      }
      
      return $visits;
    }
    
    //Changes the date and distribution type of the specified distribution
    //to the given date and distribution type
    public static function changeHistoryByID($distID, $newDate, $newDistTypeID)
    {
      SQLDB::connect();
      
      $distID = normalize($distID);
      $newDate = normalize($newDate);
      $newDistTypeID = normalize($newDistTypeID);
      
      $query = "UPDATE bcc_food_client.usage ";
      $query .= "SET type_id = '{$newDistTypeID}', ";
      $query .= "date = '{$newDate}' ";
      $query .= "WHERE dist_id = '{$distID}'";
      
      $result = mysql_query($query);
      
      return $result;
    }
    
    public static function deleteVisitByID($distID)
    {
      SQLDB::connect();
      $distID = normalize($distID);
      $query = "DELETE FROM bcc_food_client.usage WHERE dist_id = '{$distID}'";
      result = mysql_query($query);
      return $result;
    }
    
  }
