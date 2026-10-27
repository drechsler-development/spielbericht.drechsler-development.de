<?php

namespace App\Controller;

use App\Core\Controller\BackendController;
use App\Core\ErrorHandler;
use App\Core\View;
use App\Model\GameReport;
use App\Model\Player;
use App\Model\Team;
use DD\Exceptions\ValidationException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class GameReportController extends BackendController {

	public function __construct () {
		parent::__construct ();
	}

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

			$teamId = $_SESSION['login']['teamId'] ?? 0;

			$Team        = new Team();
			$targetTeams = $Team->Load ();

			$Team->id = $teamId;
			$ownTeams = $Team->Load ();

			$Players         = new Player();
			$Players->teamId = $teamId;
			$ownPlayers      = $Players->Load ();

			$data = [
				'PAGE_TITLE'       => 'Spielberichtsgenerator',
				'PAGE_DESCRIPTION' => 'Zack Peng -> Fertig isser!',
				'targetTeams'      => $targetTeams,
				'ownTeam'          => $ownTeams[0] ?? [],
				'ownPlayers'       => $ownPlayers,
			];

			$body = View::RenderTemplate ('GameReport/index.twig', $data);

		} catch (Exception $e) {

			$body = View::RenderTemplate ('Admin/Error/500.twig', array_merge ($this->globalData, ['errorMessage' => ErrorHandler::HandleErrorMessage ($e)]));

		}

		$response->getBody ()->write ($body);

		return $response;

	}

	/**
	 * Route:
	 * $app->get('/GameReport/LoadPlayers/{id:\d+}', [GameReportController::class, 'LoadPlayersAction']);
	 *
	 * @param Request  $request
	 * @param Response $response
	 * @param array    $args
	 *
	 * @return Response
	 */
	public function LoadPlayersAction (Request $request, Response $response, array $args = []): Response {

		$data = [];

		try {

			$teamId = $args['id'] ?? 0;

			if (empty($teamId)) {
				throw new ValidationException('Keine Team-ID übergeben');
			}

			$GameReport     = new GameReport();
			$GameReport->id = $teamId;

			$this->ResponseArray->data = $GameReport->Load () ?? [];

		} catch (Exception $e) {
			$this->ResponseArray->error = $e->getMessage ();
		}

		$response->getBody ()->write ($this->ResponseArray->ReturnOutput (true));

		return $response->withHeader ('Content-Type', 'application/json')->withStatus (201);

	}

	/**
	 * Route:
	 * $app->post('/GameReport/CreateReport', [GameReportController::class, 'CreateReportAction']);
	 *
	 * @param Request  $request
	 * @param Response $response
	 *
	 * @return Response
	 */
	public function CreateReportAction (Request $request, Response $response): Response {

		$post = $request->getParsedBody () ?? [];

		try {

			$playersTeam1 = [];
			$playersTeam2 = [];

			$_SESSION['report']['preview'] = !empty($post['preview']);

			$_SESSION['report']['address']           = $post['address'] ?? '';
			$_SESSION['report']['additionalAddress'] = $post['additionalAddress'] ?? '';
			$_SESSION['report']['groupName']         = $post['groupName'] ?? '';
			$_SESSION['report']['date']              = $post['date'] ?? '';
			$_SESSION['report']['startTime']         = $post['startTime'] ?? '';
			$_SESSION['report']['endTime']           = $post['endTime'] ?? '';
			$_SESSION['report']['t1Name']            = $post['t1Name'] ?? '';
			$_SESSION['report']['t2Name']            = $post['t2Name'] ?? '';

			for ($i = 0; $i < MAX_PLAYER_AMOUNT; $i++) {

				$playerName   = 'p' . $i . 'Name';
				$playerNumber = 'p' . $i . 'Number';

				if (!empty($post[$playerName])) {

					$playersTeam1[] = [
						'name'   => $post[$playerName],
						'number' => $post[$playerNumber] ?? '',
					];

				}

				$playerName   = 't2p' . $i . 'Name';
				$playerNumber = 't2p' . $i . 'Number';

				if (!empty($post[$playerName])) {

					$playersTeam2[] = [
						'name'   => $post[$playerName],
						'number' => $post[$playerNumber] ?? '',
					];

				}

			}

			$_SESSION['report']['playersTeam1'] = $playersTeam1;
			$_SESSION['report']['playersTeam2'] = $playersTeam2;

		} catch (Exception $e) {
			$this->ResponseArray->error = $e->getMessage ();
		}

		$response->getBody ()->write ($this->ResponseArray->ReturnOutput (true));

		return $response->withHeader ('Content-Type', 'application/json')->withStatus (201);

	}

}
