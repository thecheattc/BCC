<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<link rel="stylesheet" href="bryant.css" type="text/css"/>
<script type="text/javascript" 
			src="js/jquery-1.4.4.min.js"></script>
	<?php 
		session_start();
		
		session_register($user);
	?>
<head>
<title>Bryant Food Distribution Management Tool</title>
</head>
	<body>
		<div id="header">
			<h1>Bryant Food Distribution Admin Login</h1>
			<h2>Sign in to manage food distribution clients</h2>
			<hr/>
		</div><!-- /header -->
		<div id="adLogin">
		<?php
			$myusername=$_POST['uName'];
			if(empty($myusername)){
				echo'
			<form method="post">
				<fieldset>
					<legend>Administrator Login</legend>
					
					<table>
						<tr>
							<td><label for="uName">Enter user name:</label></td>
							<td><input name="uName" type="text" size="30"/></td>
						</tr>
						<tr>
							<td><label for="pWord">Enter password:</label></td> <td><input name="pWord" type="text" size="30"/></td>
						</tr>
						<tr>
							<td><input type="submit" name="log" value="Login"/></td>
					</table>
				</fieldset>
			</form>';
			}
			elseif($myusername!="Bryant"){
				echo'
				<form method="post">
				<fieldset>
					<legend>Administrator Login</legend>
					<h4 style="color:red;">Please enter a valid user name</h4>
					<table>
						<tr>
							<td><label for="uName">Enter user name:</label></td>
							<td><input name="uName" type="text" size="30"/></td>
						</tr>
						<tr>
							<td><label for="pWord">Enter password:</label></td> <td><input name="pWord" type="text" size="30"/></td>
						</tr>
						<tr>
							<td><input type="submit" name="log" value="Login"/></td>
					</table>
				</fieldset>
			</form>';    
			}
			else{
				echo '<META HTTP-EQUIV="Refresh" Content="0;URL=selectTask.php">';    
  			  	exit;
			}
			?>
			<p><a href="register.php">Click here to register a new administrator</a></p>
		</div><!-- /adLogin -->
	</body>
</html>
