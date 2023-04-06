<?php

namespace App;

use DD\Helper\Strings;
use DD\SystemType;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * View (CORE)
 *
 */
class View
{

	/**
	 * Render a view template using Twig
	 *
	 * @param string $template The template file
	 * @param array $content Associative array of data to display in the view (optional)
	 * @return void
	 * @throws LoaderError
	 * @throws RuntimeError
	 * @throws SyntaxError
	 */
	public static function RenderTemplate (string $template, array $content = []): void {

		static $twig = null;

		static $debug = SYSTEMTYPE == SystemType::DEV || SYSTEMTYPE == SystemType::TEST;

		if ($twig === null) {
			$loader       = new FilesystemLoader('../App/View');
			$optionsArray = $debug ? ['debug' => true] : [];
			$twig         = new Environment($loader, $optionsArray ?? []);
			$twig->addFunction (new TwigFunction('asset', function($asset) {

				// implement whatever logic you need to determine the asset path
				return sprintf ('%s', trim ($asset));
			}));
			$twig->addFilter (new TwigFilter ('phone', function($num) {

				$num = preg_replace ('/[^\d+]/', '', $num);

				$num = Strings::FormatTelephoneNumber ($num);

				return str_starts_with ($num, "+") ? substr ($num, 0, 3).' '.substr ($num, 3, 3).' '.substr ($num, 6) : $num;
			}));
			if ($debug) {
				$twig->addExtension (new DebugExtension());
			}
		}

		echo $twig->render ($template, $content);

	}
}
