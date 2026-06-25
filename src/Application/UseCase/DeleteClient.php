<?php
/**
 * DeleteClient Use Case
 * Compatible with PHP 5.2.3
 */
class DeleteClient {
    private $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository) {
        $this->clientRepository = $clientRepository;
    }

    /**
     * Execute logical deletion (deactivation) of a client
     * @param int $id
     * @return bool
     */
    public function execute($id) {
        return $this->clientRepository->delete($id);
    }
}
