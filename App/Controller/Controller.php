<?php

namespace App\Controller;

use DD\Exceptions\ValidationException;
use Exception;
use Monolog\Logger;

/**
 * Base controller
 *

 */
abstract class Controller
{

	public array $params = [];
	/**
	 * Parameters from the matched route
	 * @var array
	 */
	protected array  $route_params = [];
	protected string $codeVersion  = '';
	protected Logger $LoggerSQL;
	protected Logger $LoggerValidation;
	protected Logger $LoggerError;

	/**
	 * Class constructor
	 *
	 * @param array $route_params Parameters from the route
	 *
	 * @return void
	 * @throws ValidationException
	 */
	public function __construct (array $route_params) {

		$this->route_params = $route_params;

	}

	/**
	 * Magic method called when a non-existent or inaccessible method is
	 * called on an object of this class. Used to execute before and after
	 * filter methods on action methods. Action methods need to be named
	 * with an "Action" suffix, e.g. indexAction, showAction etc.
	 *
	 * @param string $name Method name
	 * @param array $args Arguments passed to the method
	 *
	 * @return void
	 * @throws Exception
	 */
	public function __call (string $name, array $args) {

		$method = $name."Action";

		if (method_exists ($this, $method)) {
			if ($this->Before () !== false) {
				call_user_func_array ([
					$this,
					$method
				], $args);
				$this->After ();
			}

		} else {
			throw new Exception("Method $method not found in controller ".get_class ($this));
		}
	}

	/**
	 * Before filter - called before an action method.
	 *
	 * @return bool
	 */
	protected function Before (): bool {

		return true;
	}

	/**
	 * After filter - called after an action method.
	 *
	 * @return void
	 */
	protected function After (): void {

	}
}
