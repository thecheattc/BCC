<?php
	
	//----------Make sure you factor out all the temp table BS
	//----------Should duplicated households include homeless?
	
  //Returns all the unduplicated individuals who successfully got food in a month 
  //given a valid MySQL date range from the first of a month (YYYY-MM-01)
  //to the first of the next month
	class Report
	{
		private $HOMELESS_REASON_ID = 6;
		private $REJECTED_ID = 3;
		private $start;
		private $end;
		
		private function getUnduplicatedIndividualsExcludingHomelessChildren()
		{			
			$numParents = 0;
			$numHomeChildren = 0;
			
			$parents = "SELECT COUNT(*) FROM visiting_clients";
			$homeChildren = "SELECT COUNT(*)
												FROM (SELECT DISTINCT f.fam_member_id 
															FROM bcc_food_client.clients c JOIN bcc_food_client.houses h ON c.house_id = h.house_id
															JOIN bcc_food_client.family_members f ON h.house_id = f.member_house_id
															JOIN visiting_clients v ON c.client_id = v.client_id) as family";
			$result = mysql_query($parents);
			if ($row = mysql_fetch_array($result))
			{
				$numParents = $row[0];
			}
			$result = mysql_query($homeChildren);
			if ($row = mysql_fetch_array($result))
			{
				$numHomeChildren = $row[0];
			}
			return $numParents + $numHomeChildren;
		}
		
		//Returns the number of duplicated households that successfully got food this month
		//(just the number of visits)
		private function getDuplicatedHouseholds()
		{
			$query = "SELECT COUNT(1) FROM bcc_food_client.usage WHERE date >= '{$this->start}' AND date < '{$this->end}' AND type_id != '{$this->REJECTED_ID}'";
			$result = mysql_query($query);
			
			$count = 0;
			if ($row = mysql_fetch_array($result))
			{
				$count = $row[0];
			}
			return $count;
		}  
		
		//Returns the number of households that received food between the start date and the end date, not including homeless visitors.
		private function getUnduplicatedHouseholdsExcludingHomeless()
		{
			//People with homes
			$query = "SELECT COUNT(1) 
								FROM (SELECT DISTINCT c.house_id
											FROM visiting_clients c WHERE c.house_id IS NOT NULL) as houses";
			$result = mysql_query($query);
			
			$count = 0;
			if ($row = mysql_fetch_array($result))
			{
				$count = $row[0];
			}
			
			return $count;
		}
		
		//Returns the number of homeless people who successfully received food this month
		private function getTotalHomelessParents()
		{
			$numHomelessParents = 0;
			$homelessParents = "SELECT COUNT(1)
													FROM visiting_clients
													WHERE house_id IS NULL";
			$result = mysql_query($homelessParents);
			echo mysql_error();
			if ($row = mysql_fetch_array($result))
			{
				$numHomelessParents = $row[0];
			}
			return $numHomelessParents;
		}
		
		private function getTotalHomelessChildren()
		{
			$numHomelessChildren = 0;
			$homelessChildren = "SELECT COUNT(1)
														FROM (SELECT DISTINCT f.fam_member_id
																	FROM visiting_clients c JOIN bcc_food_client.family_members f ON f.guardian_id = c.client_id) as members";
			$result = mysql_query($homelessChildren);
			if ($row = mysql_fetch_array($result))
			{
				$numHomelessChildren = $row[0];
			}
			return $numHomelessChildren;
		}
		
		/*private function getHouseholdLocations()
		{
			$query = "SELECT zip, COUNT(zip) FROM
				(SELECT DISTINCT c.house_id FROM bcc_food_client.clients c JOIN bcc_food_client.usage u
				ON c.client_id = u.client_id WHERE c.house_id IS NOT NULL AND u.date >= '{$this->start}' AND u.date < '{$this->end}') AS clients
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
		
		private function getGenderCount()
		{
			//Get gender count of parents
			$query1 = 
			"SELECT gender_desc, COUNT(gender_desc) 
			FROM 
				(SELECT DISTINCT c.client_id AS client_id, c.gender_id AS gender_id 
				FROM bcc_food_client.clients c JOIN bcc_food_client.usage u ON c.client_id = u.client_id 
				WHERE u.date >= '{$this->start}' AND u.date < '{$this->end}') AS clients 
				JOIN bcc_food_client.genders g ON g.gender_id = clients.gender_id
			GROUP BY gender_desc
			ORDER BY COUNT(gender_desc)";
			
			//Get gender count of children with homes
			$query2 = 
			"SELECT gender_desc, COUNT(gender_desc) 
			FROM 
			(SELECT DISTINCT c.house_id AS house_id
			FROM bcc_food_client.clients c JOIN bcc_food_client.usage u ON c.client_id = u.client_id 
			WHERE u.date >= '{$this->start}' AND u.date < '{$this->end}') AS clients 
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
			 WHERE c.house_id IS NULL AND u.date >= '{$this->start}' AND u.date < '{$this->end}') AS clients 
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
		
		
		private function getEthnicityCount()
		{
			//Get ethnicity count of parents
			$query1 = 
			"SELECT ethnicity_desc, COUNT(ethnicity_desc) 
			FROM 
			(SELECT DISTINCT c.client_id AS client_id, c.ethnicity_id AS ethnicity_id 
			FROM bcc_food_client.clients c JOIN bcc_food_client.usage u ON c.client_id = u.client_id 
			WHERE u.date >= '{$this->start}' AND u.date < '{$this->end}') AS clients 
			JOIN bcc_food_client.ethnicities e ON e.ethnicity_id = clients.ethnicity_id
			GROUP BY ethnicity_desc
			ORDER BY COUNT(ethnicity_desc)";
			
			//Get ethnicity count of children with homes
			$query2 = 
			"SELECT ethnicity_desc, COUNT(ethnicity_desc) 
			FROM 
			(SELECT DISTINCT c.house_id AS house_id
			FROM bcc_food_client.clients c JOIN bcc_food_client.usage u ON c.client_id = u.client_id 
			WHERE u.date >= '{$this->start}' AND u.date < '{$this->end}') AS clients 
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
			WHERE c.house_id IS NULL AND u.date >= '{$this->start}' AND u.date < '{$this->end}') AS clients 
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
		private function getAgeCount()
		{
			//Get age count of parents
			$query1 = 
			"SELECT ethnicity_desc, COUNT(ethnicity_desc) 
			FROM 
			(SELECT DISTINCT c.client_id AS client_id, c.ethnicity_id AS ethnicity_id 
			FROM bcc_food_client.clients c JOIN bcc_food_client.usage u ON c.client_id = u.client_id 
			WHERE u.date >= '{$this->start}' AND u.date < '{$this->end}') AS clients 
			JOIN bcc_food_client.ethnicities e ON e.ethnicity_id = clients.ethnicity_id
			GROUP BY ethnicity_desc
			ORDER BY COUNT(ethnicity_desc)";
			
			//Get age count of children with homes
			$query2 = 
			"SELECT ethnicity_desc, COUNT(ethnicity_desc) 
			FROM 
			(SELECT DISTINCT c.house_id AS house_id
			FROM bcc_food_client.clients c JOIN bcc_food_client.usage u ON c.client_id = u.client_id 
			WHERE u.date >= '{$this->start}' AND u.date < '{$this->end}') AS clients 
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
			WHERE c.house_id IS NULL AND u.date >= '{$this->start}' AND u.date < '{$this->end}') AS clients 
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
		
		private function getReasonCount()
		{
			$query = "SELECT reason_desc, COUNT(reason_desc)
			FROM
			(SELECT DISTINCT c.reason_id AS reason_id
			FROM bcc_food_client.clients c JOIN bcc_food_client.usage u ON c.client_id = u.client_id 
			WHERE c.house_id IS NULL AND u.date >= '{$this->start}' AND u.date < '{$this->end}') AS clients 
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
		*/
		public function getReport($start, $end)
		{
			SQLDB::connect("bcc_food_client");
			
			$this->start = mysql_real_escape_string($start);
			$this->end = mysql_real_escape_string($end);
			
			$tempTable = "CREATE TEMPORARY TABLE IF NOT EXISTS visiting_clients
										SELECT DISTINCT c.client_id, c.age, c.house_id, c.ethnicity_id, c.gender_id, c.reason_id,	
																		c.unemployment_date, c.application_date, c.receives_stamps, c.wants_stamps
										FROM bcc_food_client.clients c JOIN bcc_food_client.usage u
										ON c.client_id = u.client_id WHERE u.date >= '{$this->start}' AND u.date < '{$this->end}' AND u.type_id != '{$this->REJECTED_ID}'";
			$result = mysql_query($tempTable);
			$homelessParents = $this->getTotalHomelessParents();
			$homelessChildren = $this->getTotalHomelessChildren();
			$duplicatedHouseholds = $this->getDuplicatedHouseholds();
			$unduplicatedHouseholds = $this->getUnduplicatedHouseholdsExcludingHomeless() + $homelessParents;
			$unduplicatedIndividuals = $this->getUnduplicatedIndividualsExcludingHomelessChildren() + $homelessChildren;
			
			/*$locations = getLocations();
			$genderCounts = getGenderCount();
			$ageCounts = getAgeCount();
			$ethnicityCounts = getEthnicityCount();
			$totalHomeless = $homelessParents + $homelessChildren;
			$newlyUnemployed = getNewlyUnemployed();*/
			
			mysql_query("DROP TEMPORARY TABLE IF EXISTS visiting_clients");
			
			echo "<PRE>";
			echo "Homeless parents: " . $homelessParents . "\n";
			echo "Homeless children: " . $homelessChildren . "\n";
			echo "Duplicated households: " . $duplicatedHouseholds . "\n";
			echo "Unduplicated households: " . $unduplicatedHouseholds . "\n";
			echo "Unduplicated individuals: " . $unduplicatedIndividuals . "\n";
			echo "</PRE>";
		}
	}
  
  
  
?>