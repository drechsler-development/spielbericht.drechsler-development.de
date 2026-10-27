<?php

namespace App\Core\Model;

use DD\SystemType;

trait TDebug {

	public array $debugArray = [];

	/**
	 * @param string $key
	 * @param mixed  $value
	 *
	 * @return void
	 */
	public function AddDebugData (string $key, mixed $value): void {
		$this->debugArray[$key] = $value;
	}

	/**
	 * @return array
	 */
	public function GetDebugData (): array {
		return SYSTEMTYPE == SystemType::DEV ? $this->debugArray : [];
	}

}
