<?php
session_start ();
require_once __DIR__.'/config.php';
$spielerNummer = "Nr.";
$spielerName = "Spieler-Name";

$demo = !empty($_GET['demo']);

if($demo){
	$team1Example = [
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
	$team2Example = [
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
	$_SESSION['demo']['playersTeam1'] = $team1Example;
	$_SESSION['demo']['playersTeam2'] = $team2Example;

	$_SESSION['demo']['locationName'] = 'Martin Luther Gymnasium';
	$_SESSION['demo']['groupName']    = "A1";
	$_SESSION['demo']['date']         = date("d.m.Y");
	$_SESSION['demo']['startTime']    = '20:15';
	$_SESSION['demo']['endTime']      = '22:15';
	$_SESSION['demo']['t1Name']       = 'Alles wird gut II';
	$_SESSION['demo']['t2Name']       = 'Bunte TV Mische';
}else{
	$_SESSION['demo'] = [];
}

?>
<!doctype html>
<html lang="de">
<head>
	<title>Spielbericht-Generator HobbyLiga Leipzig</title>
	<!-- jQuery -->
	<script src="assets/jquery/jQuery_3.6.3.js"></script>

	<!-- Less Lib -->
	<script src="//cdnjs.cloudflare.com/ajax/libs/less.js/2.5.1/less.min.js"></script>

	<!-- General Bootstrap -->
	<script src="assets/bootstrap/js/bootstrap.js"></script>
	<link rel="stylesheet" href="assets/bootstrap/css/bootstrap.css" />

	<!-- Bootstrap Datepicker -->
	<script src="assets/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
	<script src="assets/bootstrap-datepicker/locales/bootstrap-datepicker.de.min.js"></script>
	<link rel="stylesheet" href="assets/bootstrap-datepicker/css/bootstrap-datepicker.css" />

	<!-- Bootstrap Timepicker -->
	<!-- Requires Moment.js -->
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js"></script>
	<!-- Include Bootstrap DateTimePicker CDN -->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
	<style>
		.heim {
			background-color: #95b3c2;
			color: black;
		}

		.gast {
			background-color: #e3babe;
			color: black;
		}
	</style>
</head>
<body>
<div class="container">
	<div class="mt-3">
	<?php
	if($demo){
	?>
		<a href="/?normal=1" class="btn btn-success">Zur Normal-Version</a>
	<?php
	}else{
	?>
		<a href="/?demo=1" class="btn btn-success">Lade Demo-Version</a>
	<?php
	}
	?>
	<button type="button" id="btnDeleteSession" class="btn btn-danger">Lösche Daten</button>
	</div>
	<h3 class="mt-2">Spielbericht-Generator</h3>
	<form>
		<div class="form-row">
			<div class="form-group col-md-3">
				<label for="groupName">Staffelname:</label>
				<input type="text" class="form-control" id="groupName" placeholder="z.B A3 oder B5" value="<?php echo $_SESSION['demo']['groupName'] ? : ($_SESSION['groupName'] ? : ''); ?>">
			</div>
			<div class="form-group col-md-9">
				<label for="locationName">Spielstätte</label>
				<input type="text" class="form-control" id="locationName" placeholder="Erich-Kästner-Schule" value="<?php echo $_SESSION['demo']['locationName'] ? : ($_SESSION['locationName'] ? : ''); ?>">
			</div>
		</div>
		<div class="form-row">
			<div class="form-group col-md-3">
				<label for="date">Datum:</label>
				<input type="text" class="form-control" id="date" value="<?php echo $_SESSION['demo']['date'] ? : ($_SESSION['date'] ? : ''); ?>">
			</div>
			<div class="form-group col-md-3">
				<label for="startTime">Start (Zeit)</label>
				<input type="text" class="form-control" id="startTime" value="<?php echo $_SESSION['demo']['startTime'] ? : ($_SESSION['startTime'] ? : ''); ?>">
			</div>
			<div class="form-group col-md-3">
				<label for="endTime">Ende (Zeit)</label>
				<input type="text" class="form-control" id="endTime" value="<?php echo $_SESSION['demo']['endTime'] ? : ($_SESSION['endTime'] ? : ''); ?>">
			</div>
		</div>
		<div class="form-row">
			<!-- TEAM 1 NAME -->
			<h5>Team-Namen</h5>
		</div>
		<div class="form-row">
			<div class="form-group col-md-6">
				<label for="t1Name">Team (HEIM):</label>
				<input type="text" class="form-control heim" id="t1Name" placeholder="Name Team (Heim)" value="<?php echo $_SESSION['demo']['t1Name'] ? : ($_SESSION['t1Name'] ? : ''); ?>">
			</div>
			<div class="form-group col-md-6">
				<label for="t2Name">Team (GAST):</label>
				<input type="text" class="form-control gast" id="t2Name" placeholder="Name Team (Gast)" value="<?php echo $_SESSION['demo']['t2Name'] ? : ($_SESSION['t2Name'] ? : ''); ?>">
			</div>
		</div>
		<div class="form-row">
			<!-- TEAM 1 NAME -->
			<h3>Spieler-Namen</h3>
		</div>
		<div class="form-row">
			<div class="form-group col-md-6">
				<h5>Spieler (HEIM)</h5>
			</div>
			<div class="form-group col-md-6">
				<h5>Spieler (GAST)</h5>
			</div>
		</div>
		<?php
		for ($i = 0; $i < MAX_PLAYER_AMOUNT; $i++){
		?>
		<div class="form-row">
			<!-- TEAM 1 -->
			<!-- PLAYER 1 -->
			<div class="form-group col-md-1">
				<!--<label for="t1p<?php /*echo $i; */?>>Number">Nummer:</label>-->
				<input type="number" class="form-control heim" id="t1p<?php echo $i; ?>Number" placeholder="<?php echo $spielerNummer; ?>" value="<?php echo $_SESSION['playersTeam1'][$i]['number'] ?: $_SESSION['demo']['playersTeam1'][$i]['number'] ? : ''; ?>">
			</div>
			<div class="form-group col-md-5">
				<!--<label for="t1p<?php /*echo $i; */?>Name">Name:</label> -->
				<input type="text" class="form-control heim" id="t1p<?php echo $i; ?>Name" placeholder="<?php echo $spielerName; ?>" value="<?php echo $_SESSION['playersTeam1'][$i]['name'] ? : $_SESSION['demo']['playersTeam1'][$i]['name'] ? : ''; ?>">
			</div>
			<!-- TEAM 2 -->
			<!-- PLAYER 1 -->
			<div class="form-group col-md-1">
				<!--<label for="t2p<?php /*echo $i; */?>Number">Nummer:</label> -->
				<input type="number" class="form-control gast" id="t2p<?php echo $i; ?>Number" placeholder="<?php echo $spielerNummer; ?>" value="<?php echo $_SESSION['playersTeam2'][$i]['number'] ? : $_SESSION['demo']['playersTeam2'][$i]['number'] ? : ''; ?>">
			</div>
			<div class="form-group col-md-5">
				<!--<label for="t2p<?php /*echo $i; */?>Name">Name:</label> -->
				<input type="text" class="form-control gast" id="t2p<?php echo $i; ?>Name" placeholder="<?php echo $spielerName; ?>" value="<?php echo $_SESSION['playersTeam2'][$i]['name'] ? : $_SESSION['demo']['playersTeam2'][$i]['name'] ? : ''; ?>">
			</div>
		</div>
		<?php
		}
		?>
		<button type="button" class="btn btn-primary mb-5" id="btnGenerate">Generiere Spielbericht</button>
	</form>
</div>
<script>
	/** THANKS TO //HELP AT: https://uxsolutions.github.io/bootstrap-datepicker **/
	$('#date').datepicker({
		language: "de",
		keyboardNavigation: false,
		todayHighlight: true
	});

	$('#startTime,#endTime').datetimepicker({
		format: 'HH:mm'
	});

	$('#btnGenerate').click(function (){
		$.ajax({

			url: "createReportSheet.php",
			type: "POST",
			data: {

				//
				groupName: $('#groupName').val(),
				t1Name: $('#t1Name').val(),
				t2Name: $('#t2Name').val(),
				locationName: $('#locationName').val(),
				date: $('#date').val(),
				startTime: $('#startTime').val(),
				endTime: $('#endTime').val(),
				//TEAM1
				t1p1Number: $('#t1p1Number').val(),
				t1p1Name: $('#t1p1Name').val(),
				t1p2Number: $('#t1p2Number').val(),
				t1p2Name: $('#t1p2Name').val(),
				t1p3Number: $('#t1p3Number').val(),
				t1p3Name: $('#t1p3Name').val(),
				t1p4Number: $('#t1p4Number').val(),
				t1p4Name: $('#t1p4Name').val(),
				t1p5Number: $('#t1p5Number').val(),
				t1p5Name: $('#t1p5Name').val(),
				t1p6Number: $('#t1p6Number').val(),
				t1p6Name: $('#t1p6Name').val(),
				t1p7Number: $('#t1p7Number').val(),
				t1p7Name: $('#t1p7Name').val(),
				t1p8Number: $('#t1p8Number').val(),
				t1p8Name: $('#t1p8Name').val(),
				t1p9Number: $('#t1p9Number').val(),
				t1p9Name: $('#t1p9Name').val(),
				t1p10Number: $('#t1p10Number').val(),
				t1p10Name: $('#t1p10Name').val(),
				t1p11Number: $('#t1p11Number').val(),
				t1p11Name: $('#t1p11Name').val(),
				t1p12Number: $('#t1p12Number').val(),
				t1p12Name: $('#t1p12Name').val(),
				//TEAM2
				t2p1Number: $('#t2p1Number').val(),
				t2p1Name: $('#t2p1Name').val(),
				t2p2Number: $('#t2p2Number').val(),
				t2p2Name: $('#t2p2Name').val(),
				t2p3Number: $('#t2p3Number').val(),
				t2p3Name: $('#t2p3Name').val(),
				t2p4Number: $('#t2p4Number').val(),
				t2p4Name: $('#t2p4Name').val(),
				t2p5Number: $('#t2p5Number').val(),
				t2p5Name: $('#t2p5Name').val(),
				t2p6Number: $('#t2p6Number').val(),
				t2p6Name: $('#t2p6Name').val(),
				t2p7Number: $('#t2p7Number').val(),
				t2p7Name: $('#t2p7Name').val(),
				t2p8Number: $('#t2p8Number').val(),
				t2p8Name: $('#t2p8Name').val(),
				t2p9Number: $('#t2p9Number').val(),
				t2p9Name: $('#t2p9Name').val(),
				t2p10Number: $('#t2p10Number').val(),
				t2p10Name: $('#t2p10Name').val(),
				t2p11Number: $('#t2p11Number').val(),
				t2p11Name: $('#t2p11Name').val(),
				t2p12Number: $('#t2p12Number').val(),
				t2p12Name: $('#t2p12Name').val(),
			},
			dataType: "json",

		}).done(function (response) {

			console.log(response);

			let error = response.error;
			if (error === "") {

				window.location = 'createReportSheet.php';

			} else {
				console.log(error);
			}

		}).fail(function (error) {
			console.log(error);
		});
	});

	$('#btnDeleteSession').click(function (){
		$.ajax({

			url: "deleteSession.php",
			type: "POST",
			dataType: "json",

		}).done(function (response) {

			console.log(response);

			let error = response.error;
			if (error === "") {

				window.location = '/?normal=2';

			} else {
				console.log(error);
			}

		}).fail(function (error) {
			console.log(error);
		});
	});
</script>
</body>
</html>
