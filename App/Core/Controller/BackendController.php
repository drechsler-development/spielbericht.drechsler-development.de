<?php

namespace App\Core\Controller;

use App\Model\Session;
use DateTime;
use DD\Exceptions\PermissionException;
use DD\Exceptions\ValidationException;
use Exception;

/**
 * Base controller
 */
abstract class BackendController extends BaseController {

	public array     $RIGHTS        = [];
	protected bool   $isGlobalAdmin = false;
	protected bool   $isLocalAdmin  = false;
	protected bool   $isSystemUser  = false;
	protected int    $vabsClientId;
	protected string $vabsClientName;
	protected int    $vabsUserId;
	protected string $vabsUserName;
	protected string $vabsUserEmail;
	protected string $folder        = "";

	/**
	 * @throws ValidationException
	 * @throws Exception
	 */
	public function __construct () {

		parent::__construct ();

		//Checks if a user is logged in otherwise redirect to the login page
		self::CheckLogin ();

		//Set the current session time to now to be able to check if the session is still valid
		Session::SetCurrentSessionTime (new DateTime('now'));

		//Assign some client/user data to variables
		$this->vabsClientId   = Session::VabsClientId ();
		$this->vabsClientName = Session::GetVabsClientName ();
		$this->vabsUserId     = Session::VabsUserId ();
		$this->vabsUserName   = Session::VabsUserFirstName () . " " . Session::VabsUserLastName ();
		$this->vabsUserEmail  = Session::VabsUserEmail ();
		$this->isGlobalAdmin  = Session::IsGlobalAdmin ();
		$this->isLocalAdmin   = Session::IsLocalAdmin ();

		//Check, if we have a videoLink
		foreach ($this->navigationArray as $nav) {

			if ($nav['bVideoLink'] != '' && $nav['bVideoDate'] != '') {

				if (str_contains (strtolower ($_SERVER['REQUEST_URI']), strtolower ($nav['bFolder']))) {
					$this->globalData['VIDEO_LINK'] = $nav['bVideoLink'];
					break;
				}
			}
		}

		//Put some data into the global data to have easier access in other controllers/models/views
		$this->globalData = array_merge ($this->globalData, [
			"isGlobalAdmin"   => $this->isGlobalAdmin,
			"isLocalAdmin"    => $this->isLocalAdmin,
			"isSystemUser"    => $this->isSystemUser,
			"SYSTEMTYPE"      => SYSTEMTYPE,
			"vabsClientId"    => $this->vabsClientId,
			"vabsClientName"  => $this->vabsClientName,
			"vabsUserId"      => $this->vabsUserId,
			"vabsUserName"    => $this->vabsUserName,
			"vabsUserEmail"   => $this->vabsUserEmail,
			"docRoot"         => $this->docRoot,
			"navigationArray" => $this->navigationArray,
			"headerArray"     => $this->headerArray,
			"RIGHTS"          => $this->RIGHTS,
		]);

	}

	/**
	 * @param string $className
	 *
	 * @return void
	 * @throws ValidationException|PermissionException
	 */
	private static function CheckLogin (): void {

		if (!Session::IsLoggedIn ()) {

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				throw new Exception('Du bist nicht mehr angemeldet. Wahrscheinlich hast du mehrere Seiten offen und auf einer bist du schon abgemeldet. Bitte melde Dich <a class="btn btn-xs blue" href="/Admin">hier</a> an!');
			}

			header ('Location: /Login');
			exit();

		}

	}

}
