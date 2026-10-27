<?php

namespace App\Core\Controller;

use App\Core\ResponseArray;
use App\Model\CostSteps\Company;
use App\Model\Countries\Country;
use App\Model\Helper\Session;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class SessionController extends BaseController {

	public function __construct () {

	}

	public function SetLanguageAction (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {

		$this->ResponseArray = new ResponseArray();

		try {

			$lang = $request->getParsedBody ()['lang'] ?? '';
			Session::SetLanguage(strtolower($lang));

			$Country = new Country();
			$languageId = $Country->LoadByLanguage ($lang);

			$Company = new Company();
			$Company->id = Session::GetCompanyId ();
			$Company->Update (['languageId' => $languageId]);


		} catch (Exception $e) {
			$this->ResponseArray->error = $e->getMessage ();
		}

		$response->getBody ()->write ($this->ResponseArray->ReturnOutput (true));

		return $response->withHeader ('Content-Type', 'application/json')->withStatus (201);

	}

}
