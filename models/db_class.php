<?php 
session_start();
ini_set('display_errors', 0);
ini_set('display_warnings', 0);

class dbHandler{

  /*
		 	Configuration for database can be changed in these 4 variables:
			$dbName, $dbHost, $dbUser, $dbPass
			This is the ONLY place that you would have to change and all
			the functions should work fine.
			

	*/

 	public $dbName = 'BryantFoodDB';
 	public $dbHost = 'localhost';
    public $dbUser = 'root';	
	public $dbPass = 'root';



	
	// Other Variables to keep track of the DB state
  public $dbConnected = false;
 
	//Construtor	
	function dbHandler()
	{
		 // print_r("Constructor is being called"."<BR>");

	}
	
	function __destruct(){

		//mysql_close($dbConnected);	
  	// print_r("destructor is being called"."<BR>");
    
	}

	
 	function db_connect()
 	{
		$dbConnected = mysql_connect($this->dbHost, $this->dbUser, $this->dbPass);
		if (!$dbConnected) {
    		echo ("die");
    		die('Could not connect: ' . mysql_error());

  		}else{
    	//	echo ("ok");
  		}
		
		mysql_select_db($this->dbName) or die(mysql_error());
	
 		
	}  
	

  	function db_select($queryString)
	{
  
    $query = mysql_query($queryString);
		if (!$query) {
	    die('Invalid query: ' . mysql_error());
		}
		//$row = mysql_fetch_array($query);
		return $query;
		//return $row;
	}
	

  function db_insert($queryString)
	{
		$query= mysql_query($queryString);
		if (!$query) {
	      echo('Invalid query: ' . mysql_error());
		}

	}




};

	function helper_makeInsertString( $arrayListToInsert, $table)

	/*
		PURPOSE: To concatenate components to make "INSERT" sql string

		This function is here so that you DON'T have to manually type something like (below) everytime you want to insert into a table
		All you have to do is pass in array (please see example in sample1.php) and indicate what table you want to put it in as
		then it would make an INSERT query string for you! :):):)

		BELOW is what you DON'T Want to DO EVERY TIME you want to insert, it's a pain and you WILL most likely get syntax error
		
		queryString = "insert into user (idUser, FirstName,LastName, UserName, UserType, Password) VALUES('101,'. .$_POST['firstname'].,.$_POST['lastname'].",".$_POST['username'].",".$_POST['usertype'].",".$_POST['password'].")";


	*/

	{
		$key = array_keys($arrayListToInsert);

		$insertQuery="insert into ".$table."(";
		$i = 0;
		while( $i < sizeof($key)-1){
				$insertQuery = $insertQuery.$key[$i].',';
				$i++;
		}

		$insertQuery = $insertQuery.$key[$i].')';
		$insertQuery = $insertQuery."values (";
		$i = 0;

		while( $i < sizeof($key)-1){
				$insertQuery = $insertQuery."'".$arrayListToInsert[$key[$i]]."'".',';
				$i++;
		}
		$insertQuery = $insertQuery."'".$arrayListToInsert[$key[$i]]."'".')';
	
		return $insertQuery;


	}


	function mysql_fetch_data($result, $numass=MYSQL_BOTH) {
		$i=0;

	
   mysql_data_seek($result, 0);
	$arrayField = array();
  $numField = mysql_num_fields($result);
	for ($x = 0 ; $x < $numField; $x++){
		
		array_push($arrayField, mysql_field_name($result, $x));

	} 

	
		$keys=array_keys(mysql_fetch_array($result, $numass));
		mysql_data_seek($result, 0);
		  while ($row = mysql_fetch_array($result, $numass)) {
        $l =0;
				for($l = 0; $l< $numField; $l++){
		   
 	        $got[$arrayField[$l]][$i]=$row[$arrayField[$l]];
					
		    }
		  $i++;
		  }
		return $got;
	}
	


?>