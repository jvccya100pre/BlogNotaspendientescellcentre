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

        // SHA1 hash verification or plain text dialview password verification
        if (sha1($password) === $user->password || $password === $user->contrasena_dialview) {
            return $user;
        }

        return null;
    }
}
