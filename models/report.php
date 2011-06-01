<?php
	
	date_default_timezone_set('America/NewYork');
	class Report
	{
		private $HOMELESS_REASON_ID = 6;
		private $REJECTED_ID = 3;
		private $newlyUnemployedDate;
		private $start;
		private $end;
		private $FISCAL_YEAR_BEGIN;
		private $NEXT_YEAR_BEGIN;
		
		public static function showAllClients()
		{
			SQLDB::connect("bcc_food_client");
			$query = "SELECT *
									FROM bcc_food_client.clients AS c LEFT JOIN (
										SELECT u.dist_id, u.client_id, u.date
										FROM bcc_food_client.usage u JOIN (
											SELECT client_id, MAX(date) AS maxdate
											FROM bcc_food_client.usage
											GROUP BY client_id
											) AS most_recent ON most_recent.client_id=u.client_id AND most_recent.maxdate=u.date
										) AS recent_visit ON c.client_id=recent_visit.client_id
										LEFT JOIN bcc_food_client.houses AS h ON c.house_id=h.house_id
										JOIN bcc_food_client.ethnicities AS e ON c.ethnicity_id=e.ethnicity_id
										JOIN bcc_food_client.genders AS g ON c.gender_id=g.gender_id
										JOIN bcc_food_client.reasons AS r ON r.reason_id=c.reason_id
										ORDER BY c.last_name, c.first_name
									";
			$result = mysql_query($query);
			echo "<table>
							<tr>
								<th>Client ID</th>
								<th>Name</th>
								<th>Age</th>
								<th>Phone number</th>
								<th>Spouse ID</th>
								<th>Address</th>
								<th>Gender</th>
								<th>Ethnicity</th>
								<th>Reason</th>
								<th>Explanation</th>
								<th>Unemployment date</th>
								<th>Application date</th>
								<th>Most recent visit ID</th>
								<th>Most recent visit date</th>
							</tr>
			";
			while($row = mysql_fetch_assoc($result))
			{
				echo "
				<tr>
					<td> {$row['client_id']} </td>
					<td> {$row['first_name']} {$row['last_name']} </td>
					<td> {$row['age']} </td>
					<td> {$row['phone_number']} </td>
					<td> {$row['spouse_id']} </td>
					<td> {$row['street_number']} {$row['street_name']} {$row['street_type']} {$row['line_2']} {$row['city']} {$row['zip']} </td>
					<td> {$row['gender_desc']} </td>
					<td> {$row['ethnicity_desc']} </td>
					<td> {$row['reason_desc']} </td>
					<td> {$row['explanation']} </td>
					<td> {$row['unemployment_date']} </td>
					<td> {$row['application_date']} </td>
					<td> {$row['dist_id']} </td>
					<td> {$row['date']} </td>
					<td> {$row['location']} </td>
				</tr>
				";
			}
			echo "</table>";
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
		
		private function getUnduplicatedHouseholds()
		{
			$result = mysql_query("SELECT COUNT(1) FROM visiting_houses");
			
			$count = -1;
			if ($row = mysql_fetch_array($result))
			{
				$count = $row[0];
			}
			
			$result = mysql_query("SELECT COUNT(1)/2 FROM homeless_parents WHERE spouse_id IS NOT NULL");
			echo mysql_error();
			if ($row = mysql_fetch_array($result))
			{
				$count += $row[0];
			}
			
			$result = mysql_query("SELECT COUNT(1) FROM homeless_parents WHERE spouse_id IS NULL");
			
			if ($row = mysql_fetch_array($result))
			{
				$count += $row[0];
			}
			
			return $count;
		}
		
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
		
		private function getTotalHomeParents()
		{
			$count = -1;
			$result = mysql_query("SELECT COUNT(1) FROM home_parents");
			if ($row = mysql_fetch_array($result))
			{
				$count = $row[0];
			}
			
			return $count;
		}
		
		private function getTotalHomeChildren()
		{
			$count = -1;
			$result = mysql_query("SELECT COUNT(1) FROM home_children");
			if ($row = mysql_fetch_array($result))
			{
				$count = $row[0];
			}
			
			return $count;
		}
		
		private function getHouseholdLocations()
		{
			$query = "SELECT zip, COUNT(zip) FROM visiting_houses
								GROUP BY zip
								ORDER BY zip";
			return runCountQueriesAndUnionResults(array($query));
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
			
			$queries = array($homeParentReasons, $homelessParentReasons);
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
			FROM homeless_children
			GROUP BY ageband";
			
			$queries = array($homeParentAges, $homelessParentAges, $homeChildrenAges, $homelessChildrenAges);
			return runCountQueriesAndUnionResults($queries);
		}
		
		private function getNewlyUnemployed()
		{
			$query = "SELECT COUNT(*) 
								FROM visiting_clients 
								WHERE unemployment_date >= '{$this->FISCAL_YEAR_BEGIN}' 
								AND (reported_on = 0 OR reported_on IS NULL)";
			
			$result = mysql_query($query);
			$count = -1;
			if ($row = mysql_fetch_array($result))
			{
				$count = $row[0];
			}
			//Mark those who have been reported on as ineligible for reporting until the next year
			$query = "UPDATE bcc_food_client.clients 
								SET reported_on = 1, noreport_until = '{$this->NEXT_YEAR_BEGIN}' 
								WHERE client_id IN (SELECT client_id FROM visiting_clients) 
								AND (reported_on = 0 OR reported_on IS NULL)
								AND unemployment_date >= '{$this->FISCAL_YEAR_BEGIN}'";
			mysql_query($query);
			return $count;
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
			$nextYear = date("Y");
			$fiscalYearDateTime = createMySQLDate(date("Y")-1 . "-07-01");
			$nextYearDateTime = createMySQLDate(date("Y") . "-07-01");
			$this->FISCAL_YEAR_BEGIN = $fiscalYearDateTime->format("Y-m-d");
			$this->NEXT_YEAR_BEGIN = $nextYearDateTime->format("Y-m-d");
			
			//List of clients that actually came to get food and were successful
			$visitingClients = 
			"CREATE TEMPORARY TABLE visiting_clients
			SELECT DISTINCT c.client_id, c.spouse_id, c.age, c.house_id, c.ethnicity_id, c.gender_id, c.reason_id,	
			c.unemployment_date, c.application_date, c.receives_stamps, c.wants_stamps, c.reported_on, c.noreport_until
			FROM bcc_food_client.clients c JOIN bcc_food_client.usage u
			ON c.client_id = u.client_id WHERE u.date >= '{$this->start}' AND u.date < '{$this->end}' AND u.type_id != '{$this->REJECTED_ID}'";
			$result = mysql_query($visitingClients);
			if ($result === FALSE)
			{
				echo "visitingClients";
				return FALSE;
			}
			
			//List of houses of those who actually came to get food
			$visitingHouses = 
			"CREATE TEMPORARY TABLE visiting_houses
			SELECT DISTINCT h.house_id, h.street_number, h.street_name, h.street_type, h.line2, h.city, h.zip
			FROM bcc_food_client.houses h JOIN visiting_clients v ON h.house_id = v.house_id
			WHERE v.reason_id != {$this->HOMELESS_REASON_ID}";
			$result = mysql_query($visitingHouses);
			if ($result === FALSE)
			{
				echo "visitingHouses";
				return FALSE;
			}
			
			$parents = "CREATE TEMPORARY TABLE parents
			SELECT c.client_id, c.spouse_id, c.first_name, c.last_name, c.age, c.phone_number, c.house_id, c.ethnicity_id,
			c.gender_id, c.reason_id, c.explanation, c.unemployment_date, c.application_date,
			c.receives_stamps, c.wants_stamps
			FROM 
			(SELECT DISTINCT w.client_id 
			FROM bcc_food_client.clients w JOIN bcc_food_client.usage x ON w.client_id=x.client_id 
			WHERE x.type_id != {$this->REJECTED_ID} AND x.date >= '{$this->start}' AND x.date < '{$this->end}'
			UNION
			SELECT DISTINCT y.spouse_id AS client_id
			FROM bcc_food_client.clients y JOIN bcc_food_client.usage z ON y.client_id=z.client_id 
			WHERE z.type_id != {$this->REJECTED_ID} AND z.date >= '{$this->start}' AND z.date < '{$this->end}'
			) AS parent_ids JOIN bcc_food_client.clients c ON parent_ids.client_id = c.client_id";
			$result = mysql_query($parents);
			if ($result === FALSE)
			{
				echo "parents";
				return FALSE;
			}
			
			//List of homeless people that got food as well as their spouses
			$homelessParents = 
			"CREATE TEMPORARY TABLE homeless_parents
			SELECT client_id, spouse_id, first_name, last_name, age, phone_number, house_id, ethnicity_id,
				gender_id, reason_id, explanation, unemployment_date, application_date,
				receives_stamps, wants_stamps
			FROM parents WHERE reason_id = {$this->HOMELESS_REASON_ID}";
			$result = mysql_query($homelessParents);
			if ($result === FALSE)
			{
				echo "homeless_parents";
				return FALSE;
			}

			//List of people with homes that got food including their spouses
			$homeParents = 
			"CREATE TEMPORARY TABLE home_parents
			SELECT client_id, spouse_id, first_name, last_name, age, phone_number, house_id, ethnicity_id,
							gender_id, reason_id, explanation, unemployment_date, application_date,
							receives_stamps, wants_stamps
			FROM parents WHERE reason_id != {$this->HOMELESS_REASON_ID}";
			$result = mysql_query($homeParents);
			if ($result === FALSE)
			{
				echo "home_parents";
				return FALSE;
			}
			
			//Children of homeless parents that got food
			$homelessChildren = 
			"CREATE TEMPORARY TABLE homeless_children
			SELECT DISTINCT f.fam_member_id, f.age, f.gender_id, f.ethnicity_id, f.guardian_id
			FROM bcc_food_client.family_members f JOIN homeless_parents hp ON f.guardian_id = hp.client_id";
			$result = mysql_query($homelessChildren);
			if ($result === FALSE)
			{
				echo "homeless_children";
				return FALSE;
			}
			
			//Children of parents with homes that got food
			$homeChildren = 
			"CREATE TEMPORARY TABLE home_children
			SELECT f.fam_member_id, f.age, f.gender_id, f.ethnicity_id, f.member_house_id
			FROM visiting_houses vh JOIN bcc_food_client.family_members f ON f.member_house_id = vh.house_id";
			$result = mysql_query($homeChildren);
			if ($result === FALSE)
			{
				echo "home_children";
				return FALSE;
			}
			
			//Mark those who were reported as unemployed last fiscal year as eligible for reporting again.
			mysql_query("UPDATE bcc_food_clients.client
									SET reported_on = 0 AND noreport_until = '{$this->NEXT_YEAR_BEGIN}'
									WHERE client_id IN (SELECT client_id FROM visiting_clients)
									AND reported_on = 1 AND noreport_until <= CURDATE()");
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
		
		public function getReport($start, $end)
		{
			SQLDB::connect("bcc_food_client");
			
			$this->start = normalDateToMySQL($start);
			$this->end = normalDateToMySQL($end);
			
			if ($this->initialize() === FALSE)
			{
				return;
			}
			
			$homelessParents = $this->getTotalHomelessParents();
			$homelessChildren = $this->getTotalHomelessChildren();
			$homeParents = $this->getTotalHomeParents();
			$homeChildren = $this->getTotalHomeChildren();
			
			$duplicatedHouseholds = $this->getDuplicatedHouseholds();
			$rejections = $this->getNumberOfRejections();
			$receivesStamps = $this->getReceivesFoodstampsCount();
			$wantsStamps = $this->getWantsFoodstampsCount();
			$unduplicatedHouseholds = $this->getUnduplicatedHouseholds();
			$unduplicatedIndividuals = $homelessParents + $homeParents + $homelessChildren + $homeChildren;
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
			echo "Newly unemployed: " . $newlyUnemployed . "\n";
			echo "</PRE>";
		}
	}
  
  
  
?>