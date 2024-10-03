<?php

/**
 * Routes configuration.
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * It's loaded within the context of `Application::routes()` method which
 * receives a `RouteBuilder` instance `$routes` as method argument.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;


return static function (RouteBuilder $routes) {

    $routes->setRouteClass(DashedRoute::class);

    $routes->scope('/', function (RouteBuilder $builder) {



        $builder->connect('/', ['controller' => 'Pages', 'action' => 'home']);
        $builder->connect('/a-propos-de-nous.htm', ['controller' => 'Pages', 'action' => 'display', 'a-propos-de-nous']);
        $builder->connect('/la-carte.htm', ['controller' => 'Products', 'action' => 'index']);
        $builder->connect('/carte/{id}-{slug}.htm', ['controller' => 'Products', 'action' => 'view'])->setPass(['id', 'slug'])->setPatterns([
            'id' => '[0-9]+',
        ]);
        $builder->connect('/cgv.htm', ['controller' => 'Pages', 'action' => 'display', 'cgv']);
        $builder->connect('/politique-de-confidentialite.htm', ['controller' => 'Pages', 'action' => 'display', 'privacy-policy']);
        $builder->connect('/sitemap.xml',['controller'=>'Pages','action'=>'sitemap']);


        $builder->connect('/pages/*', 'Pages::display');

        // CHECKOUT
        $builder->connect('/checkout/billing/{id}/{cart_uuid}', ['controller' => 'Orders', 'action' => 'billing'])->setPass(['id', 'cart_uuid'])->setPatterns([
            'id' => '[0-9]+',
        ]);

        //$builder->connect('/api/contracts/{id}', ['controller' => 'Api', 'action' => 'subContracts'], ['_name' => 'apiSubContracts'])->setPass(['id'])->setExtensions(['json']);

        $builder->fallbacks();
    });

    $routes->prefix('v1', function (RouteBuilder $routes) {
        $routes->setExtensions(['json']);

        $routes->resources('Articles', function (RouteBuilder $routes){
            $routes->resources('Comments', ['prefix' => 'Articles']);
        });

        $routes->resources('Recipes', function (RouteBuilder $routes) {
            $routes->resources('Comments');
        });
        $routes->connect('/v1/login/test', ['controller' => 'ManageConnection', 'action' => 'test']);

        $routes->connect('/login', ['controller' => 'ManageConnection', 'action' => 'login']);
        $routes->connect('/logout', ['controller' => 'Users', 'action' => 'logout']);
    });


    $routes->prefix('Admin', function (RouteBuilder $builder) {
        // Toutes les routes ici seront préfixées avec `/admin`, et
        // l'élément de route `'prefix' => 'Admin'` sera ajouté qui
        // sera requis lors de la génération d'URL pour ces routes

        //$routes->connect('/subscriptions/{action}/*', ['controller' => 'Subscriptions']);



        $builder->fallbacks(DashedRoute::class);
    });

};
