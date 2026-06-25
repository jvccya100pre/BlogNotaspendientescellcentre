<?php
/**
 * Client Repository Interface
 * Compatible with PHP 5.2.3
 */
interface ClientRepositoryInterface {
    /**
     * Find a client by their ID
     * @param int $id
     * @return Client|null
     */
    public function findById($id);

    /**
     * Find a client by their unique identifier
     * @param string $identifier
     * @return Client|null
     */
    public function findByIdentifier($identifier);

    /**
     * Get all active clients
     * @return array Array of Client objects
     */
    public function findAllActive();

    /**
     * Get all active clients created/updated on a specific date (Y-m-d)
     * @param string $dateString
     * @return array Array of Client objects
     */
    public function findAllActiveByDate($dateString);

    /**
     * Save a client record (create or update)
     * @param Client $client
     * @return bool
     */
    public function save(Client $client);

    /**
     * Delete (deactivate) a client record
     * @param int $id
     * @return bool
     */
    public function delete($id);

    /**
     * Get state name by ID
     * @param int $id
     * @return string
     */
    public function getEstadoName($id);

    /**
     * Get municipality name by ID
     * @param int $id
     * @return string
     */
    public function getMunicipioName($id);

    /**
     * Get city name by ID
     * @param int $id
     * @return string
     */
    public function getCiudadName($id);
}
