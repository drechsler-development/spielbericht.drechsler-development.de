<?php

namespace App\Controller;

use App\Core\ErrorHandler;
use App\Core\ResponseArray;
use App\Core\View;
use App\Model\Login;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class LoginController extends Controller {

	public ResponseArray $ResponseArray;

	public function __construct () {
		$this->ResponseArray = new ResponseArray();
	}

	public function ShowLoginAction (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {

		$data = [
			'login' => 1,
		];

		try {

			$data = [

				'PAGE_TITLE'       => 'Login',
				'PAGE_DESCRIPTION' => '',
			];

			$body = View::RenderTemplate ('login.twig', $data);

		} catch (Exception $e) {

			$body = View::RenderTemplate ('Admin/Error/500.twig', array_merge (['errorMessage' => ErrorHandler::HandleErrorMessage ($e)]));

		}

		$response->getBody ()->write ($body);

		return $response;

	}

	public function LoginAction (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {

		try {

			$email    = $_POST['emailaddress'] ?? '';
			$password = $_POST['password'] ?? '';

			$Login           = new Login();
			$Login->email    = $email;
			$Login->password = $password;
			$Login->Login ();

		} catch (Exception $e) {
			$this->ResponseArray->error = $e->getMessage ();
		}

		$response->getBody ()->write ($this->ResponseArray->ReturnOutput (true));

		return $response->withHeader ('Content-Type', 'application/json')->withStatus (201);

	}

	/**
	 * @return void
	 * @throws LoaderError
	 * @throws RuntimeError
	 * @throws SyntaxError
	 */
	public function LogoutAction (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {

		try {

			Login::Logout ();

			$data = [

				'PAGE_TITLE'       => 'Login',
				'PAGE_DESCRIPTION' => '',
				'logout'           => 1,
			];

			$body = View::RenderTemplate ('login.twig', $data);

		} catch (Exception $e) {

			$body = View::RenderTemplate ('Admin/Error/500.twig', array_merge (['errorMessage' => ErrorHandler::HandleErrorMessage ($e)]));

		}

		$response->getBody ()->write ($body);

		return $response;

	}
}
