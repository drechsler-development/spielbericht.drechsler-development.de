<?php

namespace App\Controller;

use App\Core\Controller\BackendController;
use App\Core\ErrorHandler;
use App\Core\View;
use App\Error;
use App\Model\Group;
use App\Model\Player;
use App\Model\Team;
use DD\Exceptions\ValidationException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class TeamController extends BackendController {

	/**
	 * @param Request  $request
	 * @param Response $response
	 *
	 * @return Response
	 * @throws LoaderError
	 * @throws RuntimeError
	 * @throws SyntaxError
	 */
	public function IndexAction (Request $request, Response $response): Response {

		try {

			$Team  = new Team();
			$teams = $Team->Load ();

			$data = [
				'PAGE_TITLE'       => 'Teamübersicht',
				'PAGE_DESCRIPTION' => '',
				'rows'             => $teams,
			];

			$body = \App\Core\View::RenderTemplate ('Teams/index.twig', $data);

		} catch (Exception $e) {

			$body = \App\Core\View::RenderTemplate ('Admin/Error/500.twig', array_merge ($this->globalData, ['errorMessage' => ErrorHandler::HandleErrorMessage ($e)]));

		}

		$response->getBody ()->write ($body);

		return $response;

	}

	/**
	 * Route-Beispiel:
	 * $app->get('/Team/Load/{id:\d+}', [TeamController::class, 'LoadAction']);
	 *
	 * @param Request  $request
	 * @param Response $response
	 * @param array    $args
	 *
	 * @return Response
	 */
	public function LoadAction (Request $request, Response $response, array $args = []): Response {

		try {

			$Team     = new Team();
			$Team->id = $args['id'] ?? 0;

			$teams = $Team->Load ();

			$this->ResponseArray->AddData ([
				                               'Teams' => $teams,
			                               ]);

		} catch (Exception $e) {
			$this->ResponseArray->error = $e->getMessage ();
		}

		$response->getBody ()->write ($this->ResponseArray->ReturnOutput (true));

		return $response->withHeader ('Content-Type', 'application/json')->withStatus (201);

	}

	/**
	 * @param Request  $request
	 * @param Response $response
	 *
	 * @return Response
	 */
	public function SaveAction (Request $request, Response $response): Response {

		try {

			$post = $request->getParsedBody () ?? [];

			$id                 = $post['id'] ?? 0;
			$teamName           = $post['teamName'] ?? '';
			$address            = $post['address'] ?? '';
			$additionalAddress  = $post['additionalAddress'] ?? '';
			$postCode           = $post['postCode'] ?? '';
			$city               = $post['city'] ?? '';
			$day                = $post['day'] ?? '';
			$startTime          = $post['startTime'] ?? '';
			$endTime            = $post['endTime'] ?? '';
			$teamLeadName       = $post['teamLeadName'] ?? '';
			$teamLeadEmail      = $post['teamLeadEmail'] ?? '';
			$teamLeadTelephone  = $post['teamLeadTelephone'] ?? '';
			$teamLeadName2      = $post['teamLeadName2'] ?? '';
			$teamLeadEmail2     = $post['teamLeadEmail2'] ?? '';
			$teamLeadTelephone2 = $post['teamLeadTelephone2'] ?? '';

			$teamLeadTelephone  = str_replace ([' ', '0049'], ['', '+49'], $teamLeadTelephone);
			$teamLeadTelephone2 = str_replace ([' ', '0049'], ['', '+49'], $teamLeadTelephone2);


			if (empty($teamName)) {
				throw new ValidationException('Keinen Teamnamen übergeben');
			}

			if (empty($address)) {
				throw new ValidationException('Keine Adresse übergeben');
			}

			if (empty($postCode)) {
				throw new ValidationException('Keine PLZ übergeben');
			}

			if (empty($city)) {
				throw new ValidationException('Keine Stadt übergeben');
			}

			if (empty($day)) {
				throw new ValidationException('Keinen Trainingstag übergeben');
			}

			if (empty($startTime)) {
				throw new ValidationException('Keine Startzeit übergeben');
			}

			if (empty($endTime)) {
				throw new ValidationException('Keine Endzeit übergeben');
			}

			if (empty($teamLeadName)) {
				throw new ValidationException('Keinen Ansprechpartner übergeben');
			}

			if (empty($teamLeadEmail) && empty($teamLeadTelephone)) {
				throw new ValidationException('Bitte E-Mail oder Telefonnummer für Ansprechpartner 1 angeben');
			}

			if (!empty($teamLeadName2)) {
				if (empty($teamLeadEmail2) && empty($teamLeadTelephone2)) {
					throw new ValidationException('Bitte E-Mail oder Telefonnummer für Ansprechpartner 2 angeben');
				}
			}

			if (!empty($teamLeadEmail)) {
				if (!filter_var ($teamLeadEmail, FILTER_VALIDATE_EMAIL)) {
					throw new ValidationException('Keine gültige E-Mail für Ansprechpartner 1 übergeben');
				}
			}

			if (!empty($teamLeadEmail2)) {
				if (!filter_var ($teamLeadEmail2, FILTER_VALIDATE_EMAIL)) {
					throw new ValidationException('Keine gültige E-Mail für Ansprechpartner 2 übergeben');
				}
			}

			$Team     = new Team();
			$Team->id = $id;

			$array = [
				'teamName'           => $teamName,
				'address'            => $address,
				'additionalAddress'  => $additionalAddress,
				'postCode'           => $postCode,
				'city'               => $city,
				'day'                => $day,
				'startTime'          => $startTime,
				'endTime'            => $endTime,
				'teamLeadName'       => $teamLeadName,
				'teamLeadEmail'      => $teamLeadEmail,
				'teamLeadTelephone'  => $teamLeadTelephone,
				'teamLeadName2'      => $teamLeadName2,
				'teamLeadEmail2'     => $teamLeadEmail2,
				'teamLeadTelephone2' => $teamLeadTelephone2,
			];

			$Team->Update ($array);

			$this->ResponseArray->success = true;


		} catch (Exception $e) {
			$this->ResponseArray->error = Error::HandleErrorMessage ($e);
		}

		$response->getBody ()->write ($this->ResponseArray->ReturnOutput (true));

		return $response->withHeader ('Content-Type', 'application/json')->withStatus (201);

	}

	/**
	 * @param Request  $request
	 * @param Response $response
	 *
	 * @return Response
	 */
	public function LoadSingleAction (Request $request, Response $response): Response {


		$post = $request->getParsedBody () ?? [];

		$id   = $post['id'] ?? 0;
		$json = !empty($post['json']);

		try {

			if (empty($id)) {
				throw new ValidationException('Team ID is empty');
			}

			$data = [];

			if (!$json) {
				$Group          = new Group();
				$groups         = $Group->Load ();
				$data['groups'] = $groups;
			}

			$Team     = new Team();
			$Team->id = $id;

			$teams = $Team->Load ();
			$data  = $teams[0] ?? [];

			if ($json) {

				$this->ResponseArray->AddData ([
					                               'row' => $data,
				                               ]);

				$response->getBody ()->write ($this->ResponseArray->ReturnOutput (true));

				return $response->withHeader ('Content-Type', 'application/json')->withStatus (201);
			}

			$this->ResponseArray->data = View::RenderTemplate ('Teams/index.twig', array_merge ($data, $this->globalData));

		} catch (Exception $e) {
			$this->ResponseArray->error = $e->getMessage ();
		}

		$response->getBody ()->write ($this->ResponseArray->ReturnOutput (true));

		return $response->withHeader ('Content-Type', 'application/json')->withStatus (201);

	}


	/**
	 * @param Request  $request
	 * @param Response $response
	 *
	 * @return Response
	 */
	public function UpdatePlayersAction (Request $request, Response $response): Response {


		$post = $request->getParsedBody () ?? [];

		try {

			$players = [];

			for ($i = 0; $i < MAX_PLAYER_AMOUNT; $i++) {

				$playerName   = 'p' . $i . 'Name';
				$playerNumber = 'p' . $i . 'Number';

				if (!empty($post[$playerName])) {

					$players[] = [
						'name'   => $post[$playerName],
						'number' => $post[$playerNumber] ?? '',
					];

				}

			}

			$teamId = $_SESSION['login']['teamId'] ?? 0;

			if (empty($teamId)) {
				throw new ValidationException('Keine Team-ID in der Session gefunden');
			}

			$Team     = new Team();
			$Team->id = $teamId;

			foreach ($players as $player) {

				$Player         = new Player();
				$Player->name   = $player['name'];
				$Player->number = $player['number'];

				$Team->AddPlayer ($Player);

			}

			$Team->UpdatePlayers ();

			$this->ResponseArray->success = true;

		} catch (Exception $e) {
			$this->ResponseArray->error = $e->getMessage ();
		}

		$response->getBody ()->write ($this->ResponseArray->ReturnOutput (true));

		return $response->withHeader ('Content-Type', 'application/json')->withStatus (201);

	}

}
