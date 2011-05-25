<?php
  
	include ('controllers/utility.php');
  include ('models/report.php');
  include ('models/sqldb.php');
	include ('models/administrator.php');
  include ('models/gender.php');
  include ('models/ethnicity.php');
  include ('models/reason.php');
  $start = "0000-00-00";
  $end = "2011-06-10";
	$report = new Report();
  echo "<PRE>";
	$report->getReport($start, $end);
	echo "</PRE>";


?>