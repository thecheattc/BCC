<?php
	session_start();
	$errors = isset($_SESSION['errors'])? $_SESSION['errors'] : array();
	unset($_SESSION);
	session_destroy();
	session_start();
	$_SESSION['errors'] = $errors;
	$_SESSION['errors'][] = "You have been logged out due to inactivity.";
	header('Location: ./');
