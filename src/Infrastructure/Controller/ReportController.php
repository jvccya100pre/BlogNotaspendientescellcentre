<?php
/**
 * ReportController
 * Compatible with PHP 5.2.3
 */
class ReportController {
    private $generateReportUseCase;

    public function __construct() {
        $clientRepository = new MysqlClientRepository();
        $this->generateReportUseCase = new GenerateReport($clientRepository);
    }

    /**
     * Enforce user authentication
     */
    private function checkAuth() {
        if (!isset($_SESSION['user'])) {
            header('Location: ./login');
            exit();
        }
    }

    /**
     * Generate and trigger plain text report download
     */
    public function download() {
        $this->checkAuth();
        
        $dateString = isset($_GET['date']) ? trim($_GET['date']) : '';
        
        // Basic validation of date format Y-m-d
        if (empty($dateString) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateString)) {
            $dateString = date('Y-m-d');
        }
        
        $result = $this->generateReportUseCase->execute($dateString);
        
        // Send headers for file download
        header('Content-Type: text/plain; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $result['filename'] . '"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        echo $result['content'];
        exit();
    }
}
