<?php

require_once 'inc.php';

$page='404';
if($loggedindata[0]['id']>0)
	$template_type='full';
else
	$template_type='login';
$page_title='Oops...';
$page_description='A wedding planning service.';

$body_template_file=CONST_THEMES_TEMPLATE_INCLUDE_PATH.'404.tpl';
$body_template_data=array();
$page_renderer->registerBodyTemplate($body_template_file,$body_template_data);

$additional_base_template_data=array(
										'page_title'=>$page_title,
										'page_description'=>$page_description,
										'template_type'=>$template_type,
										'dom_ready_code'=>\scriptProviderFuncs\getDomReadyJsCode($page),
										'other_js_code'=>"",
										'user_id'=>$loggedindata[0]['id']
									);
									
$navbar_template_data=array('navbar'=>array(
										'template_type'=>$template_type
										)
									);

/*$additional_body_template_data=array(
						'referurl'=>$_GET['referurl']		
					);
*/									
									
//$page_renderer->updateBodyTemplateData($additional_body_template_data);
$page_renderer->updateBodyTemplateData($navbar_template_data);
$page_renderer->updateBaseTemplateData($additional_base_template_data);
$page_renderer->addCss(\scriptProviderFuncs\getCss($page));
$js_files=\scriptProviderFuncs\getJavascripts($page);
$page_renderer->addJavascript($js_files['BSH'],'BEFORE_SLASH_HEAD');
$page_renderer->addJavascript($js_files['BSB'],'BEFORE_SLASH_BODY');
$page_renderer->renderPage();

?>
