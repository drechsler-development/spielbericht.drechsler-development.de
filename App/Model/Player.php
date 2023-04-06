<?php

namespace App\Model;

use App\Model;
use PDO;

class Player extends Model
{

	const SETTER        = 1;
	const MIDDLEBLOCKER = 2;
	const OUTSIDEHITTER = 3;
	const OPPSITEHITTER = 4;
	const LIBERO        = 5;

	public int    $id            = 0;
	public int    $teamId        = 0;
	public string $name          = '';
	public string $number        = '';
	public int    $position      = 0;
	public int    $fieldPosition = 0;

	public function __construct () {

		parent::__construct ();

	}

	public function Load (): array {

		$whereString = "1 = 1";

		if (!empty($this->id)) {
			$whereString .= " AND p.id = :id";
		}

		if (!empty($this->teamId)) {
			$whereString .= " AND p.teamId = :teamId";
		}

		$SQL  = "SELECT 
					IFNULL(p.id,0) as id,
					IFNULL(p.name,'') as name,
					IFNULL(p.number,'') as number,
					IFNULL(p.position,'') as position,
					IFNULL(p.fieldPosition,'') as fieldPosition
					
				FROM 
				    players p
				WHERE 
				    $whereString
				ORDER BY 
				    p.number";
		$stmt = $this->conPDO->prepare ($SQL);
		if (!empty($this->id)) {
			$stmt->bindValue (':id', $this->id, PDO::PARAM_INT);
		}
		if (!empty($this->teamId)) {
			$stmt->bindValue (':teamId', $this->teamId, PDO::PARAM_INT);
		}
		$stmt->execute ();

		return $stmt->fetchAll (PDO::FETCH_ASSOC);
	}

}
