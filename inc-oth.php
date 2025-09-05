<?php
mb_http_output("UTF-8");
ob_start("mb_output_handler");
require_once("config.php");

date_default_timezone_set(CONST_TIME_ZONE);
ini_set('display_errors',1);
switch(ERRORREPORTING){
	case "1": error_reporting(E_ALL); break;
	case "2": error_reporting(E_ERROR | E_PARSE); break;
	default: error_reporting(0);
}

require_once(CONST_INCLUDES_DIR."/ebiz-autoload.php");
require_once(CONST_CLASS_DIR."/phpmailer/vendor/autoload.php");
require_once(CONST_INCLUDES_DIR . "general-func.php");

