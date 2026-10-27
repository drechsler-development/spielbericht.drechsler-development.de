<?php

namespace App\Core\Controller;

use App\Core\ResponseArray;
use App\Model\Session;
use DateTime;
use DD\Exceptions\PermissionException;
use DD\Exceptions\ValidationException;
use DD\SystemType;
use Exception;
use Monolog\ErrorHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Formatter\NormalizerFormatter;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Base controller
 */
abstract class BaseController {

	public ResponseArray $ResponseArray;
	public array         $navigationArray    = [];
	public array         $headerArray        = [];
	protected Logger     $Logger;
	protected array      $globalData         = [];
	protected string     $deploymentType     = '';
	protected string     $deploymentDate     = '';
	protected string     $codeVersion        = '';
	protected string     $docRoot;
	protected int        $httpResponseStatus = 201;

	/**
	 * BaseController constructor.
	 *
	 * @throws Exception
	 */
	public function __construct () {

		//DEFINE LOGGER and LOGGERLEVEL

		$this->CreateLogger ();
		$this->GetVersion ();

		$this->docRoot = $_SERVER['DOCUMENT_ROOT'];

		$this->globalData = [
			'CODE_VERSION'    => SYSTEMTYPE == SystemType::PROD ? $this->codeVersion : $this->codeVersion . '.' . rand (123456, 999999),
			/*'CODE_VERSION'    => $this->codeVersion,*/
			'DEPLOYMENT_DATE' => $this->deploymentDate,
			'DEPLOYMENT_TYPE' => $this->deploymentType,
			'COPYRIGHTYEAR'   => 2023,
			'SYSTEMNAME'      => SYSTEMNAME,
			"SYSTEMTYPE"      => SYSTEMTYPE,
			"navigationArray" => $this->navigationArray,
			"headerArray"     => $this->headerArray,
		];

		$this->ResponseArray = new ResponseArray();

		//Set the current session time to now(), to be able to check if the session is still valid
		Session::SetCurrentSessionTime (new DateTime('now'));

	}

	/**
	 * @throws Exception
	 */
	private function CreateLogger (): void {
		$formatter = new LineFormatter(LineFormatter::SIMPLE_FORMAT, NormalizerFormatter::SIMPLE_DATE);
		$formatter->includeStacktraces ();

		$this->Logger  = new Logger("LOGGER");
		$streamHandler = new StreamHandler(ROOTPATH . '/LOG/' . date ("Y-m-d") . '.log', Logger::DEBUG);
		$streamHandler->setFormatter ($formatter);
		$this->Logger->pushHandler ($streamHandler);
		$this->Logger->pushHandler (new FirePHPHandler());

		ErrorHandler::register ($this->Logger);
	}

	/**
	 * @throws Exception
	 */
	private function GetVersion (): void {

		$this->codeVersion    = '1.0.0';
		$this->deploymentType = '';
		$this->deploymentDate = '';

	}

	/**
	 * @param array $keys
	 *
	 * @return string
	 */
	public function Translate (array $keys): string {
		$translationArray = $this->globalData['translation'];
		$default          = 'n/a';  // Default value if translation is not found

		foreach ($keys as $key) {
			if (isset($translationArray[$key])) {
				$translationArray = $translationArray[$key];
			} else {
				return $default; // Return default value if key is not found
			}
		}

		return is_string ($translationArray) ? $translationArray : $default;
	}

	/**
	 * @throws PermissionException
	 */
	protected function CheckRights (array $rightsArray, bool $byepassRightCheck): void {
		if (empty($rightsArray) && !$byepassRightCheck) {
			throw new PermissionException("You have no rights to access this page");
		}
	}

	/**
	 * @param int $companyId
	 *
	 * @return void
	 * @throws ValidationException
	 */
	protected function GetLanguage (int $companyId = 0): void {

		if (defined ('STANDARD_LANGUAGE')) {
			$language = STANDARD_LANGUAGE;
		} else {
			$language = 'de';
		}

		if (!empty($companyId)) {

			$Company     = new Company();
			$Company->id = $companyId;
			$language    = $Company->Load ()[0]['languageCode'] ?? $language;

		} else if (isset($_SESSION['language'])) {
			$language = $_SESSION['language'];
		}

		$languageFile = TRANSLATION_FOLDER . "/$language.json";

		if (!file_exists ($languageFile)) {
			$language     = 'de';
			$languageFile = TRANSLATION_FOLDER . "/$language.json";
		}

		$this->globalData['translation'] = json_decode (file_get_contents ($languageFile), true);
		$this->globalData['language']    = $language;

	}

	/**
	 * @param int $companyId
	 *
	 * @return void
	 */
	protected function GetCountryTranslations (int $companyId = 0): void {

		if (defined ('STANDARD_COUNTRY_CODE')) {
			$countryCode = STANDARD_COUNTRY_CODE;
		} else {
			$countryCode = 'de';
		}

		if (!empty($companyId)) {

			$Company     = new Company();
			$Company->id = $companyId;
			$countryCode = $Company->Load ()[0]['countryCode'] ?? $countryCode;

		} else if (isset($_SESSION['countryCode'])) {
			$countryCode = $_SESSION['countryCode'];
		}
		$countryCode = strtolower ($countryCode);

		$countryCodeFile = TRANSLATION_FOLDER . "/country/$countryCode.json";

		if (!file_exists ($countryCodeFile)) {
			$countryCode     = 'de';
			$countryCodeFile = TRANSLATION_FOLDER . "/country/$countryCode.json";
		}

		$this->globalData['countryTranslation'] = json_decode (file_get_contents ($countryCodeFile), true);

	}

}
