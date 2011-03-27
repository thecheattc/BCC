<?php
  
  include ('models/sqldb.php');
  include ('models/client.php');
  include ('models/gender.php');
  include ('models/ethnicity.php');
  include ('models/reason.php');
  include ('controllers/utility.php');

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
         
                  
           
            //Popup date pickers for application date and unemployment date
            $('#bDate').datepicker({ dateFormat: 'mm-dd-yy' });
            $('#eDate').datepicker({ dateFormat: 'mm-dd-yy' });  	 
        });
    </script>	
	
		<title>Bryant Food Distribution Report Creation Page</title>
	</head>
	<body>
		<div id="header">
			<h1>Create a Report for Food Gatherers</h1>
			<h2>Use this form to create a report
			of clients who have been served.<h2>
			<hr/>
		</div><!-- /header -->
    <div id="newClient">
    	<form method="post" action="">
    		
    		<fieldset>	
        			<legend>Create a report</legend>
        		<table>
        			<tr>
        				<td><label>Select a beginning date: </label></td>
        				<td><input value="Click to enter a beginning date" type="text" size="25" name="bDate" id="bDate"  /></td>
        			</tr>
        			<tr>
        				<td><label>Select an end date: </label></td>
        				<td><input value="Click to enter an end date" type="text" size="25" name="eDate" id="eDate"  /></td>
        			</tr>
        			<tr>
        				<td><label>How many hours was the program open? </label></td>
        				<td><input type="text" name="repHours" size="3" maxlength="3"/></td>
        			</tr>
        			<tr>
        				<td><label>How many days was the program open? </label></td>
        				<td><input type="text" name="repDays" size="3" maxlength="3"/></td>
        			</tr>
        			<tr>
        				<td><label>Did your agency have to turn anyone away? </label></td>
        				<td>Yes: <input name="refuse" type="radio" value="1" /> No: <input name="refuse" type="radio" value="2" checked="checked" /></td>
        			</tr>
        			<tr>
        				<td VALIGN="top"><label style="margin-bottom:5px;">How can Food Gatherers better support your programs? </label></td>
        				<td><textarea name="fdGath" rows="5" cols="50"></textarea></td>
        			</tr>
        			<tr>
        				<td><button type="submit" name="repSub" value="Done">Generate the Report</button></td>
        				<td></td>
        			</tr>
        	</table>
        	</fieldset>
        </form>
    </div><!-- /newClient` -->
</body>
</html>