<?php
  session_start();
  include('models/sqldb.php');
  include('controllers/utility.php');
	include('models/administrator.php');
  include('models/gender.php');
  include('models/ethnicity.php');
  include('models/reason.php');
	
	if (!hasAccess(TRUE))
	{
		$_SESSION['errors'] = array();
		$_SESSION['errors'][] = "This operation requires administrative privileges.";
		header("Location: ./");
		exit();
	}
	resetTimeout();
	$genders = Gender::getAllGenders();
	$removableGenderIDs = Gender::getRemovableGenderIDs();
  $ethnicities = Ethnicity::getAllEthnicities();
	$removableEthnicityIDs = Ethnicity::getRemovableEthnicityIDs();
  $reasons = Reason::getAllReasons(); 
	$removableReasonIDs = Reason::getRemovableReasonIDs();
	$genderID = '';
	$ethnicityID = '';
	$reasonID = '';
	$newEthnicity = '';
	$newReason = '';
	$newGender = '';
	
  if (!empty($_SESSION['errors']))
  {
		$genderID = $_SESSION['genderID'];
		$ethnicityID = $_SESSION['ethnicityID'];
		$reasonID = $_SESSION['reasonID'];
		$newEthnicity = $_SESSION['newEthnicity'];
		$newReason = $_SESSION['newReason'];
		$newGender = $_SESSION['newGender'];
	}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<link rel="stylesheet" href="style/bryant.css" type="text/css"/>
	<link type="text/css" href="scripts/js/jquery-ui-1.8.10.custom/css/ui-lightness/jquery-ui-1.8.10.custom.css" rel="Stylesheet" />
</head>
	<body>
		<div id="header">
			<?php showHeader("BCC Form Options", "", "Add or remove options from the client entry form", TRUE); ?>
		</div>
		<p>All options marked with a * are not currently in use and are removable.</p>
		<div id="error">
		<?php showErrors();	?>
		</div>
		<div id="editForms">
			<h4>Ethnicities</h4>
			<form id="addEthnicity" method="post" action="controllers/editForm.php" />
				<input name="action" type="hidden" value="addEthnicity" />
				<input type="text" name="newEthnicity" value="<?php echo $newEthnicity; ?>" />
				<input type="submit" value="Add ethnicity" />
			</form>
			<form id="removeEthnicity" method="post" action="controllers/editForm.php">
				<input name="action" type="hidden" value="removeEthnicity" />
				<select name="ethnicityID">
					<?php 
						foreach ($ethnicities as $ethnicity)
						{
							echo "\t\t\t";
							echo '<option value="' . $ethnicity->getEthnicityID() . '"';
							if ($ethnicityID == $ethnicity->getEthnicityID()){ echo " selected "; }
							echo '>' . $ethnicity->getEthnicityDesc();
							if (in_array($ethnicity->getEthnicityID(), $removableEthnicityIDs)) { echo "*"; }
							echo "</option>\n";
						}
						?>
				</select>
				<input type="submit" value="Remove selected ethnicity" />
			</form>
			<h4>Reasons for getting food</h4>
			<form id="addReason" method="post" action="controllers/editForm.php" />
				<input name="action" type="hidden" value="addReason" />
				<input type="text" name="newReason" value="<?php echo $newReason; ?>" />
				<input type="submit" value="Add reason" />
			</form>
			<form id="removeReason" method="post" action="controllers/editForm.php">
				<input name="action" type="hidden" value="removeReason" />
					<select name="reasonID">
						<?php 
							foreach ($reasons as $reason)
							{
								echo "\t\t\t";
								echo '<option value="' . $reason->getReasonID() . '"';
								if ($reasonID == $reason->getReasonID()){ echo " selected"; }
								echo '>' . $reason->getReasonDesc();
								if (in_array($reason->getReasonID(), $removableReasonIDs)) { echo "*"; }
								echo "</option>\n";
							}
							?>
					</select>
				<input type="submit" value="Remove selected reason" />
			</form>
			<h4>Genders</h4>
			<form id="addGender" method="post" action="controllers/editForm.php" />
				<input name="action" type="hidden" value="addGender" />
				<input type="text" name="newGender" value="<?php echo $newGender; ?>" />
				<input type="submit" value="Add gender" />
			</form>
			<form id="removeGender" method="post" action="controllers/editForm.php">
				<input name="action" type="hidden" value="removeGender" />
					<select name="genderID">
						<?php 
							foreach ($genders as $gender)
							{
								echo "\t\t\t";
								echo '<option value="' . $gender->getGenderID() . '"';
								if ($genderID == $gender->getGenderID()) { echo " selected"; }
								echo '>' . $gender->getGenderDesc();
								if (in_array($gender->getGenderID(), $removableGenderIDs)) { echo "*"; }
								echo "</option>\n";
							}
							?>
					</select>
				<input type="submit" value="Remove selected gender" />
			</form>
		</div>
	</body>

</html>