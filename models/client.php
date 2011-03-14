<?php
  
  include_once("visit.php");
  
  class Client
  {
    private $clientID;
    private $firstName;
    private $lastName;
    private $age;
    private $phoneNumber;
    private $houseID;
    private $ethnicityID;
    private $genderID;
    private $reasonID;
    private $unemploymentDate;
    private $applicationDate;
    
    //If a client is retrieved from the database, flag it as such
    private $createdFromDB = true;
    
    //This flag keeps track of whether or not this object is inconsistent
    //with the database and needs to be written back to the database on
    //destruction
    private $dirty = false;
    
    //No setter for clientID because that should be handled internally
    public function getClientID()
    {
      return $this->clientID;
    }
    
    public function getFirstName()
    {
      return $this->firstName;
    }
    
    public function setFirstName($val)
    {
      $this->dirty = true;
      $this->firstName = $val;
      return $this->firstName;
    }
    
    public function getLastName()
    {
      return $this->lastName;
    }
    
    public function setLastName($val)
    {
      $this->dirty = true;
      $this->lastName = $val;
      return $this->lastName;
    }
    
    public function getAge()
    {
      return $this->age;
    }
    
    public function setAge($val)
    {
      $this->dirty = true;
      $this->age = $val;
      return $this->Age;
    }
    
    public function getPhoneNumber()
    {
      return $this->firstName;
    }
    
    public function setPhoneNumber($val)
    {
      $this->dirty = true;
      $this->phoneNumber = $val;
      return $this->phoneNumber;
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
    
    public function getReasonID()
    {
      return $this->reasonID;
    }
    
    public function setReasonID($val)
    {
      $this->dirty = true;
      $this->reasonID = $val;
      return $this->reasonID;
    }
    
    public function getUnemploymentDate()
    {
      return $this->unemploymentDate;
    }
    
    public function setUnemploymentDate($val)
    {
      $this->dirty = true;
      $this->unemploymentDate = $val;
      return $this->unemploymentDate;
    }
    
    public function getApplicationDate()
    {
      return $this->applicationDate;
    }
    
    public function setApplicationDate($val)
    {
      $this->dirty = true;
      $this->applicationDate = $val;
      return $this->applicationDate;
    }
    
    public function __destruct()
    {
      if($this->dirty)
      {
        $this->save();
      }
    }
    
    //Creates a new client
    public static function create()
    {
      $client = new Client();
      $client->createdFromDB = FALSE;
      return $client;
    }
    
    //Deletes a client from the database
    public function delete()
    {
      // Ensure DB Connection
      SQLDB::connect();
      
      $query = "DELETE FROM bcc_food_client.clients WHERE client_id = '{$this->clientID}'";
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
      $firstNameParam = mysql_real_escape_string($this->firstName);
      $lastNameParam = mysql_real_escape_string($this->lastName);
      $ageParam = mysql_real_escape_string($this->age);
      $phoneNumberParam = mysql_real_escape_string($this->phoneNumber);
      $houseIDParam = mysql_real_escape_string($this->houseID);
      $ethnicityIDParam = mysql_real_escape_string($this->ethnicityID);
      $genderIDParam = mysql_real_escape_string($this->genderID);
      $reasonIDParam = mysql_real_escape_string($this->reasonID);
      $unempDateParam = mysql_real_escape_string($this->unemploymentDate);
      $appDateParam = mysql_real_escape_string($this->applicationDate);
      $query = "";
      
      //If this client already existed in the database, update it
      if($this->createdFromDB)
      {
        $query = "UPDATE bcc_food_client.clients SET ";
        $query .= "first_name='$firstNameParam', ";
        $query .= "last_name='$lastNameParam', ";
        $query .= "age='$ageParam', ";
        $query .= "phone_number='$phoneNumberParam' ";
        $query .= "house_id='$houseIDParam', ";
        $query .= "ethnicity_id='$ethnicityIDParam', ";
        $query .= "gender_id='$genderIDParam', ";
        $query .= "reason_id='$reasonIDParam' ";
        $query .= "unemployment_date='$unempDateParam', ";
        $query .= "application_date='$appDateParam' ";
        $query .= "WHERE client_id = '{$this->clientID}'";
      }
      //If the client was freshly created, insert it into the database.
      else
      {
        $query = "INSERT INTO bcc_food_client.clients (first_name, last_name, age, phone_number, ";
        $query .= "house_id, ethnicity_id, gender_id, reason_id, unemployment_date, ";
        $query .= "application_date) VALUES ";
        $query .= "('$firstNameParam', '$lastNameParam', '$ageParam', ";
        $query .= "'$phoneNumberParam', '$houseIDParam', '$ethnicityIDParam', ";
        $query .= "'$genderIDParam', '$reasonIDParam', '$unempDateParam', ";
        $query .= "'$appDateParam')";
      }
      
      
      $result = mysql_query($query);
      
      if ($result !== FALSE)
      {
        //If the update or insert was successful, this object is now consistent with the database
        //so if it's deleted we don't need to update the database
        $this->dirty = FALSE;
        
        if(!$this->createdFromDB)
        {
          $this->clientID = mysql_insert_id();
        }
      }
      return $result;
    }
    
    
    //Creates a new client given a row from the bcc_food_client.clients table
    private static function createFromSQLRow($row)
    {
      $client = new Client();
      $client->clientID = $row["client_id"];
      $client->firstName = $row["first_name"];
      $client->lastName = $row["last_name"];
      $client->age = $row["age"];
      $client->phoneNumber = $row["phone_number"];
      $client->houseID = $row["house_id"];
      $client->ethnicityID = $row["ethnicity_id"];
      $client->genderID = $row["gender_id"];
      $client->reasonID = $row["reason_id"];
      $client->unemploymentDate = $row["unemployment_date"];
      $client->applicationDate = $row["application_date"];
      $client->createdFromDB = true;
      $client->dirty = false;
      return $client;
    }
    
    //Returns an array of clients associated with a houseID
    public static function getClientsByHouseID($houseID)
    {
      SQLDB::connect();
      
      $houseID = mysql_real_escape_string($houseID);
      
      $query = "SELECT client_id, first_name, last_name, age, phone_number, ";
      $query .= "house_id, ethnicity_id, gender_id, reason_id, unemployment_date, ";
      $query .= "application_date ";
      $query .= "FROM bcc_food_client.clients ";
      $query .= "WHERE house_id = '{$houseID}'";
      
      $result = mysql_query($query);
      
      $clients = array();
      while ($row = mysql_fetch_array($result))
      {
        $clients[] = Client::createFromSQLRow($row);
      }
      
      return $clients;
    }
    
    
    //Returns an array of clients that match the given first name or last name
    public static function searchByNameAndStreet($firstName = '', $lastName = '', $street = '')
    {
      SQLDB::connect();
      
      $firstName = mysql_real_escape_string($firstName);
      $lastName = mysql_real_escape_string($lastName);
      $street = mysql_real_escape_string($street);
      
      $query = "SELECT c.client_id, c.first_name, c.last_name, c.age, c.phone_number, ";
      $query .= "c.house_id, c.ethnicity_id, c.gender_id, c.reason_id, c.unemployment_date, c.application_date ";
      $query .= "FROM bcc_food_client.clients c LEFT JOIN bcc_food_client.houses h ON c.house_id = h.house_id ";
      $query .= "WHERE first_name LIKE '{$firstName}' OR last_name LIKE '{$lastName}' OR address LIKE '{$street}'";
            
      $result = mysql_query($query);
      
      $clients = array();
      while ($row = mysql_fetch_array($result))
      {
        $clients[] = Client::createFromSQLRow($row);
      }
      
      return $clients;
    }
    
    //Returns a client given a client ID. Returns NULL on failure.
    public static function getClientByID($id)
    {
      SQLDB::connect();
      
      $id = mysql_real_escape_string($id);
      
      $query = "SELECT client_id, first_name, last_name, age, phone_number, ";
      $query .= "house_id, ethnicity_id, gender_id, reason_id, unemployment_date, ";
      $query .= "application_date ";
      $query .= "FROM bcc_food_client.clients ";
      $query .= "WHERE client_id = '{$id}'";
      
      $result = mysql_query($query);
      
      $client = NULL;
      if ($row = mysql_fetch_array($result))
      {
        $client = Client::createFromSQLRow($row);
      }
      
      return $client;
    }
    
    //Returns an array of Visits
    //Should I include some sort of ability to specify a timeframe, or default to
    //just the current month?
    public function getVisitHistory()
    {
      return Visit::getHistoryByClientID($this->clientID);
    }
    
    //Changes the visit given by distID to the new date and the new type
    //Returns TRUE on success, FALSE on error
    public function changeHistory($distID, $newDate, $newDistTypeID)
    {
      return Visit::changeHistoryByID($distID, $newDate, $newDistTypeID);
    }
    
    //Function to record a user visit
    //Returns TRUE on success, FALSE on error.
    public function receivedFood($dist_type_id)
    {
      $dist_type_id = mysql_real_escape_string($dist_type_id);
      
      SQLDB::connect();
      
      $query = "INSERT INTO bcc_food_client.usage (client_id, dist_type_id, date) ";
      $query .= "VALUES ('{$this->clientID}', '{$dist_type_id}', CURDATE()) ";
      
      $result = mysql_query($query);
      
      return $result;
    }
    
  }

  ?>