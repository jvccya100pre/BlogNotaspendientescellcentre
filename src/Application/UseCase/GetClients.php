<?php
/**
 * GetClients Use Case
 * Compatible with PHP 5.2.3
 */
class GetClients {
    private $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository) {
        $this->clientRepository = $clientRepository;
    }

    /**
     * Execute use case to get all active clients
     * @return array Array of Client objects
     */
    public function execute() {
        return $this->clientRepository->findAllActive();
    }
}
