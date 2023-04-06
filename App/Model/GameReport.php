<?php

namespace App\Model;

use App\Model;
use DD\Exceptions\ValidationException;
use PDO;

class GameReport extends Model
{

	public int $id = 0;

	/**
	 * @throws ValidationException
	 */
	public function __construct () {

		parent::__construct ();
	}

	/**
	 * @param bool $json
	 * @return array|string
	 * @throws ValidationException
	 */
	public function Load (): array|string {

		$whereString = "1 = 1";

		if (!empty($this->id)) {
			$whereString .= " AND t.id = :id";
		}

		$SQL  = "SELECT 
                    t.id, 
                    t.teamName, 
                    t.address, 
                    TIME_FORMAT(t.startTime,'%H:%i') as startTime, 
                    TIME_FORMAT(t.endTime,'%H:%i') as endTime, 
                    tg.name as groupName 
				FROM 
				    teams t
				INNER JOIN 
				    teamGroups tg 
				    ON tg.id = t.groupId
				WHERE  
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

	/*public function Export (): string {

		$path = "/TEMP/".$this->teamName.".json";

		$data['players']           = $this->players;
		$data['team']['name']      = $this->teamName;
		$data['team']['address']   = $this->address;
		$data['team']['startTime'] = $this->startTime;
		$data['team']['endTime']   = $this->endTime;

		$jsonData       = json_encode ($data);
		$this->teamName = preg_replace ('/[^A-Za-z0-9\-]/', '', $this->teamName);
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

	}*/

}
