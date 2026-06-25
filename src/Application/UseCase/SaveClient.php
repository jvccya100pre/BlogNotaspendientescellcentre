<?php
/**
 * SaveClient Use Case
 * Compatible with PHP 5.2.3
 */
class SaveClient {
    private $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository) {
        $this->clientRepository = $clientRepository;
    }

    /**
     * Execute save client
     * @param Client $client
     * @return array|bool Returns array of validation errors, or true if successfully saved
     */
    public function execute(Client $client) {
        // Run validations first
        $errors = $client->validate();
        if (!empty($errors)) {
            return $errors;
        }

        $now = date('Y-m-d H:i:s');
        $client->fecha_actualizacion = $now;

        if (empty($client->id)) {
            // New client
            $client->fecha_creacion = $now;
            // Generate unique identifier
            $client->identificador_unico = $this->generateUniqueId();
            $client->estado = 1; // Default to active
        } else {
            // Updating existing client
            $existing = $this->clientRepository->findById($client->id);
            if ($existing === null) {
                return array('global' => 'El cliente a modificar no existe.');
            }
            // Keep original unique identifier and creation date
            $client->identificador_unico = $existing->identificador_unico;
            $client->fecha_creacion = $existing->fecha_creacion;
            $client->estado = $existing->estado;
        }

        $saved = $this->clientRepository->save($client);
        if ($saved) {
            return true;
        } else {
            return array('global' => 'Error al guardar el cliente en la base de datos.');
        }
    }

    /**
     * Helper to generate a unique random identifier CLI-XXXXXX
     * @return string
     */
    private function generateUniqueId() {
        do {
            $randHex = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
            $identifier = 'CLI-' . $randHex;
            $exists = $this->clientRepository->findByIdentifier($identifier);
        } while ($exists !== null);

        return $identifier;
    }
}
