<?php
mb_http_output("UTF-8");
ob_start("mb_output_handler");
require_once("config.php");

date_default_timezone_set(CONST_TIME_ZONE);
define('CONST_SESSION_INDEX',CONST_APP_SESSION_BASE_INDEX);
ini_set('display_errors',1);
switch(ERRORREPORTING){
	case "1": error_reporting(E_ALL); break;
	case "2": error_reporting(E_ERROR | E_PARSE); break;
	default: error_reporting(0);
}

require_once(CONST_INCLUDES_DIR."/ebiz-autoload.php");
require_once(CONST_CLASS_DIR."/phpmailer/vendor/autoload.php");
require_once(CONST_INCLUDES_DIR . "general-func.php");
require_once CONST_INCLUDES_DIR."/sess-init.php";
require_once CONST_CLASS_DIR . 'instamojo/vendor/autoload.php';

// Check for IP based access restrictions //////////////////
$your_ip = \eBizIndia\getRemoteIP();
if(!empty(CONST_RESTRICTED_TO_IP) && (empty($your_ip) || !in_array($your_ip,CONST_RESTRICTED_TO_IP) ) ){
	\eBizIndia\logoutSession();
    header('HTTP/1.0 403 Forbidden', true, 403);
    exit;
}
///////////////////////////////////////////////////////////


if(CONST_MAINTENANCE_MODE && (empty(CONST_MTNC_MODE_EXCL_IP) || !in_array(\eBizIndia\getRemoteIP(), CONST_MTNC_MODE_EXCL_IP) ) && (empty(CONST_MTNC_MODE_FOR_MENUS) || in_array($page, CONST_MTNC_MODE_FOR_MENUS)) ){
	if(in_array($page, CONST_MTNC_MODE_FOR_MENUS))
		\eBizIndia\ErrorHandler::showMsgToUser(CONST_MAINTENANCE_MODE_MSG[$page]??CONST_MAINTENANCE_MODE_MSG['default']);
	else	
		\eBizIndia\ErrorHandler::showMsgToUser(CONST_MAINTENANCE_MODE_MSG['default']);
}



\eBizIndia\PDOConn::connectToDB('mysql');

if(isset($_GET['dev_mode'])){
	$_SESSION['dev_mode']=$_GET['dev_mode'];
}

$script=pathinfo($_SERVER['SCRIPT_NAME']);
$currscriptname=$script['basename'];
$loggedin=0; // not logged in
$loggedindata=array();

if(isset($_SESSION['userobj']) && $_SESSION['userobj']!=''){

	$usercls=unserialize($_SESSION['userobj']);
	// $usercls->setDbConnection($conn);

	$loggedindata=$usercls->getLoggedinUserData();
	if($loggedindata[0]['loggedin']==1){

		// Check for ROLE based access restrictions //////////////////
		$your_role = $loggedindata[0]['profile_details']['assigned_roles'][0]['role'];
		if(!empty(CONST_RESTRICTED_TO_ROLES) && (empty($your_role) || !in_array($your_role,CONST_RESTRICTED_TO_ROLES) ) ){
			\eBizIndia\logoutSession();
		    header("Location:?"); exit; // redirecting to the same page
		    // header('HTTP/1.0 403 Forbidden', true, 403);
		    // exit;
		}
		///////////////////////////////////////////////////////////

		if($loggedindata[2]!=CONST_APP_PATH_FROM_ROOT){
			// User is trying to access this location after logging in through another location of the same domain
			// Destroy all the data in session and reload the page
			\eBizIndia\logoutSession(); // destroying the session
			header("Location:?"); exit; // redirecting to the same page
		}else{
			$loggedin=1; // logged in
			$adminmenulist=$loggedindata[1];

			define('CONST_DATE_DISPLAY_FORMAT', $loggedindata[0]['dateDisplayFormat']=='' ? 'd-m-Y' : $loggedindata[0]['dateDisplayFormat']);

		}

	}

}else{


	$usercls=new \eBizIndia\User();

	$res=$usercls->loginWithCookie(CONST_APP_PATH_FROM_ROOT, CONST_RESTRICTED_TO_ROLES);

	if($res!==false){
		$loginstats['user_id']=(isset($res[1]['user_id']))?$res[1]['user_id']:'';
		$loginstats['account']=$res[1]['username'];
		$loginstats['login_datetime']=$res[1]['login_datetime'];
		$loginstats['login_from']=$res[1]['ip'];
		$loginstats['login_type']='REMEMBER';
		$loginstats['login_as']=$res[1]['login_as'];

		if($res[0]){
			$loginstats['login_status']='1';
			$loginstats['reason']='';
			$loggedindata=$usercls->getLoggedinUserData();
			if($loggedindata[0]['loggedin']==1){
				$loggedin=1; // logged in
				$adminmenulist=$loggedindata[1];
			}
		}else{
			$loginstats['login_status']='0';
			switch($res[1]['errorcode']){
				case 1: $loginstats['reason']='DB Error'; break;
				case 2:	$loginstats['reason']='Username is invalid'; break;
				case 3: $loginstats['reason']='Invalid Password'; break;
				case 4: $loginstats['reason']='Account suspended'; break;
				case 5: $loginstats['reason']='Error fetching profile details post authentication.'; break;
				case 6: $loginstats['reason']='Profile suspended.'; break;
				case 7: $loginstats['reason']='Access restricted by role.'; break;
				default: $loginstats['reason']='Unknown';
			}
		}
		$usercls->saveLoginData($loginstats); 
	}
}

