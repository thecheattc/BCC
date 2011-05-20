<?php
	//----------Should duplicated households include homeless?
	
  //Returns all the unduplicated individuals who successfully got food in a month 
  //given a valid MySQL date range from the first of a month (YYYY-MM-01)
  //to the first of the next month
	class Report
	{
		private $HOMELESS_REASON_ID = 6;
		private $REJECTED_ID = 3;
		private $newlyUnemployedDate;
		private $start;
		private $end;
		
		private function getUnduplicatedIndividualsExcludingHomeless()
		{	
			$numHomeParents = -1;
			$numHomeChildren = -1;
			
			$homeParents = "SELECT COUNT(*) FROM home_parents";
			$homeChildren = "SELECT COUNT(*) FROM home_children";
			$result = mysql_query($homeParents);
			if ($row = mysql_fetch_array($result))
			{
				$numHomeParents = $row[0];
			}
			$result = mysql_query($homeChildren);
			if ($row = mysql_fetch_array($result))
			{
				$numHomeChildren = $row[0];
			}
			if ($numHomeParents === -1 || $numHomeChildren === -1)
			{
				return -1;
			}
			return $numHomeParents + $numHomeChildren;
		}
		
		//Returns the number of duplicated households that successfully got food this month
		//(just the number of visits)
		private function getDuplicatedHouseholds()
		{
			$query = "SELECT COUNT(1) FROM bcc_food_client.usage 
								WHERE date >= '{$this->start}' AND date < '{$this->end}' AND type_id != '{$this->REJECTED_ID}'";
			$result = mysql_query($query);
			
			$count = -1;
			if ($row = mysql_fetch_array($result))
			{
				$count = $row[0];
			}
			return $count;
		} 
		
		//Returns the total number of times Bryant had to turn away an individual - doesn't include families, includes
		//duplicate rejections
		private function getNumberOfRejections()
		{
			$query = "SELECT COUNT(1) FROM bcc_food_client.usage 
								WHERE date >= '{$this->start}' AND date < '{$this->end}' AND type_id = '{$this->REJECTED_ID}'";
			$result = mysql_query($query);
			
			$count = -1;
			if ($row = mysql_fetch_array($result))
			{
				$count = $row[0];
			}
			return $count;
		}
		
		//Returns the number of households that received food between the start date and the end date, not including homeless visitors or their children.
		private function getUnduplicatedHouseholdsExcludingHomeless()
		{
			$query = "SELECT COUNT(1) FROM visiting_houses";
			$result = mysql_query($query);
			
			$count = -1;
			if ($row = mysql_fetch_array($result))
			{
				$count = $row[0];
			}
			
			return $count;
		}
		
		//Returns the number of homeless parents who successfully received food this month
		private function getTotalHomelessParents()
		{
			$numHomelessParents = -1;
			$homelessParents = "SELECT COUNT(1)	FROM homeless_parents";
			$result = mysql_query($homelessParents);
			if ($row = mysql_fetch_array($result))
			{
				$numHomelessParents = $row[0];
			}
			return $numHomelessParents;
		}
		
		private function getTotalHomelessChildren()
		{
			$numHomelessChildren = -1;
			$homelessChildren = "SELECT COUNT(1) FROM homeless_children";
			$result = mysql_query($homelessChildren);
			if ($row = mysql_fetch_array($result))
			{
				$numHomelessChildren = $row[0];
			}
			return $numHomelessChildren;
		}
		
		private function getHouseholdLocations()
		{
			$query = "SELECT zip, COUNT(zip) FROM visiting_houses
								GROUP BY zip
								ORDER BY zip";
			$result = mysql_query($query);
			$houses = array();
			while ($row = mysql_fetch_array($result))
			{
				$houses["{$row[0]}"] = $row[1];
			}
			return $houses;
		}
		
		private function getGenderCount()
		{
			$homeParentGenders = 
			"SELECT gender_desc, COUNT(gender_desc) 
			FROM home_parents hp JOIN bcc_food_client.genders g ON hp.gender_id = g.gender_id
			GROUP BY gender_desc
			ORDER BY gender_desc";
			
			$homelessParentGenders = 
			"SELECT gender_desc, COUNT(gender_desc) 
			FROM homeless_parents hp JOIN bcc_food_client.genders g ON hp.gender_id = g.gender_id
			GROUP BY gender_desc
			ORDER BY gender_desc";
			
			$homeChildrenGenders = 
			"SELECT gender_desc, COUNT(gender_desc) 
			FROM home_children h JOIN bcc_food_client.genders g ON h.gender_id = g.gender_id
			GROUP BY gender_desc
			ORDER BY gender_desc";
			
			$homelessChildrenGenders = 
			"SELECT gender_desc, COUNT(gender_desc) 
			FROM homeless_children hc JOIN bcc_food_client.genders g ON hc.gender_id = g.gender_id
			GROUP BY gender_desc
			ORDER BY gender_desc";
						
			$queries = array($homeParentGenders, $homelessParentGenders, $homeChildrenGenders, $homelessChildrenGenders);
			return runCountQueriesAndUnionResults($queries);
		}
		
		
		private function getEthnicityCount()
		{
			$homeParentEthnicities = 
			"SELECT ethnicity_desc, COUNT(ethnicity_desc) 
			FROM home_parents hp JOIN bcc_food_client.ethnicities e ON hp.ethnicity_id = e.ethnicity_id
			GROUP BY ethnicity_desc
			ORDER BY ethnicity_desc";
			
			$homelessParentEthnicities = 
			"SELECT ethnicity_desc, COUNT(ethnicity_desc) 
			FROM homeless_parents hp JOIN bcc_food_client.ethnicities e ON hp.ethnicity_id = e.ethnicity_id
			GROUP BY ethnicity_desc
			ORDER BY ethnicity_desc";
			
			$homeChildrenEthnicities = 
			"SELECT ethnicity_desc, COUNT(ethnicity_desc) 
			FROM home_children h JOIN bcc_food_client.ethnicities e ON h.ethnicity_id = e.ethnicity_id
			GROUP BY ethnicity_desc
			ORDER BY ethnicity_desc";
			
			$homelessChildrenEthnicities = 
			"SELECT ethnicity_desc, COUNT(ethnicity_desc) 
			FROM homeless_children hc JOIN bcc_food_client.ethnicities e ON hc.ethnicity_id = e.ethnicity_id
			GROUP BY ethnicity_desc
			ORDER BY ethnicity_desc";
			
			$queries = array($homeParentEthnicities, $homelessParentEthnicities, $homeChildrenEthnicities, $homelessChildrenEthnicities);
			return runCountQueriesAndUnionResults($queries);
		}
		
		private function getReasonCount()
		{
			$homeParentReasons = 
			"SELECT reason_desc, COUNT(reason_desc) 
			FROM home_parents hp JOIN bcc_food_client.reasons e ON hp.reason_id = e.reason_id
			GROUP BY reason_desc
			ORDER BY reason_desc";
			
			$homelessParentReasons = 
			"SELECT reason_desc, COUNT(reason_desc) 
			FROM homeless_parents hp JOIN bcc_food_client.reasons e ON hp.reason_id = e.reason_id
			GROUP BY reason_desc
			ORDER BY reason_desc";
			
			$homeChildrenReasons = 
			"SELECT reason_desc, COUNT(reason_desc) 
			FROM home_parents hp JOIN bcc_food_client.reasons e ON hp.reason_id = e.reason_id
			JOIN home_children hc ON hc.member_house_id = hp.house_id
			GROUP BY reason_desc
			ORDER BY reason_desc";
			
			$homelessChildrenReasons = 
			"SELECT reason_desc, COUNT(reason_desc) 
			FROM homeless_parents hp JOIN bcc_food_client.reasons e ON hp.reason_id = e.reason_id
			JOIN homeless_children hc ON hc.guardian_id= hp.client_id
			GROUP BY reason_desc
			ORDER BY reason_desc";
			
			$queries = array($homeParentReasons, $homelessParentReasons, $homeChildrenReasons, $homelessChildrenReasons);
			return runCountQueriesAndUnionResults($queries);
		}
		
		private function getAgeCount()
		{
			$homeParentAges = 
			"SELECT
				CASE
					WHEN age < 6 THEN 'under 5'
					WHEN age BETWEEN 6 AND 10 THEN '6-10'
					WHEN age BETWEEN 11 AND 15 THEN '11-15'
					WHEN age BETWEEN 16 AND 20 THEN '16-20'
					WHEN age BETWEEN 21 AND 25 THEN '21-25'
					WHEN age BETWEEN 26 AND 30 THEN '26-30'
					WHEN age BETWEEN 31 AND 35 THEN '31-35'
					WHEN age BETWEEN 36 AND 40 THEN '36-40'
					WHEN age BETWEEN 41 AND 45 THEN '41-45'
					WHEN age BETWEEN 46 AND 50 THEN '46-50'
					WHEN age BETWEEN 51 AND 55 THEN '51-55'
					WHEN age BETWEEN 56 AND 60 THEN '56-60'
					WHEN age BETWEEN 61 AND 65 THEN '61-65'
					WHEN age BETWEEN 66 AND 70 THEN '66-70'
					WHEN age > 70 THEN 'over 70'
				END as ageband, COUNT(*)
			FROM home_parents
			GROUP BY ageband";
			
			$homelessParentAges = 
			"SELECT
				CASE
					WHEN age < 6 THEN 'under 5'
					WHEN age BETWEEN 6 AND 10 THEN '6-10'
					WHEN age BETWEEN 11 AND 15 THEN '11-15'
					WHEN age BETWEEN 16 AND 20 THEN '16-20'
					WHEN age BETWEEN 21 AND 25 THEN '21-25'
					WHEN age BETWEEN 26 AND 30 THEN '26-30'
					WHEN age BETWEEN 31 AND 35 THEN '31-35'
					WHEN age BETWEEN 36 AND 40 THEN '36-40'
					WHEN age BETWEEN 41 AND 45 THEN '41-45'
					WHEN age BETWEEN 46 AND 50 THEN '46-50'
					WHEN age BETWEEN 51 AND 55 THEN '51-55'
					WHEN age BETWEEN 56 AND 60 THEN '56-60'
					WHEN age BETWEEN 61 AND 65 THEN '61-65'
					WHEN age BETWEEN 66 AND 70 THEN '66-70'
					WHEN age > 70 THEN 'over 70'
				END as ageband, COUNT(*)
			FROM homeless_parents
			GROUP BY ageband";
			
			$homeChildrenAges = 
			"SELECT
				CASE
					WHEN age < 6 THEN 'under 5'
					WHEN age BETWEEN 6 AND 10 THEN '6-10'
					WHEN age BETWEEN 11 AND 15 THEN '11-15'
					WHEN age BETWEEN 16 AND 20 THEN '16-20'
					WHEN age BETWEEN 21 AND 25 THEN '21-25'
					WHEN age BETWEEN 26 AND 30 THEN '26-30'
					WHEN age BETWEEN 31 AND 35 THEN '31-35'
					WHEN age BETWEEN 36 AND 40 THEN '36-40'
					WHEN age BETWEEN 41 AND 45 THEN '41-45'
					WHEN age BETWEEN 46 AND 50 THEN '46-50'
					WHEN age BETWEEN 51 AND 55 THEN '51-55'
					WHEN age BETWEEN 56 AND 60 THEN '56-60'
					WHEN age BETWEEN 61 AND 65 THEN '61-65'
					WHEN age BETWEEN 66 AND 70 THEN '66-70'
					WHEN age > 70 THEN 'over 70'
				END as ageband, COUNT(*)
			FROM home_children
			GROUP BY ageband";
			
			$homelessChildrenAges = 
			"SELECT
				CASE
					WHEN age < 6 THEN 'under 5'
					WHEN age BETWEEN 6 AND 10 THEN '6-10'
					WHEN age BETWEEN 11 AND 15 THEN '11-15'
					WHEN age BETWEEN 16 AND 20 THEN '16-20'
					WHEN age BETWEEN 21 AND 25 THEN '21-25'
					WHEN age BETWEEN 26 AND 30 THEN '26-30'
					WHEN age BETWEEN 31 AND 35 THEN '31-35'
					WHEN age BETWEEN 36 AND 40 THEN '36-40'
					WHEN age BETWEEN 41 AND 45 THEN '41-45'
					WHEN age BETWEEN 46 AND 50 THEN '46-50'
					WHEN age BETWEEN 51 AND 55 THEN '51-55'
					WHEN age BETWEEN 56 AND 60 THEN '56-60'
					WHEN age BETWEEN 61 AND 65 THEN '61-65'
					WHEN age BETWEEN 66 AND 70 THEN '66-70'
					WHEN age > 70 THEN 'over 70'
				END as ageband, COUNT(*)
			FROM home_children
			GROUP BY ageband";
			
			$queries = array($homeParentAges, $homelessParentAges, $homeChildrenAges, $homelessChildrenAges);
			return runCountQueriesAndUnionResults($queries);
		}
		
		private function getNewlyUnemployed()
		{
			$query = "SELECT COUNT(*) FROM visiting_clients WHERE unemployment_date > '{$this->newlyUnemployedDate}'";
			$result = mysql_query($query);
			if ($row = mysql_fetch_array($result))
			{
				return $row[0];
			}
			return -1;
		}
		
		private function getReceivesFoodstampsCount()
		{
			$query = "SELECT COUNT(1) FROM visiting_clients WHERE receives_stamps = 1";
			$result = mysql_query($query);
			if ($row = mysql_fetch_array($result))
			{
				return $row[0];
			}
			return -1;
		}
		
		private function getWantsFoodstampsCount()
		{
			$query = "SELECT COUNT(1) FROM visiting_clients WHERE wants_stamps = 1";
			$result = mysql_query($query);
			if ($row = mysql_fetch_array($result))
			{
				return $row[0];
			}
			return -1;
		}
		
		private function initialize()
		{
			if ($this->dropTemporaryTables() === FALSE)
			{
				return FALSE;
			}
			
			$visitingClients = 
			"CREATE TEMPORARY TABLE visiting_clients
			SELECT DISTINCT c.client_id, c.age, c.house_id, c.ethnicity_id, c.gender_id, c.reason_id,	
			c.unemployment_date, c.application_date, c.receives_stamps, c.wants_stamps
			FROM bcc_food_client.clients c JOIN bcc_food_client.usage u
			ON c.client_id = u.client_id WHERE u.date >= '{$this->start}' AND u.date < '{$this->end}' AND u.type_id != '{$this->REJECTED_ID}'";
			$result = mysql_query($visitingClients);
			if ($result === FALSE)
			{
				echo "visitingClients";
				return FALSE;
			}
			
			$visitingHouses = 
			"CREATE TEMPORARY TABLE visiting_houses
			SELECT DISTINCT h.house_id, h.street_number, h.street_name, h.street_type, h.line2, h.city, h.zip
			FROM bcc_food_client.houses h JOIN visiting_clients v ON h.house_id = v.house_id";
			$result = mysql_query($visitingHouses);
			if ($result === FALSE)
			{
				echo "visitingHouses";
				return FALSE;
			}
			//Since there's only a link between parent and children, and not parent and parent,
			//This will possibly give us a slightly lower count for queries regarding the homeless
			//because spouses won't be factored in.
			$homelessParents = 
			"CREATE TEMPORARY TABLE homeless_parents
			SELECT * FROM visiting_clients WHERE reason_id = {$this->HOMELESS_REASON_ID}";
			$result = mysql_query($homelessParents);
			if ($result === FALSE)
			{
				echo "homeless_parents";
				return FALSE;
			}
			//Parents that got food and have homes, including their spouses
			$homeParents = 
			"CREATE TEMPORARY TABLE home_parents
			SELECT c.client_id, c.first_name, c.last_name, c.age, c.phone_number, c.house_id, c.ethnicity_id,
							c.gender_id, c.reason_id, c.explanation, c.unemployment_date, c.application_date,
							c.receives_stamps, c.wants_stamps
			FROM	(SELECT house_id FROM visiting_houses) AS visiting_house_ids JOIN bcc_food_client.clients c ON visiting_house_ids.house_id = c.house_id";
			$result = mysql_query($homeParents);
			if ($result === FALSE)
			{
				echo "home_parents";
				return FALSE;
			}
			
			$homelessChildren = 
			"CREATE TEMPORARY TABLE homeless_children
			SELECT f.fam_member_id, f.age, f.gender_id, f.ethnicity_id, f.guardian_id
			FROM bcc_food_client.family_members f JOIN homeless_parents hp ON f.guardian_id = hp.client_id";
			$result = mysql_query($homelessChildren);
			if ($result === FALSE)
			{
				echo "homeless_children";
				return FALSE;
			}
			
			$homeChildren = 
			"CREATE TEMPORARY TABLE home_children
			SELECT  f.fam_member_id, f.age, f.gender_id, f.ethnicity_id, f.member_house_id
			FROM (SELECT DISTINCT house_id
						FROM visiting_clients) AS distinct_houses JOIN bcc_food_client.family_members f ON f.member_house_id = distinct_houses.house_id";
			$result = mysql_query($homeChildren);
			if ($result === FALSE)
			{
				echo "home_children";
				return FALSE;
			}
			return TRUE;
		}
		
		private function dropTemporaryTables()
		{
			mysql_query("DROP TEMPORARY TABLE IF EXISTS visiting_clients");
			mysql_query("DROP TEMPORARY TABLE IF EXISTS visiting_houses");
			mysql_query("DROP TEMPORARY TABLE IF EXISTS home_parents");
			mysql_query("DROP TEMPORARY TABLE IF EXISTS homeless_parents");
			mysql_query("DROP TEMPORARY TABLE IF EXISTS home_children");
			mysql_query("DROP TEMPORARY TABLE IF EXISTS homeless_children");
		}
		
		public function getReport($start, $end, $newlyUnemployedDate = "00-00-0000")
		{
			SQLDB::connect("bcc_food_client");
			
			$this->newlyUnemployedDate = createMySQLDate($newlyUnemployedDate);
			$this->start = mysql_real_escape_string($start);
			$this->end = mysql_real_escape_string($end);
			
			if ($this->initialize() === FALSE)
			{
				return;
			}
			
			$homelessParents = $this->getTotalHomelessParents();
			$homelessChildren = $this->getTotalHomelessChildren();
			$duplicatedHouseholds = $this->getDuplicatedHouseholds();
			$rejections = $this->getNumberOfRejections();
			$receivesStamps = $this->getReceivesFoodstampsCount();
			$wantsStamps = $this->getWantsFoodstampsCount();
			$unduplicatedHouseholds = $this->getUnduplicatedHouseholdsExcludingHomeless() + $homelessParents;
			$unduplicatedIndividuals = $this->getUnduplicatedIndividualsExcludingHomeless() + $homelessParents + $homelessChildren;
			
			$locations = $this->getHouseholdLocations();
			$genderCount = $this->getGenderCount();
			$ageCount = $this->getAgeCount();
			$ethnicityCount = $this->getEthnicityCount();
			$reasonCount = $this->getReasonCount();
			$totalHomeless = $homelessParents + $homelessChildren;
			$newlyUnemployed = $this->getNewlyUnemployed();
						
			$this->dropTemporaryTables();
			
			echo "<PRE>";
			echo "Duplicated households:\n\t" . $duplicatedHouseholds . "\n";
			echo "Unduplicated households:\n\t" . $unduplicatedHouseholds . "\n";
			echo "Unduplicated individuals:\n\t" . $unduplicatedIndividuals . "\n";
			echo "Household locations:\n";
			printKeyValue($locations);
			echo "Gender count:\n";
			printKeyValue($genderCount);
			echo "Ethnicity count:\n";
			printKeyValue($ethnicityCount);
			echo "Reason count:\n";
			printKeyValue($reasonCount);
			echo "Age count:\n";
			printKeyValue($ageCount);
			echo "Total homeless:\n\t" . $totalHomeless . "\n";
			echo "Duplicated rejections: " . $rejections . "\n";
			echo "Visitors on foodstamps: " . $receivesStamps . "\n";
			echo "Visitors that want foodstamps: " . $wantsStamps . "\n";
			echo "Newly unemployed since " . $newlyUnemployedDate . ":" . $newlyUnemployed . "\n";
			echo "</PRE>";
		}
	}
  
  
  
?>