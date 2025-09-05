<?php
define('RESOURCE_VERSION',4); //used for version number to prevent caching of js and css
define('CONST_MEM_REG_FEE', 3100); // INR, Inclusinve of GST
define('CONST_MEM_REG_FEE_GST', 0); // GST rate as %

define('CONST_SHOW_YEARS',10);

define('CONST_NEW_REG_NOTIF', [
	'',
]);

define('CONST_REG_APPROVAL_RECP', [
	'cc' =>[
	    'arun@ebizindia.com',
	],
]);

define('CONST_ERROR_ALERT_RECP', [
	'to' =>['ebizindia@gmail.com'],
    'bcc' =>['arun@ebizindia.com'],
]);

define('CONST_SEND_BDAY_ANNV_GREET', true); // set true to allow sending of automated birthday and anniversary email greetings, false otherwise
// This software is supposed to be accessed from one or more fixed IPs only. 
//For restricting access to any IP write the IP address on a new line within two single quotes, inside the square brackets below the comment line in the code below. Do not forget to put a comma after the closing single quote. To remove any IP restriction just remove all your IPs from the code below.
define('CONST_RESTRICTED_TO_IP', [
	// Add your IPs below this line but above the closing square bracket, each on a new line enclosed within single quotes and followed by a comma
    // '122.163.120.160',

	]
);
define('CONST_RESTRICTED_TO_ROLES', [
	// Add the roles, each on a new line, to which the access should be restricted. Empty list meas all roles are allowed access 
	
]);
define('CONST_SHOW_SPONSOR_AD', false);
define('CONST_AD_DISP_INTERVAL', 5000); // ms

$_mtnc_tmp = (int)date('YmdHi');
define('CONST_MAINTENANCE_MODE', false); // || $_mtnc_tmp>=202406290000 && $_mtnc_tmp<202406290800); // set true to activate the maintenance mode
define('CONST_MAINTENANCE_MODE_MSG', ['scan-tkt'=>"This module is under development.\nClick the browser's back button to go back to the previous page.",'event-registrations'=>"This module is under maintenance.\nClick the browser's back button to go back to the previous page.",'default'=>"The system is under maintenance.\nPlease try later."]); // maintenance mode message for users
define('CONST_MTNC_MODE_EXCL_IP', ['122.163.120.160']); // 
define('CONST_MTNC_MODE_FOR_MENUS', []); // expects the value of the $page variable defined at the top of the main php file of the respective menu

define('CONST_INSTAMOJO_CREDS', [
    'client_id' => 'test_DN07H7xZDCHR7yNzI55cDKgosDpgVBEeSir',
	'client_secret' => 'test_wU6gXPWfjHmdUya2WesPgwbI1cKFqmm0wLepijr9H0gAWJsCRSnqPeljJaRP3JR4ApSJuiIBorC8xYnjk63Z8zbVGUF27JU6XnuAccekLGEhkOhT6Ls19nvMQHv',
	'sandbox' => true, // set true to use the sandbox account instead and make sure that the client ID and secret too are from the sandbox account
]);

define('CONST_DB_CREDS', [
	'mysql' => [
		'host'=> 'localhost', 
		'port'=> 3306,
		'db'=> 'ebiz10x_tenxaifdirectory',
		'user'=> 'ebiz10x_tenxaif',
		'pswd'=> '#x7ScaZTM8RIHvO!', 
	]
]);

define('CONST_NOTICE_EMAIL_VARS',
	array(
		'$memno' => 'membership_no',
		'$fname' => 'fname',	
		'$mname' => 'lname',	
		'$lname' => 'mname',	
		'$name' => 'name',	
		'$email' => 'email',	
		'$mobile' => 'mobile',	
	)
);

