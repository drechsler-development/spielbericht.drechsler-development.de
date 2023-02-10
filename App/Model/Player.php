<?php

namespace App\Model;

class Player
{

	const SETTER        = 1;
	const MIDDLEBLOCKER = 2;
	const OUTSIDEHITTER = 3;
	const OPPSITEHITTER = 4;
	const LIBERO        = 5;

	public int    $id            = 0;
	public string $name          = '';
	public string $number        = '';
	public int    $position      = 0;
	public int    $fieldPosition = 0;

	public function __construct () {
	}

}
