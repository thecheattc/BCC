<?php
  session_start();
  include ('models/sqldb.php');
  include ('controllers/utility.php');
	include ('models/administrator.php');
	include ('models/house.php');
  include ('models/familyMember.php');
  include ('models/client.php');
  include ('models/gender.php');
  include ('models/ethnicity.php');
  include ('models/reason.php');

	$_SESSION['errors'] = array();
	if (!hasAccess())
	{
		$_SESSION['errors'][] = "This operation requires administrative privileges.";
		header("Location: ./");
		exit();
	}
  
	resetTimeout();
	
	if (!isset($_GET['client']))
	{
		$_SESSION['errors'][] = "No client specified.";
		header("Location: ./search.php");
		exit();
	}
	
	$client = Client::getClientByID($_GET['client']);
	if (empty($client))
	{
		$_SESSION['errors'][] = "Request client not found.";
		header("Location: ./search.php");
		exit();
	}
	
	$gender = Gender::getGenderByID($client->getGenderID());
	$ethnicity = Ethnicity::getEthnicityByID($client->getEthnicityID());
	$reason = Reason::getReasonByID($client->getReasonID());
	
	$spouseID = $client->getSpouseID();
	if (isset($spouseID))
	{
		$spouse = Client::getClientByID($spouseID);
		if (empty($spouse))
		{
			$_SESSION['errors'][] = "Client is listed as having a spouse but none could be found.";
		}
	}
	
	$houseID = $client->getHouseID();
	if (isset($houseID))
	{
		$house = House::getHouseByID($houseID);
		if (empty($house))
		{
			$_SESSION['errors'][] = "Client is listed as having a home but none could be found.";
		}
	}
	
	$familyMemberObjects = FamilyMember::getAllFamilyMembersForClient($client->getClientID(), $spouseID, $houseID);
	
	if (empty($familyMemberObjects))
	{
		$familyMembers = "None";
	}
	else
	{
		$familyMembers = '';
		for ($i=0; $i < count($familyMemberObjects); $i++)
		{
			$familyMemberObject = $familyMemberObjects[$i];
			$j = $i+1;
			$familyGender = Gender::getGenderByID($familyMemberObject->getGenderID());
			$familyEthnicity = Ethnicity::getEthnicityByID($familyMemberObject->getEthnicityID());
			$familyMembers .= htmlentities("Child {$j}: age: {$familyMemberObject->getAge()}; gender: {$familyGender->getGenderDesc()}; 
																		 ethnicity: {$familyEthnicity->getEthnicityDesc()}") . "<br />"; 
		}
	}
	
	$address = (empty($house))? "None" : htmlentities($house->getStreetNumber() . " " . $house->getStreetName() . " " . $house->getStreetType()) . "<br />";
	$addressLine2 = $house->getLine2();
	if (!empty($addressLine2))
	{
		$address .= htmlentities($addressLine2) . "<br />";
	}
	$address .= htmlentities($house->getCity() . " " . $house->getZip());
	
	$spouse = empty($spouse)? "None" : htmlentities($spouse->getFirstName() . " " . $spouse->getLastName() . ", " 
						. $spouse->getAge() . ", client ID: " . $spouse->getClientID());
	
	$unemploymentDate = $client->getUnemploymentDate();
	$unemploymentDateAsString = $unemploymentDate->format("m-d-Y");
	$jobLossDate = empty($unemploymentDateAsString)? "N/A" : htmlentities($unemploymentDateAsString);
	
	$explanation = $client->getExplanation();
	$explanation = empty($explanation)? "None" : htmlentities($client->getExplanation());
	
	$onFoodStamps = $client->getReceivesStamps();
	$onFoodStamps = $onFoodStamps? "Yes" : "No";
	
	$wantsFoodStamps = $client->getWantsStamps();
	$wantsFoodStamps = $wantsFoodStamps? "Yes" : "No";

?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<link rel="stylesheet" href="style/bryant.css" type="text/css"/>
<script type="text/javascript" src="scripts/js/jquery-1.4.4.min.js"></script>
	<head>
	</head>
	<body>
		<div id="header">
		<?php showHeader("BCC Client Information", "{$client->getFirstName()} {$client->getLastName()}'s Account Information", "", FALSE); ?>
		</div>
		<?php showErrors();	?>
		<div id="clientInfo">
			<p> Client id:  <?php echo $client->getClientID(); ?></p>
			<p> Application date: <?php $appDate = $client->getApplicationDate(); echo $appDate->format("m-d-Y"); ?></p>
			<p> Name: <?php echo $client->getFirstName() . " " . $client->getLastName(); ?></p>
			<p> Spouse: <?php echo $spouse; ?></p>
			<p> Address:<br /> <?php echo $address; ?></p>
			<p> Phone number: <?php echo $client->getPhoneNumber(); ?></p>
			<p> Age: <?php echo $client->getAge(); ?></p>
			<p> Gender: <?php echo $gender->getGenderDesc(); ?></p>
			<p> Ethnicity: <?php echo $ethnicity->getEthnicityDesc(); ?></p>
			<div id="familyMemberInfo">
				<p> Children: <br/><?php echo $familyMembers; ?></p>
			</div>
			<p> Reason for assistance: <?php echo $reason->getReasonDesc(); ?></p>
			<p> Date of job loss: <?php echo $jobLossDate; ?></p>
			<p> Explanation of reason: <?php echo $explanation; ?></p>
			<p> Receiving food stamps?: <?php echo $onFoodStamps; ?></p>
			<p> Interested in information about food stamps?: <?php echo $wantsFoodStamps; ?></p>
		</div>
	</body>
</html>








