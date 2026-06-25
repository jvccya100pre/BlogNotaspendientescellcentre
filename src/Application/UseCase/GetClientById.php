<?php
/**
 * GetClientById Use Case
 * Compatible with PHP 5.2.3
 */
class GetClientById {
    private $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository) {
        $this->clientRepository = $clientRepository;
    }

    /**
     * Execute use case to get a client by ID
     * @param int $id
     * @return Client|null
     */
    public function execute($id) {
        return $this->clientRepository->findById($id);
    }
}
