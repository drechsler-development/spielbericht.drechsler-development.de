<?php

namespace App\Core;

use DD\SystemType;

class ResponseArray {

	public string $error      = '';
	public string $data       = '';
	public string $debug      = '';
	public array  $debugArray = [];
	public array  $dataArray  = [];
	public string $redirect   = '';
	public string $hint       = '';

	private array $outputArray = [];

	public function __construct () {
	}

	public function AddData (array $array): void {

		$this->outputArray = array_merge ($this->outputArray, $array);

	}

	/**
	 * @param bool $json
	 *
	 * @return array|string
	 */
	public function ReturnOutput (bool $json = false): array|string {

		$this->error = mb_convert_encoding ($this->error, 'UTF-8', 'UTF-8');

		$this->outputArray = array_merge ($this->outputArray, [
			'success'    => empty($this->error),
			'error'      => $this->error ?? '',
			'data'       => $this->data ?? '',
			'dataArray'  => $this->dataArray ?? [],
			'debug'      => SYSTEMTYPE != SystemType::PROD ? $this->debug : null,
			'debugArray' => SYSTEMTYPE != SystemType::PROD ? $this->debugArray : null,
			'redirect'   => $this->redirect ?: null,
			'hint'       => $this->hint ?: null,

		]);

		return $json ? json_encode ($this->outputArray) : $this->outputArray;

	}
}
