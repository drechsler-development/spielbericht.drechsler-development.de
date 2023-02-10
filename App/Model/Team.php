<?php

namespace App\Model;

use DD\Database;
use DD\Exceptions\ValidationException;
use PDO;

class Team
{

	public int    $id        = 0;
	public string $name      = '';
	public string $address   = '';
	public string $startTime = '';
	public string $endTime   = '';

	public array  $players   = [];
	public string $groupName = '';

	private PDO $conPDO;

	/**
	 * @throws ValidationException
	 */
	public function __construct () {

		$this->conPDO = Database::getInstance ();
	}

	/**
	 * @param bool $json
	 * @return array|string
	 * @throws ValidationException
	 */
	public function Load (): array|string {

		$players     = [];
		$whereString = "";
		if (!empty($this->id)) {
			$whereString = " AND id = :id";
		}
		$SQL  = "SELECT 
                    id, 
                    name, 
                    address, 
                    TIME_FORMAT(startTime,'%H:%i') as startTime, 
                    TIME_FORMAT(endTime,'%H:%i') as endTime, 
                    groupName 
				FROM 
				    teams 
				WHERE 
				    1 = 1 
				    $whereString";
		$stmt = $this->conPDO->prepare ($SQL);
		if (!empty($this->id)) {
			$stmt->bindValue (':id', $this->id, PDO::PARAM_INT);
		}
		$stmt->execute ();
		$team = $stmt->fetchAll (PDO::FETCH_ASSOC);
		if (!$team) {
			throw new ValidationException ("Team not found");
		}

		if (!empty($this->id)) {

			$SQL  = "SELECT id, name, number, position FROM players WHERE teamId = :teamId";
			$stmt = $this->conPDO->prepare ($SQL);
			$stmt->bindValue (':teamId', $this->id, PDO::PARAM_INT);
			$stmt->execute ();
			$players = $stmt->fetchAll (PDO::FETCH_ASSOC);
			$team    = array_merge ($team[0], ['players' => $players]);
		}

		return $team;

	}

	public function AddPlayer (Player $player): void {

		$this->players[] = $player;
	}

	//export players to a json file
	public function Export (): string {

		$path = "/TEMP/".$this->name.".json";

		$data['players']           = $this->players;
		$data['team']['name']      = $this->name;
		$data['team']['address']   = $this->address;
		$data['team']['startTime'] = $this->startTime;
		$data['team']['endTime']   = $this->endTime;

		$jsonData   = json_encode ($data);
		$this->name = preg_replace ('/[^A-Za-z0-9\-]/', '', $this->name);
		file_put_contents ($_SERVER['DOCUMENT_ROOT'].$path, $jsonData);

		return $path;

	}

	public function Import ($filePath): string {

		//Get filename extension
		$ext = pathinfo ($filePath, PATHINFO_EXTENSION);
		if ($ext != "json") {
			return json_encode (["error" => "File extension not allowed. Only json files are allowed."]);
		}

		$data = file_get_contents ($filePath);
		//check if $data contains json data
		if (!json_decode ($data)) {
			return json_encode (["error" => "File is not a valid json file."]);
		}

		return $data;

	}

	//save data to database
	public function Save (): void {

		//Check if name already exists
		$SQL = "SELECT id FROM teams WHERE name LIKE :name";
		$stm = $this->conPDO->prepare ($SQL);
		$stm->bindValue (':name', $this->name);
		$stm->execute ();
		if ($stm->rowCount () > 0) {
			$array    = $stm->fetch (PDO::FETCH_ASSOC);
			$this->id = $array['id'];
		} else {
			$SQL = "INSERT INTO 
                    teams (
                           name
                           ) 
					VALUES (
					        'NEU'
					        )";
			$stm = $this->conPDO->prepare ($SQL);
			$stm->execute ();
			$this->id = $this->conPDO->lastInsertId ();
		}

		$this->Update ();

	}

	public function Update (): void {

		$SQL = "UPDATE 
                    teams 
				SET 
					name = :name, 
					address = :address, 
					groupName = :groupName, 
					startTime = :startTime, 
					endTime = :endTime
				WHERE 
				    id = :id";
		$stm = $this->conPDO->prepare ($SQL);
		$stm->bindValue (':name', $this->name);
		$stm->bindValue (':address', $this->address);
		$stm->bindValue (':groupName', $this->groupName);
		$stm->bindValue (':startTime', $this->startTime);
		$stm->bindValue (':endTime', $this->endTime);
		$stm->bindValue (':id', $this->id);
		$stm->execute ();

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