define('CONST_MEM_EXPORT_FLDS',[
	'title'=>'Title', 
	'fname'=>'Fname', 
	'mname'=>'Mname', 
	'lname'=>'surname', 
	'email'=>'Email', 
	'membership_no'=>'Membership No.',
	'groups'=>'Groups',
	'desig_in_assoc'=>'Designation In Association',
	'role'=>'Role',
	'mobile'=>'WhatsApp Number', 
	'mobile2'=>'2nd Mobile', 
	'edu_qual'=>'Educational Qualification', 
	'fb_accnt'=>'Facebook Profile', 
	'x_accnt'=>'Twitter (X) Profile', 
	'linkedin_accnt'=>'LinkedIn Profile', 
	'website'=>'Website', 
	'gender'=>'Gender', 
	'blood_grp'=>'Blood Group', 
	'dob'=>'Date Of Birth', 
	'annv'=>'Anniversary Date', 
	'joining_dt'=>'Joining Date', 
	'residence_city'=>'Residence City', 
	'residence_state'=>'Residence State', 
	'residence_country'=>'Residence Country', 
	'residence_pin'=>'Residence PIN', 
	'residence_addrline1'=>'Residence Address Line 1', 
	'residence_addrline2'=>'Residence Address Line 2', 
	'residence_addrline3'=>'Residence Address Line 3',
	'sector'=>'Sector', 
	'work_type'=>'Business/Professional Details', 
// 	'work_ind'=>'Work Industry',
	'work_company'=>'Work Company',	 
	'designation'=>'Designation',	 
	'work_city'=>'Work City', 
	'work_state'=>'Work State', 
	'work_country'=>'Work Country', 
	'work_pin'=>'Work PIN', 
	'work_addrline1'=>'Work Address Line 1', 
	'work_addrline2'=>'Work Address Line 2', 
	'work_addrline3'=>'Work Address Line 3',
	'active'=>'Status',
	'dnd' => 'DND',
	'remarks' => 'Admin Remarks',
]);


define('CONST_3RD_PARTY_TRACKING', [
	'gtm'=> false, // Gtag code added to the main template file.
]);

define('CONST_TBL_PREFIX','');
define('CONST_MAIL_SUBJECT_PREFIX', '[SS]');
define('CONST_MAIL_SENDERS_NAME', '10X AIF Team');
define('CONST_MAIL_SENDERS_EMAIL', 'noreply@ebizindia.com');
define('CONST_MAIL_REPLYTO_EMAIL', '');
define('CONST_EMAIL_OVERRIDE',''); // comma separated list of email IDs
define('CONST_USE_SERVERS_DEFAULT_SMTP',0); //1 - use servers default smtp, 0 - user other smtp. If 1 then provide SMTP details below
define('CONST_SMTP_SECURE', 0); // 1 - use ssl, 0 - do not use ssl
define('CONST_SMTP_HOST', '');
define('CONST_SMTP_PORT', '587');
define('CONST_SMTP_USER', '');
define('CONST_SMTP_PASSWORD', '');

define('CONST_COUNTRY_CODE', '91');

define('CONST_QRCODE_PARAMS', ['size'=>140, 'margin'=>5]);

