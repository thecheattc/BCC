<?php
  include ('gender.php');
  include ('ethnicity.php');
  include ('reason.php');
  $HOMELESS_REASON_ID = 6;
  $REJECTED_ID = 3;
  
  //Returns all the unduplicated individuals who successfully got food in a month 
  //given a valid MySQL date range from the first of a month (YYYY-MM-01)
  //to the first of the next month
  function getUnduplicatedIndividuals($start, $end)
  {
    SQLDB::connect();
    
    global $REJECTED_ID;
    $start = mysql_real_escape_string($start);
    $end = mysql_real_escape_string($end);
    
    $query = "SELECT COUNT(1) FROM ";
    $query .= "(SELECT DISTINCT c.client_id FROM bcc_food_client.clients c JOIN bcc_food_client.usage u ";
    $query .= "ON c.client_id = u.client_id WHERE u.date >= '{$start}' AND u.date < '{$end}' AND u.type_id != '{$REJECTED_ID}') cids";
    echo $query . "\n";
    $result = mysql_query($query);
    
    $count = 0;
    if ($row = mysql_fetch_array($result))
    {
      $count = $row[0];
    }
    return $count;
  }
  
  //Returns the number of duplicated households that successfully got food this month
  //(just the number of visits)
  function getDuplicatedHouseholds($start, $end)
  {
    SQLDB::connect();
    
    global $REJECTED_ID;
    $start = mysql_real_escape_string($start);
    $end = mysql_real_escape_string($end);
    
    $query = "SELECT COUNT(1) FROM bcc_food_client.usage WHERE date >= '{$start}' AND date < '{$end}' AND type_id != '{$REJECTED_ID}'";
    //echo $query . "\n";
    $result = mysql_query($query);
    
    $count = 0;
    if ($row = mysql_fetch_array($result))
    {
      $count = $row[0];
    }
    return $count;
  }  
  
  //Returns the number of homeless people who successfully received food this month
  function getTotalHomeless($start, $end)
  {
    
    SQLDB::connect();
    
    global $HOMELESS_REASON_ID;
    global $REJECTED_ID;
    $start = mysql_real_escape_string($start);
    $end = mysql_real_escape_string($end);
    
    $query = "SELECT COUNT(1) FROM ";
    $query .= "(SELECT DISTINCT c.client_id as clients FROM bcc_food_client.clients c JOIN bcc_food_client.usage u ";
    $query .= "ON c.client_id = u.client_id WHERE c.reason_id = '{$HOMELESS_REASON_ID}' AND u.date >= '{$start}' AND u.date < '{$end}' ";
    $query .= "AND u.type_id != '{$REJECTED_ID}') homeless";   
    //echo $query;
    $result = mysql_query($query);
    
    $count = 0;
    if ($row = mysql_fetch_array($result))
    {
      $count = $row[0];
    }
    
    return $count;
    
  }
  
  //Returns the number of households that received food between the start date and the end date, treating each homeless visitor
  //as a distinct household
  function getUnduplicatedHouseholds($start, $end)
  {
    SQLDB::connect();
    
    global $HOMELESS_REASON_ID;
    global $REJECTED_ID;
    $start = mysql_real_escape_string($start);
    $end = mysql_real_escape_string($end);
    
    //People with homes
    $query = "SELECT COUNT(1) FROM ";
    $query .= "(SELECT DISTINCT c.house_id FROM bcc_food_client.clients c JOIN bcc_food_client.usage u ";
    $query .= "ON c.client_id = u.client_id WHERE c.reason_id != '{$HOMELESS_REASON_ID}' AND u.date >= '{$start}' AND u.date < '{$end}' ";
    $query .= "AND u.type_id != '{$REJECTED_ID}') homes ";
    
   // echo $query . "\n";
    $result = mysql_query($query);
    
    $count = 0;
    if ($row = mysql_fetch_array($result))
    {
      $count = $row[0];
    }
    
    //Factor in homeless individuals
    $count += getTotalHomeless($start, $end);
    
    return $count;
  }
  
  function getHouseholdLocations($start, $end)
  {
    SQLDB::connect();
    
    $start = mysql_real_escape_string($start);
    $end = mysql_real_escape_string($end);
    
    $query = "SELECT zip, COUNT(zip) FROM
      (SELECT DISTINCT c.house_id FROM bcc_food_client.clients c JOIN bcc_food_client.usage u
      ON c.client_id = u.client_id WHERE c.house_id IS NOT NULL AND u.date >= '{$start}' AND u.date < '{$end}') AS clients
      JOIN bcc_food_client.houses h ON h.house_id = clients.house_id 
    GROUP BY zip ORDER BY zip";
    $result = mysql_query($query);

    $houses = array();
    while ($row = mysql_fetch_array($result))
    {
      $house = array();
      $house['zip'] = $row[0];
      $house['count'] = $row[1];
      $houses[] = $house;
    }
    return $houses;
  }
  
  function getGenderCount($start, $end)
  {
    SQLDB::connect();
    
    $start = mysql_real_escape_string($start);
    $end = mysql_real_escape_string($end);
    //Get gender count of parents
    $query1 = 
    "SELECT gender_desc, COUNT(gender_desc) 
    FROM 
      (SELECT DISTINCT c.client_id AS client_id, c.gender_id AS gender_id 
      FROM bcc_food_client.clients c JOIN bcc_food_client.usage u ON c.client_id = u.client_id 
      WHERE u.date >= '{$start}' AND u.date < '{$end}') AS clients 
      JOIN bcc_food_client.genders g ON g.gender_id = clients.gender_id
    GROUP BY gender_desc
    ORDER BY COUNT(gender_desc)";
    
    //Get gender count of children with homes
    $query2 = 
    "SELECT gender_desc, COUNT(gender_desc) 
    FROM 
    (SELECT DISTINCT c.house_id AS house_id
    FROM bcc_food_client.clients c JOIN bcc_food_client.usage u ON c.client_id = u.client_id 
    WHERE u.date >= '{$start}' AND u.date < '{$end}') AS clients 
    JOIN bcc_food_client.family_members f ON f.member_house_id = clients.house_id
    JOIN bcc_food_client.genders g ON g.gender_id = f.gender_id
    GROUP BY gender_desc
    ORDER BY COUNT(gender_desc)";
    //Get gender count of homeless children    
    $query3 = 
    "SELECT gender_desc, COUNT(gender_desc) 
    FROM 
    (SELECT DISTINCT c.client_id AS client_id
     FROM bcc_food_client.clients c JOIN bcc_food_client.usage u ON c.client_id = u.client_id 
     WHERE c.house_id IS NULL AND u.date >= '{$start}' AND u.date < '{$end}') AS clients 
    JOIN bcc_food_client.family_members f ON f.guardian_id = clients.client_id
    JOIN bcc_food_client.genders g ON g.gender_id = f.gender_id
    GROUP BY gender_desc
    ORDER BY COUNT(gender_desc)";
    
    $allGenders = Gender::getAllGenders();
    $count = array();
    foreach($allGenders as $gender)
    {
      $count[$gender->getGenderDesc()] = 0;
    }
    
    $queries = array($query1, $query2, $query3);
    foreach($queries as $query)
    {
      $results = mysql_query($query);
      if (!results)
      {
        return FALSE;
      }
      while ($row = mysql_fetch_array($results))
      {
        $count["{$row[0]}"] += $row[1];
      }
    }
   
    return $count;
  }
  
  
  function getEthnicityCount($start, $end)
  {
    SQLDB::connect();
    
    $start = mysql_real_escape_string($start);
    $end = mysql_real_escape_string($end);
    //Get ethnicity count of parents
    $query1 = 
    "SELECT ethnicity_desc, COUNT(ethnicity_desc) 
    FROM 
    (SELECT DISTINCT c.client_id AS client_id, c.ethnicity_id AS ethnicity_id 
    FROM bcc_food_client.clients c JOIN bcc_food_client.usage u ON c.client_id = u.client_id 
    WHERE u.date >= '{$start}' AND u.date < '{$end}') AS clients 
    JOIN bcc_food_client.ethnicities e ON e.ethnicity_id = clients.ethnicity_id
    GROUP BY ethnicity_desc
    ORDER BY COUNT(ethnicity_desc)";
    
    //Get ethnicity count of children with homes
    $query2 = 
    "SELECT ethnicity_desc, COUNT(ethnicity_desc) 
    FROM 
    (SELECT DISTINCT c.house_id AS house_id
    FROM bcc_food_client.clients c JOIN bcc_food_client.usage u ON c.client_id = u.client_id 
    WHERE u.date >= '{$start}' AND u.date < '{$end}') AS clients 
    JOIN bcc_food_client.family_members f ON f.member_house_id = clients.house_id
    JOIN bcc_food_client.ethnicities e ON e.ethnicity_id = f.ethnicity_id
    GROUP BY ethnicity_desc
    ORDER BY COUNT(ethnicity_desc)";
    //Get ethnicity count of homeless children    
    $query3 = 
    "SELECT ethnicity_desc, COUNT(ethnicity_desc) 
    FROM 
    (SELECT DISTINCT c.client_id AS client_id
    FROM bcc_food_client.clients c JOIN bcc_food_client.usage u ON c.client_id = u.client_id 
    WHERE c.house_id IS NULL AND u.date >= '{$start}' AND u.date < '{$end}') AS clients 
    JOIN bcc_food_client.family_members f ON f.guardian_id = clients.client_id
    JOIN bcc_food_client.ethnicities e ON e.ethnicity_id = f.ethnicity_id
    GROUP BY ethnicity_desc
    ORDER BY COUNT(ethnicity_desc)";
    
    $allEthnicities = Ethnicity::getAllEthnicities();
    $count = array();
    foreach($allEthnicities as $ethnicity)
    {
      $count[$ethnicity->getEthnicityDesc()] = 0;
    }
    
    $queries = array($query1, $query2, $query3);
    foreach($queries as $query)
    {
      $results = mysql_query($query);
      if (!results)
      {
        return FALSE;
      }
      while ($row = mysql_fetch_array($results))
      {
        $count["{$row[0]}"] += $row[1];
      }
    }
    
    return $count;
  }
  
  
  //Fill this in
  function getAgeCount($start, $end)
  {
    SQLDB::connect();
    
    $start = mysql_real_escape_string($start);
    $end = mysql_real_escape_string($end);
    //Get age count of parents
    $query1 = 
    "SELECT ethnicity_desc, COUNT(ethnicity_desc) 
    FROM 
    (SELECT DISTINCT c.client_id AS client_id, c.ethnicity_id AS ethnicity_id 
    FROM bcc_food_client.clients c JOIN bcc_food_client.usage u ON c.client_id = u.client_id 
    WHERE u.date >= '{$start}' AND u.date < '{$end}') AS clients 
    JOIN bcc_food_client.ethnicities e ON e.ethnicity_id = clients.ethnicity_id
    GROUP BY ethnicity_desc
    ORDER BY COUNT(ethnicity_desc)";
    
    //Get age count of children with homes
    $query2 = 
    "SELECT ethnicity_desc, COUNT(ethnicity_desc) 
    FROM 
    (SELECT DISTINCT c.house_id AS house_id
    FROM bcc_food_client.clients c JOIN bcc_food_client.usage u ON c.client_id = u.client_id 
    WHERE u.date >= '{$start}' AND u.date < '{$end}') AS clients 
    JOIN bcc_food_client.family_members f ON f.member_house_id = clients.house_id
    JOIN bcc_food_client.ethnicities e ON e.ethnicity_id = f.ethnicity_id
    GROUP BY ethnicity_desc
    ORDER BY COUNT(ethnicity_desc)";
    //Get age count of homeless children    
    $query3 = 
    "SELECT ethnicity_desc, COUNT(ethnicity_desc) 
    FROM 
    (SELECT DISTINCT c.client_id AS client_id
    FROM bcc_food_client.clients c JOIN bcc_food_client.usage u ON c.client_id = u.client_id 
    WHERE c.house_id IS NULL AND u.date >= '{$start}' AND u.date < '{$end}') AS clients 
    JOIN bcc_food_client.family_members f ON f.guardian_id = clients.client_id
    JOIN bcc_food_client.ethnicities e ON e.ethnicity_id = f.ethnicity_id
    GROUP BY ethnicity_desc
    ORDER BY COUNT(ethnicity_desc)";
    
    $allEthnicities = Ethnicity::getAllEthnicities();
    $count = array();
    foreach($allEthnicities as $ethnicity)
    {
      $count[$ethnicity->getEthnicityDesc()] = 0;
    }
    
    $queries = array($query1, $query2, $query3);
    foreach($queries as $query)
    {
      $results = mysql_query($query);
      if (!results)
      {
        return FALSE;
      }
      while ($row = mysql_fetch_array($results))
      {
        $count["{$row[0]}"] += $row[1];
      }
    }
    
    return $count;
  }
  
  function getReasonCount($start, $end)
  {
    SQLDB::connect();
    
    $start = mysql_real_escape_string($start);
    $end = mysql_real_escape_string($end);
    
    $query = "SELECT reason_desc, COUNT(reason_desc)
    FROM
    (SELECT DISTINCT c.reason_id AS reason_id
    FROM bcc_food_client.clients c JOIN bcc_food_client.usage u ON c.client_id = u.client_id 
    WHERE c.house_id IS NULL AND u.date >= '{$start}' AND u.date < '{$end}') AS clients 
    JOIN bcc_food_client.reasons r ON  r.reason_id = clients.reason_id
    GROUP BY reason_desc
    ORDER BY COUNT(reason_desc)";
    
    $allReasons = Reason::getAllReasons();
    $count = array();
    foreach($allReasons as $reason)
    {
      $count[$reason->getReasonDesc()] = 0;
    }    
    
    $results = mysql_query($query);
    if (!results)
    {
      return FALSE;
    }
    while ($row = mysql_fetch_array($results))
    {
      $count["{$row[0]}"] += $row[1];
    }
    return $count;
  }
  
  
  
?>