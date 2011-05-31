<?php
	session_start();
	define("ROOT_ACCESS_ID", 1);

  
  function processString ($string, $stripSpaces = FALSE, $lenLimit = TRUE)
  {
		$pattern = '/[^\w\.\,\s\/]/';
		$string = stripslashes(trim($string));
		if ($lenLimit && strlen($string) > 20)
		{
			return NULL;
		}
    if ($stripSpaces)
    {
      $string = str_replace(' ', '', $string);
    }
		if (preg_match($pattern, $string) !== 0)
		{
			return NULL;
		}
    return $string;
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
	
	function processPhone($phoneNumber)
	{
		$pattern = '/[^\d.-]/';
		$string = stripslashes(trim($phoneNumber));
		if (empty($phoneNumber))
		{
			return TRUE;
		}
		if (preg_match($pattern, $string) !== 0)
		{
			return NULL;
		}
		return $string;
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
	
	function showHeader($title, $heading="", $subheading="", $showAdmin = FALSE)
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
	
	function linkWrap($linkTarget, $textToWrap, $shouldWrap)
	{
		if($shouldWrap)
		{
			return "<a href=\"{$linkTarget}\">{$textToWrap}</a>";
		}
		return "{$textToWrap}";
	}
	
	function showClientEntrySteps($stepNumber)
	{
		$style = "style=\"color:red\"";
		$atStepOne = ($stepNumber == 1)? $style : "";
		$atStepTwo = ($stepNumber == 2)? $style : "";
		$atStepThree = ($stepNumber == 3)? $style : "";
		$atStepFour = ($stepNumber == 4)? $style : "";
		$atStepFive = ($stepNumber == 5)? $style : "";
		$atStepSix = ($stepNumber == 6)? $style : "";
		echo "
		<div id=\"clientEntrySteps\">
			<label for=\"formSteps\">Form steps:</label>
			<ol id=\"formSteps\">";
		echo linkWrap("addressEntry.php?noHouseSearch=1", "<li {$atStepOne}>Address Entry and Search</li>", ($stepNumber > 1));
		echo linkWrap("addressEntry.php", "<li {$atStepTwo}>Address Selection</li>", ($stepNumber > 2));
		echo linkWrap("spouseEntry.php?noSpouseSearch=1", "<li {$atStepThree}>Spouse Entry and Search</li>", ($stepNumber > 3));
		echo linkWrap("spouseEntry.php", "<li {$atStepFour}>Spouse Selection</li>", ($stepNumber > 4));
		echo linkWrap("clientEntry.php", "<li {$atStepFive}>Personal Information</li>", ($stepNumber > 5));
		echo linkWrap("clientConfirm.php", "<li {$atStepSix}>Confirmation and Submission</li>", ($stepNumber > 6));
		echo "</ol>
		</div><br />";
	}
	
	//Given queries that return group names and counts of the occurrences of those group names
	// - in that order - this will
	//run each query and sum the counts of all identical keys to condense it into one array
	function runCountQueriesAndUnionResults($queryArray)
	{
		$resultsArray = array();
		foreach ($queryArray as $query)
		{
			$result = mysql_query($query);
			if ($result === FALSE)
			{
				return FALSE;
			}
			$queryResultSet = array();
			while ($row = mysql_fetch_array($result))
			{
				$queryResultSet[$row[0]] = (int) $row[1];
			}
			
			$resultsArray[] = $queryResultSet;
		}
		return array_add($resultsArray);
	}
	
	function array_add($arrayOfArraysToAdd)
	{
		if (empty($arrayOfArraysToAdd))
		{
			return $arrayOfArraysToAdd;
		}
		$result = $arrayOfArraysToAdd[0];
		for ($i=1; $i<sizeof($arrayOfArraysToAdd); $i++)
		{
			$curArray = $arrayOfArraysToAdd[$i];
			foreach ($curArray as $key => $val)
			{
				$result[$key] += $val;
			}
		}
		return $result;
	}
	
	function printKeyValue($array, $showSum = TRUE)
	{
		if (!is_array($array))
		{
			return FALSE;
		}
		$sum = 0;
		foreach($array as $key => $value)
		{
			$sum += $value;
			echo "\t{$key}: {$value}\n";
		}
		if ($showSum)
		{
			echo "Total: {$sum}\n";
		}
	}
	
	?>