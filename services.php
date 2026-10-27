<?php

//use App\Core\QRCode\QRCodeService;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$services                    = [];
$services[App::class]        = DI\factory ([
	                                           AppFactory::class,
	                                           'createFromContainer'
                                           ]);
$services['twig.directory']  = __DIR__ . '/App/View';
$services['twig.fileLoader'] = function (ContainerInterface $container) {

	return new FilesystemLoader($container->get ('twig.directory'));
};
$services['twig.engine']     = function (ContainerInterface $container) {
	$loader = $container->get ('twig.fileLoader');

	$twig = new Environment($loader, ['cache' => false]);

	//$twig->addGlobal ('qr', $container->get (QRCodeService::class));

	return $twig;
};
$services['logger']          = function (ContainerInterface $container) {

	$logger       = new Logger('my_logger');
	$file_handler = new StreamHandler(__DIR__ . '/logs/app.log');
	$logger->pushHandler ($file_handler);

	return $logger;
};

return $services;
