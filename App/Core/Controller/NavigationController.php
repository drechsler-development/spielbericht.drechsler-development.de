<?php

namespace App\Core\Controller;

use App\Core\ErrorHandler;
use App\Core\View;
use App\Model\Helper\Session;
use App\Model\Navigation\Area;
use App\Model\Navigation\Group;
use App\Model\System\Icon;
use DD\Exceptions\PermissionException;
use DD\Exceptions\ValidationException;
use DD\Helper\Date;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class NavigationController extends BackendController {

	CONST TYPE_GROUP = 'group';
	CONST TYPE_AREA  = 'area';

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

				'PAGE_TITLE'       => 'Navigation',
				'PAGE_DESCRIPTION' => 'Administration der Navigationsmenüstruktur',
			];

			$body = View::RenderTemplate ('Admin/Navigation/list.twig', array_merge ($data, $this->globalData));

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

			$Areas = new Area();
			$data['rows'] = $Areas->Load ();

			$this->ResponseArray->data = View::RenderTemplate ('Admin/Navigation/lines.twig', array_merge ($data, $this->globalData));

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

		$id   = $request->getParsedBody ()['id'] ?? 0;
		$type = $request->getParsedBody ()['type'] ?? '';

		try {

			if (!Session::IsGlobalAdmin ()) {
				throw new PermissionException("Du bist kein Admin!!! Schäm dich! Also, ab in die Ecke! JETZT!!!!");
			}

			if (empty($type)) {
				throw new ValidationException("Kein Typ wurde übermittelt");
			} else if ($type !== self::TYPE_AREA && $type !== self::TYPE_GROUP) {
				throw new ValidationException("Es wurde ein ungültiger Typ übermittelt");
			}

			if ($type == self::TYPE_GROUP) {

				$Groups     = new Group();
				$Groups->id = $id;
				$data['row'] = $id ? $Groups->Load ()[0] : [];

				$Icons = new Icon();
				$data['icons'] = $Icons->Load ();

				$this->ResponseArray->data = View::RenderTemplate ('Admin/Navigation/Group/form.twig', array_merge ($data, $this->globalData));

			} else {

				$Areas     = new Area();
				$Areas->id = $id;
				$Areas->Load ();

				$data['row'] = $id ? $Areas->Load ()[0] : [];

				$Groups = new Group();
				$data['groups'] = $Groups->Load ();

				$Icons         = new Icon();
				$data['icons'] = $Icons->Load ();

				$this->ResponseArray->data = View::RenderTemplate ('Admin/Navigation/Area/form.twig', array_merge ($data, $this->globalData));
			}

		} catch (Exception $e) {
			$this->ResponseArray->error = ErrorHandler::HandleErrorMessage ($e);
		}

		$response->getBody ()->write ($this->ResponseArray->ReturnOutput (true));

		return $response->withHeader ('Content-Type', 'application/json')->withStatus (201);
	}

	function SaveAction (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {


		$type = $request->getParsedBody ()['type'] ?? '';

		$id   = $request->getParsedBody ()['id'] ?? 0;
		$name = $request->getParsedBody ()['name'] ?? '';
		$icon = $request->getParsedBody ()['icon'] ?? '';
		$pos  = $request->getParsedBody ()['pos'] ?? 0;

		try {

			if (empty($type)) {
				throw new ValidationException("Keinen Typ übergeben");
			}

			if (empty($name)) {
				throw new ValidationException("Keinen Namen übergeben");
			}

			if ($type == "area") {

				$path             = $request->getParsedBody ()['path'] ?? '';
				$description      = $request->getParsedBody ()['description'] ?? '';
				$groupId          = $request->getParsedBody ()['groupId'] ?? 0;
				$showForAdmin     = $request->getParsedBody ()['showForAdmin'] ?? 0;
				$showForAdminOnly = $request->getParsedBody ()['showForAdminOnly'] ?? 0;
				$videoLink        = $request->getParsedBody ()['videoLink'] ?? null;
				$videoDate        = $request->getParsedBody ()['videoDate'] ?? null;

				if (empty($path)) {
					throw new ValidationException("Keinen Pfad übergeben");
				}

				if (empty($description)) {
					throw new ValidationException("Keine Beschreibung übergeben");
				}

				if (empty($groupId)) {
					throw new ValidationException("Keine GruppenId übergeben");
				}

				$Areas = new Area();
				if (empty($id)) {
					$Areas->Create ();
				} else {
					$Areas->id = $id;
				}

				$array = [
					'gruppen_id'   => $groupId,
					'name'         => $name,
					'beschreibung' => $description,
					'ordner'       => $path,
					'pos'          => $pos,
					'admin'        => $showForAdmin,
					'admin_only'   => $showForAdminOnly,
					'icon'         => $icon,
					'videoLink'    => $videoLink,
					'videoDate'    => $videoDate ? Date::FormatDateToFormat ($videoDate, Date::DATE_FORMAT_SQL_DATE) : null,
				];

				$this->ResponseArray->debug = print_r ($array, true);

				$Areas->Update ($array);

			} else if ($type == "group") {

				$Group = new Group();
				if (empty($id)) {
					$Group->Create ();
				} else {
					$Group->id = $id;
				}

				$array = [
					'name' => $name,
					'pos'  => $pos,
					'icon' => $icon,
				];

				$Group->Update ($array);

			} else {
				throw new ValidationException("Unbekannter Typ übergeben");
			}

		} catch (Exception $e) {
			$this->ResponseArray->error = $e->getMessage ();
		}

		$response->getBody ()->write ($this->ResponseArray->ReturnOutput (true));

		return $response->withHeader ('Content-Type', 'application/json')->withStatus (201);

	}

	function DeleteAction (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {


		$id   = $request->getParsedBody ()['id'] ?? 0;
		$type = $request->getParsedBody ()['type'] ?? 0;

		try {

			if (empty($type)) {
				throw new ValidationException("Keinen Typ übergeben");
			}

			if (empty($id)) {
				throw new ValidationException("Keine ID übergeben");
			}

			if ($type == "area") {

				$Areas     = new Area();
				$Areas->id = $id;

				$Areas->Delete ();

			} else if ($type == "group") {

				$Group     = new Group();
				$Group->id = $id;

				$Group->Delete ();

			} else {
				throw new ValidationException("Unbekannter Typ übergeben");
			}

		} catch (Exception $e) {
			$this->ResponseArray->error = $e->getMessage ();
		}

		$response->getBody ()->write ($this->ResponseArray->ReturnOutput (true));

		return $response->withHeader ('Content-Type', 'application/json')->withStatus (201);

	}

}
