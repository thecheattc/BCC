<?php
  session_start();
  include('models/sqldb.php');
  include('controllers/utility.php');
  include('models/visit.php');
  include('models/gender.php');
  include('models/ethnicity.php');
  include('models/reason.php');
  include('models/client.php');
  include('models/house.php');
  
  define("LOST_JOB", 1);
  define("MAX_FAMILY_MEMBERS", 20);
  
  //Set the houseID so the controller will know how to handle it.
  //Only do this when coming from the addressEntry page
  if (!isset($_SESSION['fromConfirm']))
  {
    if (empty($_POST['houseID']))
    {
      if (!isset($_SESSION['errors']))
      {
        $_SESSION['errors'] = array();
      }
      $_SESSION['errors'][] = "Please select an address from the list.";
      header("Location: addressEntry.php");
      exit();
    }
    else
    {
       $_SESSION['houseID'] = $_POST['houseID'];
    }
  }
  
  $_SESSION['fromConfirm'] = NULL;
  $_SESSION['errors'] = NULL;
  
  $genders = Gender::getAllGenders();
  $ethnicities = Ethnicity::getAllEthnicities();
  $reasons = Reason::getAllReasons();
  $changed = "added";
  $changing = "adding";
  
  if (!empty($_SESSION['edit']))
  {
    $changed = "edited";
    $changing = "editing";
    $client = Client::getClientByID($_SESSION['clientID']);
    if($client === NULL)
    {
      session_destroy();
      header("Location: search.php?error=1");
    }
    else
    { 
      $_SESSION['clientID'] = $client->getClientID();
      $_SESSION['appDate'] = $client->getApplicationDate();
      $_SESSION['firstName'] = $client->getFirstName();
      $_SESSION['lastName'] = $client->getLastName();
      $_SESSION['number'] = $client->getPhoneNumber();
      $_SESSION['age'] = $client->getAge();
      $_SESSION['gengroup'] = $client->getGenderID();
      $_SESSION['ethgroup'] = $client->getEthnicityID();
      $_SESSION['reasongroup'] = $client->getReasonID();
      $_SESSION['explanation'] = $client->getExplanation();
      $_SESSION['uDate'] = $client->getUnemploymentDate();
      $_SESSION['receivesStamps'] = $client->getReceivesStamps();
      $_SESSION['wantsStamps'] = $client->getWantsStamps();
      
      //Populate session with client children
      $familyMembers = $client->getAllFamilyMembers();
      $sessionFamilyMembers = array();
      foreach($familyMembers as $familyMember)
      {
        $sessionFamilyMember = array();
        $sessionFamilyMember["age"] = $familyMember->getAge();
        $sessionFamilyMember["gender"] = $familyMember->getGenderID();
        $sessionFamilyMember["ethnicity"] = $familyMember->getEthnicityID();
        $sessionFamilyMembers[] = $sessionFamilyMember;
      }
      $_SESSION['memberCount'] = count($sessionFamilyMembers);
      $_SESSION['familyMembers'] = $sessionFamilyMembers;
    }
  }
  
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
      $("#reasongroup option:selected").each(function () {
          if($(this).text() === "Lost job"){
              $(".showUDate").show("slow");
           }
          else{
              $(".showUDate").hide("slow");
          }
      });
                      
  		 //Makes the date inputs appear if lost job is selected      
      $("#reasongroup").change(function() {
          $("#reasongroup option:selected").each(function () {
              if($(this).text() === "Lost job"){
                 $(".showUDate").show("slow");
              }
              else{
                 $(".showUDate").hide("slow");
              }
             });
            });
                      
  	 //Popup date pickers for application date and unemployment date
  	  $('#appDate').datepicker({ dateFormat: 'mm-dd-yy' });
  	  $('#uDate').datepicker({ dateFormat: 'mm-dd-yy' });  	 
    });
	</script>
  <script>
    function changeToDo(toDo) {
      $("#toDo").val(toDo);
    }
  </script>
	
	<title>Bryant Food Distribution Client Data Entry Screen</title>
