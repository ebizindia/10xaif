<?php 
$page='dashboard';
require_once 'inc.php';

//header("Location:".AFTER_LOGIN_REDIRECT_USER_TO_URL);
$template_type='full';
$page_title='Dashboard'.CONST_TITLE_AFX;
$page_description='User\'s dashboard.';

$body_template_file=CONST_THEMES_TEMPLATE_INCLUDE_PATH.'index.tpl';
$body_template_data=array();
$show_dnd_status = true;

// Get members count
$options = [];
$options['fieldstofetch'] = ['recordcount'];
if($loggedindata[0]['profile_details']['assigned_roles'][0]['role']==='REGULAR'){
	// REGULAR members are not allowed access to inactive member profiles
	$options['filters'] = [
		[
			'field' => 'active',
			'type' => 'EQUAL',
			'value' => 'y'
		],
		// [
		// 	'field' => 'dnd',
		// 	'type' => 'EQUAL',
		// 	'value' => 'n'
		// ],

		// [
		// 'field' => 'active_dndno_or_id',
		// 'type' => 'EQUAL',
		// 'value' => [$loggedindata[0]['profile_details']['id']]
		// ]
		

	];

	$show_dnd_status = false;

}
$recordcount = \eBizIndia\Member::getList($options);
$recordcount = !empty($recordcount)?$recordcount[0]['recordcount']:false;
$body_template_data['tot_members'] = $recordcount;

$bday_annv_list = \eBizIndia\Member::getBdayAnnvOndate(date('Y-m-d'), $loggedindata[0]['profile_details']['assigned_roles'][0]['role']==='REGULAR'?'y':'', '', [$loggedindata[0]['profile_details']['id']]); // REGULAR members are allowed access to active member profiles only. The logged in user should be included irrespective of his active and dnd status
$body_template_data['bday_annv_list'] = $bday_annv_list;
$body_template_data['show_dnd_status'] = $show_dnd_status;
$body_template_data['cu_role'] = $loggedindata[0]['profile_details']['assigned_roles'][0]['role'];


$page_renderer->registerBodyTemplate($body_template_file,$body_template_data);
$additional_base_template_data=array(
										'page_title'=>$page_title,
										'page_description'=>$page_description,
										'template_type'=>$template_type,
										'dom_ready_code'=>\scriptProviderFuncs\getDomReadyJsCode($page),
										'other_js_code'=>'',
										'module_name'=>$page,
										'can_access_members'=>$usercls->canAccessThisProgram('users.php'),
										
									);
									
$page_renderer->updateBaseTemplateData($additional_base_template_data);
$page_renderer->addCss(\scriptProviderFuncs\getCss($page));
$js_files=\scriptProviderFuncs\getJavascripts($page);
$page_renderer->addJavascript($js_files['BSH'],'BEFORE_SLASH_HEAD');
$page_renderer->addJavascript($js_files['BSB'],'BEFORE_SLASH_BODY');
$page_renderer->renderPage();