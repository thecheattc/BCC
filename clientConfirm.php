<?php
  session_start();
  include ('models/sqldb.php');
  include ('controllers/utility.php');
  include ('models/client.php');
  include ('models/gender.php');
  include ('models/ethnicity.php');
  include ('models/reason.php');
  
  define("LOST_JOB", 1);
  $_SESSION['errors'] = NULL;
  
  //Grab everything from POST, put it in SESSION.
  $_SESSION['appDate'] = $_POST['appDate'];
  $_SESSION['firstName'] = $_POST['firstName'];
  $_SESSION['lastName'] = $_POST['lastName'];
  $_SESSION['address'] = $_POST['address'];
  $_SESSION['city'] = $_POST['city'];
  $_SESSION['zip'] = $_POST['zip'];
  $_SESSION['number'] = $_POST['number'];
  $_SESSION['age'] = $_POST['age'];
  $_SESSION['gengroup'] = $_POST['gengroup'];
  $_SESSION['ethgroup'] = $_POST['ethgroup'];
  $_SESSION['reasongroup'] = $_POST['reasongroup'];
  $_SESSION['uDate'] = $_POST['uDate'];
  $_SESSION['houseNum'] = isset($_POST['houseNum'])? $_POST['houseNum'] : 0;
  $_SESSION['receivesStamps'] = $_POST['receivesStamps'];
  $_SESSION['wantsStamps'] = $_POST['wantsStamps'];
  for ($i=0; $i< $_SESSION['houseNum']; $i++)
  {
    $_SESSION["child{$i}"] = $_POST["child{$i}"];
  }

  $gender = Gender::getGenderByID($_SESSION['gengroup']);
  $ethnicity = Ethnicity::getEthnicityByID($_SESSION['ethgroup']);
  $reason = Reason::getReasonByID($_SESSION['reasongroup']);
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
			<h1>Confirm Client Information</h1>
			<h2>Please check that the information you have entered is correct.</h2>
			<hr/>
		</div><!-- /header -->
    <div id="newClient">
        <table>
          <tr>
            <td><label>Date of Application:</label></td>
            <td><?php echo htmlentities($_SESSION['appDate']); ?></td>
          </tr>
          <tr>
            <td><label>First Name: </label></td>
            <td><?php echo htmlentities($_SESSION['firstName']); ?></td>
          </tr>
          <tr>
            <td><label>Last Name: </label></td>
            <td><?php echo htmlentities($_SESSION['lastName']); ?></td>
          </tr>
          <tr>
            <td><label>Current Address: </label></td>
            <td><?php echo htmlentities($_SESSION['address']); ?></td>
          </tr>
          <tr>
            <td><label>Current City: </label></td>
            <td><?php echo htmlentities($_SESSION['city']); ?></td>
          </tr>
          <tr>
            <td><label>Zip Code: </label></td>
            <td><?php echo htmlentities($_SESSION['zip']); ?></td>
          </tr>
          <tr>
            <td><label>Phone Number: <span class="example">(111-222-3333)</span></label></td>
            <td><?php echo htmlentities($_SESSION['number']); ?></td>
          </tr>
          <tr>
            <td><label>Client Age: </label></td>
            <td><?php echo htmlentities($_SESSION['age']);  ?></td>
          </tr>
          <tr>
            <td><label>Client Gender: </label></td>
            <td><?php if (!empty($gender)) {echo $gender->getGenderDesc();} ?></td>
          </tr>
          <tr>
            <td><label>Client Ethnicity: </label></td>
            <td><?php if (!empty($ethnicity)) {echo $ethnicity->getEthnicityDesc();} ?></td>
          </tr>
          <tr>
            <td><label>Reason For Assistance: </label></td>
            <td><?php if (!empty($reason)) {echo $reason->getReasonDesc();} ?></td>
          </tr>
          <?php
            if (!empty($reason) && $reason->getReasonID() == LOST_JOB)
            {
              echo "\t<tr>\n\t\t<td><label>Date of Job Loss: </label></td>\n\t\t<td>";
              echo htmlentities($_SESSION['uDate']);
              echo "</td>\n\t</tr>\n";
            }
            ?>
          <tr>
            <td><label>Number of other people in household that have not registered with Bryant:</label></td>
            <td><?php echo $_SESSION['houseNum']; ?></td>
          </tr>
          <tr>
            <td><label>Are you currently receving food stamps?</label></td>
            <td><?php 
                  if(isset($_SESSION['receivesStamps']) && $_SESSION['receivesStamps'] == 1)
                  { 
                    echo "Yes";
                  }
                  else
                  {
                    echo "No";
                  } 
                ?>
            </td>
          </tr>
          <?php 
            if($_SESSION['receivesStamps'] == 0)
            {
              echo "<tr>\n\t<td><label>If no, are you interested in finding out if you are eligible for food stamps?</label></td>";
              echo "\n\t<td>";
              if(isset($_SESSION['wantsStamps']))
              {
                if ($_SESSION['wantsStamps'] == 1)
                {
                  echo "Yes";
                }
                else
                {
                  echo "No";
                }
                echo "</td>\n</tr>";
              }
            }
            ?>
          <tr>
<td><form method="post" action="controllers/modifyClient.php"><input type="submit" value="<?php 
                                                                                            if(!empty($_SESSION['client']))
                                                                                            {
                                                                                              echo 'Edit Client';
                                                                                            }
                                                                                            else
                                                                                            {
                                                                                              echo 'Add New Client';
                                                                                            }
                                                                                            ?>"/></form>
          </tr>
        </table>
  </div><!-- /confirm client -->
  <a href="dataEntry.php">Go back to change information</a>
</body>
</html>