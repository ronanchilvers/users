<?php

namespace Ronanchilvers\Users\Middleware;

use Exception;
use Psr\Http\Message\ServerRequestInterface;
use Ronanchilvers\Foundation\Traits\Optionable;
use Ronanchilvers\Users\Manager;
use Slim\Http\Response;
use Slim\Router;

/**
 * Authentication middleware responsible for managing access to protected routes
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
class AuthenticationMiddleware
{
    use Optionable;

    /**
     * @var Slim\Router
     */
    protected $router;

    /**
     * @var Ronanchilvers\Users\Manager
     */
    protected $manager;

    /**
     * Class constructor
     *
     * @param array $anonymouseRoutes
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function __construct(
        Router $router,
        Manager $manager,
        $options = []
    ) {
        $this->router = $router;
        $this->manager = $manager;
        $this->setDefaults([
            'anonymous_routes' => [],
            'login_route' => 'user.login',
        ]);
        $this->setOptions($options);
        $anonymousRoutes = $this->getOption('anonymous_routes', []);
        $loginRoute = $this->getOption('login_route', 'user.login');
        if (!in_array($loginRoute, $anonymousRoutes)) {
            $anonymousRoutes[] = $loginRoute;
            $this->setOption(
                'anonymous_routes',
                $anonymousRoutes
            );
        }
    }

    public function __invoke(ServerRequestInterface $request, Response $response, $next)
    {
        if (is_null($request->getAttribute('route'))) {
            throw new Exception('Request route is null - check HTTP methods and "determineRouteBeforeAppMiddleware"');
        }
        $anonymousRoutes = $this->getOption('anonymous_routes', []);
        if (!in_array($request->getAttribute('route')->getName(), $anonymousRoutes)) {
            if (!$this->manager->hasLogin()) {
                return $response->withRedirect(
                    $this->router->pathFor(
                        $this->getOption('login_route')
                    )
                );
            }
        }

        return $next($request, $response);
    }
}