if(!defined('CONST_DATE_DISPLAY_FORMAT')){
	define('CONST_DATE_DISPLAY_FORMAT', 'd-m-Y');
}

if(strstr($_SERVER['PHP_SELF'],CONST_APP_PATH_FROM_ROOT."/404.php") || strstr($_SERVER['PHP_SELF'],CONST_APP_PATH_FROM_ROOT."/mark-rsvp.php") || strstr($_SERVER['PHP_SELF'],CONST_APP_PATH_FROM_ROOT."/update-contact.php") || strstr($_SERVER['PHP_SELF'],CONST_APP_PATH_FROM_ROOT."/rss/rss.php")){

}else if(!strstr($_SERVER['PHP_SELF'],CONST_APP_PATH_FROM_ROOT."/login.php")){
	if($loggedin!=1){ 

		if(($_SERVER['HTTP_REFERER']??'')!='' && !stristr($_SERVER['HTTP_REFERER'],CONST_APP_PATH_FROM_ROOT.'/login.php')){
			$_SESSION['expired']=1;
			setcookie('expired','1',0,CONST_APP_PATH_FROM_ROOT.'/',$_SERVER['HTTP_HOST'],false,true);

		}

		\eBizIndia\sendSessionTimeOutNotification();
		exit;
	}else{


			register_shutdown_function('\eBizIndia\beforeShutdown');
			if(!isset($_SESSION['dev_mode']) || $_SESSION['dev_mode']!=1){
				$accessed_program = $usercls->canAccessThisProgram($currscriptname);
				
				if( $accessed_program === false ){
					header("Location:".CONST_APP_ABSURL."/");
					exit;
				}else{
					if($_SESSION['last_accessed_program'] != $currscriptname){
						$log_data = array(
									'user_id' => $loggedindata[0]['id'],
									'script' => $currscriptname,
									'menuname' => $accessed_program['menuname'],
									'log_datetime' => date('Y-m-d H:i:s'),
									'ip' => \eBizIndia\getRemoteIP()
							);
						$usercls->logMenuAccess($log_data); 
						$_SESSION['last_accessed_program'] = $currscriptname;
					}
				}
			}



	}


} else {

	if($loggedin!=1 && $_GET['mode']=='logout'){
		header("Location:".CONST_APP_ABSURL."/login.php"); // default root page
		exit;
	}else if($loggedin==1 && $_POST['mode'] == 'login' && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
			// Already logged in
		header("HTTP/1.0 302 Found",true, 302); exit;

	}elseif($loggedin==1 && $_GET['mode']!='logout'){

		register_shutdown_function('\eBizIndia\beforeShutdown');
		header("Location:".CONST_APP_ABSURL); // default root page
		exit;
	}

}

$allowed_menu_perms = [];

if(is_array($adminmenulist)){
	foreach($adminmenulist as $cat){

		foreach($cat['menus'] as $menu){

			if($menu['menupage'] == $page){
				if($menu['availableByDefault'] == '1')
					$allowed_menu_perms = ['ALL'];
				else
					$allowed_menu_perms = $menu['perms'];

				break;
			}
		}

		if(!empty($allowed_menu_perms))
			break;
	}
}

$loggedindata[0]['dateDisplayFormatArray']=explode('-',$loggedindata[0]['dateDisplayFormat']);
require_once CONST_INCLUDES_DIR . "script-provider.php";
$user_settings = array(
		'date_format'=> $loggedindata[0]['dateDisplayFormat'],
		'date_format_display'=>$_date_display_formats[$loggedindata[0]['dateDisplayFormat']]['for_user_disp'],
		'date_format_bootstrap_picker'=>$_date_display_formats[$loggedindata[0]['dateDisplayFormat']]['for_bootstrap_picker'],
		'date_format_js'=>$_date_display_formats[$loggedindata[0]['dateDisplayFormat']]['js_format']
	);
if($loggedindata[0]['user_settings']!='' && $loggedindata[0]['user_settings']!='{}'){
	$user_settings=array_merge(json_decode($loggedindata[0]['user_settings'], true),$user_settings);
}
$loggedindata[0]['user_settings_array'] = $user_settings;

define('CONST_RECORDS_PER_PAGE',$user_settings['no_of_data_per_row']!=''?$user_settings['no_of_data_per_row']:10);

