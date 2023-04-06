<?php

session_start ();

use Dompdf\Dompdf;
use Dompdf\Options;

require __DIR__.'/../config.php';

if (!empty($_SESSION['report'])) {

	$responseArray['error'] = '';

	// instantiate and use the dompdf class
	$options = new Options();
	$options->set ('isRemoteEnabled', true);

	$dompdf     = new Dompdf($options);
	$pattern    = "/[^a-zA-Z0-9_\-]/";
	$t1FileName = preg_replace ($pattern, "_", $_SESSION['report']['t1Name']);
	$t2FileName = preg_replace ($pattern, "_", $_SESSION['report']['t2Name']);
	$data       = file_get_contents ('assets/img/spielbericht.jpg');
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

	<div class="grey" style="position: absolute; top: 50px; left: 410px; width: 250px;"><?php echo $_SESSION['report']['address']; ?></div>
	<div class="grey" style="position: absolute; top: 80px; left: 45px;"><?php echo "Staffel ".$_SESSION['report']['groupName']; ?></div>
	<div class="grey" style="position: absolute; top: 80px; left: 410px; width: 100px;"><?php echo $_SESSION['report']['date']; ?></div>
	<div class="grey" style="position: absolute; top: 80px; left: 560px; width: 100px;"><?php echo $_SESSION['report']['startTime']; ?></div>
	<div class="grey" style="position: absolute; top: 80px; left: 710px; width: 100px;"><?php echo $_SESSION['report']['endTime']; ?></div>

	<div class="grey" style="position: absolute; top: 127px; left: 95px; width: 250px;"><b><?php echo $_SESSION['report']['t1Name']; ?></b></div>
	<div class="grey" style="position: absolute; top: 127px; left: 825px; width: 250px;"><b><?php echo $_SESSION['report']['t1Name']; ?></b></div>
	<div class="grey" style="position: absolute; top: 127px; left: 540px; width: 250px;"><b><?php echo $_SESSION['report']['t2Name']; ?></b></div>
	<div class="grey" style="position: absolute; top: 450px; left: 825px; width: 250px;"><b><?php echo $_SESSION['report']['t2Name']; ?></b></div>
	<!-- PLAYERS -->
	<?php
	$startYTeam1 = 169;
	$startYTeam2 = 493;
	$gap         = 22.5;

	$y = $startYTeam1;
	foreach ($_SESSION['report']['playersTeam1'] as $player) {
		?>
		<div class="number" style="top: <?php echo $y; ?>px;"><?php echo $player['number']; ?></div>
		<div class="player" style="top: <?php echo $y; ?>px;"><?php echo $player['name']; ?></div>
		<?php
		$y += $gap;
	}

	$y = $startYTeam2;
	foreach ($_SESSION['report']['playersTeam2'] as $player) {
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

	if (!empty($_SESSION['report']['preview'])) {
		echo $content;
		exit;
	}

	$dompdf->loadHtml ($content);

	//echo $content;

	// (Optional) Setup the paper size and orientation
	$dompdf->setPaper ('A4', 'landscape');

	// Render the HTML as PDF
	$dompdf->render ();

	// Output the generated PDF to Browser
	$dompdf->stream ();
}




