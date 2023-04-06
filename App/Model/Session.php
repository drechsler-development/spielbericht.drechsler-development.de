<?php

namespace App\Model;

class Session
{

	#region LOGIN

	public static function IsGlobalAdmin (): bool {

		return !empty($_SESSION['login']['admin']);
	}

	public static function SetGlobalAdmin (bool $value): void {

		$_SESSION['login']['admin'] = $value === true ? true : null;
	}

	public static function IsLoggedIn (): bool {

		return !empty($_SESSION['login']['id']);
	}

	//Getter and Setter

	public static function Set (string $fieldName, mixed $value): void {

		$_SESSION['login'][$fieldName] = $value;
	}

	public static function Get (string $fieldName): mixed {

		return $_SESSION['login'][$fieldName] ?? null;
	}

	#endregion
}
