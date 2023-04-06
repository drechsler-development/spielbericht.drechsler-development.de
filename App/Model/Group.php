<?php

namespace App\Model;

use App\Model;
use PDO;

class Group extends Model
{

	public int    $id   = 0;
	public string $name = '';

	public function __construct () {

		parent::__construct ();
	}

	public function Load (): array {

		$whereString = "1 = 1";

		if (!empty($this->id)) {
			$whereString .= " AND tg.id = :id";
		}

		$SQL  = "SELECT 
					IFNULL(tg.id,0) as id,
					IFNULL(tg.name,'') as name,
					IFNULL(tg.gender,0) as gender
					
				FROM 
				    teamGroups tg 
				WHERE 
				    $whereString
				ORDER BY 
				    tg.name";
		$stmt = $this->conPDO->prepare ($SQL);
		if (!empty($this->id)) {
			$stmt->bindValue (':id', $this->id, PDO::PARAM_INT);
		}
		$stmt->execute ();

		return $stmt->fetchAll (PDO::FETCH_ASSOC);
	}

}
