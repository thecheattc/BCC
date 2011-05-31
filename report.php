<?php
  date_default_timezone_set('America/New_York');
	include ('controllers/utility.php');
  include ('models/report.php');
  include ('models/sqldb.php');
	include ('models/administrator.php');
  include ('models/gender.php');
  include ('models/ethnicity.php');
  include ('models/reason.php');
	
	$_SESSION['errors'] = array();
	if (!hasAccess(TRUE))
	{
		$_SESSION['errors'][] = "This operation requires administrative privileges.";
		header("Location: ../");
		exit();
	}
	$_SESSION['reportStart'] = isset($_POST['start'])? $_POST['start'] : '';
	$_SESSION['reportEnd'] = isset($_POST['end'])? $_POST['end'] : '';
	$_SESSION['recentlyUnemployedDate'] = isset($_POST['recentlyUnemployedDate'])? $_POST['recentlyUnemployedDate'] : '';
	
	$start = createNormalDate($_SESSION['reportStart']);
	$end = createNormalDate($_SESSION['reportEnd']);
	$recentlyUnemployedDate = createNormalDate($_SESSION['recentlyUnemployedDate']);
	if (!empty($_POST))
	{
		if (empty($start))
		{
			$_SESSION['errors'][] = "Please pick a start date";
		}
		if (empty($end))
		{
			$_SESSION['errors'][] = "Please pick an end date";
		}
		if (empty($recentlyUnemployedDate))
		{
			$_SESSION['errors'][] = "Please pick a \"recently unemployed\" date";
		}
		if (!empty($start) && !empty($end) && $start >= $end )
		{
			$_SESSION['errors'][] = "The start date must be before the end date";
		}
		$validReport = empty($_SESSION['errors']);
	}
	$report = new Report();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<link rel="stylesheet" href="style/bryant.css" type="text/css"/>
		<link type="text/css" href="scripts/js/jquery-ui-1.8.10.custom/css/ui-lightness/jquery-ui-1.8.10.custom.css" rel="Stylesheet" />
		<script type="text/javascript" src="scripts/js/jquery-1.4.4.min.js"></script>
		<script src="scripts/js/jquery.simplemodal-1.4.1.js" type="text/javascript" language="javascript" charset="utf-8"></script>
		<script type="text/javascript" src="scripts/js/jquery-ui-1.8.10.custom/js/jquery-ui-1.8.10.custom.min.js"></script>
		<script type="text/javascript">
			$(document).ready(function() {
					$('#start').datepicker({ dateFormat: 'mm-dd-yy' });
					$('#end').datepicker({ dateFormat: 'mm-dd-yy' });
					$('#recentlyUnemployedDate').datepicker({ dateFormat: 'mm-dd-yy' });
				});
		</script>
	</head>
	<body>
		<div id="header">
			<?php showHeader("BCC Monthly Report", "BCC Monthly Report", 
											 "Choose a time period for the report. The report includes the start date but not the end date.", TRUE); ?>
		</div>
		<?php showErrors();	
			if (!$validReport)
			{
echo<<<REPORT_FORM
		<div id="reportForm">
			<form method="post" action="report.php">
				<label for="start">Start date:</label><input id="start" name="start" type="text" value="{$_POST['start']}" /><br />
				<label for="end">End date:</label><input id="end" name="end" type="text" value="{$_POST['end']}" /><br />
				<label for="recentlyUnemployedDate">Date after which a client is considered recently unemployed:</label>
				<input id="recentlyUnemployedDate" name="recentlyUnemployedDate" type="text" value="{$_POST['recentlyUnemployedDate']}" /><br />
				<input type="submit" value="Submit" />
			</form>
		</div>
REPORT_FORM;
			}
			else
			{
echo <<<REPORT
		<div id="report">
				{$report->getReport($start, $end, $recentlyUnemployedDate)}
				<a href="report.php?clean=1">Pick another time period</a>
		</div>
REPORT;
			}
			?>
	</body>
</html>