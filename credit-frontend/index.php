<?php
// index.php — front controller

require_once 'config.php';
require_once 'ApiClient.php';
require_once 'models/ScoringGatewayClient.php';
require_once 'controllers/ScoreController.php';

$controller = new ScoreController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->submitForm($_POST);
} else {
    $controller->showForm();
}
