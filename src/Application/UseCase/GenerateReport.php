<?php
/**
 * GenerateReport Use Case
 * Compatible with PHP 5.2.3
 */
class GenerateReport {
    private $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository) {
        $this->clientRepository = $clientRepository;
    }

    /**
     * Execute report generation
     * @param string $dateString Date in Y-m-d format
     * @return array Array with 'filename' and 'content' keys
     */
    public function execute($dateString) {
        $clients = $this->clientRepository->findAllActiveByDate($dateString);
        
        $content = "REPORTE DE CLIENTES PENDIENTES - FECHA: " . $dateString . "\n";
        $content .= "Generado el: " . date('Y-m-d H:i:s') . "\n";
        $content .= "======================================================================\n\n";

        if (empty($clients)) {
            $content .= "No se encontraron llamadas registradas en esta fecha.\n";
        } else {
            foreach ($clients as $client) {
                $content .= "Nombre: " . $client->nombre . "\n";
                $content .= "Número de teléfono: " . $client->telefono . "\n";
                
                // Fetch location names using the repository helper
                $estName = $this->clientRepository->getEstadoName($client->estado_id);
                $muniName = $this->clientRepository->getMunicipioName($client->municipio_id);
                $cityName = $this->clientRepository->getCiudadName($client->ciudad_id);
                $locationStr = implode(', ', array_filter(array($estName, $muniName, $cityName)));
                if ($locationStr !== '') {
                    $content .= "Ubicación: " . $locationStr . "\n";
                }
                
                $content .= "Dirección: " . $client->direccion . "\n";
                
                if ($client->archivo_adjunto) {
                    $content .= "Archivo Adjunto: " . $client->archivo_adjunto . "\n";
                }
                
                $content .= "Estado de llamada: " . $client->estado_llamada . "\n";
                $content .= "Observación: " . ($client->observacion !== '' ? $client->observacion : 'Ninguna') . "\n";
                $content .= "Llamar en (Horas): " . ($client->lapso_tiempo !== '' ? $client->lapso_tiempo : 'Ninguno') . "\n";
                $content .= "Llamar en (Días): " . ($client->lapso_dias !== '' ? $client->lapso_dias : 'Ninguno') . "\n";
                $content .= "Fecha_creacion: " . $client->fecha_creacion . "\n";
                $content .= "Fecha_actualizacion: " . $client->fecha_actualizacion . "\n";
                $content .= "----------------------------------------------------------------------\n\n";
            }
        }

        // Parse day, month, and year for the filename
        $timestamp = strtotime($dateString);
        if (!$timestamp) {
            $timestamp = time();
        }
        
        $day = date('j', $timestamp);
        $monthNum = (int)date('n', $timestamp);
        $year = date('Y', $timestamp);
        
        $months = array(
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        );
        
        $monthName = isset($months[$monthNum]) ? $months[$monthNum] : 'Mes';
        
        // Use current time for the download hour
        $hourMin = date('H_i');
        
        // Filename format: Día Mes año - Hora.txt (e.g. 23 Junio 2026 - 15_30.txt)
        $filename = $day . ' ' . $monthName . ' ' . $year . ' - ' . $hourMin . '.txt';

        return array(
            'filename' => $filename,
            'content' => $content
        );
    }
}
