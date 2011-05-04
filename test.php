<?php
  
  include ('models/reportLib.php');
  include ('models/sqldb.php');
  $start = "0000-00-00";
  $end = "2011-04-10";
  echo "<PRE>";
  //echo getUnduplicatedIndividuals($start, $end);
  //echo getDuplicatedHouseholds($start, $end);
  //echo getTotalHomeless($start, $end);
 $houses =  getHouseholdLocations($start, $end);
  foreach($houses as $house)
  {
    echo $house["zip"] . " : " . $house["count"] . "\n";
  }
  /*
  $count = getGenderCount($start, $end);
  var_dump($count);
  $count = getEthnicityCount($start, $end);
  var_dump($count);*/
  
echo "</PRE>";


?>