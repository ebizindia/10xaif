<?php
$page = 'view-forum-roles';
require_once 'inc.php';
$template_type = '';
$page_title = 'View Forum Roles' . CONST_TITLE_AFX;
$page_description = 'View Forum Roles History';
$body_template_file = CONST_THEMES_TEMPLATE_INCLUDE_PATH . 'view-forum-roles.tpl';
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

// Get all forum roles data for multiple years
function getAllForumRoles($years) {
    try {
        // Create named parameters for years
        $params = [];
        $yearPlaceholders = [];
        foreach ($years as $index => $year) {
            $paramName = ":year{$index}";
            $params[$paramName] = $year;
            $yearPlaceholders[] = $paramName;
        }

        $yearPlaceholdersStr = implode(', ', $yearPlaceholders);
        
        $query = "
            SELECT 
                fr.year,
                fr.position,
                fr.member_id,
                m.name as member_name
            FROM forum_roles fr
            LEFT JOIN members m ON fr.member_id = m.id 
            WHERE fr.year IN ({$yearPlaceholdersStr})
            ORDER BY fr.year DESC, fr.position ASC
        ";
        
        $stmt = \eBizIndia\PDOConn::query($query, $params);
        
        $roles = [];
        foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $roles[$row['year']][$row['position']] = [
                'member_id' => $row['member_id'],
                'name' => $row['member_name']
            ];
        }
        return $roles;
    } catch (\PDOException $e) {
        error_log("Database error in getAllForumRoles: " . $e->getMessage());
        return [];
    } catch (\Exception $e) {
        error_log("General error in getAllForumRoles: " . $e->getMessage());
        return [];
    }
}

try {
    // Get the years list and all roles data
    $yearsList = getYearsList();
    $allRoles = getAllForumRoles($yearsList);

    // Prepare template data
    $body_template_data['forum_positions'] = $forum_positions;
    $body_template_data['years_list'] = $yearsList;
    $body_template_data['all_roles'] = $allRoles;

    $page_renderer->registerBodyTemplate($body_template_file, $body_template_data);
    $page_renderer->renderPage();
} catch (\Exception $e) {
    error_log("Error in view-forum-roles.php: " . $e->getMessage());
    // Handle the error appropriately - could redirect to an error page
    // or display a user-friendly error message
    echo "An error occurred while loading the forum roles. Please try again later.";
}
?>