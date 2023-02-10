<?php

namespace App\Controller;

use DD\Exceptions\ValidationException;

/**
 * Base controller
 */
abstract class BackendController extends Controller
{

	protected array  $RIGHTS                    = [];
	protected array  $navigationArray           = [];
	protected array  $headerArray               = [];
	protected array  $globalData                = [];
	protected bool   $isCourseProvider          = false;
	protected bool   $isShopProvider            = false;
	protected bool   $isFeWoProvider            = false;
	protected bool   $isBeachChairProvider      = false;
	protected bool   $isRentableArticleProvider = false;
	protected bool   $isGlobalAdmin             = false;
	protected bool   $isLocalAdmin              = false;
	protected bool   $isSystemUser              = false;
	protected int    $vabsClientId              = 0;
	protected string $vabsClientName            = '';
	protected int    $vabsUserId                = 0;
	protected string $vabsUserName              = '';
	protected string $vabsUserEmail             = '';
	protected string $docRoot                   = '';
	protected string $deploymentType            = '';
	protected string $deploymentDate            = ''; // Format: 2020-01-01

	/**
	 * Class constructor
	 *
	 * @param array $route_params Parameters from the route
	 * @return void
	 * @throws ValidationException
	 */

	public function __construct (array $route_params) {

		parent::__construct ($route_params);
		$this->docRoot      = $_SERVER['DOCUMENT_ROOT'];
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

		/*if (!Login::CheckLogin ()) {
			header ('Location: /login');
			exit;
		}*/

		/*$this->navigationArray           = Navigation::GetNavigation ();
		$this->headerArray               = Navigation::GetHeader ();*/

		$this->globalData = array_merge ($this->globalData, [
			"isGlobalAdmin"             => $this->isGlobalAdmin,
			"isLocalAdmin"              => $this->isLocalAdmin,
			"isSystemUser"              => $this->isSystemUser,
			"isCourseProvider"          => $this->isCourseProvider,
			"isShopProvider"            => $this->isShopProvider,
			"isFeWoProvider"            => $this->isFeWoProvider,
			"isBeachChairProvider"      => $this->isBeachChairProvider,
			"isRentableArticleProvider" => $this->isRentableArticleProvider,
			"SYSTEMTYPE"                => SYSTEMTYPE,
			"vabsClientId"              => $this->vabsClientId,
			"vabsClientName"            => $this->vabsClientName,
			"vabsUserId"                => $this->vabsUserId,
			"vabsUserName"              => $this->vabsUserName,
			"vabsUserEmail"             => $this->vabsUserEmail,
			"docRoot"                   => $this->docRoot,
			/*"user"                      => Right::GetUserRights ($_SERVER['QUERY_STRING'], Session::VabsUserId ())*/
		]);

		return true;
	}

	public function After (): void {

	}

	/**
	 * @param string $folder
	 * @return array
	 * @throws ValidationException
	 */
	protected function GetRights (string $folder): array {

		$rights = [];

		/*$rights['READ']   = Right::HasRight (Right::READ, $folder);
		$rights['ADD']    = Right::HasRight (Right::ADD, $folder);
		$rights['EDIT']   = Right::HasRight (Right::EDIT, $folder);
		$rights['DELETE'] = Right::HasRight (Right::DELETE, $folder);
		$rights['ADMIN']  = Right::HasRight (Right::ADMIN, $folder);*/

		return $rights;

	}
}
