<?php
ini_set('session.gc_maxlifetime', 18000); // 5 hours
session_set_cookie_params(0,CONST_SESSION_COOKIE_PATH,$_SERVER['HTTP_HOST'],false,true);
session_name(CONST_SESSION_NAME);
if(session_status() == PHP_SESSION_DISABLED){
	header('HTTP/1.1 403 Forbidden', true, 403);
	echo 'A session could not be initiated.';
	exit;
}
if(session_status() == PHP_SESSION_NONE){ 
	if(!session_start()){
		header('HTTP/1.1 403 Forbidden', true, 403);
		echo 'A session could not be started.';
		exit;
	}
}