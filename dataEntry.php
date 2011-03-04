<?php 	include 'bryant_db.php'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<meta name="original-source" content="http://commons.wikimedia.org/wiki/File:CampbellsModif.png">
<meta name="original-source" content="http://upload.wikimedia.org/wikipedia/commons/a/a4/Old_Woman_in_Suzdal_-_Russia.JPG">
<link rel="stylesheet" href="bryant.css" type="text/css"/>
<link type="text/css" href="js/jquery-ui-1.8.10.custom/css/ui-lightness/jquery-ui-1.8.10.custom.css" rel="Stylesheet" />	
<script type="text/javascript" 
			src="js/jquery-1.4.4.min.js"></script>
<script src="js/jquery.simplemodal-1.4.1.js" type="text/javascript" language="javascript" charset="utf-8">
</script>
<script type="text/javascript" src="js/jquery-ui-1.8.10.custom/js/jquery-ui-1.8.10.custom.min.js"></script>
<script>
	$(document).ready(function() {
   //Makes the date inputs appear if lost job is selected      
    $("#reasongroup").change(function () {
       		   $("#reasongroup option:selected").each(function () {
         		 	if($(this).text()=="Lost job"){
          		 		$(".show").show("slow");
          			}
          			else{
          				$(".show").hide("slow");
          			}
             	 });
       		 })
        .change();
    //Gets the value from the number in household Input
    //and puts it in a variable
    $("#houseNum").focusout(function(){
        var value = $("#houseNum").val();
        var val=value;
       	var msg="Please enter a whole number (example 1)."; 
       	
       	if(val !=""){
       			var result = /\d+(?:\.\d+)?/.exec(val);
       			if(result != null){
       				if(result<16){
       					for($i=0;$i<result;$i++){
       						var $inputs = ('<tr><td><label>Age of House Member:</label></td><td><input type="text" size="2" maxlength="2" name="hAge'+$i+'"/></td></tr>');
							$('.add').append($inputs);  
						}
					}
					else{
						window.alert('Only 15 members per house are allowed.');
					}
				}
       			else{
        			window.alert(msg);
        		}
        }
  	  });
  	  $('#date').datepicker({ altFormat: 'yy-mm-dd' });
  	  $('#uDate').datepicker({ altFormat: 'yy-mm-dd' });
    });

</script>			
	<?php 
             $myDBConnection = new dbHandler();
             $myDBConnection->db_connect();
	?>
<head>
<title>Bryant Food Distribution Client Data Entry Input Screen</title>
</head>
	<body>
		<div id="header">
			<h1>Add a New Client</h1>
			<h2>Enter the information for a new client</h2>
			<hr/>
		</div><!-- /header -->
		<div id="newClient">
			<form method="post">
			<fieldset>
				<legend>Enter data for a new client</legend>
				<table>
					<tr>
						<td><label for="appDate">Date of Application:</label></td>
						<td><input type="text" name="date" id="date" value="Click here to enter a date." /></td>
					</tr>
					<tr>
						<td><label for="cfName">First Name: </label></td>
						<td><input type="text" size="60" name="cfName"/></td>
					</tr>
					<tr>
						<td><label for="clName">Last Name: </label></td>
						<td><input name="clName" type="text" size="60" /></td>
					</tr>
					<tr>
						<td><label for="cAddress">Current Address: </label></td>
						<td><input name="cAddress" type="text" size="80" /></td>
					</tr>
					<tr>
						<td></td>
						<td><input name="cAddress2" type="text" size="80" /></td>
					</tr>
					<tr>
						<td><label for="cCity">Current City: </label></td>
						<td><input name="cCity" type="text" size="50" /></td>
					</tr>
					<tr>
						<td><label for="cZip">Zip Code: </label></td>
						<td><input name="cZip" type="text" size="11" maxlength="11" /></td>
					</tr>
					<tr>
						<td><label for="cPhone">Phone Number: <span class="example">(111-222-3333)</span></label></td>
						<td><input name="cPhone" type="text" size="12" maxlength="12" /></td>
					</tr>
					<tr>
						<td><label for="cAge">Client Age: </label></td>
						<td><input name="cAge" type="text" size="2" maxlength="3" /></td>
					</tr>
					<tr>
						<td><label for="gengroup">Client Gender: </label></td>
						<td>Male: <input name="gengroup" type="radio" value="1" /> Female: <input name="gengroup" type="radio" value="2" /></td>
					</tr>
					<tr>
						<td><label for="ethgroup">Client Ethnicity: </label></td>
						<td><select id="ethgroup" name="ethgroup">
								<option value="1">White</option>
								<option value="2">African American</option>
								<option value="3">Hispanic</option>
								<option value="4">Asian or Pacific Islander</option>
								<option value="5">American Indian</option>
							</select>
						</td>
					</tr>
					<tr>
						<td><label for="reasongroup">Reason For Assistance: </label></td>
						<td><select id="reasongroup" name="reasongroup">
							<option value="0">Select a reason</option>
							<option value="1">Lost job</option>
							<option value="2">Unusual expenses this month</option>
							<option value="3">To make ends meet</option>
							<option value="4">Assistance lost/reduced</option>
							<option value="5">DHS application in progress</option>
							<option value="6">Homeless</option>
							<option value="7">Other</option>
							
						</select></td>
						

					</tr>
					
					<tr>
						<td><div class="show" style="display:none;"><label for="uDate">Date of Job Loss: </label></div></td>
						<td><input class="show" type="text" name="uDate" id="uDate" value="Click here to enter a date." />
						</td>
					</tr>
					<tr>
						<td><label for="houseNum">Number of People in Household:</label></td>
						<td><input name="houseNum" id="houseNum" type="text" size="2" maxlength="2" numeric="integer" /></td>
					</tr>
					<tr class="add"></tr>
					<tr>
						<td><button type="submit" name="clientSub" value="Done" >Add New Client</button></td>
						<td></td>
					</tr>
				</table>
			</fieldset>
			</form>
			<p>
			
			</p>
		</div><!-- /newClient -->
	</body>
	<!-- DATA ENTRY SUBMISSION HANDLER -->
	<?php
		if($_POST['clientSub'] == 'Done'){
			
				//Adds the client info to the client table
				include 'client.php';
				//include 'house.php';
				$firstName = $_POST['cfName'];
				$lastName = $_POST['clName'];
				$age = $_POST['cAge'];
				$phoneNumber = $_POST['cPhone'];
				//$applicationDate = $_POST['date'];
				//$unemploymentDate = $_POST['uDate'];
				create();
				save();
				
			
				//adds the client house info to the house table. 
				/*$address = $_POST['cAddress'];
				$city = $_POST['cCity'];
				$zip = $_POST['cZip'];
			
			echo $applicationDate;*/
		}
	?>
</html>
