<?php

namespace App\Controller;

use App\Model\Login;
use App\ResponseArray;
use App\View;
use Exception;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class LoginController extends Controller
{

	public function __construct () { }

	public function ShowLoginAction () {

		$data = [
			'login' => 1,
		];

		View::RenderTemplate ('login.twig', $data);

	}

	public function LoginAction () {

		$ResponseArray = new ResponseArray();
		$data          = [];

		try {

			$response['data']    = [];
			$response['status']  = '';
			$response['message'] = '';
			$response['error']   = '';

			$email    = $_POST['emailaddress'] ?? '';
			$password = $_POST['password'] ?? '';

			$Login           = new Login();
			$Login->email    = $email;
			$Login->password = $password;
			$Login->Login ();

		} catch (Exception $e) {
			$ResponseArray->error = $e->getMessage ();
		}

		$ResponseArray->ReturnOutput ();

	}

	/**
	 * @return void
	 * @throws LoaderError
	 * @throws RuntimeError
	 * @throws SyntaxError
	 */
	public function LogoutAction (): void {

		Login::Logout ();

		$data = [
			'logout' => 1,
		];

		View::RenderTemplate ('login.twig', $data);

	}
}
