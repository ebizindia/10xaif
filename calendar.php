<?php
$page = 'calendar';
require_once 'inc.php';
$template_type = '';
$page_title = 'Calendar' . CONST_TITLE_AFX;
$page_description = 'Calendar View';
$body_template_file = CONST_THEMES_TEMPLATE_INCLUDE_PATH . 'calendar.tpl';
$body_template_data = array();

class CalendarModule {
    private $month;
    private $year;
    private $meetings;
    
    public function __construct($month = null, $year = null) {
        $this->month = $month ?: date('n');
        $this->year = $year ?: date('Y');
        $this->meetings = $this->getMeetings();
    }
    
    private function getMeetings() {
        $meetings = [
            'days' => [],
            'lookup' => [] // Add lookup array to store meeting IDs by day
        ];
        try {
            $startDate = sprintf('%d-%02d-01', $this->year, $this->month);
            $endDate = sprintf('%d-%02d-31', $this->year, $this->month);
            
            $stmt = \eBizIndia\PDOConn::query("
                SELECT id, meet_date, meet_time, venue 
                FROM meetings 
                WHERE meet_date BETWEEN :start_date AND :end_date 
                AND (active = 'y' OR active IS NULL)
                ORDER BY meet_date ASC", 
                [
                    ':start_date' => $startDate,
                    ':end_date' => $endDate
                ]
            );
            
            $meetings = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $dayOfMonth = (int)date('j', strtotime($row['meet_date']));
                $meetings['days'][$dayOfMonth] = true;
                $meetings['lookup'][$dayOfMonth] = $row['id'];
                $meetings['list'][] = [
                    'date' => date('Y-m-d', strtotime($row['meet_date'])),
                    'formatted_date' => date('d/m/Y', strtotime($row['meet_date'])),
                    'time' => $row['meet_time'],
                    'venue' => $row['venue']
                ];
            }
            return $meetings;
            
        } catch (\PDOException $e) {
            error_log("Database error in getMeetings: " . $e->getMessage());
            return ['days' => [], 'list' => []];
        }
    }
    
    public function getCalendarData() {
        $firstDay = mktime(0, 0, 0, $this->month, 1, $this->year);
        $daysInMonth = date('t', $firstDay);
        $startingDay = date('w', $firstDay);
        
        // Adjust for Monday start (0 = Monday, 6 = Sunday)
        $startingDay = ($startingDay == 0) ? 6 : $startingDay - 1;
        
        $prevMonth = $this->month - 1;
        $nextMonth = $this->month + 1;
        $prevYear = $nextYear = $this->year;
        
        if ($prevMonth < 1) {
            $prevMonth = 12;
            $prevYear--;
        }
        if ($nextMonth > 12) {
            $nextMonth = 1;
            $nextYear++;
        }
        
        // Get month options
        $monthOptions = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthOptions[$m] = date('M', mktime(0, 0, 0, $m, 1));
        }
        
        // Get year options (5 years before and after current year)
        $yearOptions = [];
        $currentYear = date('Y');
        for ($y = $currentYear - 5; $y <= $currentYear + 5; $y++) {
            $yearOptions[] = $y;
        }
        
        // Calculate calendar grid
        $calendar = [];
        $dayCount = 1;
        $currentDay = 1;
        
        while ($currentDay <= $daysInMonth) {
            $week = [];
            for ($i = 0; $i < 7; $i++) {
                if (($dayCount < $startingDay + 1) || ($currentDay > $daysInMonth)) {
                    $week[] = '';
                } else {
                    $week[] = $currentDay;
                    $currentDay++;
                }
                $dayCount++;
            }
            $calendar[] = $week;
        }
        
        return [
            'current_month' => $this->month,
            'current_year' => $this->year,
            'month_name' => date('M', $firstDay),
            'prev_month' => $prevMonth,
            'next_month' => $nextMonth,
            'prev_year' => $prevYear,
            'next_year' => $nextYear,
            'month_options' => $monthOptions,
            'year_options' => $yearOptions,
            'calendar_grid' => $calendar,
            'today' => [
                'day' => date('j'),
                'month' => date('n'),
                'year' => date('Y')
            ],
            'meetings' => $this->meetings
        ];
    }
}

// Get calendar data
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('n');
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

$calendar = new CalendarModule($month, $year);
$body_template_data['calendar'] = $calendar->getCalendarData();

$page_renderer->registerBodyTemplate($body_template_file, $body_template_data);
$page_renderer->renderPage();
?>