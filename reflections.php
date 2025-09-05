<?php
$page = 'reflections';
require_once 'inc.php';
$template_type = '';
$page_title = 'Monthly Reflections' . CONST_TITLE_AFX;
$page_description = 'Monthly Reflections and Significant Moments';
$body_template_file = CONST_THEMES_TEMPLATE_INCLUDE_PATH . 'reflections.tpl';
$body_template_data = array();

// Categories for reflections
$categories = [
    'business' => 'My Business',
    'family' => 'My Family',
    'personal' => 'Myself',
    'community' => 'My Community'
];

function getCurrentMonthYear() {
    return date('Y-m');
}

function getMonthsList() {
    $months = [];
    for ($i = 0; $i < 12; $i++) {
        $date = date('Y-m', strtotime("-$i months"));
        $months[] = $date;
    }
    return $months;
}

function getReflectionData($userId, $monthYear) {
    try {
        // Get main reflection data
        $stmt = \eBizIndia\PDOConn::query("
            SELECT * FROM reflections_main 
            WHERE user_id = :user_id 
            AND DATE_FORMAT(entry_date, '%Y-%m') = :month_year
        ", [':user_id' => $userId, ':month_year' => $monthYear]);
        $mainReflection = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $reflectionsByCategory = [];
        if ($mainReflection) {
            // Get category reflections
            $stmt = \eBizIndia\PDOConn::query("
                SELECT rc.* 
                FROM reflections_category rc
                WHERE rc.reflection_id = :reflection_id
            ", [':reflection_id' => $mainReflection['id']]);
            $categoryReflections = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Organize category reflections
            foreach ($categoryReflections as $reflection) {
                $reflectionsByCategory[$reflection['category']] = array_merge(
                    $reflection,
                    [
                        'physical_health_score' => $mainReflection['physical_health_score'],
                        'mental_health_score' => $mainReflection['mental_health_score'],
                        'financial_health_score' => $mainReflection['financial_health_score'],
                        'community_score' => $mainReflection['community_score'],
                        'energy_vampire' => $mainReflection['energy_vampire'],
                        'reluctant_topic' => $mainReflection['reluctant_topic'],
                        'current_issue' => $mainReflection['current_issue']
                    ]
                );
            }
        }

        // Structure the response to match the expected format in the template
        return [
            'reflections' => $reflectionsByCategory,
            'lookforward' => [
                'lookforward_text' => $mainReflection['lookforward_text'] ?? null
            ],
            'insight' => [
                'insight_text' => $mainReflection['insight_text'] ?? null
            ],
            'main_reflection' => $mainReflection
        ];
    } catch (\Exception $e) {
        if(!is_a($e, '\PDOException'))
            \eBizIndia\ErrorHandler::logError(['Function: '.__FUNCTION__, 'exception_msg: '.$e->getMessage()], $e);
        return false;
    }
}

function saveReflectionData($data) {
    try {
        $conn = \eBizIndia\PDOConn::getInstance();
        $conn->beginTransaction();

        // Save main reflection data
        $mainParams = [
            ':user_id' => $data['user_id'],
            ':entry_date' => $data['entry_date'],
            ':physical_health_score' => !empty($data['health_scores']['physical'])?$data['health_scores']['physical']:NULL,
            ':mental_health_score' => !empty($data['health_scores']['mental'])?$data['health_scores']['mental']:NULL,
            ':financial_health_score' => !empty($data['health_scores']['financial'])?$data['health_scores']['financial']:NULL,
            ':community_score' => !empty($data['health_scores']['community'])?$data['health_scores']['community']:NULL,
            ':energy_vampire' => $data['energy_vampire'],
            ':reluctant_topic' => $data['reluctant_topic'],
            ':current_issue' => $data['current_issue'],
            ':lookforward_text' => $data['lookforward'],
            ':insight_text' => $data['insight'],
            ':created_by' => $data['user_id'],
            ':created_on' => date('Y-m-d H:i:s'),
            ':created_from_ip' => $_SERVER['REMOTE_ADDR']
        ];

        \eBizIndia\PDOConn::query("
            INSERT INTO reflections_main 
            (user_id, entry_date, physical_health_score, mental_health_score, 
            financial_health_score, community_score, energy_vampire, 
            reluctant_topic, current_issue, lookforward_text, insight_text,
            created_by, created_on, created_from_ip)
            VALUES 
            (:user_id, :entry_date, :physical_health_score, :mental_health_score,
            :financial_health_score, :community_score, :energy_vampire,
            :reluctant_topic, :current_issue, :lookforward_text, :insight_text,
            :created_by, :created_on, :created_from_ip)
            ON DUPLICATE KEY UPDATE
            physical_health_score = VALUES(physical_health_score),
            mental_health_score = VALUES(mental_health_score),
            financial_health_score = VALUES(financial_health_score),
            community_score = VALUES(community_score),
            energy_vampire = VALUES(energy_vampire),
            reluctant_topic = VALUES(reluctant_topic),
            current_issue = VALUES(current_issue),
            lookforward_text = VALUES(lookforward_text),
            insight_text = VALUES(insight_text),
            last_updated_by = :created_by,
            last_updated_on = :created_on,
            last_updated_from_ip = :created_from_ip
        ", $mainParams);

        // Get reflection_id (either newly inserted or existing)
        $stmt = \eBizIndia\PDOConn::query("
            SELECT id FROM reflections_main 
            WHERE user_id = :user_id 
            AND DATE_FORMAT(entry_date, '%Y-%m') = DATE_FORMAT(:entry_date, '%Y-%m')
        ", [
            ':user_id' => $data['user_id'],
            ':entry_date' => $data['entry_date']
        ]);
        $reflectionId = $stmt->fetchColumn();

        // Save category reflections
        foreach ($data['reflections'] as $category => $reflection) {
            $categoryParams = [
                ':reflection_id' => $reflectionId,
                ':category' => $category,
                ':situation' => $reflection['situation'],
                ':significance' => $reflection['significance'],
                ':feelings' => $reflection['feelings'],
                ':created_by' => $data['user_id'],
                ':created_on' => date('Y-m-d H:i:s'),
                ':created_from_ip' => $_SERVER['REMOTE_ADDR']
            ];

            \eBizIndia\PDOConn::query("
                INSERT INTO reflections_category 
                (reflection_id, category, situation, significance, feelings,
                created_by, created_on, created_from_ip)
                VALUES 
                (:reflection_id, :category, :situation, :significance, :feelings,
                :created_by, :created_on, :created_from_ip)
                ON DUPLICATE KEY UPDATE
                situation = VALUES(situation),
                significance = VALUES(significance),
                feelings = VALUES(feelings),
                last_updated_by = :created_by,
                last_updated_on = :created_on,
                last_updated_from_ip = :created_from_ip
            ", $categoryParams);
        }



        $conn->commit();
        return true;
    } catch (\Exception $e) {
        if ($conn && $conn->inTransaction()) {
            $conn->rollBack();
        }
        if(!is_a($e, '\PDOException'))
            \eBizIndia\ErrorHandler::logError(['Function: '.__FUNCTION__, 'exception_msg: '.$e->getMessage()], $e);
        return false;
    }
}

// Handle form submission
if (filter_has_var(INPUT_POST, 'mode') && $_POST['mode'] == 'saveReflection') {
    $result = ['error_code' => 0, 'message' => '', 'other_data' => []];
    
    try {
        $data = [
            'user_id' => $loggedindata[0]['id'],
            'entry_date' => date('Y-m-d', strtotime($_POST['month_year'] . '-01')),
            'reflections' => $_POST['reflections'],
            'health_scores' => $_POST['health_scores'],
            'lookforward' => $_POST['lookforward'],
            'insight' => $_POST['insight'],
            'energy_vampire' => $_POST['energy_vampire'],
            'reluctant_topic' => $_POST['reluctant_topic'],
            'current_issue' => $_POST['current_issue']
        ];
        
        if (saveReflectionData($data)) {
            $result['message'] = 'Reflection saved successfully.';
        } else {
            throw new \Exception("Error saving reflection data");
        }
    } catch (\Exception $e) {
        $result['error_code'] = 1;
        $result['message'] = 'Error saving reflection: ' . $e->getMessage();
    }
    
    $_SESSION['save_reflection_result'] = $result;
    header("Location: reflections.php?month=" . $_POST['month_year']);
    exit;
}

// Get selected month or default to current
$selectedMonth = $_GET['month'] ?? getCurrentMonthYear();
$monthsList = getMonthsList();
$reflectionData = getReflectionData($loggedindata[0]['id'], $selectedMonth);

$tmp = explode('-',$selectedMonth); 
$sel_yr = trim($tmp[0]); 
$sel_m = (int)trim($tmp[1]); 
// Get month options
$monthOptions = [];
for ($m = 1; $m <= 12; $m++) {
    $monthOptions[$m] = date('M', mktime(0, 0, 0, $m, 1));
}
// Get year options (5 years before and after current year)
$yearOptions = [];
for ($y = $sel_yr - 5; $y <= $sel_yr + 5; $y++) {
    $yearOptions[] = $y;
}

// Prepare template data
$body_template_data['categories'] = $categories;
$body_template_data['months_list'] = $monthsList;
$body_template_data['selected_month'] = $selectedMonth;
$body_template_data['month_options'] = $monthOptions;
$body_template_data['sel_yr'] = $sel_yr;
$body_template_data['sel_m'] = $sel_m;
$body_template_data['year_options'] = $yearOptions;
$body_template_data['reflection_data'] = $reflectionData;

if (isset($_SESSION['save_reflection_result'])) {
    $body_template_data['save_result'] = $_SESSION['save_reflection_result'];
    unset($_SESSION['save_reflection_result']);
}

// Register required JS/CSS files
$additional_base_template_data = array();
$additional_base_template_data['domreadyjscode'] = array();
//$additional_base_template_data['domreadyjscode'][] = $script_provider->getScriptContainer($page);
//$page_renderer->additionalBaseTemplateData($additional_base_template_data);

$page_renderer->registerBodyTemplate($body_template_file, $body_template_data);
$page_renderer->renderPage();
?>