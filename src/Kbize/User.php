<?php
namespace Kbize;

interface User
{
    /**
     * @return boolean
     */
    public function isAuthenticated();

    /* public function ensureIsLoggedIn(); */

    public function update(array $userData);

    public function logout();

    public function apikey();
}
