<?php
  echo "<PRE>";
  
  
  //Generates a random mysql date between Jan 01, 2000 and today inclusive
  function randDate()
  {
    $startStamp = mktime(0, 0, 0, 1, 1, 2000);
    $endStamp = time();
    $rand = mt_rand($startStamp, $endStamp);
    return date("Y-m-d", $rand);
  }
  
  //House bootstrapping
  $file = "houses.sql";
  $stream = fopen($file, 'w');
  $zipArr = array(48103, 48104, 48105, 48106, 48107, 48108, 48109, 48113, 48115, 48118, 48130, 48137, 48158, 48160, 48167,
                  48168, 48170, 48175, 48176, 48178, 48189, 48190, 48191, 48197, 48198, 49229, 49236, 49240, 49285);
  $insert = "INSERT INTO bcc_food_client.houses (street_number, street_name, street_type, line2, city, zip) VALUES\n";
  for ($i=0; $i<500; $i++)
  {
    $zip = $zipArr[mt_rand(0, count($zipArr)-1)];
    $query .= "('{$i}', 'Street{$i}', 'Drive', 'Apt {$i}', 'Ann Arbor', '{$zip}'),\n";
    
  }
  $lastComma = strrpos($query, ',');
  $query = substr_replace($query, ';', $lastComma);
  $sql = $insert . $query;
  fwrite($stream, $sql);
  fclose($stream);
  $insert = '';
  $query = '';

  
  $file = "clients.sql";
  $stream = fopen($file, 'w');
  
  //Client bootstrapping
  $insert = "INSERT INTO bcc_food_client.clients (first_name, last_name, age, phone_number, ";
  $insert .= "house_id, ethnicity_id, gender_id, reason_id, explanation, unemployment_date, ";
  $insert .= "application_date, receives_stamps, wants_stamps) VALUES \n";
  for($i=0; $i<1000; $i++)
  {
    $temp = (int)($i/2) + 1;
    $ethnicity = ($i % 5) + 1;
    $gender = ($i % 2) + 1;
    $reason = ($i % 7) + 1;
    $house = ($reason === 6)? "NULL" : $temp;
    $unempDate = ($reason === 1)? "'" . randDate() . "'" : "NULL";
    $appDate = randDate();
    $receives = $i%2;
    $wants = ($receives === 1)? 0 : mt_rand(0,1);
    $query .= "('First{$i}', 'Last{$i}', {$i}, ";
    $query .= "'Phone{$i}', {$house}, {$ethnicity}, ";
    $query .= "{$gender}, {$reason}, NULL, " . $unempDate . ", '{$appDate}', {$receives}, {$wants} ), \n";
  }
  $lastComma = strrpos($query, ',');
  $query = substr_replace($query, ';', $lastComma);
  $sql = $insert . $query;
  fwrite($stream, $sql);
  fclose($stream);
  $insert = '';
  $query = '';
  
  $file = "members.sql";
  $stream = fopen($file, 'w');
  //Family member bootstrapping
  $insert = "INSERT INTO bcc_food_client.family_members (member_house_id, guardian_id, ethnicity_id, age, gender_id) VALUES\n";
  for($i=0; $i<1000; $i++)
  {
    $house = (($i%7) === 6)? "NULL" : (int)($i/2) + 1;
    $guardian = ($house === "NULL")? $i : "NULL";
    $ethnicity = ($i % 5) + 1;
    $gender = ($i % 2) + 1;
    $query .= "({$house}, {$guardian}, {$ethnicity}, {$i}, {$gender} ), \n";
  }
  $lastComma = strrpos($query, ',');
  $query = substr_replace($query, ';', $lastComma);
  $sql = $insert . $query;
  fwrite($stream, $sql);
  fclose($stream);
  $insert = '';
  $query = '';

  $file = "visits.sql";
  $stream = fopen($file, 'w');
  //Visit bootstrapping
  $insert = "INSERT INTO bcc_food_client.usage (client_id, type_id, location_id, date) VALUES\n";
  for($i=0; $i<5000; $i++)
  {
    $client = (int)($i/5) + 1;
		$location = 1;
    $type = mt_rand(1,3);
    $date = randDate();
    $query .= "({$client}, {$type}, {$location}, '{$date}'), \n";
  }
  $lastComma = strrpos($query, ',');
  $query = substr_replace($query, ';', $lastComma);
  $sql = $insert . $query;
  fwrite($stream, $sql);
  fclose($stream);
  


?>