</head>
	<body>
		<div id="header">
    <?php 
      if (isset($_SESSION['edit']))
      {
        echo "<h1>Edit Client</h1>
        <h2>Enter the information to change this client</h2>";
      }
      else
      {
        echo " <h1>Add a New Client</h1>
        <h2>Enter the information for a new client</h2>";
      }
      ?>
			<hr/>
			<ul>
				<li><a href="selectTask.php">Select a Task</a></li>
				<li><a href="search.php">Search for a Client</a></li>
        <?php
          if (isset($_SESSION['edit']))
          {
            echo '<li><a href="addressEntry.php?clean=1">Add a new client</a></li>';
          }
        ?>
			</ul>
		</div><!-- /header -->
		<div id="newClient">
			<form method="post" action="clientConfirm.php">
      <input name="toDo" id="toDo" type="hidden" value="submit" />
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
						<td><label for="number">Phone Number: <span class="example">(111-222-3333)</span></label></td>
						<td><input name="number" id="number"type="text" size="16" maxlength="16" value="<?php echo $_SESSION['number']; ?>" /></td>
					</tr>
					<tr>
						<td><label for="age">Client Age: </label></td>
						<td><input name="age" type="text" size="2" maxlength="3" value="<?php echo $_SESSION['age']; ?>"/></td>
					</tr>
					<tr>
						<td><label for="gengroup">Client Gender: </label></td>
						<td>
                <?php 
                  foreach ($genders as $gender)
                  {
                    echo "\t\t\t\t\t\t";
                    echo $gender->getGenderDesc() . ': <input name="gengroup" type="radio" value="' . $gender->getGenderID() . '"';
                    if (isset($_SESSION['gengroup']) && $_SESSION['gengroup'] == $gender->getGenderID()){ echo " checked"; }
                    echo "/>\n";
                  }
                  ?>
            </td>
					</tr>
					<tr>
						<td><label for="ethgroup">Client Ethnicity: </label></td>
						<td><select id="ethgroup" name="ethgroup">
                <option value="0" <?php if (!isset($_SESSION['ethgroup'])) echo "selected"; ?>>Select an ethnicity</option>
                <?php 
                  foreach ($ethnicities as $ethnicity)
                  {
                    echo "\t\t\t\t\t\t";
                    echo '<option value="' . $ethnicity->getEthnicityID() . '"';
                    if (isset($_SESSION['ethgroup']) && $_SESSION['ethgroup'] == $ethnicity->getEthnicityID()){ echo " selected"; }
                    echo '>' . $ethnicity->getEthnicityDesc();
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
                  echo '<option value="' . $reason->getReasonID() . '"';
                  if (isset($_SESSION['reasongroup']) && $_SESSION['reasongroup'] == $reason->getReasonID()){ echo " selected"; }
                  echo  '>' . $reason->getReasonDesc();
                  echo "</option>\n";
                }
                ?>
						</select></td>
					</tr>	
          <?php
            $_SESSION['memberCount'] = (!isset($_SESSION['memberCount']))? 0: $_SESSION['memberCount'];
            for($i = 0; $i<$_SESSION['memberCount']; $i++)
            {
              //Echo out family member values
              $familyMember = $_SESSION['familyMembers'][$i];
              $j = $i+1;
              echo "\t<tr>\n\t\t<td>" . '<label for="' . "memberAge{$i}" . '">Family member ' . $j . ' age: ' . "</label></td>\n";
              echo "\t\t<td>" . '<input type="text" name="' . "memberAge{$i}" . '" value="' . $familyMember["age"] . '" /></td>' . "\n\t</tr>\n";
              echo "\t<tr>\n\t\t<td><label for=" . '"' . "memberGender{$i}" . '">Family member ' . $j . ' gender: ' . "</label></td>\n";
              echo "\t\t<td>"; 
              foreach ($genders as $gender)
              {
                echo $gender->getGenderDesc() . ': <input name="' . "memberGender{$i}" . '" type="radio" value="' . $gender->getGenderID() . '"';
                if ($familyMember["gender"] == $gender->getGenderID()){ echo " checked"; }
                echo "/>";
              }
              echo "</td>\n\t</tr>\n";
              echo "\t<tr>\n\t\t<td>" . '<label for="' . "memberEthnicity{$i}" . '">Family member ' . $j . ' ethnicity: ' . "</label></td>\n";
              echo "\t\t<td>" . '<select name="' . "memberEthnicity{$i}" . '">' . "\n";
              echo '<option value="0" ';
              if (!isset($familyMember["ethnicity"])){ echo " selected"; }
              echo ">Select an ethnicity</option>\n";
              foreach ($ethnicities as $ethnicity)
              {
                echo "\t\t\t";
                echo '<option value="' . $ethnicity->getEthnicityID() . '"';
                if ($familyMember["ethnicity"] == $ethnicity->getEthnicityID()){ echo " selected"; }
                echo '>' . $ethnicity->getEthnicityDesc();
                echo "</option>\n";
              }
              echo "\t\t</select></td>\n\t</tr>";
            }
            if (!isset($_SESSION['memberCount']) || $_SESSION['memberCount'] < MAX_FAMILY_MEMBERS)
            {
              echo "\t<tr>\n\t\t<td><input type=" . '"submit" onClick="changeToDo(' . "'addMember');" . '" value="Add family member"/></td></tr>';
              echo "\n";
            }
            if (isset($_SESSION['memberCount']) && $_SESSION['memberCount'] > 0)
            {
              echo "\t<tr>\n\t\t<td><input type=" . '"submit" onClick="changeToDo(' . "'deleteMember');" . '" value="Remove family member"/></td></tr>'; 
              echo "\n";
            }
            ?>
          <tr>
            <td><label for="explanation">Explanation (required if the reason is "Other"): </label></div></td>
            <td><input type="text" name="explanation" id="explanation" value="<?php echo $_SESSION['explanation']; ?>"/></td>
          </tr>
					<tr>
						<td><div class="showUDate" style="display:none;"><label for="uDate">Date of Job Loss: </label></div></td>
						<td><input class="showUDate" style="display:none;"type="text" name="uDate" id="uDate" 
                  value="<?php echo $_SESSION['uDate']; ?>"/></td>
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
						<td><input type="submit" name="clientSub" id="clientSub" onClick="changeToDo('submit');"value="<?php 
                                                                                if(!empty($_SESSION['edit']))
                                                                                  {
                                                                                    echo 'Edit Client';
                                                                                  }
                                                                                  else
                                                                                  {
                                                                                    echo 'Add New Client';
                                                                                  }
                                                                                ?>"/></td>
					</tr>
				</table>
			</fieldset>
			</form>
      <a href="addressEntry.php">Back to address information</a>
		</div><!-- /newClient -->
<?php 
  if (!empty($_SESSION['clientID']))
  {
    echo "<a href=controllers/deleteClient.php?client=" . $_SESSION['clientID'] . " ";
    echo "onClick=" . '"' . "return confirm('Are you sure you want to delete this client? ";
    echo "Doing so will remove all information related to the client from the database.')" . '">Delete this client</a>';
  }
  ?>
	</body>

</html>