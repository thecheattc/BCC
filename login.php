<?php
session_start(); 
 
ob_start();




$host="localhost"; // Host name
$username="root"; // Mysql username
$password="root"; // Mysql password
$db_name="BryantFoodDB"; // Database name
$tbl_name="Admin"; // Table name

// Connect to server and select databse.
mysql_connect("$host", "$username", "$password")or die("cannot connect");
mysql_select_db("$db_name")or die("cannot select DB");


// Define $myusername and $mypassword
$myusername=$_POST['user_name'];
$md5pass = md5($_POST['password']);



// To protect MySQL injection 
$myusername = stripslashes($myusername);
$md5pass = stripslashes($md5pass);
$myusername = mysql_real_escape_string($myusername);
$md5pass = mysql_real_escape_string($md5pass);

$sql="SELECT * FROM $tbl_name WHERE UserName='$myusername' and Password='$md5pass'";
$result=mysql_query($sql);

// Mysql_num_row is counting table row
$count=mysql_num_rows($result);
// If result matched $myusername and $mypassword, table row must be 1 row

if($count==1){
  // Register $myusername, $mypassword and redirect to file "login_success.php"
  
  session_register("myusername");
  session_register("mypassword");
  if(isset($_SESSION['myusername'])){
    unset($_SESSION['myusername']);    
  }
  $_SESSION['myusername'] = $myusername;
  
  
  header("location:myhome.php");
  unset($_SESSION['ERROR']);
}else {

  
  $_SESSION['ERROR'] = 'Bad Password or User Name, Please try again!';
  header("location:index.php");
}

ob_end_flush();


?>