<?php

namespace Ronanchilvers\Users;

/**
 * Interface for user models
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
interface UserInterface
{
    const ACTIVE   = 'active';
    const INACTIVE = 'inactive';

    /**
     * Get the id for this user
     *
     * @return int
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function getId();

    /**
     * Get the name for this user
     *
     * @return string
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function getName();

    /**
     * Get the email address for this user
     *
     * @return string
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function getEmail();

    /**
     * Verify a user password
     *
     * @param string $password
     * @return boolean
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function verify(string $password);
}
