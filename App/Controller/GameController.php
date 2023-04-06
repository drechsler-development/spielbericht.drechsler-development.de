<?php

namespace App\Controller;

use App\Error;
use App\Model\Game;
use App\Model\Group;
use App\Model\Team;
use App\ResponseArray;
use App\View;
use DD\Exceptions\PermissionException;
use Exception;

class GameController extends BackendController
{

	public function IndexAction (): void {

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

		View::RenderTemplate ('Game/index.twig', array_merge ($data, $this->globalData));

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
