<?php

session_start ();

use Dompdf\Dompdf;
use Dompdf\Options;

require 'vendor/autoload.php';
require 'config.php';

if(!empty($_POST)){

	$responseArray['error'] = '';

	$locationName = $_POST['locationName'] ?? '';
	$groupName    = $_POST['groupName'] ?? '';
	$date         = $_POST['date'] ?? '';
	$startTime    = $_POST['startTime'] ?? '';
	$endTime      = $_POST['endTime'] ?? '';
	$t1Name       = $_POST['t1Name'] ?? '';
	$t2Name       = $_POST['t2Name'] ?? '';

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

	//Hold Data in the session to use it in the get request later on

	$_SESSION['locationName'] = $locationName;
	$_SESSION['groupName']    = $groupName;
	$_SESSION['date']         = $date;
	$_SESSION['startTime']    = $startTime;
	$_SESSION['endTime']      = $endTime;
	$_SESSION['t1Name']       = $t1Name;
	$_SESSION['t2Name']       = $t2Name;
	$_SESSION['playersTeam1'] = $playersTeam1;
	$_SESSION['playersTeam2'] = $playersTeam2;

	//If all went well, we can send back a proper JSON answer and a redirect link to call the GET Request from the original script as a redirect (window.location)
	echo json_encode ($responseArray);

}else{
	$locationName = $_SESSION['locationName'] ?? '';
	$groupName    = $_SESSION['groupName'] ?? '';
	$date         = $_SESSION['date'] ?? '';
	$startTime    = $_SESSION['startTime'] ?? '';
	$endTime      = $_SESSION['endTime'] ?? '';
	$t1Name       = $_SESSION['t1Name'] ?? '';
	$t2Name       = $_SESSION['t2Name'] ?? '';
	$playersTeam1 = $_SESSION['playersTeam1'] ?? '';
	$playersTeam2 = $_SESSION['playersTeam2'] ?? '';

	// instantiate and use the dompdf class
	$options = new Options();
	$options->set ('isRemoteEnabled', true);

	$dompdf     = new Dompdf($options);
	$pattern    = "/[^a-zA-Z0-9_\-]/";
	$t1FileName = preg_replace ($pattern, "_", $t1Name);
	$t2FileName = preg_replace ($pattern, "_", $t2Name);
	$data       = file_get_contents ('spielbericht.jpg');
	$base64     = 'data:image/jpeg;base64,'.base64_encode ($data);

	ob_start ();
	?>
	<!DOCTYPE html>
	<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<style>
			@page {
				size: A4 landscape;
				margin: 0.5cm 0 0;
				padding: 0;
			}

			body {
				font-family: helvetica !important;
				font-size: 10pt;
				position: relative;
				background-image: url(<?php echo $base64; ?>);
				background-position: top left;
				background-repeat: no-repeat;
				background-size: 100% 100%;
			}

			.number {
				position: absolute;
				left: 820px;
				width: 25px;
				/*background-color: blue;*/
				text-align: right;
			}

			.player {
				position: absolute;
				left: 857px;
				width: 235px;
				/*background-color: grey;*/
			}

			.grey {
				/*background-color: grey;*/
			}

		</style>
	</head>
	<body>
	<?php

	?>

	<div class="grey" style="position: absolute; top: 50px; left: 410px; width: 250px;"><?php echo $locationName; ?></div>
	<div class="grey" style="position: absolute; top: 80px; left: 45px;"><?php echo "Staffel ".$groupName; ?></div>
	<div class="grey" style="position: absolute; top: 80px; left: 410px; width: 100px;"><?php echo $date; ?></div>
	<div class="grey" style="position: absolute; top: 80px; left: 560px; width: 100px;"><?php echo $startTime; ?></div>
	<div class="grey" style="position: absolute; top: 80px; left: 710px; width: 100px;"><?php echo $endTime; ?></div>

	<div class="grey" style="position: absolute; top: 127px; left: 95px; width: 250px;"><b><?php echo $t1Name; ?></b></div>
	<div class="grey" style="position: absolute; top: 127px; left: 825px; width: 250px;"><b><?php echo $t1Name; ?></b></div>
	<div class="grey" style="position: absolute; top: 127px; left: 540px; width: 250px;"><b><?php echo $t2Name; ?></b></div>
	<div class="grey" style="position: absolute; top: 450px; left: 825px; width: 250px;"><b><?php echo $t2Name; ?></b></div>
	<!-- PLAYERS -->
	<?php
	$startYTeam1 = 169;
	$startYTeam2 = 493;
	$gap         = 22.5;

	$y = $startYTeam1;
	foreach ($playersTeam1 as $player) {
		?>
		<div class="number" style="top: <?php echo $y; ?>px;"><?php echo $player['number']; ?></div>
		<div class="player" style="top: <?php echo $y; ?>px;"><?php echo $player['name']; ?></div>
		<?php
		$y += $gap;
	}

	$y = $startYTeam2;
	foreach ($playersTeam2 as $player) {
		?>
		<div class="number" style="top: <?php echo $y; ?>px;"><?php echo $player['number']; ?></div>
		<div class="player" style="top: <?php echo $y; ?>px;"><?php echo $player['name']; ?></div>
		<?php
		$y += $gap;
	}
	?>
	</body>
	</html>
	<?php
	$content = ob_get_contents ();
	ob_end_clean ();
	$dompdf->loadHtml ($content);

	//echo $content;

	// (Optional) Setup the paper size and orientation
	$dompdf->setPaper ('A4', 'landscape');

	// Render the HTML as PDF
	$dompdf->render ();

	// Output the generated PDF to Browser
	$dompdf->stream ();
}




