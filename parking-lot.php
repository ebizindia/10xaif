<?php
$page = 'parking-lot';
require_once 'inc.php';
$template_type = '';
$page_title = 'Parking Lot' . CONST_TITLE_AFX;
$page_description = 'Parking Lot Information';
$body_template_file = CONST_THEMES_TEMPLATE_INCLUDE_PATH . 'parking-lot.tpl';
$body_template_data = array();
$error = '';
$success = '';
$parkingEntries = [];


// Handle form submission
/*
if (filter_has_var(INPUT_POST, 'mode')) {
    $mode = $_POST['mode'];
    $conn = \eBizIndia\PDOConn::getInstance();

    try {
        if ($mode == 'saveParkingLot') {
            $result = ['error_code' => 0, 'message' => '', 'other_data' => []];
            $dates = $_POST['date'] ?? [];
            $members = $_POST['members'] ?? [];
            $notes = $_POST['notes'] ?? [];
            $submitted_by = $loggedindata[0]['id'];
            $submitted_date = date('Y-m-d H:i:s');

            if (!empty($dates) && !empty($members)) {
                $conn->beginTransaction();

                foreach ($members as $index => $member) {
                    $note = trim($notes[$index] ?? '');
                    $date = trim($dates[$index] ?? '');

                    // Check if a record with the same date & member exists
                    $stmt = $conn->prepare("SELECT id, description FROM parking_lot WHERE `date` = :date AND `name` = :name");
                    $stmt->execute([':date' => $date, ':name' => $member]);
                    $existingRecord = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($existingRecord) {
                        $existingDescription = trim($existingRecord['description']);
                        $existingParts = array_map('trim', explode('|', $existingDescription));
                        $newParts = array_map('trim', explode('|', $note));

                        // **1️⃣ Updating an existing record → Replace existing text instead of appending**
                        foreach ($newParts as $newPart) {
                            foreach ($existingParts as &$existingPart) {
                                if (strpos($existingDescription, $newPart) !== false) {
                                    $existingPart = $newPart; // Replace only changed text
                                }
                            }
                        }

                        // **2️⃣ Append new descriptions if they don't exist**
                        foreach ($newParts as $newPart) {
                            if (!in_array($newPart, $existingParts)) {
                                $existingParts[] = $newPart;
                            }
                        }

                        // Ensure unique and clean descriptions
                        $finalDescription = implode(' | ', array_unique($existingParts));

                        // **Only update if the description changed**
                        if ($finalDescription !== $existingDescription) {
                            $updateStmt = $conn->prepare("
                                UPDATE parking_lot 
                                SET `description` = :description, 
                                    `submitted_by` = :submitted_by, 
                                    `submitted_date` = :submitted_date
                                WHERE `id` = :id
                            ");
                            $updateStmt->execute([
                                ':description' => $finalDescription,
                                ':submitted_by' => $submitted_by,
                                ':submitted_date' => $submitted_date,
                                ':id' => $existingRecord['id']
                            ]);
                        }
                    } else {
                        // Insert a new record if it doesn't exist
                        $insertStmt = $conn->prepare("
                            INSERT INTO parking_lot (`date`, `name`, `description`, `submitted_by`, `submitted_date`)
                            VALUES (:date, :name, :description, :submitted_by, :submitted_date)
                        ");
                        $insertStmt->execute([
                            ':date' => $date,
                            ':name' => $member,
                            ':description' => $note,
                            ':submitted_by' => $submitted_by,
                            ':submitted_date' => $submitted_date
                        ]);
                    }
                }

                $conn->commit();
                $result['message'] = 'Parking lot note successfully saved!';
            }

        } elseif ($mode == 'deleteParkingLot' && isset($_POST['id'])) {
            // Deletion logic
            $id = $_POST['id'];
            $stmt = $conn->prepare("DELETE FROM parking_lot WHERE id = :id");
            $stmt->execute([':id' => $id]);

            $result = ['error_code' => 0, 'message' => 'Record deleted successfully!'];
            echo json_encode($result);
            exit;
        }
    } catch (\Exception $e) {
        //print_r($e);
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        $result = ['error_code' => 1, 'message' => 'Error processing request'];
        echo json_encode($result);
        exit;
    }

    $_SESSION['save_parking_lot_result'] = $result;
    header("Location: parking-lot.php");
    exit;
}

*/




if (filter_has_var(INPUT_POST, 'mode')) {
    $mode = $_POST['mode'];
    $conn = \eBizIndia\PDOConn::getInstance();

    try {
        if ($mode == 'saveParkingLot') {
            // Saving logic
            $result = ['error_code' => 0, 'message' => '', 'other_data' => []];
            $dates = $_POST['date'] ?? [];
            $members = $_POST['members'] ?? [];
            $notes = $_POST['notes'] ?? [];
            $submitted_by = $loggedindata[0]['id'];
            $submitted_date = date('Y-m-d H:i:s');

            if (!empty($dates) && !empty($members)) {
                $conn->beginTransaction();
                foreach ($members as $index => $member) {
                    $note = $notes[$index] ?? '';
                    $date = $dates[$index] ?? '';

                    $stmt = \eBizIndia\PDOConn::query("
                        INSERT INTO parking_lot (`date`, `name`, `description`, `submitted_by`, `submitted_date`)
                        VALUES (:date, :name, :description, :submitted_by, :submitted_date)
                        ON DUPLICATE KEY UPDATE 
                            `description` = VALUES(`description`),
                            `submitted_by` = VALUES(`submitted_by`),
                            `submitted_date` = VALUES(`submitted_date`)
                    ", [
                        ':date' => $date,
                        ':name' => $member,
                        ':description' => $note,
                        ':submitted_by' => $submitted_by,
                        ':submitted_date' => $submitted_date,
                    ]);
                }
                $conn->commit();
                $result['message'] = 'Parking lot note successfully saved!';
            }

        } elseif ($mode == 'deleteParkingLot' && isset($_POST['id'])) {
            // Deletion logic
            $id = $_POST['id'];
            $stmt = $conn->prepare("DELETE FROM parking_lot WHERE id = :id");
            $stmt->execute([':id' => $id]);

            $result = ['error_code' => 0, 'message' => 'Record deleted successfully!'];
            echo json_encode($result);
            exit;
        }
    } catch (\Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        $result = ['error_code' => 1, 'message' => 'Error processing request'];
        echo json_encode($result);
        exit;
    }

    $_SESSION['save_parking_lot_result'] = $result;
    header("Location: parking-lot.php");
    exit;
}


