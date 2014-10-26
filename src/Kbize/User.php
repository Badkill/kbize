<?php
namespace Kbize;

interface User
{
    /**
     * @return boolean
     */
    public function isAuthenticated();

    /* public function ensureIsLoggedIn(); */

    /**
     * @return User
     */
    public function update(array $userData);

    /**
     * @return User
     */
    public function logout();

    public function apikey();
}
