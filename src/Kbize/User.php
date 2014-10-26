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
     * @return StateUser
     */
    public function update(array $userData);

    /**
     * @return StateUser
     */
    public function logout();

    public function apikey();
}
