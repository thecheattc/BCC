<?php

  include('models/sqldb.php');
  include('models/gender.php');
  include('models/ethnicity.php');
  include('models/reason.php');
  
  if (!isset($_SESSION))
  {
    session_start();
  }
  echo "<PRE>";
  var_dump($_SESSION);
  echo "</PRE>";
  $genders = Gender::getAllGenders();
  $ethnicities = Ethnicity::getAllEthnicities();
  $reasons = Reason::getAllReasons();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta name="original-source" content="http://commons.wikimedia.org/wiki/File:CampbellsModif.png">
	<meta name="original-source" content="http://upload.wikimedia.org/wikipedia/commons/a/a4/Old_Woman_in_Suzdal_-_Russia.JPG">
	<link rel="stylesheet" href="style/bryant.css" type="text/css"/>
	<link type="text/css" href="scripts/js/jquery-ui-1.8.10.custom/css/ui-lightness/jquery-ui-1.8.10.custom.css" rel="Stylesheet" />	
	<script type="text/javascript" 
			src="scripts/js/jquery-1.4.4.min.js"></script>
	<script src="scripts/js/jquery.simplemodal-1.4.1.js" type="text/javascript" language="javascript" charset="utf-8">
	</script>
	<script type="text/javascript" src="scripts/js/jquery-ui-1.8.10.custom/js/jquery-ui-1.8.10.custom.min.js"></script>
	<script language="javascript" type="text/javascript">
		function stopRKey(evt) {
		var evt  = (evt) ? evt : ((event) ? event : null);
		var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
		if ((evt.keyCode == 13) && (node.type=="text")) { return false; }
		}
		document.onkeypress = stopRKey;
	</script>
	<script>
		$(document).ready(function() {
  		 //Makes the date inputs appear if lost job is selected      
   		 $("#reasongroup").change(function () {
       		   $("#reasongroup option:selected").each(function () {
         		 	if($(this).text() === "Lost job"){
          		 		$(".show").show("slow");
          			}
          			else{
          				$(".show").hide("slow");
          			}
             	 });
            });
   		
   	//Gets the value from the number in household Input
    $("#houseNum").focusout(function(){
        var val = $("#houseNum").val();
       	var msg="Please enter a whole number (example 1)."; 
       	
       	if(val !=""){
       			var result = /\d+(?:\.\d+)?/.exec(val);
       			if(result != null){
       				if(result<16){
       					var $inpHed = '<td>Age of House Member: </td><td>Gender of House Member: </td>';
       					$('#add').append($inpHed); 
       					for($i=0;$i<result;$i++){
       						var $inputs = ('<tr><td class="pad">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" size="2" maxlength="2" name="hAge'+$i+'"/></td><td class="pad"> Male: <input type="radio" name="hGen'+$i+'" value="1"> Female: <input type="radio" name="hGen'+$i+'" value="2"></td></tr>');
							    
                  $('#add1').append($inputs);  
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
                      
  	 //Popup date pickers for application date and unemployment date
  	  $('#appDate').datepicker({ dateFormat: 'mm-dd-yy' });
  	  $('#uDate').datepicker({ dateFormat: 'mm-dd-yy' });  	 
    });
	</script>		
	
	<title>Bryant Food Distribution Client Data Entry Input Screen</title>
</head>
	<body>
		<div id="header">
			<h1>Add a New Client</h1>
			<h2>Enter the information for a new client</h2>
      <?php 
        if (!empty($_GET['error']) && $_GET['error'] == 1)
        {
          echo "<h4 style='color:red;'>There was an error adding the client.</h4>";
        }
        elseif (!empty($_GET['success']) && $_GET['success'] == 1)
        {
          echo "<h4style='color:green;'>Client added successfully.</h4>";
        }
        ?>
			<hr/>
			<ul>
				<li><a href="selectTask.php">Select a Task</a></li>
				<li><a href="search.php">Search for a Client</a></li>
			</ul>
		</div><!-- /header -->
		<div id="newClient">
			<form method="post" action="clientConfirm.php">
			<fieldset>
				<legend>Enter data for a new client</legend>
				<table>
					<tr>
						<td><label for="appDate">Date of Application:</label></td>
						<td><input type="text" name="appDate" id="appDate" value="<?php echo $_SESSION['appDate']; ?>"/></td>
					</tr>
					<tr>
						<td><label for="firstName">First Name: </label></td>
						<td><input type="text" size="60" name="firstName" value="<?php echo $_SESSION['firstName']; ?>"/></td>
					</tr>
					<tr>
						<td><label for="lastName">Last Name: </label></td>
						<td><input name="lastName" type="text" size="60" value="<?php echo $_SESSION['lastName']; ?>"/></td>
					</tr>
					<tr>
						<td><label for="address">Current Address: </label></td>
						<td><input name="address" type="text" size="80" value="<?php echo $_SESSION['address']; ?>"/></td>
					</tr>
					<tr>
						<td><label for="city">Current City: </label></td>
						<td><input name="city" type="text" size="50" value="<?php echo $_SESSION['city']; ?>"/></td>
					</tr>
					<tr>
						<td><label for="zip">Zip Code: </label></td>
						<td><input name="zip" type="text" size="11" maxlength="11" value="<?php echo $_SESSION['zip']; ?>" /></td>
					</tr>
					<tr>
						<td><label for="number">Phone Number: <span class="example">(111-222-3333)</span></label></td>
						<td><input name="number" id="number"type="text" size="16" maxlength="16" value="<?php echo $_SESSION['phone']; ?>" /></td>
					</tr>
					<tr>
						<td><label for="age">Client Age: </label></td>
						<td><input name="age" type="text" size="2" maxlength="3" value="<?php echo $_SESSION['age']; ?>"/></td>
					</tr>
					<tr>
						<td><label for="gengroup">Client Gender: </label></td>
						<td>
                <?php foreach ($genders as $gender)
                  {
                    echo "\t\t\t\t\t\t";
                    echo $gender->getGenderDesc().': <input name="gengroup" type="radio" value="'.$gender->getGenderID().'"';
                    if (isset($_SESSION['gengroup']) && $_SESSION['gengroup'] == $gender->getGenderID()){ echo "checked "; }
                    echo "/>\n";
                  }
                  ?>
            </td>
					</tr>
					<tr>
						<td><label for="ethgroup">Client Ethnicity: </label></td>
						<td><select id="ethgroup" name="ethgroup">
                <option value="0" selected>Select an ethnicity</option>
                <?php foreach ($ethnicities as $ethnicity)
                  {
                    echo "\t\t\t\t\t\t";
                    echo '<option value="'.$ethnicity->getEthnicityID().'">'.$ethnicity->getEthnicityDesc();
                    if (isset($_SESSION['ethgroup']) && $_SESSION['ethgroup'] == $ethnicity->getEthnicityID()){ echo " selected "; }
                    echo "</option>\n";
                  }
                  ?>
							</select>
						</td>
					</tr>
					<tr>
						<td><label for="reasongroup">Reason For Assistance: </label></td>
						<td><select id="reasongroup" name="reasongroup">
              <option value="0" selected>Select a reason</option>
              <?php foreach ($reasons as $reason)
                {
                  echo "\t\t\t\t\t\t";
                  echo '<option value="'.$reason->getReasonID().'">'.$reason->getReasonDesc();
                  if (isset($_SESSION['reasongroup']) && $_SESSION['reasongroup'] == $reason->getReasonID()){ echo " selected "; }
                  echo "</option>\n";
                }
                ?>
						</select></td>
					</tr>					
					<tr>
						<td><div class="show" style="display:none;"><label for="uDate">Date of Job Loss: </label></div></td>
						<td><input class="show" style="display:none;"type="text" name="uDate" id="uDate" />
						</td>
					</tr>
					<tr>
          <?php 
            for($i=0; $i<$_SESSION['houseNum']; $i++)
            {
              $_SESSION["child{$i}"]->print();
            }
            ?>
					</tr>
          <tr>
            <td><label for="receivesStamps">Are you currently receving food stamps?</label></td>
            <td>
              No <input name="receivesStamps" id="receivesStamps" type="radio" value="0" <?php if (isset($_SESSION['receivesStamps']) &&
                                                                                                   $_SESSION['receivesStamps'] == 0) 
                                                                                                      {echo "checked";} ?>/>
              Yes <input name="receivesStamps" id="receivesStamps" type="radio" value="1" <?php if (isset($_SESSION['receivesStamps']) &&
                                                                                                    $_SESSION['receivesStamps'] == 1) 
                                                                                                      {echo "checked";} ?>/>
            </td>
          </tr>
          <tr>
            <td><label for="wantsStamps">If no, are you interested in finding out if you are eligible for food stamps?</label></td>
            <td>
              No <input name="wantsStamps" id="wantsStamps" type="radio" value="0" <?php if (isset($_SESSION['wantsStamps']) &&
                                                                                            $_SESSION['wantsStamps'] == 0) 
                                                                                              {echo "checked";} ?>/>
              Yes <input name="wantsStamps" id="wantsStamps" type="radio" value="1" <?php if (isset($_SESSION['wantsStamps']) &&
                                                                                              $_SESSION['wantsStamps'] == 1) 
                                                                                                {echo "checked";} ?>/>
            </td>
          </tr>
					<tr>
						<td><input type="submit" name="clientSub" id="clientSub" value="Add New Client" /></td>
					</tr>
					
				</table>
				
			</fieldset>
			</form>
		</div><!-- /newClient -->
	</body>

</html>
