<?php
$page = 'forum-roles';
require_once 'inc.php';
$template_type = '';
$page_title = 'Forum Roles' . CONST_TITLE_AFX;
$page_description = 'Forum Roles';
$body_template_file = CONST_THEMES_TEMPLATE_INCLUDE_PATH . 'forum-roles.tpl';
$body_template_data = array();

// Forum role positions definition
$forum_positions = [
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

// Get current year in format YYYY-YY
function getCurrentYear() {
    $year = date('Y');
    $month = date('n');
    if ($month < 7) { // If before July, we're in previous year's cycle
        $year--;
    }
    return $year . '-' . substr($year + 1, -2);
}

// Get last 5 years including current
function getYearsList() {
    $currentYear = (int)date('Y');
    $month = date('n');
    if ($month < 7) {
        $currentYear--;
    }
    $years = [];
    for ($i = 0; $i < CONST_SHOW_YEARS; $i++) {
        $year = $currentYear - $i;
        $years[] = $year . '-' . substr($year + 1, -2);
    }
    return $years;
}

// Get forum roles data
function getForumRoles($year) {
    $stmt = \eBizIndia\PDOConn::query("
        SELECT fr.*, m.name as member_name 
        FROM forum_roles fr
        LEFT JOIN members m ON fr.member_id = m.id 
        WHERE fr.year = :year
    ", [':year' => $year]);
    
    $roles = [];
    foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $roles[$row['position']] = [
            'member_id' => $row['member_id'],
            'name' => $row['member_name']
        ];
    }
    return $roles;
}

// Save/Update forum role
function saveForumRole($data) {
    try {
        $stmt = \eBizIndia\PDOConn::query("
            INSERT INTO forum_roles 
            (year, position, member_id, created_by, created_at)
            VALUES 
            (:year, :position, :member_id, :created_by, NOW())
            ON DUPLICATE KEY UPDATE
            member_id = VALUES(member_id),
            updated_by = :created_by,
            updated_at = NOW()
        ", $data);
        return true;
    } catch (\Exception $e) {
        return false;
    }
}

// Handle form submission
if (filter_has_var(INPUT_POST, 'mode') && $_POST['mode'] == 'saveForumRoles') {
    $result = ['error_code' => 0, 'message' => '', 'other_data' => []];
    
    try {
        // Start transaction for saving all positions
        $conn = \eBizIndia\PDOConn::getInstance();
        $conn->beginTransaction();
        
        $year = filter_var($_POST['year'], FILTER_SANITIZE_STRING);
        if (!preg_match('/^\d{4}-\d{2}$/', $year)) {
            throw new \Exception('Invalid year format');
        }
        
        foreach($forum_positions as $position_key => $position_label) {
            $member_id = filter_var($_POST['roles'][$position_key] ?? 0, FILTER_VALIDATE_INT);
            if($member_id) {
                $data = [
                    ':year' => $year,
                    ':position' => $position_key,
                    ':member_id' => $member_id,
                    ':created_by' => $loggedindata[0]['id']
                ];
                
                if (!saveForumRole($data)) {
                    throw new \Exception("Error saving forum role for position: $position_label");
                }
            }
        }
        
        // If all saves successful, commit transaction
        $conn->commit();
        $result['message'] = 'Forum roles saved successfully for year ' . htmlspecialchars($year);
    } catch (\Exception $e) {
        // If any error occurs, rollback all changes
        if ($conn && $conn->inTransaction()) {
            $conn->rollBack();
        }
        $result['error_code'] = 1;
        $result['message'] = 'Error saving forum roles: ' . $e->getMessage();
    }
    
    $_SESSION['save_roles_result'] = $result;
    header("Location: forum-roles.php?year=" . urlencode($year));
    exit;
}

// Get selected year or default to current
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
$currentRoles = getForumRoles($selectedYear);

$body_template_data['forum_positions'] = $forum_positions;
$body_template_data['years_list'] = $yearsList;
$body_template_data['selected_year'] = $selectedYear;
$body_template_data['members_list'] = $membersList;
$body_template_data['current_roles'] = $currentRoles;

if (isset($_SESSION['save_roles_result'])) {
    $body_template_data['save_result'] = $_SESSION['save_roles_result'];
    unset($_SESSION['save_roles_result']);
}

$page_renderer->registerBodyTemplate($body_template_file, $body_template_data);
$page_renderer->renderPage();
?>