<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<link rel="stylesheet" href="bryant.css" type="text/css"/>
<script type="text/javascript" 
			src="js/jquery-1.4.4.min.js"></script>
<head>
<title>Bryant New Admin Registration</title>
</head>
	<body>
	<div id="header">
		<h1>Register a New Bryant Food Adminstrator</h1>
		<h2>Create an account</h2>
		<hr/>
	</div><!-- /header -->
	<div id="regform">
		<form method="post">
			<fieldset>
				<legend>New Administrator Creation: </legend>
				<table>
					<tr>
						<td><label for="fname">First Name: </label></td>
						<td><input name="fname" type="text" size="50"/></td>
					</tr>
					<tr>
						<td><label for="lname">Last Name: </label></td>
						<td><input name="lname" type="text" size="50" /></td>
					</tr>
					<tr>
						<td><label for="email">Email Address: </label></td>
						<td><input name="email" type="text" size="50"/></td>
					</tr>
					<tr>
						<td><label for="phone">Phone: <span class="example">(111-222-3333)</span> </label></td>
						<td><input name="phone" type="text" size="12"/></td>
					</tr>
					<tr>
						<td><label for="newUname">Choose a User Name: </label></td>
						<td><input name="newPword" type="text" size="50"/></td>
					</tr>
					<tr>
						<td><label for="newPword">Choose a Password: </label></td>
						<td><input name="newPword" type="text" size="50"/></td>
					</tr>
					<tr>
						<td><label for="confPword">Confirm your Password: </label></td>
						<td><input name="confPword" type="text" size="50"/></td>
					</tr>
					<tr>
						<td><input type="submit" name="reg" value="Register"/></td>
						<td></td>
					</tr>
				</table>
			</fieldset>
		</form>
	</div><!-- /regform -->
	</body>
</html>