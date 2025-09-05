<?php
$page = 'all-goals';
require_once 'inc.php';
$template_type = '';
$page_title = 'All Goal Cards' . CONST_TITLE_AFX;
$page_description = 'View All Goal Cards';
$body_template_file = CONST_THEMES_TEMPLATE_INCLUDE_PATH . 'all-goals.tpl';
$body_template_data = array();

// Categories for goal cards
$categories = [
    'business' => 'Business', 
    'family' => 'Family', 
    'personal' => 'Personal', 
    'social' => 'Social/Community'
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
    for ($i = 0; $i < 5; $i++) {
        $year = $currentYear - $i;
        $years[] = $year . '-' . substr($year + 1, -2);
    }
    return $years;
}

// Get all users who have goal cards
function getUsersWithGoals() {
    $stmt = \eBizIndia\PDOConn::query("
        SELECT DISTINCT m.id, m.name, m.email, m.membership_no 
        FROM goal_cards g 
        JOIN members m ON g.user_id = m.id 
        ORDER BY m.name ASC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get goal card for specific user
function getGoalCard($year, $userId) {
    $stmt = \eBizIndia\PDOConn::query("
        SELECT g.*, m.name as user_name 
        FROM goal_cards g
        JOIN members m ON g.user_id = m.id 
        WHERE g.user_id = :user_id 
        AND g.year = :year
    ", [':user_id' => $userId, ':year' => $year]);
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $goalCard = [];
    foreach ($results as $row) {
        $goalCard[$row['category']] = $row;
    }
    return $goalCard;
}

// Get user details
function getUserDetails($userId) {
    $stmt = \eBizIndia\PDOConn::query("
        SELECT id, name, email, membership_no 
        FROM members 
        WHERE id = :user_id
    ", [':user_id' => $userId]);
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get selected year or default to current
$selectedYear = $_GET['year'] ?? getCurrentYear();
$selectedUserId = $_GET['user_id'] ?? null;
$yearsList = getYearsList();
$users = getUsersWithGoals();

// If user is selected, get their goal card and details
$goalCard = null;
$userDetails = null;
if ($selectedUserId) {
    $goalCard = getGoalCard($selectedYear, $selectedUserId);
    $userDetails = getUserDetails($selectedUserId);
}

// Prepare template data
$body_template_data['categories'] = $categories;
$body_template_data['years_list'] = $yearsList;
$body_template_data['selected_year'] = $selectedYear;
$body_template_data['users'] = $users;
$body_template_data['selected_user_id'] = $selectedUserId;
$body_template_data['goal_card'] = $goalCard;
$body_template_data['user_details'] = $userDetails;

$page_renderer->registerBodyTemplate($body_template_file, $body_template_data);
$page_renderer->renderPage();
?>