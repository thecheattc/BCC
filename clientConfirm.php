<?php 	include 'client.php'; ?>
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
					<td><?php echo $_POST['date']; ?></td>
					<td><button type="button" id="e1">Edit Client Info</button></td>
				</tr>
				<tr>
					<td><label>First Name: </label></td>
					<td><?php echo $_POST['cfName']; ?></td>
					<td><button type="button" id="e2">Edit Client Info</button></td>
				</tr>
				<tr>
					<td><label>Last Name: </label></td>
					<td><?php echo $_POST['clName']; ?></td>
					<td><button type="button" id="e3">Edit Client Info</button></td>
				</tr>
				<tr>
					<td><label>Current Address: </label></td>
					<td><?php echo $_POST['cAddress']; ?></td>
					<td><button type="button" id="e4">Edit Client Info</button></td>
				</tr>
				<tr>
					<td><label>Second Address: </label></td>
					<td><?php echo $_POST['cAddress2']; ?></td>
					<td><button type="button" id="e5">Edit Client Info</button></td>
				</tr>
				<tr>
					<td><label>Current City: </label></td>
					<td><?php echo $_POST['cCity']; ?></td>
					<td><button type="button" id="e6">Edit Client Info</button></td>
				</tr>
				<tr>
					<td><label>Zip Code: </label></td>
					<td><?php echo $_POST['cZip']; ?></td>
					<td><button type="button" id="e7">Edit Client Info</button></td>
				</tr>
				<tr>
					<td><label>Phone Number: </label></td>
					<td><?php echo $_POST['cPhone']; ?></td>
					<td><button type="button" id="e8">Edit Client Info</button></td>
				</tr>
				<tr>
					<td><label>Client Age: </label></td>
					<td><?php echo $_POST['cAge']; ?></td>
					<td><button type="button" id="e9">Edit Client Info</button></td>
				</tr>
				<tr>
					<td><label>Client Gender: </label></td>
					<td><?php echo $_POST['gengroup']; ?></td>
					<td><button type="button" id="e10">Edit Client Info</button></td>
				</tr>
				<tr>
					<td><label>Client Ethnicity: </label></td>
					<td><?php echo $_POST['ethgroup']; ?></td>
					<td><button type="button" id="e11">Edit Client Info</button></td>
				</tr>
				<tr>
					<td><label>Reason for Assistance: </label></td>
					<td><?php echo $_POST['reasongroup']; ?></td>
					<td><button type="button" id="e11">Edit Client Info</button></td>
				</tr>
				<?php
					if($uDate=="Click here to enter a date."){
						$udate = '';}
					if($_POST['reasongroup']==1){
					echo '
					<tr>
						<td><label>Unemployment Date: </label></td>
						<td>'.$uDate.'</td>
						<td><button type="button" id="e12">Edit Client Info</button></td>
					</tr>';
					}
					$house = $_POST['houseNum'];
					$ages = $_POST['hAge'];
					$multAges = explode(",",$ages);
					//echo $multAges[0];
					if($house>1){
						echo '<tr>
								<td><label>Number in Household<label></td>
								<td>'.$house.'</td>
								<td></td>
							</tr>';
							
						for($i=0;$i<$house;$i++){
							//echo $multAges[$i];
							$e = $i + 13;
							echo '<tr><td><label>House Member Age: </label></td>';
							echo '<td>'.$multiAges[$i].'</td>';
							echo '<td><button type="button" id="e'.$e.'">Edit Client Info</button></td></tr>';
						}
					}
				?>
				<tr class="noborder">					
					<td><button type="submit" name="subClientConfirm">Add the Client</button></td>
				</tr>
	
				<?php
					$client=Client::create();
				?>
			</table>
		</div><!-- /confirm -->
	</body>
	
</html>