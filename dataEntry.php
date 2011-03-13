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
         		 	if($(this).text()=="Lost job"){
          		 		$(".show").show("slow");
          			}
          			else{
          				$(".show").hide("slow");
          			}
             	 });
       		 })
       	 .change();
     $count=0;
   		
   	//Gets the value from the number in household Input
    $("#houseNum").focusout(function(){
        var val = $("#houseNum").val();
       	var msg="Please enter a whole number (example: 1)."; 
       	
       	if(val !=""){
       			var result = /\d+(?:\.\d+)?/.exec(val);
       			if(result != null){
       				//window.alert(result);
       				if(result>1){
       					if(result<16){
       					$(".show2").show("slow");
						}
						else{
						window.alert('Only 15 members per house are allowed.');
						}
       				}
				}
       			else{
        			window.alert(msg);
        		}
        }
  	  })
  	  .change();
  	 //Limits the household age members to a comma separated list
  	  $('#hAge').focusout(function(){
  	  		//window.alert('test');
  	  		var ages = $('#hAge').val();
  	  		var patt = /^([0-9]*)+(,[0-9]+)+$/;
  	  		var result = patt.exec(ages);
  	  		window.alert(result);
  	  		if(result == null){
  	  			window.alert('Please enter a comma separated list of ages');
  	  		}
  	  });
  	 //Popup date pickers for application date and unemployment date
  	  $('#date').datepicker({ dateFormat: 'yy-mm-dd' });
  	  $('#uDate').datepicker({ dateFormat: 'yy-mm-dd' });
  	  
  	  //Checks that the Application date isn't in the past.
  	  $('#date').change(function(){
  	  	var now = new Date();
  	  	var nowDay = now.getDate();
  	  	var nowMonth = (now.getMonth()+1);
  	  	if(nowDay<10){
  	  		nowDay = '0'+nowDay;
  	  	}
  	  	if(nowMonth<10){
  	  		nowMonth = '0'+nowMonth;
  	  	}
  	  	var dateNow = (now.getFullYear())+'-'+nowMonth+'-'+nowDay;
  	  	var dateInput = new Date($('#date').val());
  	  	var mili_now = now.getTime();
  	  	var mili_dateInput = dateInput.getTime();
  	  	var dateDiff = mili_now-mili_dateInput;
  	  	var num_days = (((dateDiff / 1000) / 60) / 60) / 24;
  	  	var date = $('#date').val();
  	  	if(dateNow!=date){
  	  		if( num_days>0 ){
  	  			var msg = 'This date is in the past, please select the current date, or one in the future';
  	  			window.alert(msg);
  	  			$('#date').attr('value', 'Click here to enter a date.'); 
  	  		}
  	  	}
  	  });
  	  //Checks to make sure the phone number is the right format
  	  $('#cPhone').change(function(){
  	  	var phone = $('#cPhone').val();
  	  	//if(phone != ''&&phone.length<16){
  	  		var pattern = /^(1?(-?\d{3})-?)?(\d{3})(-?\d{4})$/;
  	  		var result=pattern.exec(phone);
  	  		window.alert(result);
  	  		if(result==null){
  	  			window.alert('Please use numbers separated by a hyphen, "-" when entering a phone number. For example, 1-111-222-3333');
  	  			$('#cPhone').attr('value', phone);
  	  			
  	  			}
  	  			$('#cPhone').focus();
  	  	//}
  	  });
  	 
    });
	</script>		
	
	<title>Bryant Food Distribution Client Data Entry Input Screen</title>
</head>
	<body>
		<div id="header">
			<h1>Add a New Client</h1>
			<h2>Enter the information for a new client</h2>
			<hr/>
		</div><!-- /header -->
		<div id="newClient">
			<form method="post" action="controllers/addClient.php">
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
						<td><label>Phone Number: <span class="example">(111-222-3333)</span></label></td>
						<td><input name="cPhone" id="cPhone"type="text" size="16" maxlength="16" value="" /></td>
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
								<option value="2">African-American</option>
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
					<tr>
						<td><div class="show2" style="display:none;"><label for="hAge">Household Member Ages:<br/><span class="example">Please enter a comma separated list<br/>Example: 15, 20, 25</span></label></div></td>
						<td><div class="show2" style="display:none;"><input type="text" id="hAge" name="hAge" size="25" maxlength="45" /></div></td>
					</tr>
					<tr>
						<td><button type="submit" name="clientSub" id="clientSub" value="Done" >Add New Client</button></td>
						<td></td>
					</tr>
				</table>
			</fieldset>
			</form>
		</div><!-- /newClient -->
	</body>

</html>
