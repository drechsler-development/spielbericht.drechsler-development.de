<?php

namespace App\Core\Controller;

use App\Model\Admin\CustomerNavigation;
use App\Model\Contact\Contact;
use App\Model\CostSteps\Company;
use App\Model\Customer\Login;
use App\Model\Exceptions\LoginException;
use App\Model\Helper\Session;
use App\Model\Security\Right;
use DD\Exceptions\PermissionException;
use DD\Exceptions\ValidationException;
use Exception;

/**
 * Base controller
 */
abstract class ContactBackendController extends BaseController {

	/**
	 * @var int|mixed
	 */
	public float     $standardTaxRate = 0;
	protected array  $RIGHTS          = [];
	protected bool   $isLocalAdmin    = false;
	protected int    $companyId;
	protected string $companyName;
	protected string $salonKeeSalonId = '';
	protected int    $clientId;
	protected int    $contactId;
	protected string $contactName;
	protected string $contactEmail;
	public string    $language;

	/**
	 * @throws PermissionException|LoginException|Exception
	 */
	public function __construct (bool $byepassRightCheck = false) {

		try {

			parent::__construct ();

			//Check if we have a login via url parameter
			$contactId = $_GET['contactId'] ?? null;
			$clientId  = $_GET['clientId'] ?? null;

			if (!empty($clientId) && !empty($contactId)) {
				$this->ApplyLoginViaUrl ($clientId, $contactId);
			}

			//Checks if a user is logged in otherwise redirect to the login page
			self::CheckLogin ();

			$this->clientId        = Session::GetContactClientId ();
			$this->salonKeeSalonId = Session::GetSalonKeeSalonId ();
			$this->companyId       = Session::GetContactCompanyId ();
			$this->standardTaxRate = Session::GetStandardTaxRate ();

			$this->navigationArray = CustomerNavigation::GetNavigation ();
			$this->headerArray     = CustomerNavigation::GetHeader ();

			//Assign some client/user data to variables
			$this->companyName  = Session::GetCustomerName ();
			$this->contactId    = Session::GetContactId ();
			$this->contactName  = Session::GetContactFirstName () . " " . Session::GetContactLastName ();
			$this->contactEmail = Session::GetContactEmail ();
			$this->isLocalAdmin = Session::IsLocalAdmin ();

			//Check, if we have a videoLink
			foreach ($this->navigationArray as $nav) {

				if ($nav['bVideoLink'] != '' && $nav['bVideoDate'] != '') {

					if (str_contains (strtolower ($_SERVER['REQUEST_URI']), strtolower ($nav['bFolder']))) {
						$this->globalData['VIDEO_LINK'] = $nav['bVideoLink'];
						break;
					}
				}
			}

			$rightsArray = Right::GetBackendRights ($_SERVER['QUERY_STRING'], Session::GetContactId ());

			$this->CheckRights ($rightsArray, $byepassRightCheck);

			if (!empty($this->companyId)) {

				$Company             = new Company();
				$Company->id         = $this->companyId;
				$Company->CONTACT_ID = $this->contactId;
				$row                 = $Company->Load ()[0] ?? [];
				$currencyIcon        = $row['currencyIcon'] ?? '';
				$currencyName        = $row['currencyName'] ?? '';
				$salonKeeSalonId     = $row['salonKeeSalonId'] ?? 0;

			}

			$logo = Session::GetCompanyLogo () ?? '';

			//Put some data into the global data to have easier access in other controllers/models/views
			$this->globalData = array_merge ($this->globalData, [
				"SYSTEMTYPE"      => SYSTEMTYPE,
				"companyId"       => $this->companyId,
				"companyName"     => $this->companyName,
				"contactId"       => $this->contactId,
				"userName"        => $this->contactName,
				"userEmail"       => $this->contactEmail,
				"docRoot"         => $this->docRoot,
				"standardTaxRate" => $this->standardTaxRate,
				"user"            => $rightsArray,
				"navigationArray" => $this->navigationArray,
				"headerArray"     => $this->headerArray,
				"RIGHTS"          => $rightsArray,
				"CURRENCY_NAME"   => $currencyName ?? '',
				"CURRENCY_ICON"   => $currencyIcon ?? '',
				"TAX"             => $tax ?? '',
				"CONTACT_AREA"    => 1,
				"LOGO"            => $logo ? LOGOS_WEB . '/' . $logo : '',
				"cssDesign"       => Session::GetCSSDesign (),
				"viewMode"        => Session::GetCSSViewMode (),
				"salonKeeSalonId" => $salonKeeSalonId ?? 0,
			]);

			//This call needs to be after the globalData is set as we will ADD a new key to the globalData
			$this->GetLanguage ($this->companyId);
			$this->GetCountryTranslations ($this->companyId);

			$this->RIGHTS = $rightsArray;

		} catch (PermissionException|LoginException|Exception $e) {
			throw $e;
		}

	}

	#region PRIVATE

	/**
	 * @param int   $clientId
	 * @param mixed $contactId
	 *
	 * @return void
	 * @throws ValidationException
	 */
	private function ApplyLoginViaUrl (int $clientId, int $contactId): void {

		if (empty($clientId)) {
			throw new ValidationException("No clientId given");
		}

		if (empty($contactId)) {
			throw new ValidationException("No contactId given");
		}

		$this->contactId = $contactId;
		$this->clientId  = $clientId;

		$Company     = new Company();
		$Company->id = $this->companyId;
		$company     = $Company->Load ()[0] ?? [];

		$Contact     = new Contact($clientId);
		$Contact->id = $contactId;
		$contact     = $Contact->Load ()[0] ?? [];

		$array = [
			'id'          => $this->contactId,
			'clientId'    => $this->clientId,
			'firstName'   => $contact['firstName'] ?? '',
			'lastName'    => $contact['lastName'] ?? '',
			'department'  => $contact['department'] ?? '',
			'telephone'   => $contact['telephone'] ?? '',
			'fax'         => $contact['fax'] ?? '',
			'email'       => $contact['email'] ?? '',
			'localAdmin'  => $contact['localAdmin'] ?? 0,
			'spoofing'    => $contact['spoofing'] ?? 0,
			'bild'        => $contact['picture'] ?? '',
			'company'     => $company['companyName'],
			'companyName' => $company['companyName'],
		];

		Session::SetContactSession ($array);

	}

	/**
	 * @return void
	 * @throws LoginException
	 */
	static function CheckLogin (): void {

		//Check Request Method and if GET Show message below otherwise respond with an Exception Message
		if (!Login::CheckLogin ()) {
			if($_SERVER['REQUEST_METHOD'] == 'GET'){
				header ("Location: /Login");
				exit;
			} else {
				throw new LoginException("Sie sind nicht mehr angemeldet oder wurden ausgeloggt. Bitte Loggen Sie sich wieder ein. <a href='/Login'>Zum Login</a>");
			}
		}

	}

	#endregion
}
