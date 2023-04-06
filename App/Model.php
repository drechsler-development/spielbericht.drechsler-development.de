<?php

namespace App;

use DD\Database;
use DD\Exceptions\ValidationException;
use PDO;
use PDOException;

abstract class Model
{

	public PDO       $conPDO;
	protected array  $allowedFields = [];
	protected string $table         = '';

	/**
	 * @throws ValidationException
	 */
	public function __construct () {

		$this->conPDO = Database::getInstance ();
	}

	public function Update (array $fieldsAndValues): void {

		$setString = "";

		if (empty($this->id)) {
			throw new PDOException("No id is set");
		}

		if (count ($fieldsAndValues) == 0) {
			throw new PDOException("No fields and values have been provided to update the BeachChair");
		}

		foreach ($this->allowedFields as $field) {

			if (isset($fieldsAndValues[$field])) {

				$setString .= "`$field` = :$field ,";

			}

		}

		//Remove last comma
		$setString = rtrim ($setString, ",");

		if (empty($setString)) {
			throw new PDOException("SetString was empty");
		}

		$SQL = "UPDATE 
						$this->table 
					SET 
						$setString 
					WHERE 
						id = :id";
		$stm = $this->conPDO->prepare ($SQL);

		foreach ($this->allowedFields as $field) {

			if (isset($fieldsAndValues[$field])) {

				$keyParamStr = ":".$field;
				//Insert NULL values for date fields
				if ((str_contains ($field, 'date') || str_contains ($field, 'datum') || str_contains ($field, 'time')) && empty($fieldsAndValues[$field])) {
					$stm->bindValue ($keyParamStr, null);
				} else {
					$stm->bindValue ($keyParamStr, htmlspecialchars (strip_tags ($fieldsAndValues[$field], '<p><a><em><b><strong><br>')));
				}

			}

		}

		$stm->bindValue (":id", $this->id);

		$stm->execute ();

	}

}
