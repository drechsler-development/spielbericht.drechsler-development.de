<?php

namespace App\Model;

use App\Model;
use PDO;

class Game extends Model
{

	public int $id           = 0;
	public int $filterTeamId = 0;
	/**
	 * @var int|mixed
	 */
	public int $filterGroupId = 0;

	public function __construct () {

		parent::__construct ();

	}

	public function Load (): array {

		$whereString = "1 = 1";

		if (!empty($this->filterTeamId)) {
			$whereString .= " AND (t1.id = :id1 OR t2.id = :id2)";
		}
		if (!empty($this->filterGroupId)) {
			$whereString .= " AND (t1.groupId = :groupId1 OR t2.groupId LIKE :groupId2)";
		}

		if (!empty($this->teamId)) {
			$whereString .= " AND g.id = :id";
		}

		$SQL  = "SELECT 
					IFNULL(g.id,0) as id,
					IFNULL(t1.id,0) as teamIdHome,
					IFNULL(t1.teamName,'') as teamNameHome,
					IFNULL(t1.startTime,'') as startTime,
					IFNULL(t1.endTime,'') as endTime,
					IFNULL(t1.address,'') as address,
					IFNULL(t1.additionalAddress,'') as additionalAddress,
					IFNULL(t1.postCode,'') as postCode,
					IFNULL(t1.city,'') as city,
					
					IFNULL(t2.id,0) as teamIdGast,
					IFNULL(t2.teamName,'') as teamNameGast,
					IFNULL(g.round,0) as round,
					IFNULL(g.date,'') as date,
					WEEKDAY(g.date) as day,
					
					IFNULL(tg.id,0) as groupId,
					IFNULL(tg.name,'') as groupName
					
				FROM 
				    games g
				INNER JOIN 
				    teams t1 
				    ON t1.id = g.teamIdHome
				INNER JOIN 
				    teams t2 
				    ON t2.id = g.teamIdGast
				INNER JOIN 
				    teamGroups tg 
				    ON tg.id = t1.groupId
				WHERE 
				    $whereString
				ORDER BY 
				    g.round,
				    t1.groupId,
				    g.date,
				    t1.startTime";
		$stmt = $this->conPDO->prepare ($SQL);
		if (!empty($this->id)) {
			$stmt->bindValue (':id', $this->id, PDO::PARAM_INT);
		}
		if (!empty($this->filterTeamId)) {
			$stmt->bindValue (':id1', $this->filterTeamId);
			$stmt->bindValue (':id2', $this->filterTeamId);
		}
		if (!empty($this->filterGroupId)) {
			$stmt->bindValue (':groupId1', $this->filterGroupId, PDO::PARAM_INT);
			$stmt->bindValue (':groupId2', $this->filterGroupId, PDO::PARAM_INT);
		}
		$stmt->execute ();

		return $stmt->fetchAll (PDO::FETCH_ASSOC);
	}
}
