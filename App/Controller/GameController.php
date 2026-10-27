<?php

namespace App\Controller;

use App\Core\Controller\BackendController;
use App\Core\ErrorHandler;
use App\Core\View;
use App\Error;
use App\Model\Game;
use App\Model\Group;
use App\Model\Team;
use App\ResponseArray;
use DD\Exceptions\PermissionException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GameController extends BackendController {

	public function IndexAction (Request $request, Response $response): Response {

		try {

			$Groups = new Group();
			$groups = $Groups->Load ();

			$Team  = new Team();
			$teams = $Team->Load ();

			$Game = new Game();
			$rows = $Game->Load ();

			//print_r ($ownPlayers);

			$data = [
				/*'navigationArray'  => $this->navigationArray,
				'headerArray'      => $this->headerArray,*/
				'PAGE_TITLE'       => 'Spielberichtsgenerator',
				'PAGE_DESCRIPTION' => 'Zack Peng -> Fertig isser!',
				'rows'             => $rows,
				'groups'           => $groups,
				'teams'            => $teams,
			];

			$body = View::RenderTemplate ('Game/index.twig', array_merge ($data, $this->globalData));

		} catch (Exception $e) {

			$body = View::RenderTemplate ('Admin/Error/500.twig', array_merge ($this->globalData, ['errorMessage' => ErrorHandler::HandleErrorMessage ($e)]));

		}

		$response->getBody ()->write ($body);

		return $response;

	}

	public function LoadLinesAction (): void {

		$ResponseArray = new ResponseArray();
		$data          = [];

		$filterTeamId  = $_POST['filterTeamId'] ?? 0;
		$filterGroupId = $_POST['filterGroupId'] ?? 0;

		try {

			$Game                = new Game();
			$Game->filterTeamId  = $filterTeamId;
			$Game->filterGroupId = $filterGroupId;
			$rows                = $Game->Load ();
			$data['rows']        = $rows;

			ob_start ();
			View::RenderTemplate ('Game/lines.twig', array_merge ($data, $this->globalData));
			$data = ob_get_contents ();
			ob_end_clean ();

			$ResponseArray->data = $data;

		} catch (PermissionException $e) {
			$ResponseArray->error = $e->getMessage ();
		} catch (Exception $e) {
			$ResponseArray->error = Error::HandleErrorMessage ($e);
		}

		$ResponseArray->ReturnOutput ();

	}

}
