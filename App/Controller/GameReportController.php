<?php

namespace App\Controller;

use App\Model\GameReport;
use App\Model\Player;
use App\Model\Team;
use App\View;
use DD\Exceptions\ValidationException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class GameReportController extends BackendController
{

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function IndexAction(): void
    {

        //Get own team id based on logged in user
        $teamId = $_SESSION['login']['teamId'] ?? 0;

        $Team = new Team();
        $targetTeams = $Team->Load();
        $Team->id = $teamId;
        $ownTeams = $Team->Load();

        $ownPlayers = [];
        $Players = new Player();
        $Players->teamId = $teamId;
        $ownPlayers = $Players->Load();

        //print_r ($ownPlayers);

        $data = [
            /*'navigationArray'  => $this->navigationArray,
            'headerArray'      => $this->headerArray,*/
            'PAGE_TITLE' => 'Spielberichtsgenerator',
            'PAGE_DESCRIPTION' => 'Zack Peng -> Fertig isser!',
            'targetTeams' => $targetTeams,
            'ownTeam' => $ownTeams[0] ?? [],
            'ownPlayers' => $ownPlayers ?? [],
        ];

        View::RenderTemplate('GameReport/index.twig', array_merge($data, $this->globalData));

    }

    /**
     * @throws ValidationException
     */
    public function LoadPlayersAction(): void
    {

        $responseArray['error'] = '';

        $teamId = $this->params['id'] ?? 0;

        $Team = new GameReport();
        $Team->id = $teamId;
        $team = $Team->Load();

        $responseArray['data'] = $team;
        echo json_encode($responseArray);

    }

    public function CreateReportAction(): void
    {

        $responseArray['error'] = '';
        $playersTeam1 = [];
        $playersTeam2 = [];

        //Check if previw is enabled
        $_SESSION['report']['preview'] = !empty($_POST['preview']);

        //Put all data into a session
        $_SESSION['report']['address'] = $_POST['address'] ?? '';
        $_SESSION['report']['additionalAddress'] = $_POST['additionalAddress'] ?? '';
        $_SESSION['report']['groupName'] = $_POST['groupName'] ?? '';
        $_SESSION['report']['date'] = $_POST['date'] ?? '';
        $_SESSION['report']['startTime'] = $_POST['startTime'] ?? '';
        $_SESSION['report']['endTime'] = $_POST['endTime'] ?? '';
        $_SESSION['report']['t1Name'] = $_POST['t1Name'] ?? '';
        $_SESSION['report']['t2Name'] = $_POST['t2Name'] ?? '';

        //Building Team Player Arrays
        for ($i = 0; $i < MAX_PLAYER_AMOUNT; $i++) {
            //Team 1
            $playerName = 'p' . $i . 'Name';
            $playerNumber = 'p' . $i . 'Number';
            $singlePlayerArray = [];
            if (!empty($_POST[$playerName])) {
                $singlePlayerArray['name'] = $_POST[$playerName];
                $singlePlayerArray['number'] = !empty($_POST[$playerNumber]) ? $_POST[$playerNumber] : '';
                $playersTeam1[] = $singlePlayerArray;
            }
            //Team 2
            $playerName = 't2p' . $i . 'Name';
            $playerNumber = 't2p' . $i . 'Number';
            $singlePlayerArray = [];
            if (!empty($_POST[$playerName])) {
                $singlePlayerArray['name'] = $_POST[$playerName];
                $singlePlayerArray['number'] = !empty($_POST[$playerNumber]) ? $_POST[$playerNumber] : '';
                $playersTeam2[] = $singlePlayerArray;
            }
        }

        $_SESSION['report']['playersTeam1'] = $playersTeam1;
        $_SESSION['report']['playersTeam2'] = $playersTeam2;

        echo json_encode($responseArray);

    }

}
