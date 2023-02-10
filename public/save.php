<?php

use App\Model\Player;
use App\Model\Team;

require __DIR__.'/../config.php';
$responseArray['error'] = '';

try {

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

} catch (Exception $e) {
	$responseArray['error'] = $e->getMessage ();
}

echo json_encode ($responseArray);
