<?php

namespace Ronanchilvers\Users;

use Ronanchilvers\Orm\Orm;
use Ronanchilvers\Sessions\Session;
use Ronanchilvers\Users\UserInterface;

/**
 * Manager responsible for managing user logins
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
class Manager
{
    const SESSION_KEY = 'security.session';

    /**
     * @var \Ronanchilvers\Sessions\Session
     */
    protected $session;

    /**
     * The model class to use
     *
     * @var string
     */
    protected $model;

    /**
     * @var Ronanchilvers\Users\UserInterface
     */
    protected $user;

    /**
     * Class constructor
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function __construct(
        Session $session,
        string $model
    ) {
        $this->session = $session;
        $this->model = $model;
    }

    /**
     * Log a user in using an email address and password
     *
     * @param string $email
     * @param string $password
     * @return boolean|\Ronanchilvers\Users\UserInterface $user
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function login($email, $password)
    {
        $model = $this->model;
        $user = Orm::finder($model)->select()
            ->where($model::prefix('email'), $email)
            ->where($model::prefix('status'), UserInterface::ACTIVE)
            ->one();
        if (!$user instanceof UserInterface) {
            return false;
        }
        if (!$user->verify($password)) {
            return false;
        }
        $this->session->set(
            static::SESSION_KEY,
            [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
            ]
        );

        return $user;
    }

    /**
     * Logout the current session
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function logout()
    {
        $this->session->delete(
            static::SESSION_KEY
        );
    }

    /**
     * Is a user logged in?
     *
     * @return boolean
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function hasLogin()
    {
        return $this->session->has(
            static::SESSION_KEY
        );
    }

    /**
     * Refresh the session data
     *
     * @param Ronanchilvers\Users\UserInterface $user
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function refresh(UserInterface $user)
    {
        if (!$this->hasLogin()) {
            return false;
        }
        $session = $this->session->get(
            static::SESSION_KEY
        );
        if ($user->getId() !== $session['id']) {
            return false;
        }
        $session['name'] = $user->getName();
        $session['email'] = $user->getEmail();
        $this->session->set(
            static::SESSION_KEY,
            $session
        );

        return true;
    }

    /**
     * Get the current user id
     *
     * @return integer
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function id()
    {
        if (!$this->hasLogin()) {
            return null;
        }
        $session = $this->session->get(
            static::SESSION_KEY
        );

        return $session['id'];
    }

    /**
     * Get the current logger in email
     *
     * @return null|string
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function name()
    {
        if (!$this->hasLogin()) {
            return null;
        }
        $session = $this->session->get(
            static::SESSION_KEY
        );

        return $session['name'];
    }

    /**
     * Get the current logger in email
     *
     * @return null|string
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function email()
    {
        if (!$this->hasLogin()) {
            return null;
        }
        $session = $this->session->get(
            static::SESSION_KEY
        );

        return $session['email'];
    }

    /**
     * Get the currently logged in user
     *
     * @return null|\Ronanchilvers\Users\UserInterface
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function user()
    {
        if ($this->user instanceof UserInterface) {
            return $this->user;
        }
        if (!$this->hasLogin()) {
            return null;
        }
        $session = $this->session->get(
            static::SESSION_KEY
        );
        $user = Orm::finder($this->model)->one(
            $session['id']
        );
        if ($user instanceof UserInterface) {
            $this->user = $user;

            return $user;
        }

        return null;
    }
}
