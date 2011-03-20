<?php
  
  include ('models/sqldb.php');
  include ('models/client.php');
  include ('models/house.php');
  include ('models/gender.php');
  include ('models/ethnicity.php');
  include ('models/reason.php');
  include ('controllers/utility.php');

  $genders = NULL;
  $ethnicities = NULL;
  $reasons = NULL;
  $client = NULL;
  
  if (empty($_GET['client']))
  {
    header("Location: search.php?error=1");
  }
  else
  {
    $genders = Gender::getAllGenders();
    $ethnicities = Ethnicity::getAllEthnicities();
    $reasons = Reason::getAllReasons(); 
    $client = Client::getClientByID($_GET['client']);
    
    if($client === NULL)
    {
      header("Location: search.php?error=1");
    }
    //Only retrieve the house if the person has a house listed
    $house = NULL;
    if ($client->getHouseID() !== NULL)
    {
      $house = House::getHouseByID($client->getHouseID());
      if ($house === NULL)
      {
        header("Location: search.php?error=1");
      }
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
    <script type="text/javascript" src="scripts/js/jquery-1.4.4.min.js"></script>
    <script src="scripts/js/jquery.simplemodal-1.4.1.js" type="text/javascript" language="javascript" charset="utf-8"> </script>
    <script type="text/javascript" src="scripts/js/jquery-ui-1.8.10.custom/js/jquery-ui-1.8.10.custom.min.js"> </script>
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
              if(val != ""){
                 if (val >0 && val < 16 && !(/\D/).test(val)){
                     $(".show2").show("slow");
                  }
                  else{
                    window.alert('Please enter a whole number of household members from 1 to 15.');
                    $(".show2").hide("slow");
                   }
              }
           });
                  
           //Limits the household age members to a comma separated list
           $('#hAge').focusout(function(){
               var ages = $('#hAge').val();
               var patt = /^([0-9]*)+(,[0-9]+)+$/;
               var result = patt.exec(ages);
               window.alert(result);
               if(result === null){
                  window.alert('Please enter a comma separated list of ages');
               }
            });
                  
            //Popup date pickers for application date and unemployment date
            $('#date').datepicker({ dateFormat: 'mm-dd-yy' });
            $('#uDate').datepicker({ dateFormat: 'mm-dd-yy' });  	 
        });
    </script>	
	
		<title>Bryant Food Distribution Client Data Confirmation page</title>
	</head>
	
	<body>
		<div id="header">
			<h1>Edit Client Information</h1>
      <?php 
        if (!empty($_GET['error']) && $_GET['error'] == 1)
        {
          echo "<h4>There was an error processing the edit.</h4>";
        }
        elseif (!empty($_GET['success']) && $_GET['success'] == 1)
        {
          echo "<h4>Client edited successfully.</h4>";
        }
        ?>
			<hr/>
		</div><!-- /header -->
    <div id="newClient">
      <form method="post" action="controllers/modifyClient.php?edit=1&client=<?php echo $client->getClientID(); ?>">
      <fieldset>
      <legend>Edit <?php echo $client->getFirstName(); ?>'s information</legend>
        <table>
          <tr>
            <td><label for="appDate">Date of Application:</label></td>
            <td><input type="text" name="appDate" id="date" value="<?php echo MySQLDateToNormal($client->getApplicationDate()); ?>"/></td>
          </tr>
          <tr>
            <td><label for="firstName">First Name: </label></td>
            <td><input type="text" size="60" name="firstName" value="<?php echo $client->getFirstName(); ?>" /></td>
          </tr>
          <tr>
            <td><label for="lastName">Last Name: </label></td>
            <td><input name="lastName" type="text" size="60" value="<?php echo $client->getLastName(); ?>" /></td>
          </tr>
          <tr>
            <td><label for="address">Current Address: </label></td>
            <td><input name="address" type="text" size="80" value="<?php if ($house !== NULL){echo $house->getAddress();} ?>" /></td>
          </tr>
          <tr>
            <td><label for="city">Current City: </label></td>
            <td><input name="city" type="text" size="50" value="<?php if ($house !== NULL){echo $house->getCity();} ?>" /></td>
          </tr>
          <tr>
            <td><label for="zip">Zip Code: </label></td>
            <td><input name="zip" type="text" size="11" value="<?php if ($house !== NULL){echo $house->getZip();} ?>" maxlength="11" /></td>
          </tr>
          <tr>
            <td><labe for="number">Phone Number: <span class="example">(111-222-3333)</span></label></td>
            <td><input name="number" id="number"type="text" size="16" value="<?php echo $client->getPhoneNumber(); ?>" maxlength="16" /></td>
          </tr>
          <tr>
            <td><label for="age">Client Age: </label></td>
            <td><input name="age" type="text" size="2" value="<?php echo $client->getAge(); ?>" maxlength="3" /></td>
          </tr>
          <tr>
            <td><label for="gengroup">Client Gender: </label></td>
            <td>
            <?php foreach ($genders as $gender)
              {
                echo "\t\t\t\t\t\t";
                echo $gender->getGenderDesc().': <input name="gengroup" type="radio" value="'.$gender->getGenderID().'" ';
                if ($gender->getGenderID() === $client->getGenderID())
                {
                  echo 'checked ';
                }
                echo '/>';
                echo "\n";
              }
              ?>
            </td>
          </tr>
          <tr>
            <td><label for="ethgroup">Client Ethnicity: </label></td>
            <td><select id="ethgroup" name="ethgroup">
              <option value="0">Select an ethnicity</option>
              <?php foreach ($ethnicities as $ethnicity)
                {
                  echo "\t\t\t\t\t\t";
                  echo '<option value="'.$ethnicity->getEthnicityID().'" ';
                  if ($ethnicity->getEthnicityID() === $client->getEthnicityID())
                  {
                    echo 'selected ';
                  }
                  echo '>'.$ethnicity->getEthnicityDesc().'</option>';
                  echo "\n";
                }
                ?>
              </select>
            </td>
          </tr>
          <tr>
            <td><label for="reasongroup">Reason For Assistance: </label></td>
            <td><select id="reasongroup" name="reasongroup">
              <option value="0">Select a reason</option>
              <?php foreach ($reasons as $reason)
                {
                  echo "\t\t\t\t\t\t";
                  echo '<option value="'.$reason->getReasonID().'" ';
                  if ($reason->getReasonID() == $client->getReasonID())
                  {
                    echo 'selected ';
                  }
                  echo '>'.$reason->getReasonDesc().'</option>';
                  echo "\n";
                }
                ?>
              </select>
            </td>
          </tr>
          <tr>
            <td><div class="show" style="display:none;"><label for="uDate">Date of Job Loss: </label></div></td>
            <td><input class="show" style="display:none;"type="text" name="uDate" value="<?php echo MySQLDateToNormal($client->getUnemploymentDate()); ?>" id="uDate" /></td>
          </tr>
          <tr>
            <td><label for="houseNum">Number of People in Household:</label></td>
            <td><input name="houseNum" id="houseNum" type="text" size="2" maxlength="2" numeric="integer" /></td>
          </tr>
          <tr>
            <td><div class="show2" style="display:none;"><label for="hAge">Household Member Ages:<br/><span class="example">Please enter a comma separated list<br/>Example: 15, 20, 25</span></label></div></td>
            <td><div class="show2" style="display:none;"><input type="text" id="hAge" name="hAge" size="25" maxlength="45" /></div></td>
          </tr>
          <tr>
            <td><input type="submit" name="clientSub" id="clientSub" value="Confirm" ></td>
          </tr>
        </table>
      </fieldset>
    </form>
  </div><!-- /editClient -->
  <a href="controllers/deleteClient.php?client=<?php echo $client->getClientID(); ?>">Delete this client </a>
</body>
</html>