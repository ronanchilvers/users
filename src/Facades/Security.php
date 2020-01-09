<?php

namespace Ronanchilvers\Users\Facades;

use Ronanchilvers\Foundation\Facade\Facade;
use Ronanchilvers\Users\Manager;

/**
 * Security manager facade class
 *
 * @method static bool|Ronanchilvers\Users\UserInterface login(string $email, string $password)
 * @method static void logout()
 * @method static bool hasLogin()
 * @method static void refresh(User $user)
 * @method static int id()
 * @method static string name()
 * @method static string email()
 * @method static Ronanchilvers\Users\UserInterface user()
 * @author Ronan Chilvers <ronan@d3r.com>
 */
class Security extends Facade
{
    /**
     * @var string
     */
    protected static $serviceName = Manager::class;
}
