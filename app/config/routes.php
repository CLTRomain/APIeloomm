<?php
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

$routes->scope('/', function (RouteBuilder $routes): void {
    $routes->connect('/', ['controller' => 'Recipes', 'action' => 'index']);

    $routes->setExtensions(['json']);
    $routes->resources('Recipes');
});

$routes->scope('/api', function (RouteBuilder $routes) {
    $routes->setExtensions(['json']); // Autorise l'utilisation de l'extension .json
    $routes->connect('/test', ['controller' => 'Api', 'action' => 'test']); // Route pour l'API de test
});