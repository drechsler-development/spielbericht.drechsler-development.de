<?php

namespace App\Model;

use App\Model;
use DD\Exceptions\ValidationException;
use PDO;

class Login extends Model
{

	public int    $id        = 0;
	public int    $admin     = 0;
	public string $email     = '';
	public string $password  = '';
	public string $firstName = '';
	public string $lastName  = '';

	public function __construct () {

		parent::__construct ();

	}

	public static function Logout () {

		$_SESSION['login'] = [];

	}

	/**
	 * @return bool
	 */
	public static function CheckLogin (): bool {

		return !empty($_SESSION['login']['id']);
	}

	/**
	 * @return void
	 * @throws ValidationException
	 */
	public function Login (): void {

		$_SESSION['login'] = [];

		if (empty($this->email) || empty($this->password)) {
			throw new ValidationException('Please enter your email and password.');
		}

		//Check login against the database
		$SQL = "SELECT 
                    u.id as id,
                    u.email as email,
                    u.firstName as firstName,
                    u.lastName as lastName,
                    u.password as password,
                    u.admin as admin,
                    ut.teamId as teamId
				FROM 
				    users u
				LEFT JOIN 
				    teamUsers ut
				    ON ut.userId = u.id
				WHERE 
				    u.email = :email";
		$stm = $this->conPDO->prepare ($SQL);
		$stm->bindValue (':email', $this->email);
		$stm->execute ();
		$user = $stm->fetch (PDO::FETCH_ASSOC);
		//check against password verify
		if (!$user) {
			throw new ValidationException('Es konnte kein Benutzer unter der angegebenen Emailadresse gefunden werden');
		}

		if (!password_verify ($this->password, $user['password'])) {
			throw new ValidationException('Das angegebene Passwort ist nicht korrekt');
		}

		$_SESSION['login']['id']        = $user['id'] ?? 0;
		$_SESSION['login']['email']     = $user['email'] ?? '';
		$_SESSION['login']['firstName'] = $user['firstName'] ?? '';
		$_SESSION['login']['lastName']  = $user['lastName'] ?? '';
		$_SESSION['login']['teamId']    = $user['teamId'] ?? '';
		Session::SetGlobalAdmin (!empty($user['admin']));

	}

}
