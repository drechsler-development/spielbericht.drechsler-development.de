<?php

namespace App;

use DD\SystemType;

class ResponseArray
{

	public string $error    = '';
	public string $data     = '';
	public string $debug    = '';
	public string $redirect = '';

	private array $outputArray = [];


	public function __construct () { }

	public function AddData(array $array): void {

		$this->outputArray = array_merge($this->outputArray, $array);

	}

	public function ReturnOutput (): void {

		$this->outputArray = array_merge ($this->outputArray, [
			'success'  => empty($this->error),
			'error'    => $this->error,
			'data'     => $this->data,
			'debug'    => SYSTEMTYPE == SystemType::DEV ? $this->debug : '',
			'redirect' => $this->redirect,

		]);

		echo json_encode ($this->outputArray);

	}

}
