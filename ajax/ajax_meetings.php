<?php
require_once 'inc.php';
require_once 'eBizIndia/Meetings.php';
require_once 'scriptProviderFuncs.php';

global $page_renderer;

$page_renderer->registerBodyTemplate('meetings.tpl');
$page_renderer->addScript('meetings.js');

header('Content-Type: application/json');

if (!isset($loggedindata[0]['id'])) {
    echo json_encode(["error" => "Unauthorized access"]);
    exit;
}

$pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
]);

$meetings = new eBizIndia\Meetings($pdo, $loggedindata[0]['id']);
$action = $_POST['action'] ?? '';
$response = [];

switch ($action) {
    case 'getMeetings':
        $response = $meetings->getAllMeetings();
        break;
    default:
        $response = ["error" => "Invalid action"];
}

echo json_encode($response);
exit;
?>