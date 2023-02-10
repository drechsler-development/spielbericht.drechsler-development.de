<?php

namespace App\Controller;

use App\Model\Player;
use App\Model\Team;
use App\View;
use DD\Exceptions\ValidationException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class TeamController extends BackendController
{

	/**
	 * @throws SyntaxError
	 * @throws RuntimeError
	 * @throws LoaderError
	 */
	public function IndexAction (): void {

		$demo     = !empty($_GET['demo']);
		$demoData = [];

		if ($demo) {

			$playersTeam1 = [
				[
					'number' => '13',
					'name'   => 'Ronny Dahms-Haller',
				],
				[
					'number' => '12',
					'name'   => 'Fabian Christiansen',
				],
				[
					'number' => '11',
					'name'   => 'Chris Dahle',
				],
				[
					'number' => '10',
					'name'   => 'Gunnar Müller',
				],
				[
					'number' => '15',
					'name'   => 'Raimo Rubin',
				],
				[
					'number' => '09',
					'name'   => 'Alexander Meier',
				],
				[
					'number' => '98',
					'name'   => 'Felix Fehrmann (Libero)',
				],
				[
					'number' => '99',
					'name'   => 'Oliver Haufe (Libero)',
				],
				[
					'number' => '20',
					'name'   => 'Ansgar Gottschalk',
				]
			];
			$playersTeam2 = [
				[
					'number' => '14',
					'name'   => 'Klaus Klausen',
				],
				[
					'number' => '13',
					'name'   => 'Fritz Wepper',
				],
				[
					'number' => '12',
					'name'   => 'Arnold Schwarzenegger',
				],
				[
					'number' => '11',
					'name'   => 'Bruce Willis',
				],
				[
					'number' => '16',
					'name'   => 'Gerd Fröbe',
				],
				[
					'number' => '10',
					'name'   => 'Gottfried John',
				],
				[
					'number' => '97',
					'name'   => 'Axel Prahl',
				],
				[
					'number' => '96',
					'name'   => 'Dirk Bach',
				],
				[
					'number' => '21',
					'name'   => 'Mirco Nontschew',
				]
			];

			$demoData['demo']['playersTeam1'] = $playersTeam1;
			$demoData['demo']['playersTeam2'] = $playersTeam2;

			$demoData['demo']['address']   = 'Martin Luther Gymnasium';
			$demoData['demo']['groupName'] = "A1";
			$demoData['demo']['date']      = date ("d.m.Y");
			$demoData['demo']['startTime'] = '20:15';
			$demoData['demo']['endTime']   = '22:15';
			$demoData['demo']['t1Name']    = 'Alles wird gut II';
			$demoData['demo']['t2Name']    = 'Bunte TV Mische';

		}

		$Team  = new Team();
		$teams = $Team->Load ();

		$data = [
			/*'navigationArray'  => $this->navigationArray,
			'headerArray'      => $this->headerArray,*/
			'PAGE_TITLE'       => 'Spielberichtsgenerator',
			'PAGE_DESCRIPTION' => 'Zack Peng -> Fertig isser!',
			'teams'            => $teams,
			'demo'             => $demoData,
		];

		View::RenderTemplate ('Spielbericht/index.twig', array_merge ($data/*, $this->globalData*/));

	}

	public function SaveAction () {

		$responseArray['error'] = '';

		$address   = $_POST['address'] ?? '';
		$groupName = $_POST['groupName'] ?? '';
		$date      = $_POST['date'] ?? '';
		$startTime = $_POST['startTime'] ?? '';
		$endTime   = $_POST['endTime'] ?? '';
		$t1Name    = $_POST['t1Name'] ?? '';
		$t2Name    = $_POST['t2Name'] ?? '';
		$save      = !empty($_POST['save']);

		//Building Team Player Arrays
		$playersTeam1 = [];
		$playersTeam2 = [];
		for ($i = 0; $i < MAX_PLAYER_AMOUNT; $i++) {
			//Team 1
			$playerName        = 't1p'.$i.'Name';
			$playerNumber      = 't1p'.$i.'Number';
			$singlePlayerArray = [];
			if (!empty($_POST[$playerName])) {
				$singlePlayerArray['name']   = $_POST[$playerName];
				$singlePlayerArray['number'] = !empty($_POST[$playerNumber]) ? $_POST[$playerNumber] : '';
				$playersTeam1[]              = $singlePlayerArray;
			}
			//Team 2
			$playerName        = 't2p'.$i.'Name';
			$playerNumber      = 't2p'.$i.'Number';
			$singlePlayerArray = [];
			if (!empty($_POST[$playerName])) {
				$singlePlayerArray['name']   = $_POST[$playerName];
				$singlePlayerArray['number'] = !empty($_POST[$playerNumber]) ? $_POST[$playerNumber] : '';
				$playersTeam2[]              = $singlePlayerArray;
			}
		}

		$Team            = new Team();
		$Team->address   = $address;
		$Team->groupName = $groupName;
		$Team->startTime = $startTime;
		$Team->endTime   = $endTime;
		$Team->name      = $t1Name;
		foreach ($playersTeam1 as $player) {
			$Player         = new Player();
			$Player->name   = $player['name'];
			$Player->number = $player['number'];
			$Team->AddPlayer ($Player);
		}
		$Team->Save ();

		echo json_encode ($responseArray);
	}

	/**
	 * @throws ValidationException
	 */
	public function LoadAction () {

		$responseArray['error'] = '';

		$Team                  = new Team();
		$Team->id              = $_POST['id'] ?? 0;
		$team                  = $Team->Load ();
		$responseArray['data'] = $team;
		echo json_encode ($responseArray);
	}

}
