<?php
declare(strict_types=1);

namespace App;

use Cake\Core\Configure;
use Cake\Core\ContainerInterface;
use Cake\Datasource\FactoryLocator;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\BaseApplication;
use Cake\Http\Middleware\BodyParserMiddleware;
use Cake\Http\Middleware\CsrfProtectionMiddleware;
use Cake\Http\MiddlewareQueue;
use Cake\ORM\Locator\TableLocator;
use Cake\Routing\Middleware\AssetMiddleware;
use Cake\Routing\Middleware\RoutingMiddleware;
use Authentication\AuthenticationService;
use Authentication\AuthenticationServiceInterface;
use Authentication\AuthenticationServiceProviderInterface;
use Authentication\Identifier\AbstractIdentifier;
use Authentication\Identifier\IdentifierInterface;
use Authentication\Middleware\AuthenticationMiddleware;
use Cake\Routing\Router;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Application setup class.
 *
 * This defines the bootstrapping logic and middleware layers you
 * want to use in your application.
 *
 * @extends \Cake\Http\BaseApplication<\App\Application>
 */
class Application extends BaseApplication implements AuthenticationServiceProviderInterface
{
  
    public function bootstrap(): void
{
    parent::bootstrap();

    $this->addPlugin('Authentication');
}

public function beforeFilter(\Cake\Event\EventInterface $event)
{
    parent::beforeFilter($event);
    
    // Assure-toi que l'authentification est bien initialisée
    $this->loadComponent('Authentication.Authentication');
}

public function getAuthenticationService(ServerRequestInterface $request): AuthenticationServiceInterface
{
    $service = new AuthenticationService();

    // Define where users should be redirected to when they are not authenticated
    $service->setConfig([
        'unauthenticatedRedirect' => Router::url([
                'prefix' => false,
                'plugin' => null,
                'controller' => 'Users',
                'action' => 'login',
        ]),
        'queryParam' => 'redirect',
    ]);

    $fields = [
        AbstractIdentifier::CREDENTIAL_USERNAME => 'email',
        AbstractIdentifier::CREDENTIAL_PASSWORD => 'password'
    ];
    // Load the authenticators. Session should be first.
    $service->loadAuthenticator('Authentication.Session');
    $service->loadAuthenticator('Authentication.Form', [
        'fields' => $fields,
        'loginUrl' => Router::url([
            'prefix' => false,
            'plugin' => null,
            'controller' => 'Users',
            'action' => 'login',
        ]),
    ]);

    // Load identifiers
    $service->loadIdentifier('Authentication.Password', compact('fields'));

    return $service;
}
public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
{
    $middlewareQueue->add(new ErrorHandlerMiddleware(Configure::read('Error')))
        // Other middleware that CakePHP provides.
        ->add(new AssetMiddleware())
        ->add(new RoutingMiddleware($this))
        ->add(new BodyParserMiddleware())

        // Add the AuthenticationMiddleware. It should be
        // after routing and body parser.
        ->add(new AuthenticationMiddleware($this));

    return $middlewareQueue;
}

    public function services(ContainerInterface $container): void
    {
    }

}