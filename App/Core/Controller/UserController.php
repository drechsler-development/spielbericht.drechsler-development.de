<?php

namespace App\Core\Controller;

use App\Core\View;
use App\Model\Client\Client;
use App\Model\Helper\Session;
use App\Model\Security\SecurityGroup;
use App\Model\User\User;
use DD\Exceptions\ValidationException;
use DD\Helper\File;
use DD\Helper\Image;
use DD\Helper\Strings;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class UserController extends BackendController {

	/**
	 * @throws LoaderError
	 * @throws RuntimeError
	 * @throws SyntaxError|ValidationException
	 */
	function IndexAction (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {

		$data = [

			'PAGE_TITLE'       => 'User',
			'PAGE_DESCRIPTION' => 'Benutzer Verwaltung',
		];

		if (Session::IsGlobalAdmin ()) {
			$Client                   = new Client();
			$Client->filterNetdisplay = true;
			$Client->netdisplay       = 1;
			$data['vabsClientRows'] = $Client->LoadShort ();
		}

		$body = View::RenderTemplate ('Admin/User/list.twig', array_merge ($data, $this->globalData));

		$response->getBody ()->write ($body);

		return $response;
	}

	function LoadLinesAction (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {


		$clientId     = $request->getParsedBody ()['filterClientId'] ?? Session::GetClientId ();
		$showDeleted    = $request->getParsedBody ()['showDeleted'] ?? 0;
		$showActiveOnly = $request->getParsedBody ()['showActiveOnly'] ?? 1;

		try {

			if (!Session::IsGlobalAdmin () && $clientId != Session::GetClientId ()) {
				throw new ValidationException("Du bist kein Admin!!! Schäm dich! Jetzt! Ab in die Ecke!");
			}

			$data = [

				'PAGE_TITLE'       => SYSTEMNAME . ' User',
				'PAGE_DESCRIPTION' => 'Benutzer Verwaltung',
			];

			$User                 = new User();
			$User->CLIENT_ID      = Session::IsGlobalAdmin () ? $clientId : Session::GetClientId ();
			$User->showActiveOnly = $showActiveOnly;
			$User->showDeleted    = $showDeleted;

			$data['rows'] = $User->Load ();

			$this->ResponseArray->data = View::RenderTemplate ('Admin/User/lines.twig', array_merge ($data, $this->globalData));

		} catch (Exception $e) {
			$this->ResponseArray->error = $e->getMessage ();
		}

		$response->getBody ()->write ($this->ResponseArray->ReturnOutput (true));

		return $response->withHeader ('Content-Type', 'application/json')->withStatus (201);

	}

	function LoadSingleAction (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {


		$data = [];

		$clientId = $request->getParsedBody ()['filterClientId'] ?? Session::GetClientId ();
		$id         = $request->getParsedBody ()['id'] ?? 0;

		try {

			if (!Session::IsGlobalAdmin () && $clientId != Session::GetClientId ()) {
				throw new ValidationException("Du bist kein Admin!!! Schäm dich! Also, ab in die Ecke! JETZT!!!!");
			}

			if (Session::IsGlobalAdmin ()) {
				$Client                   = new Client();
				$Client->filterNetdisplay = true;
				$Client->netdisplay       = 1;
				$data['vabsClientRows'] = $Client->LoadShort ();
			}

			if (!empty($id)) {
				$User            = new User();
				$User->CLIENT_ID = Session::IsGlobalAdmin () ? $clientId : Session::GetClientId ();
				$User->id        = $id;
				$data['row']               = $User->Load ()[0] ?? [];

			} else {
				$data['row']['clientId'] = $clientId;
			}

			### selectable Security Groups

			// If a user is logged in, we need to get his/her Security Groups.
			// As only that groups can be assigned to a different user
			if (!Session::IsGlobalAdmin ()) {
				$data['selectableSecurityGroups'] = SecurityGroup::GetAssignedSecurityGroups (Session::GetUserId ());
				// If the user is the global admin, he can assign all security groups
			} else {
				$SecurityGroup = new SecurityGroup();
				$data['selectableSecurityGroups'] = $SecurityGroup->Load ();
			}

			### current assigned Security Groups

			$array = [];
			if (!empty($id)) {
				$assignedSecurityGroups = SecurityGroup::GetAssignedSecurityGroups ($id);
				if (!empty($assignedSecurityGroups)) {
					foreach ($assignedSecurityGroups as $element) {
						$array[] = $element['id'];
					}
				}
			}

			$data['assignedSecurityGroups'] = $array;

			$this->ResponseArray->data = View::RenderTemplate ('Admin/User/form.twig', array_merge ($data, $this->globalData));

		} catch (Exception $e) {
			$this->ResponseArray->error = $e->getMessage ();
		}

		$response->getBody ()->write ($this->ResponseArray->ReturnOutput (true));

		return $response->withHeader ('Content-Type', 'application/json')->withStatus (201);

	}

	function SaveAction (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {


		$setPasswort = false;

		$userId             = $request->getParsedBody ()['userId'] ?? 0;
		$salutation         = $request->getParsedBody ()['salutation'] ?? 1;
		$firstName          = $request->getParsedBody ()['firstName'] ?? '';
		$lastName           = $request->getParsedBody ()['lastName'] ?? '';
		$email              = $request->getParsedBody ()['email'] ?? '';
		$telephone          = $request->getParsedBody ()['telephone'] ?? '';
		$facebook           = $request->getParsedBody ()['facebook'] ?? '';
		$twitter            = $request->getParsedBody ()['twitter'] ?? '';
		$login              = $request->getParsedBody ()['login'] ?? '';
		$password1          = $request->getParsedBody ()['password1'] ?? '';
		$password2          = $request->getParsedBody ()['password2'] ?? '';
		$ownDescription     = $request->getParsedBody ()['ownDescription'] ?? '';
		$localAdmin         = $request->getParsedBody ()['localAdmin'] ?? 0;
		$active             = $request->getParsedBody ()['active'] ?? 0;
		$spoofing           = $request->getParsedBody ()['spoofing'] ?? 0;
		$showOnFrontend     = $request->getParsedBody ()['showOnFrontend'] ?? 0;
		$selectedClientId = $request->getParsedBody ()['selectedClientId'] ?? 0;
		$securityGroups     = $request->getParsedBody ()['securityGroups'] ?? [];

		$message = "";

		try {

			if (empty($salutation)) {
				throw new ValidationException("Keine Anrede eingegeben");
			}
			if (empty($firstName)) {
				throw new ValidationException("Kein Vorname eingegeben");
			}
			if (empty($lastName)) {
				throw new ValidationException("Kein Nachname eingegeben");
			}
			if (empty($email)) {
				throw new ValidationException("Keine Email eingegeben");
			}
			if (empty($telephone)) {
				throw new ValidationException("Keine Telefonnummer eingegeben");
			}
			if (empty($password1) && empty($userId)) {
				throw new ValidationException("Bei Neuanlage musst Du ein Passwort eingeben");
			}

			//Login/Passwort
			if (empty($login)) {
				throw new ValidationException("Kein login eingegeben");
			} else if (!User::CanLoginBeUsed ($login, $userId)) {
				throw new ValidationException("Login schon vergeben");
			}

			//Paswort wurde gesetzt
			if (!empty($password1)) {

				if ($password1 !== $password2) {
					throw new ValidationException("Passwörter sind nicht gleich!");
				}

				$setPasswort = true;

			}

			if (Session::IsGlobalAdmin () && empty($selectedClientId)) {
				throw new ValidationException("Auch als Admin musst du wenigstens einen Zielanbieter wählen, Du Honk!");
			}

			$clientId       = Session::IsGlobalAdmin () ? $selectedClientId : Session::GetClientId ();
			$User               = new User();
			$User->CLIENT_ID = $clientId;
			if (empty($userId)) {

				$this->ResponseArray->debug .= "User musste angelegt werden";

				$User->salutation     = $salutation;
				$User->firstName      = $firstName;
				$User->lastName       = $lastName;
				$User->email          = $email;
				$User->login          = $login;
				$User->telephone      = $telephone;
				$User->facebook       = $facebook;
				$User->twitter        = $twitter;
				$User->password       = $password1;
				$User->localAdmin     = $localAdmin;
				$User->active         = $active;
				$User->spoofing       = $spoofing;
				$User->showOnFrontend = $showOnFrontend;
				$User->ownDescription = $ownDescription;

				$User->Save ($message);
				if (!str_contains ($message, "existiert")) {
					throw new ValidationException("Fehler beim Speichern des Users. Der Administrator wurde informiert.");
				} else {
					throw new ValidationException($message);
				}

			} else {

				$this->ResponseArray->debug .= "User war schon da";

				$User->id = $userId;
				$array    = [
					'salutation'        => $salutation,
					'aktiv'             => $active,
					'spoofing'          => $spoofing,
					'telephone'         => $telephone,
					'firstName'         => $firstName,
					'lastName'          => $lastName,
					'login'             => $login,
					'email'             => $email,
					'facebook'          => $facebook,
					'twitter'           => $twitter,
					'ownDescription'    => $ownDescription,
					'visibleOnFrontend' => $showOnFrontend,
					'localAdmin'        => $localAdmin,
				];
				$User->Update ($array);

			}

			if ($setPasswort) {
				$User->password = $password1;
				if (!$User->ResetPassword ()) {
					throw new ValidationException("Passwort konnte nicht gespeichert werden");
				}
			}

			//TODO: Check if user can change it
			if (Session::IsGlobalAdmin ()) {
				if (!empty($securityGroups)) {
					$User->AddUserToSecurityGroup ($securityGroups, true);
				}
			}

			$this->ResponseArray->debug = $message;

		} catch (Exception $e) {
			$this->ResponseArray->error = $e->getMessage ();
		}

		$response->getBody ()->write ($this->ResponseArray->ReturnOutput (true));

		return $response->withHeader ('Content-Type', 'application/json')->withStatus (201);

	}

	function DeleteAction (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {


		try {

			$clientId = $request->getParsedBody ()['filterClientId'] ?? Session::GetClientId ();
			$id         = $request->getParsedBody ()['id'] ?? 0;

			if (!Session::IsGlobalAdmin ()) {

				if (Session::IsLocalAdmin ()) {

					if ($clientId != Session::GetClientId ()) {
						throw new ValidationException("Du wolltest Soeben einen User löschen, der gar nicht in deiner Hood ist! Schäm dich! Also, ab in die Ecke! JETZT!!!!");
					}

				} else {
					throw new ValidationException("Du musst mindestens Local Admin sein, um einen User löschen zu können!!!");
				}

			}

			$User     = new User($clientId);
			$User->id = $id;
			$User->Delete ();

		} catch (Exception $e) {
			$this->ResponseArray->error = $e->getMessage ();
		}

		$response->getBody ()->write ($this->ResponseArray->ReturnOutput (true));

		return $response->withHeader ('Content-Type', 'application/json')->withStatus (201);

	}

	function DuplicateAction (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {


		try {

			$clientId = $request->getParsedBody ()['filterClientId'] ?? Session::GetClientId ();
			$id         = $request->getParsedBody ()['id'] ?? 0;

			if (!Session::IsGlobalAdmin ()) {

				if (Session::IsLocalAdmin ()) {

					if ($clientId != Session::GetClientId ()) {
						throw new ValidationException("Du wolltest Soeben einen User duplizieren, der gar nicht in deiner Hood ist! Schäm dich! Also, ab in die Ecke! JETZT!!!!");
					}

				} else {
					throw new ValidationException("Du musst mindestens Local Admin sein, um einen User duplizieren zu können!!!");
				}

			}

			$User     = new User($clientId);
			$User->id = $id;
			$User->Duplicate ();

		} catch (Exception $e) {
			$this->ResponseArray->error = $e->getMessage ();
		}

		$response->getBody ()->write ($this->ResponseArray->ReturnOutput (true));

		return $response->withHeader ('Content-Type', 'application/json')->withStatus (201);

	}

	function UploadFileAction (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {


		try {

			$userId = $request->getParsedBody ()['userId'] ?? 0;
			$crop   = $request->getParsedBody ()['crop'];
			$width  = $request->getParsedBody ()['width'] ?? 0;
			$height = $request->getParsedBody ()['height'] ?? 0;
			$file   = $_FILES['file'] ?? false;

			if (empty($userId)) {
				throw new ValidationException("UserId war leer");
			}

			if (empty($file)) {
				throw new ValidationException("Kein Bild übertragen");
			}

			$path_parts   = pathinfo ($file['name']);
			$ext          = $path_parts['extension'];
			$randomString = Strings::GetRandomString (20, Strings::NUM);
			$filename     = "user_" . $userId . "-" . $randomString . "." . $ext;

			$error = File::MoveUploadedFile ($file, PASSBILDER, $filename);
			if (!empty($error)) {
				throw new ValidationException("Fileupload fehlgeschlagen. Wenden Sie sich an den Administrator. Fehler: " . $error);
			} else {
				$User               = new User();
				$User->id           = $userId;
				$User->CLIENT_ID = Session::GetClientId ();
				$User->Update (['bild' => $filename]);
			}

			//Crop the Image
			if ($crop) {
				$this->ResponseArray->debug = PASSBILDER . "/" . $filename;
				$height                     = $height ?? $width;
				$filename                   = Image::CropImage (PASSBILDER . "/" . $filename, $height, $width, true, false, false);
				$this->ResponseArray->data  = $filename;
			} else {
				$this->ResponseArray->data = PASSBILDER_WEB . "/" . $filename;
			}

		} catch (Exception $e) {
			$this->ResponseArray->error = $e->getMessage ();
		}

		$response->getBody ()->write ($this->ResponseArray->ReturnOutput (true));

		return $response->withHeader ('Content-Type', 'application/json')->withStatus (201);

	}

	/**
	 */
	function GetAssignedSecurityGroupsAction (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {


		try {

			$id = $request->getParsedBody ()['id'] ?? 0;

			$list['rows'] = SecurityGroup::GetAssignedSecurityGroups ($id);

			$this->ResponseArray->data = View::RenderTemplate ('ul.twig', array_merge ($list, $this->globalData));

		} catch (Exception $e) {
			$this->ResponseArray->error = $e->getMessage ();
		}

		$response->getBody ()->write ($this->ResponseArray->ReturnOutput (true));

		return $response->withHeader ('Content-Type', 'application/json')->withStatus (201);

	}

}
