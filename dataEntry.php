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
  
  $genders = Gender::getAllGenders();
  $ethnicities = Ethnicity::getAllEthnicities();
  $reasons = Reason::getAllReasons();
  $changed = "added";
  $changing = "adding";
  
  if (isset($_GET['clean']))
  {
    $_SESSION = NULL;
    session_destroy();
    
  }
  
  if (!empty($_SESSION['edit']) || !empty($_GET['client']) || (isset($_GET['edit']) && $_GET['edit'] == 1))
  {
    $changed = "edited";
    $changing = "editing";
  }
  
  if (!empty($_GET['client']))
  {
    $_SESSION['edit'] = TRUE;
    $client = Client::getClientByID($_GET['client']);
    
    if($client === NULL)
    {
      session_destroy();
      header("Location: search.php?error=1");
    }
    else
    {
      $house = NULL;
      if ($client->getHouseID() !== NULL)
      {
        $house = House::getHouseByID($client->getHouseID());
        if ($house === NULL)
        {
          session_destroy();
          header("Location: search.php?error=1");
        }
      }
      $_SESSION['client'] = $client->getClientID();
      $_SESSION['appDate'] = $client->getApplicationDate();
      $_SESSION['firstName'] = $client->getFirstName();
      $_SESSION['lastName'] = $client->getLastName();
      $_SESSION['address'] = ($house !== NULL)? $house->getAddress() : NULL;
      $_SESSION['city'] = ($house !== NULL)? $house->getCity() : NULL;
      $_SESSION['zip'] = ($house !== NULL)? $house->getZip() : NULL;
      $_SESSION['number'] = $client->getPhoneNumber();
      $_SESSION['age'] = $client->getAge();
      $_SESSION['gengroup'] = $client->getGenderID();
      $_SESSION['ethgroup'] = $client->getEthnicityID();
      $_SESSION['reasongroup'] = $client->getReasonID();
      $_SESSION['explanation'] = $client->getExplanation();
      $_SESSION['uDate'] = $client->getUnemploymentDate();
      $_SESSION['receivesStamps'] = $client->getReceivesStamps();
      $_SESSION['wantsStamps'] = $client->getWantsStamps();
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
            echo '<li><a href="dataEntry.php?clean=1">Add a new client</a></li>';
          }
        ?>
			</ul>
		</div><!-- /header -->
<?php 
  if (!empty($_SESSION['errors']))
  {
    $addressError = FALSE;
    echo '<div id="error">';
    echo "\n";
    echo "<h4 style='color:red;'>There was an error " . $changing . " the client. ";
    echo "Please make sure the the following fields are present and correct:</h4>\n";
    echo "\t<ul>\n";
    foreach ($_SESSION['errors'] as $error)
    {
      if ($error === "Address" || $error === "City" || $error === "Zip")
      {
        $addressError = TRUE;
      }
      echo "\t\t<li>$error</li>\n";
    }
    echo "\t</ul></div>\n";
    if ($addressError)
    {
      echo "<h5>For addresses, either list all parts of an address or no parts (if the client is homeless)</h5>\n";
    }

  }
  elseif (!empty($_GET['success']) && $_GET['success'] == 1)
  {
    echo "<h4 style='color:green;'>Client " . $changed . " successfully.</h4>";
  }
  ?>
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
          <?php          
            if($_SESSION['edit'])
            {
              echo "\n\t<tr>\n\t\t<td><label for='oldAddressValid'>If your address has changed, ";
              echo "are there still people registered with Bryant at the old address?</label></td>\n";
              echo "\t\t<td>No <input name='oldAddressValid' id='oldAddressValid' type='radio' value='0' ";
              if (isset($_SESSION['oldAddressValid']) && $_SESSION['oldAddressValid'] == 0){ echo "checked"; }
              echo "/> Yes <input name='oldAddressValid' id='oldAddressValid' type='radio' value='1' ";
              if (isset($_SESSION['oldAddressValid']) && $_SESSION['oldAddressValid'] == 1){ echo "checked"; }
              echo "/></td>\n\t</tr>";
            }
            ?>
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
                <?php foreach ($genders as $gender)
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
                <?php foreach ($ethnicities as $ethnicity)
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
          <tr>
            <td><label for="explanation">Explanation (if necessary): </label></div></td>
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
						<td><input type="submit" name="clientSub" id="clientSub" value="<?php 
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
		</div><!-- /newClient -->
<?php 
  if (!empty($_SESSION['client']))
  {
    echo "<a href=controllers/deleteClient.php?client=" . $_SESSION['client'] . " ";
    echo "onClick=" . '"' . "return confirm('Are you sure you want to delete this client? ";
    echo "Doing so will remove all information related to the client from the database.')" . '">Delete this client</a>';
  }
  ?>
	</body>

</html>