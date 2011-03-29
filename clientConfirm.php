<?php
  session_start();
  include ('models/sqldb.php');
  include ('controllers/utility.php');
  include ('models/client.php');
  include ('models/gender.php');
  include ('models/ethnicity.php');
  include ('models/reason.php');
  
  define("LOST_JOB", 1);
  define("OTHER", 7);
/*
  echo "<PRE>";
  var_dump($_SESSION);
  echo "</PRE>";
 */
   
  $_SESSION['fromConfirm'] = TRUE;
  //Grab everything from POST and put it in session, so long
  //as we haven't been redirected here. Since we never redirect here
  //unless there are errors, we can use that to check.
  if (empty($_SESSION['errors']))
  {
    $_SESSION['appDate'] = $_POST['appDate'];
    $_SESSION['firstName'] = $_POST['firstName'];
    $_SESSION['lastName'] = $_POST['lastName'];
    $_SESSION['number'] = $_POST['number'];
    $_SESSION['age'] = $_POST['age'];
    $_SESSION['gengroup'] = $_POST['gengroup'];
    $_SESSION['ethgroup'] = $_POST['ethgroup'];
    $_SESSION['reasongroup'] = $_POST['reasongroup'];
    $_SESSION['explanation'] = $_POST['explanation'];
    $_SESSION['uDate'] = $_POST['uDate'];
    $_SESSION['receivesStamps'] = $_POST['receivesStamps'];
    $_SESSION['wantsStamps'] = $_POST['wantsStamps'];
    for ($i=0; $i<$_SESSION['memberCount']; $i++)
    {
      $_SESSION['familyMembers'][$i]["age"] = $_POST["memberAge{$i}"];
      $_SESSION['familyMembers'][$i]["gender"] = $_POST["memberGender{$i}"];
      $_SESSION['familyMembers'][$i]["ethnicity"] = $_POST["memberEthnicity{$i}"];
    }
  }
  
  if ($_POST['toDo'] == "addMember")
  {
    $_SESSION['memberCount']++;
    $familyMember = array("age" => '', "gender" => '', "ethnicity" => '');
    $_SESSION['familyMembers'][] = $familyMember;
    header("Location: clientEntry.php");
  }
  if ($_POST['toDo'] == "deleteMember")
  {
    $_SESSION['memberCount']--;
    $_SESSION['familyMembers'][$_SESSION['memberCount']] = NULL;
    header("Location: clientEntry.php");
  }
  
  //To make the client confirmation screen correct, update the session
  //variables with the address they chose from search (if necessary).
  $house = array();
  if ($_SESSION['houseID'] != "new")
  {
    foreach($_SESSION['matches'] as $match)
    {
      if ($match["houseID"] == $_SESSION['houseID'])
      {
        $house['streetNumber'] = $match['streetNumber'];
        $house['streetName'] = $match['streetName'];
        $house['streetType'] = $match['streetType'];
        $house['city'] = $match['city'];
        $house['zip'] = $match['zip'];
      }
    }
  }
  else
  {
    $house['streetNumber'] = $_SESSION['streetNumber'];
    $house['streetName'] = $_SESSION['streetName'];
    $house['streetType'] = $_SESSION['streetType'];
    $house['city'] = $_SESSION['city'];
    $house['zip'] = $_SESSION['zip'];
  }
  
  $changing = "adding";
  if ($_SESSION['edit'])
  {
    $changing = "editing";
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
		<title>Bryant Food Distribution Client Data Confirmation page</title>
	</head>
	
	<body>
		<div id="header">
			<h1>Confirm Client Information</h1>
			<h2>Please check that the information you have entered is correct.</h2>
			<hr/>
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
      if ($error === "Street number" || $error === "Street name" || $error === "Street type" || $error === "City" || $error === "Zip")
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
    $_SESSION['errors'] = NULL;
  }
