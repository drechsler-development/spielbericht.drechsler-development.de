<?php

namespace App\Model;

use App\Model;
use PDO;

class Team extends Model
{

	public int    $id                = 0;
	public string $teamName          = '';
	public string $address           = '';
	public string $additionalAddress = '';
	public string $startTime         = '';
	public string $endTime           = '';
	public string $groupName         = '';

	public array $rows = [];

	public array $players = [];

	public function __construct () {

		parent::__construct ();

		$this->allowedFields = [
			'teamName',
			'address',
			'additionalAddress',
			'postCode',
			'city',
			'day',
			'startTime',
			'endTime',
			'teamLeadName',
			'teamLeadEmail',
			'teamLeadTelephone',
			'teamLeadName2',
			'teamLeadEmail2',
			'teamLeadTelephone2',
		];

		$this->table = 'teams';
	}

	public function Load (): array {

		$whereString = "1 = 1";

		if (!empty($this->id)) {
			$whereString .= " AND t.id = :id";
		}

		$SQL  = "SELECT 
					IFNULL(t.id,0) as id,
					IFNULL(t.groupId,0) as groupId,
					IFNULL(t.teamName,'') as teamName,
					IFNULL(t.teamLeadName,'') as teamLeadName,
					IFNULL(t.teamLeadEmail,'') as teamLeadEmail,
					IFNULL(t.teamLeadTelephone,'') as teamLeadTelephone,
					IFNULL(t.teamLeadName2,'') as teamLeadName2,
					IFNULL(t.teamLeadEmail2,'') as teamLeadEmail2,
					IFNULL(t.teamLeadTelephone2,'') as teamLeadTelephone2,
					IFNULL(t.address,'') as address,
					IFNULL(t.additionalAddress,'') as additionalAddress,
					IFNULL(t.postCode,'') as postCode,
					IFNULL(t.city,'') as city,
					IFNULL(t.day,'') as day,
					TIME_FORMAT(startTime,'%H:%i') as startTime, 
					TIME_FORMAT(endTime,'%H:%i') as endTime,
					IFNULL(tg.name,'') as groupName
					
				FROM 
				    teams t 
				LEFT JOIN 
				    teamGroups tg 
				    ON tg.id = t.groupId
				WHERE 
				    $whereString
				ORDER BY 
				    tg.name, 
				    t.teamName";
		$stmt = $this->conPDO->prepare ($SQL);
		if (!empty($this->id)) {
			$stmt->bindValue (':id', $this->id, PDO::PARAM_INT);
		}
		$stmt->execute ();

		return $stmt->fetchAll (PDO::FETCH_ASSOC);
	}

	public function AddPlayer (Player $player): void {

		$this->players[] = $player;
	}

	public function UpdatePlayers (): void {

		//Show Array with pre
		echo '<pre>';
		print_r ($this->players);
		echo '</pre>';

		if (!empty($this->players)) {
			$SQL = "DELETE FROM players WHERE teamId = :teamId";
			$stm = $this->conPDO->prepare ($SQL);
			$stm->bindValue (':teamId', $this->id);
			$stm->execute ();

			foreach ($this->players as $player) {
				$SQL = "INSERT INTO 
							players (
									teamId,
									number,
									name,
									position,
									fieldPosition
									) 
							VALUES (
									:teamId,
									:number,
									:name,
									:position,
									:fieldPosition
									)";
				$stm = $this->conPDO->prepare ($SQL);
				$stm->bindValue (':teamId', $this->id, PDO::PARAM_INT);
				$stm->bindValue (':name', $player->name);
				$stm->bindValue (':number', $player->number, PDO::PARAM_INT);
				$stm->bindValue (':position', $player->position, PDO::PARAM_INT);
				$stm->bindValue (':fieldPosition', $player->fieldPosition, PDO::PARAM_INT);
				$stm->execute ();
			}
		}

	}

}
