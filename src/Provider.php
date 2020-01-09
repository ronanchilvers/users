<?php

namespace Ronanchilvers\Users;

use Ronanchilvers\Container\Container;
use Ronanchilvers\Container\ServiceProviderInterface;
use Ronanchilvers\Users\Middleware\AuthenticationMiddleware;

/**
 * Provider for user services
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
class Provider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function register(Container $container)
    {
        $container->share('user.settings', [
            'model' => false,
            'anonymous_routes' => [],
            'login_route' => 'user.login',
            'store_redirect' => true,
        ]);
        $container->share(AuthenticationMiddleware::class, function ($c) {
            $slimSettings = $c->get('settings');
            if (!isset($slimSettings['determineRouteBeforeAppMiddleware']) || !$slimSettings['determineRouteBeforeAppMiddleware']) {
                $slimSettings['determineRouteBeforeAppMiddleware'] = true;
                $c->share('settings', $slimSettings);
            }
            $settings = $c->get('user.settings');
            $options = [
                'anonymous_routes' => $settings['anonymous_routes'] ?? [],
                'login_route' => $settings['login_route'] ?? 'user.login'
            ];

            return new AuthenticationMiddleware(
                $c->get('router'),
                $c->get(Manager::class),
                $options
            );
        });
        $container->share(Manager::class, function ($c) {
            $settings = $c->get('user.settings');
            return new Manager(
                $c->get('session'),
                $settings['model']
            );
        });
    }
}
