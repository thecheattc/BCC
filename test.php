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
  echo $report->getReport($start, $end);
  //echo getDuplicatedHouseholds($start, $end);
  //echo getTotalHomeless($start, $end);
	/*$houses =  getHouseholdLocations($start, $end);
  foreach($houses as $house)
  {
    echo $house["zip"] . " : " . $house["count"] . "\n";
  }
  
  $count = getGenderCount($start, $end);
  var_dump($count);
  $count = getEthnicityCount($start, $end);
  var_dump($count);*/
  
echo "</PRE>";


?>