?>  
    <div id="newClient">
        <table>
          <tr>
            <td><label>Date of Application:</label></td>
            <td><?php   echo htmlentities($_SESSION['appDate']); ?></td>
          </tr>
          <tr>
            <td><label>First Name: </label></td>
            <td><?php   echo htmlentities($_SESSION['firstName']); ?></td>
          </tr>
          <tr>
            <td><label>Last Name: </label></td>
            <td><?php   echo htmlentities($_SESSION['lastName']); ?></td>
          </tr>
          <tr>
            <td><label>Current Address: </label></td>
            <td><?php   echo htmlentities($house['streetNumber']) . " ". htmlentities($house['streetName']) . " " .
                            htmlentities($house['streetType']); ?></td>
          </tr>
          <tr>
            <td><label>Current City: </label></td>
            <td><?php   echo htmlentities($house['city']); ?></td>
          </tr>
          <tr>
            <td><label>Zip Code: </label></td>
            <td><?php   echo htmlentities($house['zip']); ?></td>
          </tr>
          <?php          
            if($_SESSION['edit'])
            {
              echo "\n\t<tr>\n\t\t<td><label >If your address has changed, ";
              echo "are there still people registered with Bryant at the old address?</label></td>\n";
              echo "\t\t<td>";
              if (isset($_SESSION['oldAddressValid']) && $_SESSION['oldAddressValid'] == 0){ echo "No"; }
              if (isset($_SESSION['oldAddressValid']) && $_SESSION['oldAddressValid'] == 1){ echo "Yes"; }
              echo "</td>\n\t</tr>";
            }
            ?>
          <tr>
            <td><label>Phone Number: <span class="example">(111-222-3333)</span></label></td>
            <td><?php   echo htmlentities($_SESSION['number']); ?></td>
          </tr>
          <tr>
            <td><label>Client Age: </label></td>
            <td><?php   echo htmlentities($_SESSION['age']);  ?></td>
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
            for ($i=0; $i<$_SESSION['memberCount']; $i++)
            {
              $familyMember = $_SESSION['familyMembers'][$i];
              $j = $i+1;
              $childGender = Gender::getGenderByID($familyMember["gender"]);
              $childEthnicity = Ethnicity::getEthnicityByID($familyMember["ethnicity"]);
              echo "\n\t<tr>\n\t\t<td><label>Child {$j} age:</label></td>\n";
              echo "\t<td> ";
              if (!empty($familyMember['age'])){ echo $familyMember['age']; }
              echo "</td>\n\t</tr>\n";
              echo "\t<tr>\n\t\t<td><label>Child {$j} gender:</label></td>\n";
              echo "\t<td> ";
              if (!empty($childGender)){ echo $childGender->getGenderDesc(); }
              echo "</td>\n\t</tr>\n";
              echo "\t<tr>\n\t\t<td><label>Child {$j} ethnicity:</label></td>\n";
              echo "\t<td> ";
              if (!empty($childEthnicity)){ echo $childEthnicity->getEthnicityDesc(); }
              echo "</td>\n\t</tr>\n";
            }
            ?>
          <tr>
            <td><label>Explanation (required if the reason is "Other"): </label></td>
            <td><?php   echo htmlentities($_SESSION['explanation']); ?></td>
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
            <td><label>Are you currently receving food stamps?</label></td>
            <td><?php 
                  if(isset($_SESSION['receivesStamps']))
                  { 
                    if ($_SESSION['receivesStamps'] == 1)
                    {
                      echo "Yes";
                    }
                    else
                    {
                      echo "No";
                    }
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
          <td><form method="post" action="controllers/modifyClient.php"><input type="submit" 
                                  value="<?php if(!empty($_SESSION['clientID']))
                                                {echo 'Edit Client';}
                                                else
                                                {echo 'Add New Client';}?>"/></form>
          </tr>
        </table>
  </div><!-- /confirm client -->
  <a href="addressEntry.php">Go back to change address information</a><br />
  <a href="clientEntry.php">Go back to change client information</a>
</body>
</html>