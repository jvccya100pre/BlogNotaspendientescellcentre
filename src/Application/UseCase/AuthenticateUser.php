<?php
/**
 * AuthenticateUser Use Case
 * Compatible with PHP 5.2.3
 */
class AuthenticateUser {
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository) {
        $this->userRepository = $userRepository;
    }

    /**
     * Execute user authentication
     * @param string $username
     * @param string $password Plain text password
     * @return User|null
     */
    public function execute($username, $password) {
        $user = $this->userRepository->findByUsername($username);
        if ($user === null) {
            return null;
        }

        // SHA1 hash verification (compatible with our seed in db.sql)
        if (sha1($password) === $user->password) {
            return $user;
        }

        return null;
    }
}
