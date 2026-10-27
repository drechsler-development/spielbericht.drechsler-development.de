<?php

namespace App\Core\Model;

use DD\Database;
use DD\Exceptions\ValidationException;
use Monolog\Formatter\LineFormatter;
use Monolog\Formatter\NormalizerFormatter;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PDO;
use PDOException;

/**
 * Base model
 */
abstract class BaseModel {

	public PDO    $conPDO;
	public Logger $logger;

	public int    $id           = 0;
	public int    $CLIENT_ID;
	public int    $COMPANY_ID;
	public int    $CONTACT_ID   = 0;
	public int    $userId       = 1;
	public int    $netdisplay   = 1;
	public string $name         = '';
	public string $description  = '';
	public string $errorMessage = '';

	/**
	 * @var mixed
	 */
	public mixed     $row;
	public string    $lastChange     = '';
	protected array  $allowedFields  = [];
	protected string $table          = '';
	protected bool   $logUpdateQuery = false;

	/**
	 * @throws ValidationException
	 */
	public function __construct () {

		$this->conPDO = self::GetDBConection ();

		$formatter = new LineFormatter(LineFormatter::SIMPLE_FORMAT, NormalizerFormatter::SIMPLE_DATE);
		$formatter->includeStacktraces ();

		$this->logger  = new Logger("LOGGER");
		$streamHandler = new StreamHandler(ROOTPATH . '/LOG/' . date ("Y-m-d") . '.log', Logger::DEBUG);
		$streamHandler->setFormatter ($formatter);
		$this->logger->pushHandler ($streamHandler);
		$this->logger->pushHandler (new FirePHPHandler());

		//Check if allowedFOelds contains duplicates
		if (count ($this->allowedFields) !== count (array_unique ($this->allowedFields))) {
			throw new ValidationException("Allowed fields contains duplicates");
		}

	}

	/**
	 * Get the PDO database connection
	 *
	 * @return PDO|null
	 * @throws ValidationException
	 */
	private static function GetDBConection (): ?PDO {

		return Database::getInstance ();

	}

	/**
	 * @return void
	 */
	public function Create (): void {

		$SQL = "INSERT INTO 
						$this->table 
					SET 
						clientId = :clientId,
						netdisplay = 1";
		$stm = $this->conPDO->prepare ($SQL);

		$stm->bindValue (":clientId", $this->CLIENT_ID);

		$stm->execute ();

		$this->id = $this->conPDO->lastInsertId ();

	}

	/**
	 * @param array $fieldsAndValues
	 *
	 * @throws PDOException
	 */
	public function Update (array $fieldsAndValues): void {

		$ignoreClientId = $this->table == 'pb_sales_lines' || $this->table == 'pb_kontakte' || str_contains ($this->table, 'pb_ticket');

		$setString   = "";
		$whereString = "1 = 1 ";

		if (empty($this->id)) {
			throw new PDOException("No id is set");
		}

		if (count ($fieldsAndValues) == 0) {
			throw new PDOException("No fields and values have been provided to update the table");
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

		if (!$ignoreClientId) {


			if (!empty($this->CLIENT_ID)) {
				$whereString .= " AND clientId = :clientId";
			}


		}

		$SQL = "UPDATE 
						$this->table 
					SET 
						$setString 
					WHERE 
						$whereString AND 
						id = :id";
		$stm = $this->conPDO->prepare ($SQL);

		foreach ($this->allowedFields as $field) {

			if (isset($fieldsAndValues[$field])) {

				$keyParamStr = ":" . $field;
				//Insert NULL values for date fields
				if (
					//If the value is provided as string with value 'NULL' we need to insert also a NULL Value into the database
					$fieldsAndValues[$field] === 'NULL' ||
					//if the field is named as a date or time field and the value is empty we need to insert also a NULL Value into the database
					(
						(
							str_contains ($field, 'date') ||
							str_contains ($field, 'datum') ||
							str_contains ($field, 'time')
						)
						&& empty($fieldsAndValues[$field])
					)
				) {
					$stm->bindValue ($keyParamStr, null);
				} else {
					$stm->bindValue ($keyParamStr, (strip_tags ($fieldsAndValues[$field], '<p><a><em><b><strong><br><span><h1><h2><h3><h4><h5><h6><strong>')));
				}

			}

		}

		$stm->bindValue (":id", $this->id);

		if (!$ignoreClientId) {


			if (!empty($this->CLIENT_ID)) {
				$stm->bindValue (":clientId", $this->CLIENT_ID);
			}

		}

		$stm->execute ();

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

		$ignoreClientId = $this->table == 'pb_sales_lines';

		$whereString = "1 = 1 ";

		if (!$ignoreClientId) {


			if (!empty($this->CLIENT_ID)) {
				$whereString .= " AND clientId = :clientId";
			}


		}

		if ($hardDelete) {

			$SQL = "DELETE FROM 
						$this->table
					WHERE 
						$whereString AND 
						id = :id";

		} else {
			$SQL = "UPDATE 
						$this->table
					SET
					    netdisplay = 0
					WHERE 
						$whereString AND 
						id = :id";
		}

		$stm = $this->conPDO->prepare ($SQL);

		$stm->bindValue (':id', $this->id, PDO::PARAM_INT);

		if (!$ignoreClientId) {


			if (!empty($this->CLIENT_ID)) {
				$stm->bindValue (":clientId", $this->CLIENT_ID);
			}

		}

		$stm->execute ();

	}

	public function ValidateVabsClientId (): void {

		if (empty($this->vabsClientId)) {
			throw new ValidationException("Keine ClientId gesetzt.");
		}

	}

	/**
	 * @return void
	 * @throws ValidationException
	 */
	protected function CheckId (): void {

		if (empty($this->id)) {
			throw new ValidationException("No id is set");
		}

	}
}
