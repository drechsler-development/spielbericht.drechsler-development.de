<?php

use App\Core\ResponseArray;
use App\Core\View;
use DD\Exceptions\LoginException;
use DD\Exceptions\PermissionException;
use DD\Mailer\Mailer;
use DD\SystemType;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;

session_start ();

require_once '../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

try {

	set_error_handler ('\App\Core\ErrorHandler::SetErrorHandler');
	set_exception_handler ('\App\Core\ErrorHandler::SetExceptionHandler');

	$containerBuilder = new ContainerBuilder();
	$containerBuilder->addDefinitions (__DIR__ . '/../services.php');
	$container = $containerBuilder->build ();

	//Create the Slim Application container
	$app = $container->get (App::class);

	//Add the routes
	require __DIR__ . '/../App/Routes/public.php';

	$app->get ('/hello/{name}', function (ServerRequestInterface $request, ResponseInterface $response) {

		$name = $request->getAttribute ('name');
		$response->getBody ()->write ('Hello ' . $name);

		return $response;
	});

	$app->run ();

} catch (PDOException|Exception $e) {

	try {

		$CODE_VERSION         = "ERR" . rand (100000, 999999); //just a random number to identify the error in the logs and emails
		$data['CODE_VERSION'] = $CODE_VERSION;
		$htmlBody             = '';
		$subjectSuffix        = '';

		if ($e instanceof PDOException) {
			$subjectSuffix = 'DB';
		} else if (str_contains ($e->getMessage (), 'Not found')) {
			$subjectSuffix = '404';
		} else {
			$subjectSuffix = '500';
		}

		if ($_SERVER['REQUEST_METHOD'] === 'GET') {

			$data['errorMessage'] = '<br><b>Message:</b> ' . $e->getMessage () . ' <br>';

			if (SYSTEMTYPE !== SystemType::PROD && SYSTEMTYPE !== "") {

				$data['errorMessage'] .= '<b>File:</b> ' . $e->getFile () . ' <br>
								 <b>Line:</b> ' . $e->getLine () . ' <br><b>Stacktrace:</b> ';
				$array                = explode ("#", $e->getTraceAsString ());
				foreach ($array as $row) {
					$row = str_replace (str_replace ('/public', '', $_SERVER['DOCUMENT_ROOT']), '', $row);
					if (!empty($row)) {
						$data['errorMessage'] .= "<br>#" . $row;
					}
				}

			}

			if ($e instanceof PDOException) {
				$htmlBody = View::RenderTemplate ('/Error/DB.twig', array_merge ($data));
			} else if (str_contains ($e->getMessage (), 'Not found')) {
				//add header with 404 status
				header ('HTTP/1.0 404 Not Found');
				$htmlBody = View::RenderTemplate ('/Error/404.twig', array_merge ($data));
			} else if ($e instanceof PermissionException) {
				$data['showErrorMessage'] = true;
				$htmlBody                 = View::RenderTemplate ('/Error/401.twig', array_merge ($data));
			} else {
				$htmlBody = View::RenderTemplate ('/Error/500.twig', array_merge ($data));
			}

			echo $htmlBody;

		} else {

			header ('Content-Type: application/json');

			if ($e instanceof PDOException) {
				header ('HTTP/1.0 500 Database Error');
				$errorMessage = 'Database error (1).';
			} else if (str_contains ($e->getMessage (), 'Not found')) {
				//add header with 404 status
				header ('HTTP/1.0 404 Not Found');
				$errorMessage = 'Page not found (2).';
			} else if ($e instanceof PermissionException) {
				header ('HTTP/1.0 403 Forbidden');
				$errorMessage = 'Unauthorized access (3).';
			} else if ($e instanceof LoginException) {
				header ('HTTP/1.0 403 Forbidden LoginException');
				$errorMessage = 'Unauthorized access (3).';
			} else {
				header ('HTTP/1.0 500 Internal Server Error');
				$errorMessage = 'Internal Server Error (4)';
			}

			if (SYSTEMTYPE !== SystemType::PROD && SYSTEMTYPE !== "") {
				$errorMessage .= ' Error: ' . $e->getMessage ();
			}

			$ResponseArray        = new ResponseArray();
			$ResponseArray->error = $errorMessage;
			echo $ResponseArray->ReturnOutput (true);

		}

		$mailBody = $e->getMessage ();
		$trace    = $e->getTraceAsString ();

		$mailBody .= '<br><br><b>File:</b> ' . $e->getFile () . ' <br>
				<b>Line:</b> ' . $e->getLine () . ' <br><b>Stacktrace:</b> ';
		$array    = explode ("#", $e->getTraceAsString ());
		foreach ($array as $row) {
			$row = str_replace (str_replace ('/public', '', $_SERVER['DOCUMENT_ROOT']), '', $row);
			if (!empty($row)) {
				$mailBody .= "<br>#" . $row;
			}
		}

		//ADD URL and Query String intio the body
		$mailBody .= '<br><br><b>URL:</b> ' . $_SERVER['REQUEST_URI'] . ' <br>
				<b>Query String:</b> ' . $_SERVER['QUERY_STRING'] . ' <br>';

		//Add Session Data (but please check if each element of the session array is an array and split that up)
		$mailBody .= '<br><br><b>Session:</b> ' . json_encode ($_SESSION);
		//Format that JSON array by adding line breaks
		$mailBody = str_replace (',', ',<br>', $mailBody);
		$mailBody = str_replace ('{', '{<br>', $mailBody);
		$mailBody = str_replace ('}', '<br>}', $mailBody);


		if ($subjectSuffix == '404') {
			$logger       = new Logger('my_logger');
			$file_handler = new StreamHandler(__DIR__ . '/logs/404.log');
			$logger->pushHandler ($file_handler);
			$logger->error ($mailBody);
		} else {
			$Mailer = new Mailer();
			$Mailer->DDAddTo (EMAIL_DEVELOPER);
			$Mailer->DDSubject ('Error :: ' . $subjectSuffix);
			$Mailer->setFrom (SYSTEMTYPE_NAME . '@' . $_SERVER['HTTP_HOST'], SYSTEMNAME . ' :: ' . SYSTEMTYPE_NAME);
			$Mailer->msgHTML ($mailBody);
			$Mailer->DDSend ();
		}

	} catch (Exception $ex) {
		//If sending the mail or logging fails, we still need to catch that to avoid infinite loops
		echo 'A critical error occurred.' . $ex->getMessage ();
	}

}
