<?php
  
  include ('models/sqldb.php');
  include ('models/client.php');
  include ('models/gender.php');
  include ('models/ethnicity.php');
  include ('models/reason.php');
  include ('controllers/utility.php');
  
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta name="original-source" content="http://commons.wikimedia.org/wiki/File:CampbellsModif.png">
		<meta name="original-source" content="http://upload.wikimedia.org/wikipedia/commons/a/a4/Old_Woman_in_Suzdal_-_Russia.JPG">
		<link rel="stylesheet" href="bryant.css" type="text/css"/>
		<link type="text/css" href="js/jquery-ui-1.8.10.custom/css/ui-lightness/jquery-ui-1.8.10.custom.css" rel="Stylesheet" />	
		<script type="text/javascript" 
			src="js/jquery-1.4.4.min.js"></script>
		<script src="js/jquery.simplemodal-1.4.1.js" type="text/javascript" language="javascript" charset="utf-8">
		</script>
		<script type="text/javascript" src="js/jquery-ui-1.8.10.custom/js/jquery-ui-1.8.10.custom.min.js"></script>
<?php
  $formArray = array(
                     'appDate'=> $_POST['date'],
                     'firstName'=> $_POST['cfName'],
                     'lastName'=> $_POST['clName'],
                     'address'=> $_POST['cAddress'],
                     'city'=> $_POST['cCity'],
                     'zip'=> $_POST['cZip'],
                     'phone'=> $_POST['cPhone'],
                     'age'=> $_POST['cAge'],
                     'gender'=> $_POST['gengroup'],
                     'ethnic'=> $_POST['ethgroup'],
                     'reason'=> $_POST['reasongroup'],
                     'udate'=> $_POST['uDate']
                     );
  $serializedForm =  base64_encode(serialize($formArray));
  $ethnicity = Ethnicity::getEthnicityByID($formArray['ethnic']);
  $gender = Gender::getGenderByID($formArray['gender']);
  $reason = Reason::getReasonByID($formArray['reason']);
  $ethDesc = (!empty($ethnicity))? $ethnicity->getEthnicityDesc() : "";
  $genDesc = (!empty($gender))? $gender->getGenderDesc() : "";
  $reasonDesc = (!empty($reason))? $reason->getReasonDesc() : "";
?>
	
		<title>Bryant Food Distribution Client Data Confirmation page</title>
	</head>
	
	<body>
		<div id="header">
			<h1>Confirm Client Information</h1>
			<h2>Please check that the information you have entered is correct.</h2>
			<hr/>
		</div><!-- /header -->
		<div id="clientCheck">
			<table >
				<tr>
					<td><label>Application Date: </label></td>
					<td><?php echo $formArray['appDate']; ?></td>
					<td><button type="button" id="e1">Edit Client Info</button></td>
				</tr>
				<tr>
					<td><label>First Name: </label></td>
					<td><?php echo $formArray['firstName']; ?></td>
					<td><button type="button" id="e2">Edit Client Info</button></td>
				</tr>
				<tr>
					<td><label>Last Name: </label></td>
					<td><?php echo $formArray['lastName']; ?></td>
					<td><button type="button" id="e3">Edit Client Info</button></td>
				</tr>
				<tr>
					<td><label>Current Address: </label></td>
					<td><?php echo $formArray['address']; ?></td>
					<td><button type="button" id="e4">Edit Client Info</button></td>
				</tr>
				<tr>
					<td><label>Current City: </label></td>
					<td><?php echo $formArray['city']; ?></td>
					<td><button type="button" id="e6">Edit Client Info</button></td>
				</tr>
				<tr>
					<td><label>Zip Code: </label></td>
					<td><?php echo $formArray['zip']; ?></td>
					<td><button type="button" id="e7">Edit Client Info</button></td>
				</tr>
				<tr>
					<td><label>Phone Number: </label></td>
					<td><?php echo $formArray['phone']; ?></td>
					<td><button type="button" id="e8">Edit Client Info</button></td>
				</tr>
				<tr>
					<td><label>Client Age: </label></td>
					<td><?php echo $formArray['age']; ?></td>
					<td><button type="button" id="e9">Edit Client Info</button></td>
				</tr>
				<tr>
					<td><label>Client Gender: </label></td>
					<td><?php echo $genDesc; ?></td>
					<td><button type="button" id="e10">Edit Client Info</button></td>
				</tr>
				<tr>
					<td><label>Client Ethnicity: </label></td>
					<td><?php echo $ethDesc; ?></td>
					<td><button type="button" id="e11">Edit Client Info</button></td>
				</tr>
				<tr>
					<td><label>Reason for Assistance: </label></td>
					<td><?php echo $reasonDesc; ?></td>
					<td><button type="button" id="e11">Edit Client Info</button></td>
				</tr>
				<tr>
					<td><label>Unemployment date</label></td>
					<td><?php echo $formArray['udate']; ?></td>
					<td></td>
				</tr>
				<tr class="noborder">		
          <form method="POST" action="controllers/addClient.php">
            <input type="hidden" name="formData" value=<?php echo $serializedForm ?>/>
            <td><input type="submit" name="subClientConfirm" value = "Add client" /></td>
          </form>
				</tr>
			</table>
		</div><!-- /confirm -->
	</body>
</html>