if(filter_has_var(INPUT_GET,'do') && $_GET['do']=='print'){
	$base_template_file_with_path=CONST_THEMES_TEMPLATE_INCLUDE_PATH.'print-base-template.tpl';
}else{
	$base_template_file_with_path=CONST_THEMES_TEMPLATE_INCLUDE_PATH.'main-template.tpl';
}


if($loggedin==1 && CONST_SHOW_SPONSOR_AD && !isset($_SESSION['sponsor_ads'])){
	$_SESSION['sponsor_ads'] = \eBizIndia\AdBanner::getAdsActiveOnDate(date('Y-m-d'));
	foreach ($_SESSION['sponsor_ads'] as $key => $ad) {
		$_SESSION['sponsor_ads'][$key]['dsk_img'] = CONST_AD_BANNER_IMG_URL_PATH.$_SESSION['sponsor_ads'][$key]['dsk_img'];
		$pic_size = getimagesize(CONST_AD_BANNER_IMG_URL_PATH.$_SESSION['sponsor_ads'][$key]['dsk_img']);
		$_SESSION['sponsor_ads'][$key]['dsk_img_w'] = $pic_size[0];

		$_SESSION['sponsor_ads'][$key]['mob_img'] = CONST_AD_BANNER_IMG_URL_PATH.$_SESSION['sponsor_ads'][$key]['mob_img'];
		$pic_size = getimagesize(CONST_AD_BANNER_IMG_URL_PATH.$_SESSION['sponsor_ads'][$key]['mob_img']);
		$_SESSION['sponsor_ads'][$key]['mob_img_w'] = $pic_size[0];
	}
	
}else if(!CONST_SHOW_SPONSOR_AD){
	unset($_SESSION['sponsor_ads']);
}

$base_template_data=array(
	'currscriptname'=>$currscriptname, 
	'base_url'=>CONST_APP_ABSURL, 
	'app_disp_name'=>CONST_APP_NAME,
	'loggedindata'=>$loggedindata, 
	'user_types'=>$user_types, 
	'phone_type_abbr'=>$_phone_type_abbr, 
	'email_type_abbr'=>$_email_type_abbr, 
	'date_display_formats'=>$_date_display_formats, 
	'dom_ready_code' => \scriptProviderFuncs\getDomReadyJsCode('common', array('user_settings'=>json_encode($user_settings), 'allowed_menu_perms'=>json_encode($allowed_menu_perms),'user_types'=>json_encode($user_types), 'other_data'=>($loggedindata[0]['loggedin'] == 1)?'{"user_type":"'.$loggedindata[0]['profile_type'].'"}':'{}', 'prod_grp_max_len'=>CONST_PROD_GRP_CODE_MAX_LEN, 'user_uploaded_files_url_path'=>'','noimage_file'=>CONST_NOIMAGE_FILE, 'cookie_path'=>CONST_SESSION_COOKIE_PATH, 'sponsor_ads'=>json_encode($_SESSION['sponsor_ads']??[]), 'show_sponsor_ad' => (CONST_SHOW_SPONSOR_AD  && count($_SESSION['sponsor_ads']??[])>0), 'ad_display_interval' => CONST_AD_DISP_INTERVAL, 'is_admin'=>$loggedindata[0]['profile_details']['assigned_roles'][0]['role']=='ADMIN'?'true':'false' ) ), // for is_admin the true and false values are being assigned as strings so that these get set as boolean values in the js code generated via the php code in the script provider. 
	'allowed_menu_perms'=>$allowed_menu_perms, 
	'copyright_info'=>array('rightsHolder'=>'Ebizindia Consulting Private Limited','dateCopyrighted'=>date('Y')), 
	'do'=>$_GET['do'], 
	'active_statuses'=>$_active_statuses, 
	'self_edit' => CONST_MEM_PROF_EDIT_RESTC[$loggedindata[0]['profile_details']['assigned_roles'][0]['role']]['self'][0]===true?true:false,
	'others_edit' => CONST_MEM_PROF_EDIT_RESTC[$loggedindata[0]['profile_details']['assigned_roles'][0]['role']]['others'][0]===true?true:false,
	'show_sponsor_ad' => (CONST_SHOW_SPONSOR_AD && count($_SESSION['sponsor_ads']??[])>0),
	'my_profile_url' => CONST_APP_ABSURL.'/users.php#mode=view&recid='.urlencode($loggedindata[0]['profile_details']['id']),
	'country_code' => CONST_COUNTRY_CODE,
	// 'prod_grp_max_len'=>CONST_PROD_GRP_CODE_MAX_LEN, 
	// 'blood_groups'=>$_blood_groups
);

$page_renderer=new \eBizIndia\PageRenderer();
$page_renderer->setThemesDirFullPhysicalPath(CONST_THEMES_DIR_FULL_PHYSICAL_PATH.'/');
$page_renderer->registerBaseTemplate($base_template_file_with_path,$base_template_data);

$pageLoadJsCode='';
$jscode=''; // Other javascript code
include 'ajax.php';
?>