<?php

namespace App\Core;

use DD\SystemType;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

/**
 * View (CORE)
 */
class View {

	/**
	 * Render a view template using Twig
	 *
	 * @param string $template The template file
	 * @param array  $context  Associative array of data to display in the view (optional)
	 * @param bool   $viaAPI
	 *
	 * @return string
	 * @throws LoaderError
	 * @throws RuntimeError
	 * @throws SyntaxError
	 */
	public static function RenderTemplate (string $template, array $context = []): string {

		static $twig = null;

		if ($twig === null) {
			$path         = __DIR__ . '/../../App/View';
			$loader       = new FilesystemLoader($path);
			$optionsArray = SYSTEMTYPE != SystemType::PROD ? ['debug' => true] : [];
			$twig         = new Environment($loader, $optionsArray);
			$twig->addFunction (
				new TwigFunction('asset', function ($asset) {
					// implement whatever logic you need to determine the asset path
					return sprintf ('%s', trim ($asset));
				}));
			/*$twig->addFilter (
				new TwigFilter ('phone', function ($num) {
					$num = preg_replace ('/[^\d+]/', '', $num);

					$num = Strings::FormatTelephoneNumber ($num);
					return str_starts_with ($num, "+") ? substr ($num, 0, 3) . ' ' . substr ($num, 3, 3) . ' ' . substr ($num, 6) : $num;
				}));
			$twig->addFilter (
				new TwigFilter ('removeTagsAndScripts', function ($text) {
					$text = strip_tags ($text);
					// Entfernen von JavaScript
					return preg_replace ('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/', '', $text);
				}));
			$twig->addFilter (
				new TwigFilter ('formatMinutes2Clock', function ($minutes) {
					if ($minutes == 0) {
						return '0:00 h';
					}
					$hours = floor ($minutes / 60);
					$mins  = $minutes % 60;
					return sprintf ('%d:%02d h', $hours, $mins);
				}));
			$twig->addFilter (
				new TwigFilter ('formatMinutes2ClockShort', function ($minutes) {
					if ($minutes == 0) {
						return '0:00';
					}
					$hours = floor ($minutes / 60);
					$mins  = $minutes % 60;
					return sprintf ('%d:%02d', $hours, $mins);
				}));
			$twig->addFilter (
				new TwigFilter('makeMoney', function ($string) {
					if (is_numeric ($string)) {
						return number_format ((float)$string, 2, ',', '.');
					}
					return $string;
				})
			);*/
			if (SYSTEMTYPE != SystemType::PROD) {
				$twig->addExtension (new DebugExtension());
				$context['SESSION'] = $_SESSION;
			}

		}

		return $twig->render ($template, $context);

	}
}
