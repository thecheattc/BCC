<?php
  class SQLDB
  {
    // Hold an instance of the class
    private static $instance;
    
    private static $sqlConn;
    
    // A private constructor; prevents direct creation of object
    private function __construct() 
    {
      //connect to database
      $username = "root";
      $password = "root";
      $hostname = "localhost";
			self::$sqlConn = mysql_connect($hostname, $username, $password) or die("Unable to connect to MySQL " . mysql_error());
    }
    
    public function __destruct()
    {
			mysql_close();
    }
    
    // The singleton method
    public static function connect($dbname) 
    {
      if (!isset(self::$instance))
			{
        $c = __CLASS__;
        self::$instance = new $c;
      }
			mysql_select_db($dbname, self::$sqlConn) or die("Unable to select database: " . mysql_error());
      return self::$instance;
    }
    
    // Prevent users from cloning the instance
    public function __clone()
    {
      trigger_error('Clone is not allowed.', E_USER_ERROR);
    }
  
  }
?>