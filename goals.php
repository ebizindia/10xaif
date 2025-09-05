<?php
$page = 'goals';
require_once 'inc.php';
$template_type = '';
$page_title = 'Goal Cards' . CONST_TITLE_AFX;
$page_description = 'Goal Cards';
$body_template_file = CONST_THEMES_TEMPLATE_INCLUDE_PATH . 'goals.tpl';
$body_template_data = array();

// Categories for goal cards
$categories = ['business'=>'Business', 'family'=>'Family', 'personal'=>'Personal', 'social'=>'Social/Community'];

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

// Get goal card data
function getGoalCard($year, $userId) {
    $stmt = \eBizIndia\PDOConn::query("
        SELECT * FROM goal_cards 
        WHERE user_id = :user_id 
        AND year = :year
    ", [':user_id' => $userId, ':year' => $year]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $goalCard = [];
    foreach ($results as $row) {
        $goalCard[$row['category']] = $row;
    }
    return $goalCard;
}

// Save/Update goal card
function saveGoalCard($data) {
    try {
        $stmt = \eBizIndia\PDOConn::query("
            INSERT INTO goal_cards 
            (user_id, year, category, goal, significance, action_planned, mid_review, final_review)
            VALUES 
            (:user_id, :year, :category, :goal, :significance, :action_planned, :mid_review, :final_review)
            ON DUPLICATE KEY UPDATE
            goal = VALUES(goal),
            significance = VALUES(significance),
            action_planned = VALUES(action_planned),
            mid_review = VALUES(mid_review),
            final_review = VALUES(final_review)
        ", $data);
        return true;
    } catch (\Exception $e) {
        return false;
    }
}

// Handle form submission
if (filter_has_var(INPUT_POST, 'mode') && $_POST['mode'] == 'saveGoals') {
    $result = ['error_code' => 0, 'message' => '', 'other_data' => []];
    
    try {
        // Start transaction for saving all categories
        $conn = \eBizIndia\PDOConn::getInstance();
        $conn->beginTransaction();
        
        foreach ($categories as $cat_key=>$category) {
            // Build data array for each category
            $data = [
                ':user_id' => $loggedindata[0]['profile_details']['id'],
                ':year' => $_POST['year'],
                ':category' => $cat_key,
                ':goal' => $_POST['goals'][$cat_key]['goal'] ?? '',
                ':significance' => $_POST['goals'][$cat_key]['significance'] ?? '',
                ':action_planned' => $_POST['goals'][$cat_key]['action_planned'] ?? '',
                ':mid_review' => $_POST['goals'][$cat_key]['mid_review'] ?? '',
                ':final_review' => $_POST['goals'][$cat_key]['final_review'] ?? ''
            ];
            
            // Save goal card for this category
            if (!saveGoalCard($data)) {
                throw new \Exception("Error saving goal card for category: $category");
            }
        }
        
        // If all saves successful, commit transaction
        $conn->commit();
        $result['message'] = 'Goal cards saved successfully.';
    } catch (\Exception $e) {
        // If any error occurs, rollback all changes
        if ($conn && $conn->inTransaction()) {
            $conn->rollBack();
        }
        $result['error_code'] = 1;
        $result['message'] = 'Error saving goal cards: ' . $e->getMessage();
    }
    
    $_SESSION['save_goals_result'] = $result;
    header("Location: goals.php?year=" . $_POST['year']);
    exit;
}

// Get selected year or default to current
$selectedYear = $_GET['year'] ?? getCurrentYear();
$yearsList = getYearsList();
$goalCard = getGoalCard($selectedYear, $loggedindata[0]['profile_details']['id']);

// Prepare template data
$body_template_data['categories'] = $categories;
$body_template_data['years_list'] = $yearsList;
$body_template_data['selected_year'] = $selectedYear;
$body_template_data['goal_card'] = $goalCard;

if (isset($_SESSION['save_goals_result'])) {
    $body_template_data['save_result'] = $_SESSION['save_goals_result'];
    unset($_SESSION['save_goals_result']);
}

$page_renderer->registerBodyTemplate($body_template_file, $body_template_data);
$page_renderer->renderPage();
?>