<?php

session_start ();

use App\Controller\GameController;
use App\Controller\GameReportController;
use App\Controller\LoginController;
use App\Controller\TeamController;
use App\Error;
use App\View;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

require_once __DIR__.'/../config.php';

$controllerName = "";
$method         = "";
$params         = [];

try {

	$dispatcher = simpleDispatcher (function(RouteCollector $r) {

		//HomePage
		$r->addRoute ('GET', '[/]', TeamController::class.'::Index');
		//Handle Login/Logout
		$r->addRoute ('GET', '/Login', LoginController::class.'::ShowLogin');
		$r->addRoute ('POST', '/Login', LoginController::class.'::Login');
		//$r->addRoute ('POST', '/Login/Login', LoginController::class.'::Login');
		$r->addRoute ('GET', '/Logout', LoginController::class.'::Logout');

		//GameReport page
		$r->addRoute ('GET', '/GameReport', GameReportController::class.'::Index');
		$r->addRoute ('GET', '/GameReport/LoadPlayers/{id:\d+}', GameReportController::class.'::LoadPlayers');
		$r->addRoute ('POST', '/GameReport/CreateReport', GameReportController::class.'::CreateReport');
		$r->addRoute ('POST', '/Team/UpdatePlayers', TeamController::class.'::UpdatePlayers');
		$r->addRoute ('GET', '/Game', GameController::class.'::Index');
		$r->addRoute ('POST', '/Game/LoadLines', GameController::class.'::LoadLines');
		$r->addRoute ('GET', '/Teams', TeamController::class.'::Index');
		$r->addRoute ('POST', '/Teams/LoadSingle', TeamController::class.'::LoadSingle');
		$r->addRoute ('POST', '/Teams/Save', TeamController::class.'::Save');
		// The /{title} suffix is optional
		$r->addRoute ('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
	});

	// Fetch method and URI from somewhere
	$httpMethod = $_SERVER['REQUEST_METHOD'];
	$uri        = $_SERVER['REQUEST_URI'];

	// Strip query string (?foo=bar) and decode URI
	if (false !== $pos = strpos ($uri, '?')) {
		$uri = substr ($uri, 0, $pos);
	}
	$uri = rawurldecode ($uri);

	$routeInfo = $dispatcher->dispatch ($httpMethod, $uri);

	switch ($routeInfo[0]) {
		case Dispatcher::NOT_FOUND:
			View::RenderTemplate ('Error/404.twig');
			break;
		case Dispatcher::METHOD_NOT_ALLOWED:
			$allowedMethods = $routeInfo[1];
			View::RenderTemplate ('Error/405.twig');
			break;
		case Dispatcher::FOUND:

			$handler        = $routeInfo[1];
			$controllerName = substr ($handler, 0, strpos ($handler, '::'));
			$params         = $routeInfo[2];

			if (class_exists ($controllerName) || class_exists ($controllerName."Controller")) {
				if (!class_exists ($controllerName)) {
					$controllerName = $controllerName."Controller";
				}
			}

			$method     = substr ($handler, strpos ($handler, '::') + 2);
			$controller = new $controllerName($params);

			/*echo "ControllerName: ".$controllerName."<br>";
			echo "MethodName: ".$method."<br>";
			echo "Params: ".json_encode ($params)."<br>";*/

			//Check if we can call the method in the controllerName
			//Attention: we do not call the correct method as we will add action at the end in the magic method __call
			// within each backend controllerName to force calling the login method first
			if (is_callable ([
				$controller,
				$method
			])) {

				if ($method != "CheckSession") {
					$_SESSION['currentSessionTime'] = date ("Y-m-d H:i:s");
				}

				//Finally calll the class with the method
				$controller->params = $params;
				$controller->$method();

			} else {
				throw new Exception("Method $method (in controllerName $controllerName) not found");
			}
	}

} catch (Exception $e) {
	echo Error::HandleErrorMessage ($e);
}

/*echo "ControllerName: ".$controllerName."<br>";
echo "MethodName: ".$method."<br>";
echo "Params: ".json_encode ($params)."<br>";*/
