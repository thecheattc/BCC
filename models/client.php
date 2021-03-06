<?php
  define("HOMELESS_REASON_ID", 6);
	
	/*Prototype for a WAY BETTER getter/setter which I just discovered, my apologies
	to whomever has to maintain the mess using explicit getters and setters that's used all over.
	public function Variable()
	{
		if (func_num_args() != 0)
		{
			$this->Variable = func_get_arg(0);
		}
		return $this->Variable
	}*/
  class Client
  {
    private $clientID;
		private $spouseID;
    private $firstName;
    private $lastName;
    private $age;
    private $phoneNumber;
    private $houseID;
    private $ethnicityID;
    private $genderID;
    private $reasonID;
    private $explanation;
    private $unemploymentDate;
    private $applicationDate;
    private $receivesStamps;
    private $wantsStamps;
    
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
		
		public function getSpouseID()
    {
      return $this->spouseID;
    }
		
		public function setSpouseID($val)
    {
			$this->dirty = true;
			$this->spouseID = $val;
      return $this->spouseID;
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
      return $this->age;
    }
    
    public function getPhoneNumber()
    {
      return $this->phoneNumber;
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
    
    public function getExplanation()
    {
      return $this->explanation;
    }
    
    public function setExplanation($val)
    {
      $this->dirty = true;
      $this->explanation = $val;
      return $this->explanation;
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
    
    public function getReceivesStamps()
    {
      return $this->receivesStamps;
    }
    
    public function setReceivesStamps($val)
    {
      $this->dirty = true;
      if ($val == TRUE)
      {
        $this->receivesStamps = 1;
      }
      else
      {
        $this ->receivesStamps = 0;
      }
      return $this->receivesStamps;
    }
    
    public function getWantsStamps()
    {
      return $this->wantsStamps;
    }
    
    public function setWantsStamps($val)
    {
      $this->dirty = true;
      if ($val == TRUE)
      {
        $this->wantsStamps = 1;
      }
      else
      {
        $this->wantsStamps = 0;
      }
      return $this->wantsStamps;
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
      SQLDB::connect("bcc_food_client");
			
			//If this person is homeless and has a spouse, grab this client's children (if any)
			//and assign them to the spouse
			if($this->reasonID == HOMELESS_REASON_ID && isset($this->spouseID))
			{
					mysql_query("UPDATE bcc_food_client.family_members 
											SET guardian_id='{$this->spouseID}' 
											WHERE guardian_id='{$this->clientID}'");
			}
      
      $query = "DELETE FROM bcc_food_client.clients WHERE client_id = '{$this->clientID}'";

      $result = mysql_query($query);
      
      if ($result === FALSE)
      {
        return $result;
      }
      
      if (Client::deleteHouseIfNotReferenced($this->houseID) === FALSE)
      {
        return FALSE;
      }
      
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
			$spouseIDParam = NULL;
			if (!isset($this->spouseID))
			{
				$spouseIDParam = "NULL";
			}
			else
			{
				$spouseIDParam = "'" . $this->spouseID . "'";
			}
      $firstNameParam = mysql_real_escape_string($this->firstName);
      $lastNameParam = mysql_real_escape_string($this->lastName);
      $ageParam = mysql_real_escape_string($this->age);
      $phoneNumberParam = NULL;
      if (mysql_real_escape_string($this->phoneNumber) === '')
      {
        $phoneNumberParam = "NULL";
      }
      else
      {
        $phoneNumberParam = "'" . mysql_real_escape_string($this->phoneNumber)."'";
      }
      $houseIDParam = NULL;
      if (mysql_real_escape_string($this->houseID) === '')
      {
        $houseIDParam = "NULL";
      }
      else
      {
        $houseIDParam = "'" . mysql_real_escape_string($this->houseID)."'";
      }
      $ethnicityIDParam = mysql_real_escape_string($this->ethnicityID);
      $genderIDParam = mysql_real_escape_string($this->genderID);
      $reasonIDParam = mysql_real_escape_string($this->reasonID);
      $explanationParam = NULL;
      if (mysql_real_escape_string($this->explanation) === '')
      {
        $explanationParam = "NULL";
      }
      else
      {
        $explanationParam = "'" . mysql_real_escape_string($this->explanation) . "'";
      }
      $unempDateParam = NULL;
      if (empty($this->unemploymentDate) || mysql_real_escape_string(normalDateToMySQL($this->unemploymentDate)) === '')
      {
        $unempDateParam = "NULL";
      }
      else
      {
        $unempDateParam = "'" . mysql_real_escape_string(normalDateToMySQL($this->unemploymentDate)) . "'";
      }
      $appDateParam = mysql_real_escape_string(normalDateToMySQL($this->applicationDate));
      $receivesStampsParam = mysql_real_escape_string($this->receivesStamps);
      $wantsStampsParam = mysql_real_escape_string($this->wantsStamps);
      $query = "";
      //If this client already existed in the database, update it
      if($this->createdFromDB)
      {
        $query = "UPDATE bcc_food_client.clients SET ";
				$query .= "spouse_id=" . $spouseIDParam . ", ";
        $query .= "first_name='$firstNameParam', ";
        $query .= "last_name='$lastNameParam', ";
        $query .= "age='$ageParam', ";
        $query .= "phone_number=" .$phoneNumberParam .", ";
        $query .= "house_id=" . $houseIDParam . ", ";
        $query .= "ethnicity_id='$ethnicityIDParam', ";
        $query .= "gender_id='$genderIDParam', ";
        $query .= "reason_id='$reasonIDParam', ";
        $query .= "explanation=" . $explanationParam . ", ";
        $query .= "unemployment_date=" . $unempDateParam . ", ";
        $query .= "application_date='$appDateParam', ";
        $query .= "receives_stamps = '$receivesStampsParam', ";
        $query .= "wants_stamps="  . $wantsStampsParam . " ";
        $query .= "WHERE client_id = '{$this->clientID}'";
      }
      //If the client was freshly created, insert it into the database.
      else
      {
        $query = "INSERT INTO bcc_food_client.clients (spouse_id, first_name, last_name, age, phone_number, ";
        $query .= "house_id, ethnicity_id, gender_id, reason_id, explanation, unemployment_date, ";
        $query .= "application_date, receives_stamps, wants_stamps) VALUES ";
        $query .= "(" . $spouseIDParam . ", '$firstNameParam', '$lastNameParam', '$ageParam', ";
        $query .= $phoneNumberParam . ", " . $houseIDParam . ", '$ethnicityIDParam', ";
        $query .= "'$genderIDParam', '$reasonIDParam', " . $explanationParam . ", ";
        $query .= $unempDateParam . ", '$appDateParam', '$receivesStampsParam', " . $wantsStampsParam . ")";
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
			
			if ($spouseIDParam !== "NULL")
			{
				mysql_query("UPDATE bcc_food_client.clients 
										SET spouse_id={$this->clientID} 
										WHERE client_id={$this->spouseID}");
			}
      return $result;
    }
    
    
    //Creates a new client given a row from the bcc_food_client.clients table
    private static function createFromSQLRow($row)
    {
      $client = new Client();
      $client->clientID = $row["client_id"];
			$client->spouseID = $row["spouse_id"];
      $client->firstName = $row["first_name"];
      $client->lastName = $row["last_name"];
      $client->age = $row["age"];
      $client->phoneNumber = $row["phone_number"];
      $client->houseID = $row["house_id"];
      $client->ethnicityID = $row["ethnicity_id"];
      $client->genderID = $row["gender_id"];
      $client->reasonID = $row["reason_id"];
      $client->explanation = $row["explanation"];
      $client->unemploymentDate = createMySQLDate($row["unemployment_date"]);
      $client->applicationDate = createMySQLDate($row["application_date"]);
      $client->receivesStamps = $row["receives_stamps"];
      $client->wantsStamps = $row["wants_stamps"];
      $client->createdFromDB = true;
      $client->dirty = false;
      return $client;
    }
    
    //Returns an array of clients associated with a houseID
    public static function getClientsByHouseID($houseID)
    {
      SQLDB::connect("bcc_food_client");
      
      $houseID = mysql_real_escape_string($houseID);
      
      $query = "SELECT client_id, spouse_id, first_name, last_name, age, phone_number, ";
      $query .= "house_id, ethnicity_id, gender_id, reason_id, explanation, unemployment_date, ";
      $query .= "application_date, receives_stamps, wants_stamps ";
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
    public static function searchByNameAndStreet($firstName = '', $lastName = '', $streetNumber = '', $streetName = '')
    {
      SQLDB::connect("bcc_food_client");
      
      $firstName = mysql_real_escape_string(processString($firstName));
      $lastName = mysql_real_escape_string(processString($lastName));
      $streetNumber = mysql_real_escape_string(processString($streetNumber, TRUE));
      $streetName = mysql_real_escape_string(processString($streetName));
      
      $query = "SELECT c.client_id, c.spouse_id, c.first_name, c.last_name, c.age, c.phone_number, ";
      $query .= "c.house_id, c.ethnicity_id, c.gender_id, c.reason_id, c.explanation, ";
      $query .= "c.unemployment_date, c.application_date, c.receives_stamps, c.wants_stamps ";
      $query .= "FROM bcc_food_client.clients c LEFT JOIN bcc_food_client.houses h ON c.house_id = h.house_id ";
      $query .= "WHERE first_name LIKE '{$firstName}' OR last_name LIKE '{$lastName}' ";
      $query .= "OR street_number LIKE '{$streetNumber}' ";
      $query .= "OR street_name LIKE '{$streetName}'";
      
      
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
      SQLDB::connect("bcc_food_client");
      
      $id = mysql_real_escape_string($id);
      
      $query = "SELECT client_id, spouse_id, first_name, last_name, age, phone_number, ";
      $query .= "house_id, ethnicity_id, gender_id, reason_id, explanation, unemployment_date, ";
      $query .= "application_date, receives_stamps, wants_stamps ";
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
		
		public function getSpouseAsClient()
		{
			return Client::getClientByID($this->spouseID);
		}
    
    //Returns an array of Visits
    public function getVisitHistory($since)
    {
      return Visit::getHistoryByClientID($this->clientID, $since);
    }
    
    //Changes the visit given by distID to the new date and the new type
    //Returns TRUE on success, FALSE on error
    public function changeHistory($distID, $newDate, $newDistTypeID)
    {
      return Visit::changeHistoryByID($distID, $newDate, $newDistTypeID);
    }
    
    public static function deleteHouseIfNotReferenced($houseID)
    {
      //homeless case
      if (empty($houseID))
      {
        return TRUE;
      }
      //Determine the number of people tied to the deleted client's house
      $query = "SELECT COUNT(*) FROM bcc_food_client.clients WHERE house_id = '{$houseID}'";
      $result = mysql_query($query);
      
      if ($result === FALSE)
      {
        return $result;
      }
      
      $countArr = mysql_fetch_array($result);
      $count = $countArr[0];
            
      if ($count == 0)
      {
        $query = "DELETE FROM bcc_food_client.houses WHERE house_id = '{$houseID}'";
        $result = mysql_query($query);
      }
      
      return $result;
    }
		
		//Returns an array of spouses matching the given first or last nmae.
    public static function searchSpouses($first = '', $last = '')
    {
      SQLDB::connect("bcc_food_client");
      
      $first = mysql_real_escape_string(processString($first));
      $last = mysql_real_escape_string(processString($last));
      
      $query = "SELECT client_id, first_name, last_name, age, phone_number ";
      $query .= "FROM bcc_food_client.clients ";
      $query .= "WHERE first_name LIKE '{$first}' OR last_name LIKE '{$last}'";
      
      $result = mysql_query($query);
      $spouses = array();      
      while ($row = mysql_fetch_array($result))
      {
        $spouse = array();
        $spouse['spouseID'] = $row['client_id'];
        $spouse['spouseFirst'] = $row['first_name'];
        $spouse['spouseLast'] = $row['last_name'];
				$spouse['age'] = $row['age'];
				$spouse['phoneNumber'] = $row['phone_number'];
        $spouses[] = $spouse;
      }
      
      return $spouses;
    }
		
		public function ensureClientNotListedAsSpouse()
		{
			SQLDB::connect("bcc_food_client");
			return mysql_query("UPDATE clients SET spouse_id = NULL WHERE spouse_id = {$this->clientID}");
		}
    
  }

  ?>