/*

if (filter_has_var(INPUT_POST, 'mode') && $_POST['mode'] == 'saveParkingLot') {

    $result = ['error_code' => 0, 'message' => '', 'other_data' => []];
     $conn = \eBizIndia\PDOConn::getInstance();
    
    try {
        

        $dates = $_POST['date'] ?? [];
        $members = $_POST['members'] ?? [];
        $notes = $_POST['notes'] ?? [];
        $submitted_by = $loggedindata[0]['id'];
        $submitted_date = date('Y-m-d H:i:s');

        if (!empty($dates) && !empty($members)) {
           
            $conn->beginTransaction();
            foreach ($members as $index => $member) {

                $note = $notes[$index] ?? '';
                $date=$dates[$index] ?? '';
               
                $stmt = \eBizIndia\PDOConn::query("INSERT INTO parking_lot (`date`, `name`, `description`, `submitted_by`, `submitted_date`) VALUES (:date, :name, :description, :submitted_by, :submitted_date)  
                    ON DUPLICATE KEY UPDATE 
                        `description` = VALUES(`description`),
                        `submitted_by` = VALUES(`submitted_by`),
                        `submitted_date` = VALUES(`submitted_date`)
                ", [
                    ':date' => $date,
                    ':name' => $member,
                    ':description' => $note,
                    ':submitted_by' => $submitted_by,
                    ':submitted_date' => $submitted_date,
                ]);
               
            }
            $conn->commit();
            $result['message'] = 'Parking lot entries successfully saved!';
        } 



    } catch (\Exception $e) {
      
        if ($conn && $conn->inTransaction()) {
            $conn->rollBack();
        }
        if(!is_a($e, '\PDOException'))
            \eBizIndia\ErrorHandler::logError(['exception_msg: '.$e->getMessage()], $e);
        $result['error_code']=1;
        $result['message']="Error saving parking lot data";
    }
    
   
    $_SESSION['save_parking_lot_result'] = $result;

    header("Location: parking-lot.php");
    exit;
}

*/

function getParkingLotData() {
    try {
        // Get main reflection data
        $stmt = \eBizIndia\PDOConn::query("
            SELECT * FROM parking_lot ORDER BY `date`");
        
        $mainParkinglot = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if(empty($mainParkinglot))
        {
           $mainParkinglot=[];
        }
        return $mainParkinglot;

    } catch (\Exception $e) {
        if(!is_a($e, '\PDOException'))
            \eBizIndia\ErrorHandler::logError(['Function: '.__FUNCTION__, 'exception_msg: '.$e->getMessage()], $e);
        return false;
    }
}


/*
try {
     $conn = \eBizIndia\PDOConn::getInstance();
     $conn->beginTransaction();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $date = $_POST['date'] ?? '';
        $members = $_POST['members'] ?? [];
        $notes = $_POST['notes'] ?? [];

        if (!empty($date) && !empty($members)) {
            foreach ($members as $index => $member) {
                $note = $notes[$index] ?? '';

                $stmt = $conn->prepare("INSERT INTO parking_lot (date, name, description) VALUES (:date, :name, :description)");
                $stmt->execute([
                    ':date' => $date,
                    ':name' => $member,
                    ':description' => $note
                ]);
            }
            $success = 'Entries successfully saved!';
        } else {
            $error = 'Please fill in all required fields.';
        }
    }

    // Fetch existing entries
    $stmt = $conn->query("SELECT * FROM parking_lot ORDER BY date DESC");
    $parkingEntries = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = 'Error: ' . $e->getMessage();
}
*/

// Get existing members list
$options = [];
$options['filters'] = [
    ['field' => 'active', 'type' => 'EQUAL', 'value' => 'y']
];
$options['fieldstofetch'] = ['id', 'name'];
$options['order_by'] = [['field' => 'name', 'type' => 'ASC']];
$membersList = \eBizIndia\Member::getList($options);





//var_dump($membersList);

// Pass data to the template
$body_template_data['members_list'] = $membersList;
$body_template_data['parking_entries'] = getParkingLotData();
$body_template_data['result'] = $_SESSION['save_parking_lot_result'] ?? [];

unset($_SESSION['save_parking_lot_result']);





$additional_base_template_data = array();
$additional_base_template_data['domreadyjscode'] = array();


//$page_renderer->registerBodyTemplate($body_template_file, $body_template_data);
$page_renderer->registerBodyTemplate($body_template_file, $body_template_data ?? []);

$additional_base_template_data = array(
                                        'page_title' => $page_title,
                                        'page_description' => $page_description,
                                        'template_type'=>$template_type,
                                        'dom_ready_code'=>\scriptProviderFuncs\getDomReadyJsCode($page,$dom_ready_data),
                                        'other_js_code'=>$jscode,
                                        'module_name' => $page
                                    );

$page_renderer->updateBaseTemplateData($additional_base_template_data);

$page_renderer->renderPage();
?>
