<?php
/**
 * User Repository Interface
 * Compatible with PHP 5.2.3
 */
interface UserRepositoryInterface {
    /**
     * Find a user by their username
     * @param string $username
     * @return User|null
     */
    public function findByUsername($username);
}
