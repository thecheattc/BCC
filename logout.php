<?php
	session_start();
	$errors = empty($_SESSION['errors'])? array() : $_SESSION['errors'];
	unset($_SESSION);
	session_destroy();
	session_start();
	$_SESSION['errors'] = $errors;
	if (isset($_GET['timeout']))
	{
		$_SESSION['errors'][] = "You have been logged out due to inactivity.";
	}
	header('Location: ./');
