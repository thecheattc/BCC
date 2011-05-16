<?php
	session_start();
	define("ROOT_ACCESS_ID", 1);

  //Trims a string, removes spaces if desired, and makes it safe to display in a browser.
	//This is not idempotent since it uses htmlentities(). It should be called only when setting
	//a member of a model.
  function processString ($string, $stripSpaces = FALSE, $lenLimit = TRUE)
  {
		if ($lenLimit && strlen($string) > 20)
		{
			return NULL;
		}
    if ($stripSpaces)
    {
      $string = str_replace(' ', '', $string);
    }
		
    return htmlentities(stripslashes(trim($string)));
  }
	
	function processPassword($pass)
  {
    if ((strlen($pass) > 4) && (strlen($pass) < 16))
    {
      return $pass;
    }
    else
    {
      return NULL;
    }
  }
	
	function createNormalDate($dateString)
	{
		return date_create_from_format("m-d-Y", $dateString);
	}
	
	function createMySQLDate($dateString)
	{
		return date_create_from_format("Y-m-d", $dateString);
	}
	
	//Takes a date time object and converts it to a short date format
	function mySQLDateToNormal($dateTime) 
	{
		// 3-9-2008
		return date_format($dateTime, 'm-d-Y');
	}
  
  //Takes a date time object and converts it to a MySQL format
  function normalDateToMySQL($dateTime) 
	{
		// 2011-3-1
		return date_format($dateTime, 'Y-m-d');
	}
	
	//Returns true if the page viewer has access to the page
	function hasAccess($requireRoot=FALSE)
	{
		if (!isset($_SESSION['adminID']))
		{
			return FALSE;
		}
		$admin = Administrator::getAdminByID($_SESSION['adminID']);
		if (empty($admin))
		{
			return FALSE;
		}
		if ($requireRoot && $admin->getAccessID() != ROOT_ACCESS_ID)
		{
			return FALSE;
		}
		return TRUE;
	}
	
	function resetTimeout() 
	{
		if (isset($_SESSION['timeout']))
		{
			$timeout = 1200; // 20 minutes
			$elapsedTime = time() - $_SESSION['timeout'];
			if ($elapsedTime > $timeout)
			{
				header('Location: /logout.php?timeout=1');
			}
			$_SESSION['timeout'] = time();
		}
		else
		{
			$_SESSION['timeout'] = time();
		}
	}
	
	function showHeader($title, $heading, $subheading, $showAdmin = FALSE)
	{
		echo 
		"<a href=\"./\"><img src=\"/style/images/orangeCANsmall.jpg\" /></a>
		<title>{$title}</title>
		<h3>{$heading}</h3>
		<h4>{$subheading}</h4>
		<hr />
		<div id=\"nav\">
			<ul>
				<li><a href=\"./\">Home</a></li>
				<li><a href=\"./addressEntry.php?clean=1\">Add a client</a></li>
				<li><a href=\"./search.php?clean=1\">Search for a client</a></li>
			";
		if ($showAdmin)
		{
			echo "\t<li><a href=\"./administration.php\">Administration</a></li>\n";
		}
			echo "</ul>
		</div>";
	}
	
	function showErrors()
	{
		if (!empty($_SESSION['errors']))
		{
			echo "\n<div class='errors'>\n\t<ul>\n";
			foreach($_SESSION['errors'] as $error)
			{
				echo "\t\t<li>" . $error . "</li>\n";
			}
			echo "\t</ul>\n</div>";
			$_SESSION['errors'] = array();
		}
	}
	
	function showClientEntrySteps($stepNumber)
	{
		$style = "style=\"color:red\"";
		$atStepOne = ($stepNumber == 1)? $style : "";
		$atStepTwo = ($stepNumber == 2)? $style : "";
		$atStepThree = ($stepNumber == 3)? $style : "";
		$atStepFour = ($stepNumber == 4)? $style : "";
		echo "
		<div id=\"clientEntrySteps\">
			<label for=\"formSteps\">Form steps:</label>
			<ol id=\"formSteps\">
				<a href=\"addressEntry.php?nosearch=1\"><li {$atStepOne}>Address Entry and Search</li></a>
				<a href=\"addressEntry.php\"><li {$atStepTwo}>Address Selection</li></a>
				<a href=\"clientEntry.php\"><li {$atStepThree}>Personal Information</li></a>
				<a href=\"clientConfirm.php\"><li {$atStepFour}>Confirmation and Submission</li></a>
			</ol>
		</div>";
	}
	
	?>