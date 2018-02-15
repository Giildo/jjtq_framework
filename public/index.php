<?php

use Core\App;
use DI\ContainerBuilder;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

require_once(dirname(__DIR__) . '/vendor/autoload.php');

// Container initiation
$builder = new ContainerBuilder();
$builder->addDefinitions(dirname(__DIR__) . '/src/App/config/config.php');
$container = $builder->build();

// Twig initiation
$twig = $container->get(Twig_Environment::class);

try {
    $app = $container->get(App::class);

    $app->run();
} catch (Exception | NotFoundExceptionInterface | ContainerExceptionInterface $e) {
    echo $twig->render('error.twig', ['e' => $e]);//Envoyer message sous système de flash
}
