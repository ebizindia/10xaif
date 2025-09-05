<?php
require_once('inc.php');

// Additional security check specific to this page
if (!isset($loggedindata[0]['id'])) {
    header("Location: login.php");
    exit;
}

// Error logging function
function logError($message, $context = []) {
    error_log(sprintf(
        "[Roster Error] %s | Context: %s | User: %s", 
        $message,
        json_encode($context),
        $loggedindata[0]['id'] ?? 'not logged in'
    ));
}

// Roster positions definition
$roster_positions = [
    'moderator' => 'Moderator',
    'moderator_elect' => 'Moderator Elect',
    'moderator_elect_elect' => 'Moderator Elect Elect',
    'treasurer' => 'Treasurer',
    'process_keeper' => 'Process Observer / Time Keeper',
    'meeting_booster' => 'Meeting Booster', 
    'social_coordinator' => 'Social Coordinator',
    'retreat_chair' => 'Retreat Chair',
    'member_goals' => 'Member Goals',
    'parking_lot' => 'Parking Lot',
    'communication' => 'Communication',
    'time_keeper' => 'Time Keeper',
    'secretary' => 'Secretary',
    'chapter_integration' => 'Chapter Integration'
];

// Function to get roster for a year
function getRoster($year) {
    $params = [':year' => $year];
    $stmt = \eBizIndia\PDOConn::query("
        SELECT r.*, m.name as member_name 
        FROM roster r
        LEFT JOIN members m ON r.user_id = m.id 
        WHERE r.year = :year
    ", $params);
    
    $roster = [];
    foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $roster[$row['position']] = [
            'user_id' => $row['user_id'],
            'name' => $row['member_name']
        ];
    }
    return $roster;
}

// Get current year in format YYYY-YY
function getCurrentYear() {
    $year = date('Y');
    $month = date('n');
    if ($month < 7) { // If before July, we're in previous year's cycle
        $year--;
    }
    return $year . '-' . substr($year + 1, -2);
}

// Get years list for dropdown
function getYearsList() {
    $currentYear = (int)date('Y');
    $month = date('n');
    if ($month < 7) {
        $currentYear--;
    }
    $years = [];
    for ($i = 0; $i < 5; $i++) {
        $year = $currentYear - $i;
        $years[] = $year . '-' . substr($year + 1, -2);
    }
    return $years;
}

// Handle form submission
if(filter_has_var(INPUT_POST,'mode') && $_POST['mode']=='saveRoster') {
    $result = ['error_code' => 0, 'message' => ''];
    
    try {
        $conn = \eBizIndia\PDOConn::getInstance();
        $conn->beginTransaction();
        
        $year = filter_var($_POST['year'], FILTER_SANITIZE_STRING);
        if (!preg_match('/^\d{4}-\d{2}$/', $year)) {
            throw new \Exception('Invalid year format');
        }
        
        foreach($roster_positions as $position_key => $position_label) {
            $user_id = filter_var($_POST['roster'][$position_key] ?? 0, FILTER_VALIDATE_INT);
            if($user_id) {
                $stmt = $conn->prepare("
                    INSERT INTO roster (year, position, user_id, created_by, created_at)
                    VALUES (:year, :position, :user_id, :created_by, NOW())
                    ON DUPLICATE KEY UPDATE 
                    user_id = VALUES(user_id),
                    updated_by = :created_by,
                    updated_at = NOW()
                ");
                
                $stmt->execute([
                    ':year' => $year,
                    ':position' => $position_key,
                    ':user_id' => $user_id,
                    ':created_by' => $loggedindata[0]['id']
                ]);
            }
        }
        
        $conn->commit();
        $result['message'] = 'Roster saved successfully for year ' . htmlspecialchars($year);
    } catch(\Exception $e) {
        if($conn && $conn->inTransaction()) {
            $conn->rollBack();
        }
        $result['error_code'] = 1;
        $result['message'] = 'Error saving roster: ' . htmlspecialchars($e->getMessage());
        logError($e->getMessage(), ['year' => $year ?? null]);
    }
    
    $_SESSION['save_roster_result'] = $result;
    header("Location: roster.php?year=" . urlencode($year));
    exit;
}

// Input validation for year parameter
$selectedYear = filter_var($_GET['year'] ?? getCurrentYear(), FILTER_SANITIZE_STRING);
if (!preg_match('/^\d{4}-\d{2}$/', $selectedYear)) {
    $selectedYear = getCurrentYear();
}

// Get existing members list
$options = [];
$options['filters'] = [
    ['field' => 'active', 'type' => 'EQUAL', 'value' => 'y']
];
$options['fieldstofetch'] = ['id', 'name'];
$options['order_by'] = [['field' => 'name', 'type' => 'ASC']];
$membersList = \eBizIndia\Member::getList($options);

// Prepare template data
$yearsList = getYearsList();
$currentRoster = getRoster($selectedYear);

$body_template_data['years_list'] = $yearsList;
$body_template_data['selected_year'] = $selectedYear;
$body_template_data['members_list'] = $membersList;
$body_template_data['current_roster'] = $currentRoster;
$body_template_data['roster_positions'] = $roster_positions;

if(isset($_SESSION['save_roster_result'])) {
    $body_template_data['save_result'] = $_SESSION['save_roster_result'];
    unset($_SESSION['save_roster_result']);
}
?>