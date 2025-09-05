<?php
$page = 'constitution';
require_once 'inc.php';
$template_type = '';
$page_title = 'Constitution' . CONST_TITLE_AFX;
$page_description = 'Constitution Document';
$body_template_file = CONST_THEMES_TEMPLATE_INCLUDE_PATH . 'constitution.tpl';
$body_template_data = array();

function getCurrentDocument() {
    $stmt = \eBizIndia\PDOConn::query("
        SELECT * FROM constitution_docs 
        ORDER BY upload_date DESC 
        LIMIT 1
    ", []);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function saveDocument($filename, $fileType, $fileData) {
    try {
        $stmt = \eBizIndia\PDOConn::query("
            INSERT INTO constitution_docs 
            (filename, file_type, file_data) 
            VALUES (:filename, :file_type, :file_data)
        ", [
            ':filename' => $filename,
            ':file_type' => $fileType,
            ':file_data' => $fileData
        ]);
        return true;
    } catch (\Exception $e) {
        return false;
    }
}

if (filter_has_var(INPUT_POST, 'mode') && $_POST['mode'] == 'upload') {
    $result = ['error_code' => 0, 'message' => ''];
    
    if (!isset($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
        $result = ['error_code' => 1, 'message' => 'Please select a valid file.'];
    } else {
        $fileType = strtolower(pathinfo($_FILES['document']['name'], PATHINFO_EXTENSION));
        $allowedTypes = ['doc', 'docx', 'pdf'];
        
        if (!in_array($fileType, $allowedTypes)) {
            $result = ['error_code' => 1, 'message' => 'Only .doc, .docx, and .pdf files are allowed.'];
        } else {
            $fileData = file_get_contents($_FILES['document']['tmp_name']);
            if (saveDocument($_FILES['document']['name'], $fileType, $fileData)) {
                $result['message'] = 'Document uploaded successfully.';
            } else {
                $result = ['error_code' => 1, 'message' => 'Error uploading document.'];
            }
        }
    }
    
    $_SESSION['upload_result'] = $result;
    header("Location: constitution.php");
    exit;
}

if (filter_has_var(INPUT_GET, 'download')) {
    $document = getCurrentDocument();
    if ($document) {
        header('Content-Type: application/' . $document['file_type']);
        header('Content-Disposition: inline; filename="' . $document['filename'] . '"');
        echo $document['file_data'];
        exit;
    }
}

$body_template_data['current_document'] = getCurrentDocument();

if (isset($_SESSION['upload_result'])) {
    $body_template_data['upload_result'] = $_SESSION['upload_result'];
    unset($_SESSION['upload_result']);
}

$page_renderer->registerBodyTemplate($body_template_file, $body_template_data);
$page_renderer->renderPage();
?>