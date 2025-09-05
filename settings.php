<?php
$page = 'settings';
require_once 'inc.php';
$template_type = '';
$page_title = 'Settings' . CONST_TITLE_AFX;
$page_description = 'Settings';
$body_template_file = CONST_THEMES_TEMPLATE_INCLUDE_PATH . 'settings.tpl';
$body_template_data = array();

try {
    $settings = new eBizIndia\Settings();
    $settings_list = $settings::getList();
    
    if (isset($_POST['action']) && $_POST['action'] === 'update_setting') {
        $setting_id = filter_input(INPUT_POST, 'setting_id', FILTER_VALIDATE_INT);
        $setting_value = filter_input(INPUT_POST, 'setting_value', FILTER_SANITIZE_STRING);
        
        if ($setting_id && $setting_value !== false) {
            $settings_obj = new eBizIndia\Settings($setting_id);
            $result = $settings_obj->updateDetails(['setting_value' => $setting_value]);
            echo json_encode(['status' => $result ? 'success' : 'error']);
            exit;
        }
        
        echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
        exit;
    }

    $body_template_file = CONST_THEMES_TEMPLATE_INCLUDE_PATH .'settings.tpl';
    $body_template_data = [
        'settings' => $settings_list,
        'page_title' => 'Settings',
        'page_description' => 'Manage System Configuration Settings'
    ];
    
    $additional_css_files = [
        'css/settings.css'
    ];
    
    $additional_js_files = [
        'js/settings.js'
    ];

   


   /*renderPage(
        page_title: 'Settings',
        body_template: $body_template,
        body_template_data: $body_template_data,
        additional_css_files: $additional_css_files,
        additional_js_files: $additional_js_files
    ); */
} catch (Exception $e) {
    eBizIndia\ErrorHandler::logError(['function' => __FILE__, 'error' => $e->getMessage()], $e);
    die("Error processing request");
}

$additional_base_template_data = array(
                                        'page_title' => $page_title,
                                        'page_description' => $page_description,
                                        'template_type'=>$template_type,
                                        'dom_ready_code'=>\scriptProviderFuncs\getDomReadyJsCode($page,$dom_ready_data),
                                        'other_js_code'=>$jscode,
                                        'module_name' => $page
                                    );


$additional_body_template_data = ['can_add'=>$can_add, 'field_meta' => CONST_FIELD_META ];

$page_renderer->registerBodyTemplate($body_template_file,$body_template_data);

//$page_renderer->updateBodyTemplateData($additional_body_template_data);

$page_renderer->updateBaseTemplateData($additional_base_template_data);
$page_renderer->addCss(\scriptProviderFuncs\getCss($page));
$js_files=\scriptProviderFuncs\getJavascripts($page);
$page_renderer->addJavascript($js_files['BSH'],'BEFORE_SLASH_HEAD');
$page_renderer->addJavascript($js_files['BSB'],'BEFORE_SLASH_BODY');
$page_renderer->renderPage();

?>