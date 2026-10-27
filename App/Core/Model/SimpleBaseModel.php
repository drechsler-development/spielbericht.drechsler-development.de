<?php

namespace App\Core\Model;

use DD\Exceptions\ValidationException;
use PDO;
use PDOException;

/**
 * Base model
 */
abstract class SimpleBaseModel extends BaseModel {

	/**
	 * @throws ValidationException
	 */
	public function __construct () {

		parent::__construct ();

	}

	/**
	 * @return void
	 */
	public function Create (): void {

		$SQL = "INSERT INTO 
						$this->table 
					SET 
						netdisplay = 1";
		$stm = $this->conPDO->prepare ($SQL);

		$stm->execute ();

		$this->id = $this->conPDO->lastInsertId ();

	}

	/**
	 * @param bool $hardDelete
	 *
	 * @throws PDOException
	 */
	public function Delete (bool $hardDelete = false): void {

		if (empty($this->id)) {
			throw new PDOException("No id is set");
		}

		if ($hardDelete) {

			$SQL = "DELETE FROM 
						$this->table
					WHERE 
						id = :id";

		} else {
			$SQL = "UPDATE 
						$this->table
					SET
					    netdisplay = 0
					WHERE 
						id = :id";
		}

		$stm = $this->conPDO->prepare ($SQL);

		$stm->bindValue (':id', $this->id, PDO::PARAM_INT);

		$stm->execute ();

	}
}
