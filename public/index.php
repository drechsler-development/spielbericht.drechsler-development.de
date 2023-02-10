<?php

session_start ();

use App\Error;

require_once __DIR__.'/../config.php';
$spielerNummer = "Nr.";
$spielerName   = "Spieler-Name";

try {

	$Router = new App\Router();

	//Standard Route (first entry point)
	$Router->AddRoute ('', [
		/*'namespace'  => '',*/
		'controller' => 'Team',
		'action'     => 'Index'
	]);

	//All others
	$Router->AddRoute ('{controller}/{action}');

	$Router->AddRoute ('{controller}/{action}/{id:\d+}', [
		'controller' => 'Team',
		'action'     => 'Index'
	]);

	$Router->Dispatch ($_SERVER['QUERY_STRING']);

} catch (Exception $e) {
	echo Error::HandleErrorMessage ($e);
}
