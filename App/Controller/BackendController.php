<?php

namespace App\Controller;

use App\Model\Login;
use App\Model\Session;
use DD\Exceptions\ValidationException;

/**
 * Base controller
 */
abstract class BackendController extends Controller
{

	protected array  $globalData     = [];
	protected bool   $isGlobalAdmin  = false;
	protected string $deploymentType = '';
	protected string $deploymentDate = ''; // Format: 2020-01-01

	/**
	 * Class constructor
	 *
	 * @param array $route_params Parameters from the route
	 * @return void
	 * @throws ValidationException
	 */

	public function __construct (array $route_params) {

		parent::__construct ($route_params);
		$this->route_params = $route_params;

		$this->globalData = [
			'CODE_VERSION'    => $this->codeVersion,
			'DEPLOYMENT_DATE' => $this->deploymentDate,
			'DEPLOYMENT_TYPE' => $this->deploymentType,
			'COPYRIGHTYEAR'   => 2013,
			/*'SYSTEMNAME'      => SYSTEMNAME,*/
			"SYSTEMTYPE"      => SYSTEMTYPE,
		];

	}

	/**
	 * @throws ValidationException
	 */
	public function Before (): bool {

		if (!Login::CheckLogin ()) {
			header ('Location: /Login');
			exit;
		}

		$this->globalData = array_merge ($this->globalData, [
			"isGlobalAdmin" => Session::IsGlobalAdmin (),
		]);

		return true;
	}

	public function After (): void {

		/*if (empty($_POST)) {
			//Show Array with pre
			echo '<pre>';
			print_r ($_SESSION);
			echo '</pre>';
		}*/
	}
}
