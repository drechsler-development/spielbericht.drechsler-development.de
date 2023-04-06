<?php

namespace App\Controller;

use App\Error;
use App\Model\Group;
use App\Model\Player;
use App\Model\Team;
use App\ResponseArray;
use App\View;
use DD\Exceptions\ValidationException;
use Exception;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class TeamController extends BackendController
{

	/**
	 * @return void
	 * @throws LoaderError
	 * @throws RuntimeError
	 * @throws SyntaxError
	 */
	public function IndexAction (): void {

		$Team  = new Team();
		$teams = $Team->Load ();

		$data = [
			/*'navigationArray'  => $this->navigationArray,
			'headerArray'      => $this->headerArray,*/
			'PAGE_TITLE'       => 'Teamübersicht',
			'PAGE_DESCRIPTION' => '',
			'rows'             => $teams,
		];

		View::RenderTemplate ('Teams/index.twig', array_merge ($data, $this->globalData));

	}

	public function LoadAction (): void {

		$ResponseArray = new ResponseArray();

		$Team     = new Team();
		$Team->id = $this->params['id'] ?? 0;
		$teams    = $Team->Load ();

		$ResponseArray->AddData (['Teams' => $teams]);

		$ResponseArray->ReturnOutput ();

	}

	public function SaveAction (): void {

		$ResponseArray = new ResponseArray();

		$id                 = $_POST['id'] ?? 0;
		$teamName           = $_POST['teamName'] ?? '';
		$address            = $_POST['address'] ?? '';
		$additionalAddress  = $_POST['additionalAddress'] ?? '';
		$postCode           = $_POST['postCode'] ?? '';
		$city               = $_POST['city'] ?? '';
		$day                = $_POST['day'] ?? '';
		$startTime          = $_POST['startTime'] ?? '';
		$endTime            = $_POST['endTime'] ?? '';
		$teamLeadName       = $_POST['teamLeadName'] ?? '';
		$teamLeadEmail      = $_POST['teamLeadEmail'] ?? '';
		$teamLeadTelephone  = $_POST['teamLeadTelephone'] ?? '';
		$teamLeadTelephone  = str_replace (' ', '', $teamLeadTelephone);
		$teamLeadTelephone  = str_replace ('0049', '+49', $teamLeadTelephone);
		$teamLeadName2      = $_POST['teamLeadName2'] ?? '';
		$teamLeadEmail2     = $_POST['teamLeadEmail2'] ?? '';
		$teamLeadTelephone2 = $_POST['teamLeadTelephone2'] ?? '';
		$teamLeadTelephone2 = str_replace (' ', '', $teamLeadTelephone2);
		$teamLeadTelephone2 = str_replace ('0049', '+49', $teamLeadTelephone2);

		try {

			if (empty($teamName)) {
				throw new ValidationException("Keinen Namen übergeben");
			}

			if (empty($address)) {
				throw new ValidationException("Keinen Namen übergeben");
			}

			if (empty($postCode)) {
				throw new ValidationException("Keinen Namen übergeben");
			}

			if (empty($city)) {
				throw new ValidationException("Keinen Namen übergeben");
			}

			if (empty($day)) {
				throw new ValidationException("Keinen Namen übergeben");
			}

			if (empty($startTime)) {
				throw new ValidationException("Keinen Namen übergeben");
			}

			if (empty($endTime)) {
				throw new ValidationException("Keinen Namen übergeben");
			}

			if (empty($teamLeadName)) {
				throw new ValidationException("Keinen Namen übergeben");
			}

			if (empty($teamLeadEmail) && empty($teamLeadTelephone)) {
				throw new ValidationException("Keinen Namen übergeben");
			}

			if (!empty($teamLeadName2)) {
				if (empty($teamLeadEmail2) && empty($teamLeadTelephone2)) {
					throw new ValidationException("Keinen Namen übergeben");
				}
			}

			if (!empty($teamLeadEmail)) {
				if (!filter_var ($teamLeadEmail, FILTER_VALIDATE_EMAIL)) {
					throw new ValidationException("Keine gültige Email übergeben für TeamLead1");
				}
			}

			if (!empty($teamLeadEmail2)) {
				if (!filter_var ($teamLeadEmail2, FILTER_VALIDATE_EMAIL)) {
					throw new ValidationException("Keine gültige Email übergeben für TeamLead2");
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

		} catch (Exception $e) {
			$ResponseArray->error = Error::HandleErrorMessage ($e);
		}

		$ResponseArray->ReturnOutput ();

	}

	public function LoadSingleAction (): void {

		$ResponseArray = new ResponseArray();

		$id   = $_POST['id'] ?? 0;
		$json = !empty($_POST['json']);

		try {

			if (empty($id)) {
				throw new ValidationException('Team ID is empty');
			}
			if (!$json) {
				$Group          = new Group();
				$groups         = $Group->Load ();
				$data['groups'] = $groups;

			}

			$Team        = new Team();
			$Team->id    = $id;
			$teams       = $Team->Load ();
			$data['row'] = $teams[0] ?? [];

			if (!$json) {
				ob_start ();
				View::RenderTemplate ('Teams/form.twig', array_merge ($data, $this->globalData));
				$data = ob_get_contents ();
				ob_end_clean ();
			} else {
				echo json_encode ($data);

				return;
			}

			$ResponseArray->data = $data;

		} catch (Exception $e) {
			$ResponseArray->error = Error::HandleErrorMessage ($e);
		}

		$ResponseArray->ReturnOutput ();

	}

	public function UpdatePlayersAction (): void {

		//Show Array with pre
		echo '<pre>';
		print_r ($_POST);
		echo '</pre>';

		$responseArray['error'] = '';

		//Building Team Player Arrays
		$players = [];
		for ($i = 0; $i < MAX_PLAYER_AMOUNT; $i++) {
			//Team 1
			$playerName   = 'p'.$i.'Name';
			$playerNumber = 'p'.$i.'Number';
			$playerArray  = [];
			if (!empty($_POST[$playerName])) {
				$playerArray['name']   = $_POST[$playerName];
				$playerArray['number'] = !empty($_POST[$playerNumber]) ? $_POST[$playerNumber] : '';
				$players[]             = $playerArray;
			}
		}

		$Team     = new Team();
		$Team->id = $_SESSION['login']['teamId'];
		foreach ($players as $player) {
			$Player         = new Player();
			$Player->name   = $player['name'];
			$Player->number = $player['number'];
			$Team->AddPlayer ($Player);
		}
		$Team->UpdatePlayers ();

		echo json_encode ($responseArray);

	}
}
