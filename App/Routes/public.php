<?php

use App\Controller\GameController;
use App\Controller\GameReportController;
use App\Controller\LoginController;
use App\Controller\TeamController;
use Slim\App;

if (empty($app) || $app instanceof App === false) {
	throw new Exception('App is not defined');
}

$allRoutes = [];

$app->get ('[/]', [TeamController::class, 'IndexAction']);

//Handle Login/Logout
$app->get ('/Login', [LoginController::class, 'ShowLoginAction']);
$app->post ('/Login', [LoginController::class, 'LoginAction']);
$app->get ('/Logout', [LoginController::class, 'LogoutAction']);

//GameReport page
$app->get ('/GameReport', [GameReportController::class, 'IndexAction']);
$app->get ('/GameReport/LoadPlayers/{id:\d+}', [GameReportController::class, 'LoadPlayersAction']);
$app->post ('/GameReport/CreateReport', [GameReportController::class, 'CreateReportAction']);

$app->get ('/Game', [GameController::class, 'IndexAction']);
$app->post ('/Game/LoadLines', [GameController::class, 'LoadLinesAction']);

$app->get ('/Team', [TeamController::class, 'IndexAction']);
$app->post ('/Team/UpdatePlayers', [TeamController::class, 'UpdatePlayersAction']);
$app->post ('/Team/LoadSingle', [TeamController::class, 'LoadSingleAction']);
$app->post ('/Team/Save', [TeamController::class, 'SaveAction']);


$app->get ('/Teams', [TeamController::class, 'IndexAction']);
$app->post ('/Teams/UpdatePlayers', [TeamController::class, 'UpdatePlayersAction']);
$app->post ('/Teams/LoadSingle', [TeamController::class, 'LoadSingleAction']);
$app->post ('/Teams/Save', [TeamController::class, 'SaveAction']);
