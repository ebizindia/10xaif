// goals.php
<?php
$page = 'goals';
require_once 'inc.php';
$template_type = '';
$page_title = 'Goal Cards'.CONST_TITLE_AFX;
$page_description = 'Manage Goal Cards';
$body_template_file = CONST_THEMES_TEMPLATE_INCLUDE_PATH . 'goals.tpl';
$body_template_data = array();

$db = new PDO('mysql:host=localhost;dbname=ebiz8_ssardirectory', 'ebiz8_ssardirectory', '8gA_Kr@CP(K(');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Get current academic year
function getCurrentYear() {
    $month = date('n');
    $year = date('Y');
    if ($month < 7) {
        return ($year - 1) . '-' . $year;
    }
    return $year . '-' . ($year + 1);
}

// Get last 5 years including current
function getYearsList() {
    $currentYear = explode('-', getCurrentYear())[0];
    $years = [];
    for ($i = 0; $i < 5; $i++) {
        $startYear = $currentYear - $i;
        $years[] = $startYear . '-' . ($startYear + 1);
    }
    return $years;
}

// Get goal card data for a specific year
function getGoalCard($year, $userId) {
    global $db;
   $stmt = \eBizIndia\PDOConn::query("
        SELECT * FROM goal_cards 
        WHERE user_id = :user_id 
        AND year = :year
    ",[':user_id' => $userId, ':year' => $year]);
    //$stmt->execute(['user_id' => $userId, 'year' => $year]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
   // $stmt->debugDumpParams();
//    exit;
  //  print_r($results);
    $goalCard = [];
    foreach ($results as $row) {
        $goalCard[$row['category']] = $row;
    }
    return $goalCard;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    try {
        $stmt = $db->prepare("
            INSERT INTO goal_cards (user_id, year, category, goal, significance, 
            action_planned, mid_review, final_review)
            VALUES (:user_id, :year, :category, :goal, :significance,
            :action_planned, :mid_review, :final_review)
            ON DUPLICATE KEY UPDATE
            goal = VALUES(goal),
            significance = VALUES(significance),
            action_planned = VALUES(action_planned),
            mid_review = VALUES(mid_review),
            final_review = VALUES(final_review)
        ");

        $categories = ['business', 'family', 'personal', 'social'];
        foreach ($categories as $category) {
            $stmt->execute([
                'user_id' => $_SESSION['user_id'],
                'year' => $_POST['year'],
                'category' => $category,
                'goal' => $_POST[$category . '_goal'],
                'significance' => $_POST[$category . '_significance'],
                'action_planned' => $_POST[$category . '_action'],
                'mid_review' => $_POST[$category . '_mid'],
                'final_review' => $_POST[$category . '_final']
            ]);
        }
        $message = "Goal card saved successfully";
    } catch (PDOException $e) {
        $error = "Error saving data: " . $e->getMessage();
    }
}

// Prepare data for template
$selectedYear = $_GET['year'] ?? getCurrentYear();
$goalCard = getGoalCard($selectedYear, $loggedindata[0]['id']);
//print_r($goalCard);
//exit;
$years = getYearsList();

$categories = [
    'business' => 'Business',
    'family' => 'Family',
    'personal' => 'Personal',
    'social' => 'Social/Community'
];

$columns = [
    'goal' => 'Goal',
    'significance' => 'Significance',
    'action_planned' => 'Action Planned/Future Expectations',
    'mid_review' => 'Mid-term Review (Dec ' . explode('-', $selectedYear)[0] . ')',
    'final_review' => 'Final Review (Jun ' . explode('-', $selectedYear)[1] . ')'
];

$body_template_data = [
    'years' => $years,
    'selectedYear' => $selectedYear,
    'goalCard' => $goalCard,
    'categories' => $categories,
    'columns' => $columns,
    'message' => $message ?? null,
    'error' => $error ?? null
];

$page_renderer->registerBodyTemplate($body_template_file, $body_template_data);
$page_renderer->renderPage();
?>