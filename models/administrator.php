<?php
  
  class Administrator
  {
    private $adminID;
    private $username;
		private $password;
		private $salt;
		private $accessID;
		private $accessLevel;
    
    //If an admin is retrieved from the database, flag it as such
    private $createdFromDB = true;
    
    //This flag keeps track of whether or not this object is inconsistent
    //with the database and needs to be written back to the database on
    //destruction
    private $dirty = false;
    
    //No setter for adminID because that should be handled internally
    public function getAdminID()
    {
      return $this->adminID;
    }
    
    public function getUsername()
    {
      return $this->username;
    }
    
    public function setUsername($val)
    {
      $this->dirty = true;
      $this->username = $val;
      return $this->username;
    }
    
    public function setPassword($val)
    {
      $this->dirty = true;
			$this->salt = self::generateSalt();
			$this->password = hash('sha256', hash('sha256', $val) . $this->salt);
      return $this->password;
    }
		
		public function authenticate($password)
    {
      return $this->password == hash('sha256', hash('sha256', $password) . $this->salt);
    }
		
		public function getAccessID()
    {
      return $this->accessID;
    }
    
    public function setAccessID($val)
    {
			SQLDB::connect("bcc_admin");
			$sanitizedID = mysql_real_escape_string($val);
			$query = "SELECT access_level_name FROM bcc_admin.access_levels WHERE access_level_id = {$val}";
			$result = mysql_query($query);
			$this->accessLevel = NULL;
			if ($row = mysql_fetch_array($result))
			{
				$this->accessLevel = $row[0];
			}
      $this->dirty = true;
      $this->accessID = $val;
      return $this->accessID;
    }
		
		public function getAccessLevel()
		{
			return $this->accessLevel;
		}
        
    public function __destruct()
    {
      if($this->dirty)
      {
        $this->save();
      }
    }
    
    //Creates a new admin
    public static function create()
    {
      $admin = new Administrator();
      $admin->createdFromDB = FALSE;
      return $admin;
    }
    
    //Deletes an admin from the database
    public function delete()
    {
      // Ensure DB Connection
      SQLDB::connect("bcc_admin");
      
      $query = "DELETE FROM bcc_admin.administrators WHERE admin_id = '{$this->adminID}'";
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
      SQLDB::connect("bcc_admin");
      
      //Sanitize user-generated input
			$usernameParam = mysql_real_escape_string($this->username);
			$passwordParam = mysql_real_escape_string($this->password);
			$saltParam = mysql_real_escape_string($this->salt);
			$accessIDParam = mysql_real_escape_string($this->accessID);
      $query = "";
      
      //If this admin already existed in the database, update it
      if($this->createdFromDB)
      {
        $query = "UPDATE bcc_admin.administrators SET ";
        $query .= "username ='{$usernameParam}' ";
				$query .= "password ='{$passwordParam}' ";
				$query .= "salt ='{$saltParam}' ";
				$query .= "access_id ='{$accessIDParam}' ";
        $query .= "WHERE admin_id = '{$this->adminID}'";
      }
      //If the admin was freshly created, insert it into the database.
      else
      {
        $query = "INSERT INTO bcc_admin.administrators (username, password, salt, access_id) ";
        $query .= "VALUES ('{$usernameParam}', '{$passwordParam}', '{$saltParam}', '{$accessIDParam}')";
      }
      
      
      $result = mysql_query($query);
      
      if ($result !== FALSE)
      {
        //If the update or insert was successful, this object is now consistent with the database
        //so if it's deleted we don't need to update the database
        $this->dirty = FALSE;
        
        if(!$this->createdFromDB)
        {
          $this->adminID = mysql_insert_id();
        }
      }
      return $result;
    }
    
    
    //Creates a new admin given a row from the bcc_admin.administrators table
    private static function createFromSQLRow($row)
    {
      $admin = new Administrator();
      $admin->adminID = $row["admin_id"];
      $admin->username = $row["username"];
			$admin->password = $row["password"];
			$admin->salt = $row["salt"];
			$admin->accessID = $row["access_id"];
			$query = "SELECT access_level_name FROM bcc_admin.access_levels WHERE access_level_id = {$admin->accessID}";
			$result = mysql_query($query);
			$admin->accessLevel = NULL;
			if ($row = mysql_fetch_array($result))
			{
				$admin->accessLevel = $row[0];
			}
      $admin->createdFromDB = true;
      $admin->dirty = false;
      return $admin;
    }
    
    //Returns an administrator object given an admin ID, or null if none found
    public static function getAdminByID($adminID)
    {
      SQLDB::connect("bcc_admin");
      
      $adminID = mysql_real_escape_string($adminID);
      
      $query = "SELECT admin_id, username, password, salt, access_id ";
      $query .= "FROM bcc_admin.administrators ";
      $query .= "WHERE admin_id = '{$adminID}'";
      
      $result = mysql_query($query);
      
      $admin = NULL;
      if ($row = mysql_fetch_array($result))
      {
        $admin = Administrator::createFromSQLRow($row);
      }
      
      return $admin;
    }
		
		//Returns an administrator object given an admin username, or null if none found
    public static function getAdminByUsername($username)
    {
      SQLDB::connect("bcc_admin");
      
      $username = mysql_real_escape_string($username);
      
      $query = "SELECT admin_id, username, password, salt, access_id ";
      $query .= "FROM bcc_admin.administrators ";
      $query .= "WHERE username = '{$username}'";
      
      $result = mysql_query($query);
      
      $admin = NULL;
      if ($row = mysql_fetch_array($result))
      {
        $admin = Administrator::createFromSQLRow($row);
      }
      
      return $admin;
    }
    
    public static function getAllAdministrators()
    {
      SQLDB::connect("bcc_admin");
      
      $query = "SELECT admin_id, username, password, salt, access_id ";
      $query .= "FROM bcc_admin.administrators";
      
      $result = mysql_query($query);
      
      $admins = array();
      while ($row = mysql_fetch_array($result))
      {
        $admins[] = Administrator::createFromSQLRow($row);
      }
      
      return $admins;
    }
		
		//Returns an array of associative arrays, each containing level ID and level name
		public static function getAllAccessLevels()
		{
			SQLDB::connect("bcc_admin");
			$query = "SELECT access_level_id, access_level_name FROM bcc_admin.access_levels";
			$result = mysql_query($query);
			$accessLevels = array(array());
			while ($row = mysql_fetch_array($result))
			{
				$accessLevels[] = array("accessLevelID" => $row[0], "accessLevelName" => $row[1]);
			}
			return $accessLevels;
		}
		
		private static function generateSalt() 
		{
			$length = 8;
			$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
			$string = "";    
			$charlen = strlen($characters) - 1;
			for ($p = 0; $p < $length; $p++) {
				$string .= $characters[mt_rand(0, $charlen)];
			}
			return $string;
		}
    
  }
	?>