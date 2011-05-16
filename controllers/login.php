<?php
  include ('utility.php');
  include ('../models/administrator.php');
  include ('../models/sqldb.php');
  session_start();
	
	$password = $_POST['bccPassword'];
	$admin = Administrator::getAdminByUsername($_POST['bccUser']);
	if (isset($admin) && $admin->authenticate($password))
	{
		$_SESSION = NULL;
		$_SESSION['adminID'] = $admin->getAdminID();
		header("Location: ../");
	}
	else
	{
		$_SESSION['attemptedUser'] = $_POST['bccUser'];
		$_SESSION['errors'] = array();
		$_SESSION['errors'][] = "The username or password is incorrect.";
		header("Location: ../");
	}