/**** WhatsApp ****/
define('ENABLE_EVREG_MSG_OVER_WHATSAPP', 0);
define('ENABLE_WHATSAPP_MSG', 0);
define('AISENSY_API_KEY', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6IjY1YTUwNzAwNzE4ODZhNDBiNmM0NmE3YyIsIm5hbWUiOiJFYml6aW5kaWEgQ29uc3VsdGluZyBQdnQuIEx0ZC4iLCJhcHBOYW1lIjoiQWlTZW5zeSIsImNsaWVudElkIjoiNjVhNTA3MDA3MTg4NmE0MGI2YzQ2YTc3IiwiYWN0aXZlUGxhbiI6IkJBU0lDX01PTlRITFkiLCJpYXQiOjE3MDUzMTQwNDh9.oCtdFJeFQirh5qqodMN5LZOhoi8c3Mt5q67pp8F4lpc');
define('AISENSY_BDAY_CAMPAIGN', '');
define('AISENSY_ANN_CAMPAIGN', '');
define('AISENSY_MEM_APPR_CAMPAIGN', '');
define('AISENSY_EVENT_REG_CAMPAIGN', 'yi_registration_cnf_api');

define('CONST_WA_OVERRIDE', ''); //  

/************************************************************************************************************/
define('CONST_TITLE_AFX', ' | 10X AIF');
define('CONST_SESSION_NAME','SSVERVIO');
define('CONST_APP_NAME', '10X AIF');
define('CONST_ORG_DISP_NAME', '10X AIF');
define('CONST_SECRET_ACCESS_KEY', '%%MEMDIRNAA***==***MDR%%');
define('CONST_HASH_FUNCTION', 'sha256');
define('CONST_ENABLE_THIRD_PARTY_CLIENT_SIDE_ERROR_TRACKING',false);
define('CONST_ENABLE_THIRD_PARTY_SERVER_SIDE_ERROR_TRACKING',false);
define('CONST_ROLLBAR_CLIENT_SIDE_ACCESS_TOKEN','');
define('CONST_ROLLBAR_SERVER_SIDE_ACCESS_TOKEN','');
define('CONST_ROLLBAR_ENVIRONMENT','local');

define('CONST_APP_PATH_FROM_ROOT', ''); // '' if the app is hosted at the root level else /path/to/the/dir/from/root without the leading slash but with the trailing slash, eg. /sites/trcnew
define('CONST_APP_FULL_PHYSICAL_PATH', preg_replace("/^(.*?)[\/\\\\]$/","$1",realpath(dirname(__FILE__))));
define('CONST_HOST', !empty($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'www.ssaraansh.com');
define('CONST_APP_ABSURL', 'http' . (( array_key_exists('HTTPS',$_SERVER) && $_SERVER['HTTPS'] != '' ? 's' : '' ). '://'.CONST_HOST ) . CONST_APP_PATH_FROM_ROOT);
define('CONST_CURR_SCRIPT',pathinfo($_SERVER['SCRIPT_NAME'])['basename']);


define('CONST_SESSION_COOKIE_PATH','/');
define('CONST_APP_SESSION_BASE_INDEX', CONST_APP_PATH_FROM_ROOT);
define('CONST_APP_ASSETS_ROOT_URI', CONST_APP_ABSURL);
define('CONST_CLASS_DIR', CONST_APP_FULL_PHYSICAL_PATH.'/cls/');
define('CONST_JAVASCRIPT_DIR', CONST_APP_PATH_FROM_ROOT.'/js/');

define('CONST_INCLUDES_DIR', CONST_APP_FULL_PHYSICAL_PATH.'/includes/');
define('CONST_TEMPORARY_FOLDER_PHYSICAL_PATH', CONST_APP_FULL_PHYSICAL_PATH.'/temporary/');

define('CONST_THEMES_DIR_FULL_PHYSICAL_PATH',CONST_APP_FULL_PHYSICAL_PATH); // without trailing '/'
define('CONST_THEMES_DIR_PATH_FROM_ROOT',CONST_APP_PATH_FROM_ROOT); // without trailing '/'
define('CONST_THEMES_JAVASCRIPT_PATH',CONST_APP_ASSETS_ROOT_URI.'/js/');
define('CONST_THEMES_CSS_PATH',CONST_APP_ASSETS_ROOT_URI.'/css/');
define('CONST_THEMES_CUSTOM_CSS_PATH',CONST_APP_ASSETS_ROOT_URI.'/custom-css/');
define('CONST_THEMES_CUSTOM_IMAGES_PATH',CONST_APP_ASSETS_ROOT_URI.'/custom-images/');
define('CONST_THEMES_CUSTOM_JAVASCRIPT_PATH',CONST_APP_ASSETS_ROOT_URI.'/custom-js/');
define('CONST_THEMES_TEMPLATE_INCLUDE_PATH',CONST_THEMES_DIR_FULL_PHYSICAL_PATH.'/templates/');
define('EMAIL_TEMPLATE_PATH', CONST_THEMES_TEMPLATE_INCLUDE_PATH.'email/');
define('CONST_NOIMAGE_FILE',CONST_THEMES_CUSTOM_IMAGES_PATH.'noimage.jpeg');
define('CONST_NOIMAGE_M_FILE',CONST_THEMES_CUSTOM_IMAGES_PATH.'noimage-man.jpg');
define('CONST_NOIMAGE_F_FILE',CONST_THEMES_CUSTOM_IMAGES_PATH.'noimage-woman.jpg');
define('CONST_PROFILE_IMG_PREFIX','dp-');
define('CONST_PROFILE_IMG_DIM', [
	'w'=>300, // px
	'h'=>113, // px
	'dw'=>200, // px display max width		
]);
define('CONST_PROFILE_IMG_DIR_PATH',CONST_THEMES_DIR_FULL_PHYSICAL_PATH.'/custom-images/dp/');
define('CONST_PROFILE_IMG_URL_PATH',CONST_APP_ASSETS_ROOT_URI.'/custom-images/dp/');

define('CONST_AD_BANNER_DIM',[
    'dsk' => [
        'mh' => 100, // px
    ],
    'mob' => [
        'mh' => 50, // px
    ]
    
]);
define('CONST_AD_BANNER_IMG_PREFIX','ad-');
define('CONST_AD_BANNER_IMG_DIR_PATH',CONST_THEMES_DIR_FULL_PHYSICAL_PATH.'/custom-images/adb/');
define('CONST_AD_BANNER_IMG_URL_PATH',CONST_APP_ASSETS_ROOT_URI.'/custom-images/adb/');

define('CONST_DISCOUNT_OFFER_CAT_IMG_PREFIX','doffcat-');
define('CONST_DISCOUNT_OFFER_IMG_PREFIX','doff-');
define('CONST_DISCOUNT_OFFER_IMG_DIR_PATH',CONST_THEMES_DIR_FULL_PHYSICAL_PATH.'/custom-images/discount-offers/');
define('CONST_DISCOUNT_OFFER_IMG_URL_PATH',CONST_APP_ASSETS_ROOT_URI.'/custom-images/discount-offers/');
define('CONST_DOFF_NOIMAGE_FILE',CONST_THEMES_CUSTOM_IMAGES_PATH.'cat-noimage.jpg');

define('CONST_DISCOUNT_OFFER_MOU_PREFIX','doff-mou-');
define('CONST_DISCOUNT_OFFER_MOU_DIR_PATH',CONST_THEMES_DIR_FULL_PHYSICAL_PATH.'/custom-images/discount-offers/mou/');
define('CONST_DISCOUNT_OFFER_MOU_URL_PATH',CONST_APP_ASSETS_ROOT_URI.'/custom-images/discount-offers/mou/');

define('CONST_EVENT_IMG_PREFIX','ev-');
define('CONST_EVENT_IMG_DIR_PATH',CONST_THEMES_DIR_FULL_PHYSICAL_PATH.'/custom-images/event/');
define('CONST_EVENT_IMG_URL_PATH',CONST_APP_ASSETS_ROOT_URI.'/custom-images/event/');


define('CONST_LOGO_IMAGE_URI',CONST_THEMES_CUSTOM_IMAGES_PATH.'logo-lg.png');
define('LOGO_IMAGE_PATH', CONST_APP_FULL_PHYSICAL_PATH.'/custom-images/logo-lg.png');

define('CONST_LOGO_IMAGE_NAVBAR',CONST_THEMES_CUSTOM_IMAGES_PATH.'t-logo-lg.png');
define('CONST_LOGO_IMAGE_LARGE', CONST_THEMES_CUSTOM_IMAGES_PATH.'t-logo-lg.png');

// Frontend section
define('CONST_FRONTEND_ROOT_URI', CONST_APP_ABSURL.'/../');
define('CONST_FRONTEND_TITLE_PFX', '');
define('CONST_FRONTEND_TITLE_SFX', '');
define('CONST_FRONTEND_INCLUDES_DIR', CONST_APP_FULL_PHYSICAL_PATH.'/../includes/');
define('CONST_FRONTEND_PAGE_COMP_DIR', CONST_FRONTEND_INCLUDES_DIR.'/page-components/');
define('CONST_FRONTEND_PAGE_COMP_DIR', CONST_APP_FULL_PHYSICAL_PATH.'/../');
define('CONST_FRONTEND_ASSETS_ROOT_URI', CONST_FRONTEND_ROOT_URI.'assets/');
define('CONST_FRONTEND_CSS_URI',CONST_FRONTEND_ASSETS_ROOT_URI.'/css/');
define('CONST_FRONTEND_JS_URI',CONST_FRONTEND_ASSETS_ROOT_URI.'/js/');
define('CONST_FRONTEND_IMAGES_URI',CONST_FRONTEND_ASSETS_ROOT_URI.'/img/');
define('CONST_FRONTEND_VENDOR_URI',CONST_FRONTEND_ASSETS_ROOT_URI.'/vendor/');
define('CONST_FRONTEND_FAVICON_URI',CONST_APP_ABSURL.'/../favicon/');


define('CONST_PROD_GRP_CODE_MAX_LEN',1);

define('CONST_LOG_ERRORS', true);
define('CONST_ERROR_LOG', CONST_INCLUDES_DIR.'system-error-log.txt');

define('CONST_TIME_ZONE','Asia/Kolkata'); // default timezone
define('CONST_ICON_IMG_PATH',CONST_APP_PATH_FROM_ROOT.'/assets/avatars/');// Icons

define('CONST_RECORDS_PER_PAGE',24);

define('CONST_PAGE_LINKS_COUNT',5);
define('DROPDOWN_ITEMS_COUNT',5);

// 0 - No Reporting, 1 - All Errors, 2 - Critical Errors
define('ERRORREPORTING','2');//
define('CONST_ENDL',chr(10));

define('CONST_PSWD_RESET_REQ_INTERVAL_IN_SEC',5*60); // 5 min
define('CONST_PSWD_RESET_LINK_VALIDITY_IN_SEC',4*60*60); // 4 hrs
define('SHOW_REFRESH', false);

$device='';
//if(preg_match('/(iPod|iPhone|iPad)/i', $_SERVER['HTTP_USER_AGENT']) && preg_match('/AppleWebKit/i', $_SERVER['HTTP_USER_AGENT'])){
//	$device = 'IOS';
//}else if(stristr($_SERVER['HTTP_USER_AGENT'], 'android')){
//	$device = 'ANDROID';
//}
define('DEVICE', $device);
define('AUTOCOMPLETE_WAIT_TIME', 300);
$_phone_type_abbr=array('Home'=>'H','Mobile'=>'M','Land line'=>'L','Work'=>'W','Fax'=>'F','Other'=>'O');
$_email_type_abbr=array('Main'=>'M','Personal'=>'P','Work'=>'W', 'Other'=>'O');

$_php_date_format_to_bootstrap_mapping=array('j'=>'d','d'=>'dd', 'D'=>'D', 'l'=>'DD', 'n'=>'m', 'm'=>'mm', 'M'=>'M', 'F'=>'MM', 'y'=>'yy', 'Y'=>'yyyy');

$_date_display_formats=array(
				'd-m-Y'=>array('for_user_disp'=>'dd-mm-yyyy', 'for_bootstrap_picker'=>'dd-mm-yyyy', 'mysql_format'=>'%d-%m-%Y', 'js_format'=>'dd-mm-yyyy'),
				'm-d-Y'=>array('for_user_disp'=>'mm-dd-yyyy', 'for_bootstrap_picker'=>'mm-dd-yyyy', 'mysql_format'=>'%m-%d-%Y', 'js_format'=>'mm-dd-yyyy'),
				'd-M-Y'=>array('for_user_disp'=>'dd-mmm-yyyy', 'for_bootstrap_picker'=>'dd-M-yyyy', 'mysql_format'=>'%d-%b-%Y', 'js_format'=>'dd-mmm-yyyy'),
				'M-d-Y'=>array('for_user_disp'=>'mmm-dd-yyyy', 'for_bootstrap_picker'=>'M-dd-yyyy', 'mysql_format'=>'%b-%d-%Y', 'js_format'=>'mmm-dd-yyyy')
			);

$_months=array(1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec');

$_salutations=array(
	"Mr"=>array('text'=>'Mr', 'gender'=>'M'),
	"Ms"=>array('text'=>'Ms', 'gender'=>'F'),
// 	"Miss"=>array('text'=>'Miss', 'gender'=>'F'),
	"Mrs"=>array('text'=>'Mrs', 'gender'=>'F'),
 	"Dr"=>array('text'=>'Dr', 'gender'=>''),
// 	"Prof"=>array('text'=>'Prof', 'gender'=>''),
// 	"ER"=>array('text'=>'ER', 'gender'=>''),
// 	"CA"=>array('text'=>'CA', 'gender'=>'')

	);

$_active_statuses = [
	1=>'Active',
	0=>'Inactive'
];

$user_types = ['member'=>'Member', 'emp'=>'Employees'];

$sms_service_domains = array('ebizindia');

define('CONST_SESSION_STORAGE','FILE'); // allowed values: DB for database based session, FILE for file based session

define('IS_LOCAL_SERVER', true);
define('IS_DEMO_SERVER', false);
define('BLOCKED_EXTENSIONS', 'php,exe,js,sh');

define('MAX_TASK_ATTACHMENTS_IN_UPLOAD', 5);
define('MAX_TASK_ATTACHMENTS_SIZE', 2 * 1024 * 1024);
define('LOGO_IMAGE_PATH_TRANS', CONST_APP_FULL_PHYSICAL_PATH.'/custom-images/t-logo-lg.png');
define('AFTER_LOGIN_REDIRECT_USER_TO_URL', CONST_APP_ABSURL . '/');
define('CONST_LICENSED_TO', '10X AIF');

define('CONST_FIELD_META', [
	'name' => [
		'len' => 15,
		'regex' => "/^[A-Z -]+$/i",
	],
	'mobile' => [
		'len' => 15,
		'regex' => "/^[+]?\d{8,15}$/",
	],
	'batch_no' => [
		'len' => 4,
		'regex' => "/^\d{4}$/",
		'min' => 1940,
		'max' => date('Y'),
	],
	'mem_no' => [
		'len' => 30,
		'code_regex' => "/^[A-Z0-9][A-Z0-9-]*$/i",
	],
	'work_type' => [
		'len' => 100,
	],
	'work_ind' => [
		'len' => 255,
	],
	'work_company' => [
		'len' => 255,
	],
	'city' => [
		'len' => 60,
	],
	'state' => [
		'len' => 60,
	],
	'country' => [
		'len' => 70,
	],
	'pin' => [
		'len' => 20,
	],
	'addrline' => [
		'len' => 255,
	],
	'pswd' => [
		'len' => 20,
	],
	'feedback_to_admin'=>[
		'len' => 500
	],
	'notice_to_mem' =>[
		'attachment_types' => ['doc', 'docx', 'xls', 'xlsx', 'pdf', 'txt', 'csv']
	],
	'profile_pic' =>[
		'file_types' => ['png', 'jpg', 'jpeg']
	],
	'ad_banner' => [
		'file_types' => ['png', 'jpg', 'jpeg'],
		
	],
	'event' => [
		'file_types' => ['png', 'jpg', 'jpeg', 'webp'],
		'img_dim' => [
		    'dsk' => [
                'mw' => 600, // px
            ],
            'mob' => [
                'mw' => 400, // px
            ]    
		    
		],
		
	],
	'discount_offer' => [
		'file_types' => ['png', 'jpg', 'jpeg']
	]
	
]);


define('CONST_MEMSHIP_TYPE', [
	'Member',

]);

define('CONST_MIME_TYPES', [
	'doc'=>['application/msword'],
	'docx'=>['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
	'xls'=>['application/vnd.ms-excel'],
	'xlsx'=>['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
	'pdf'=>['application/pdf'],
	'txt'=>['application/txt', 'text/plain'],
	'csv'=>['application/csv', 'text/csv', 'application/vnd.ms-excel'],
	'png'=>['image/png'],
	'jpg'=>['image/jpg', 'image/jpeg'],
	'jpeg'=>['image/jpg', 'image/jpeg'],
	'webp'=>['image/webp'],
]);

define('CONST_MEM_PROF_EDIT_RESTC', [
	'ADMIN' => [
		'self' => [
			true, // can edit own profile
			[
				'status',
				// 'membership_no',
			], // restricted fields list
		],
		'others' => [
			true, // can edit other's profile
			[
			 //   'membership_no',
			], // restricted fields list
		]	
	],
	'REGULAR' => [
		'self' => [
			true, // can edit own profile
			[
				'batch_no',
				'membership_no',
				'groups',
				'desig_in_assoc',
				'role',
				'status',
				'remarks',
				'joining_dt',
			], // restricted fields  list
		],
		'others' => [
			false, // cannot edit other's profile
			'restr_flds' => [], // restricted fields list
		]	

	],

]);

define('CONST_BLOOD_GRPS', [

	'A+'=>'A+',
	'A-'=>'A-',
	'B+'=>'B+',
	'B-'=>'B-',
	'AB+'=>'AB+',
	'AB-'=>'AB-',
	'O+'=>'O+',
	'O-'=>'O-',
	'Not Known'=>'Not Known',
]);



?>