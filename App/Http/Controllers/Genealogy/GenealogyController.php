<?php
namespace App\Http\Controllers\Genealogy;

use App\Core\BaseController;
use App\Services\Genealogy\GenealogyService;

class GenealogyController extends BaseController {
    private $genealogy_service;

    public function __construct() {
        $this->genealogy_service = new GenealogyService();
    }

    // Renders the main view page
    public function showGenealogyPage() {
        if (isset($_SESSION["is_logged_in"]) && $_SESSION["is_logged_in"] === true) {
            $this->renderView("User/genealogy");
        } else {
            header("Location: /login");
            exit;
        }
    }

    // API Endpoint for the JavaScript Tree
    public function getTreeData() {
        header('Content-Type: application/json');
        $userId = $_SESSION["user_id"] ?? null;

        if (!$userId) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $data = $this->genealogy_service->getFormattedTree($userId);
        echo json_encode($data);
    }

    // API Endpoint for Stats
    public function getStats() {
        header('Content-Type: application/json');
        $userId = $_SESSION["user_id"] ?? null;

        if (!$userId) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $stats = $this->genealogy_service->getNetworkStats($userId);
        echo json_encode($stats);
    }

    // API Endpoint for Breadcrumbs/Upline
    public function getUplineData() {
        header('Content-Type: application/json');
        $userId = $_GET['id'] ?? $_SESSION['user_id'];

        try {
            $upline = $this->genealogy_service->getFullUpline((int)$userId);
            echo json_encode($upline);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}