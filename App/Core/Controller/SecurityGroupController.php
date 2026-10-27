<?php

namespace App\Core\Controller;

use App\Core\ErrorHandler;
use App\Core\View;
use App\Model\Helper\Session;
use App\Model\Navigation\Area;
use App\Model\Security\Right;
use App\Model\Security\SecurityGroup;
use App\Model\User\User;
use DD\Exceptions\PermissionException;
use DD\Exceptions\ValidationException;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class SecurityGroupController extends BackendController {

	/**
	 * @throws LoaderError
	 * @throws RuntimeError
	 * @throws SyntaxError
	 */
	function IndexAction (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {

		try {

			if (!Session::IsGlobalAdmin ()) {
				throw new PermissionException();
			}

			$data = [

				'PAGE_TITLE'       => 'SecurityGroups',
				'PAGE_DESCRIPTION' => 'Administration der Zugriffsrechte von Sicherheitsgruppen',
			];

			$body = View::RenderTemplate ('Admin/SecurityGroups/list.twig', array_merge ($data, $this->globalData));

		} catch (PermissionException) {

			$body = View::RenderTemplate ('Admin/Error/666.twig', array_merge ($this->globalData));

		}

		$response->getBody ()->write ($body);

		return $response;

	}

	function LoadLinesAction (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {


		$data = [];

		try {

			if (!Session::IsGlobalAdmin ()) {
				throw new PermissionException();
			}

			$SecurityGroup = new SecurityGroup();

			$data['rows'] = $SecurityGroup->Load ();

			$this->ResponseArray->data = View::RenderTemplate ('Admin/SecurityGroups/lines.twig', array_merge ($data, $this->globalData));

		} catch (PermissionException) {
			$this->ResponseArray->error = "Permission denied!";
		} catch (Exception $e) {
			$this->ResponseArray->error = $e->getMessage ();
		}

		$response->getBody ()->write ($this->ResponseArray->ReturnOutput (true));

		return $response->withHeader ('Content-Type', 'application/json')->withStatus (201);

	}

	function LoadSingleAction (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {


		$data = [];

		$securityGroupId = $request->getParsedBody ()['id'] ?? 0;

		try {

			if (!Session::IsGlobalAdmin ()) {
				throw new PermissionException("Du bist kein Admin!!! Schäm dich! Also, ab in die Ecke! JETZT!!!!");
			}


			if($securityGroupId > 0) {
				$SecurityGroup     = new SecurityGroup();
				$SecurityGroup->id = $securityGroupId;
				$data['row'] = $SecurityGroup->Load ()[0] ?? [];
			}

			$Rights = new Right();

			$rightRows = $Rights->Load ();
			$data['rights'] = $rightRows;

			$modules                  = [];
			$Areas                    = new Area();
			$Areas->dontShowAdminOnly = true;

			$areas = $Areas->Load ();

			if (count ($areas) > 0) {

				foreach ($areas as $area) {

					$tableArray = [];

					$tableArray['securityGroupId'] = $securityGroupId;
					$tableArray['groupName']       = $area['groupName'] ?? '';
					$tableArray['areaId']          = $area['id'] ?? 0;
					$tableArray['groupId']         = $area['groupId'] ?? 0;
					$tableArray['areaName']        = $area['name'] ?? '';
					$tableArray['path']            = $area['path'] ?? '';
					$tableArray['description']     = $area['description'] ?? '';
					$tableArray['rights']          = [];

					foreach ($rightRows as $right) {

						$tableArray['rights'][] = [
							'checked' => SecurityGroup::HasCMSGroupRightsForCMSArea ($securityGroupId, $area['id'] ?? 0, $right['id'] ?? 0),
							'id'      => $right['id'] ?? '',
							'icon'    => $right['icon'] ?? '',
							'color'   => $right['color'] ?? '',
						];

					}

					reset ($rightRows);

					$modules[] = $tableArray;

				}

			}

			$data['modules'] = $modules;

			$this->ResponseArray->data = View::RenderTemplate ('Admin/SecurityGroups/form.twig', array_merge ($data, $this->globalData));

		} catch (Exception $e) {
			$this->ResponseArray->error = ErrorHandler::HandleErrorMessage ($e);
		}

		$response->getBody ()->write ($this->ResponseArray->ReturnOutput (true));

		return $response->withHeader ('Content-Type', 'application/json')->withStatus (201);
	}

	function SaveAction (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {


		$id          = $request->getParsedBody ()['id'] ?? 0;
		$name        = $request->getParsedBody ()['name'] ?? '';
		$description = $request->getParsedBody ()['description'] ?? '';

		try {

			if (!Session::IsGlobalAdmin ()) {
				throw new PermissionException("Du bist kein Admin!!! Schäm dich! Also, ab in die Ecke! JETZT!!!!");
			}

			if (empty($name)) {
				throw new ValidationException("Keinen Namen übergeben");
			}

			if (empty($description)) {
				throw new ValidationException("Keine Beschreibung übergeben");
			}

			$SecurityGroup = new SecurityGroup();
			if (empty($id)) {
				$SecurityGroup->Create ();
			} else {
				$SecurityGroup->id = $id;
			}

			$array = [
				'name'         => $name,
				'beschreibung' => $description,
			];

			$SecurityGroup->Update ($array);

		} catch (Exception $e) {
			$this->ResponseArray->error = $e->getMessage ();
		}

		$response->getBody ()->write ($this->ResponseArray->ReturnOutput (true));

		return $response->withHeader ('Content-Type', 'application/json')->withStatus (201);

	}

	function DeleteAction (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {


		$id = $request->getParsedBody ()['id'] ?? 0;

		try {

			if (!Session::IsGlobalAdmin ()) {
				throw new PermissionException("Du bist kein Admin!!! Schäm dich! Also, ab in die Ecke! JETZT!!!!");
			}

			if (empty($id)) {
				throw new ValidationException("Keine ID übergeben");
			}

			$SecurityGroup     = new SecurityGroup();
			$SecurityGroup->id = $id;

			$SecurityGroup->Delete ();

		} catch (Exception $e) {
			$this->ResponseArray->error = $e->getMessage ();
		}

		$response->getBody ()->write ($this->ResponseArray->ReturnOutput (true));

		return $response->withHeader ('Content-Type', 'application/json')->withStatus (201);

	}

	function ChangeSecurityRightsAction (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {


		$securityGroupId = $request->getParsedBody ()['securityGroupId'] ?? 0;
		$areaId          = $request->getParsedBody ()['areaId'] ?? 0;
		$rightsId        = $request->getParsedBody ()['rightsId'] ?? 0;
		$checked         = $request->getParsedBody ()['checked'] ?? 0;

		try {

			if (!Session::IsGlobalAdmin ()) {
				throw new PermissionException("Du bist kein Admin!!! Schäm dich! Also, ab in die Ecke! JETZT!!!!");
			}

			SecurityGroup::ChangeSecurityPermissions ($securityGroupId, $areaId, $rightsId, $checked);

		} catch (Exception $e) {
			$this->ResponseArray->error = $e->getMessage ();
		}

		$response->getBody ()->write ($this->ResponseArray->ReturnOutput (true));

		return $response->withHeader ('Content-Type', 'application/json')->withStatus (201);

	}

	function ShowMembersAction (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {


		$data = [];
		$id = $request->getParsedBody ()['id'] ?? 0;

		try {

			if (!Session::IsGlobalAdmin ()) {
				throw new PermissionException();
			}

			if (empty($id)) {
				throw new ValidationException("id was not set");
			}

			$data['members'] = SecurityGroup::GetMembers ($id);
			$data['groupId'] = $id;

			$this->ResponseArray->data = View::RenderTemplate ('Admin/SecurityGroups/members.twig', array_merge ($data));

		} catch (PermissionException) {
			$this->ResponseArray->error = "Permission denied!";
		} catch (Exception $e) {
			$this->ResponseArray->error = $e->getMessage ();
		}

		$response->getBody ()->write ($this->ResponseArray->ReturnOutput (true));

		return $response->withHeader ('Content-Type', 'application/json')->withStatus (201);

	}

	function ShowNoMembersAction (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {


		$data = [];
		$id = $request->getParsedBody ()['id'] ?? 0;

		try {

			if (!Session::IsGlobalAdmin ()) {
				throw new PermissionException();
			}

			if (empty($id)) {
				throw new ValidationException("id was not set");
			}

			$data['groupId'] = $id;
			$data['members'] = SecurityGroup::GetNoMembers ($id);

			$this->ResponseArray->data = View::RenderTemplate ('Admin/SecurityGroups/no-members.twig', array_merge ($data));

		} catch (PermissionException) {
			$this->ResponseArray->error = "Permission denied!";
		} catch (Exception $e) {
			$this->ResponseArray->error = $e->getMessage ();
		}

		$response->getBody ()->write ($this->ResponseArray->ReturnOutput (true));

		return $response->withHeader ('Content-Type', 'application/json')->withStatus (201);

	}

	function AddUserToGroupAction (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {


		$userId  = $request->getParsedBody ()['userId'] ?? 0;
		$groupId = $request->getParsedBody ()['groupId'] ?? 0;

		try {

			if (!Session::IsGlobalAdmin ()) {
				throw new PermissionException();
			}

			if (empty($groupId)) {
				throw new ValidationException("No groupId is set");
			}

			if (empty($userId)) {
				throw new ValidationException("No userId is set");
			}

			$User     = new User();
			$User->id = $userId;
			$User->AddUserToSecurityGroup ([$groupId]);
			$this->ResponseArray->AddData (['ronny' => 'ronny']);

		} catch (PermissionException) {
			$this->ResponseArray->error = "Permission denied!";
		} catch (Exception $e) {
			$this->ResponseArray->error = $e->getMessage ();
		}

		$response->getBody ()->write ($this->ResponseArray->ReturnOutput (true));

		return $response->withHeader ('Content-Type', 'application/json')->withStatus (201);

	}

	function RemoveUserFromGroupAction (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {


		$userId  = $request->getParsedBody ()['userId'] ?? 0;
		$groupId = $request->getParsedBody ()['groupId'] ?? 0;

		try {

			if (!Session::IsGlobalAdmin ()) {
				throw new PermissionException();
			}

			if (empty($groupId)) {
				throw new ValidationException("No groupId is set");
			}

			if (empty($userId)) {
				throw new ValidationException("No userId is set");
			}

			$User     = new User();
			$User->id = $userId;
			$User->RemoveUserFromSecurityGroup ($groupId);

		} catch (PermissionException) {
			$this->ResponseArray->error = "Permission denied!";
		} catch (Exception $e) {
			$this->ResponseArray->error = $e->getMessage ();
		}

		$response->getBody ()->write ($this->ResponseArray->ReturnOutput (true));

		return $response->withHeader ('Content-Type', 'application/json')->withStatus (201);

	}

}
