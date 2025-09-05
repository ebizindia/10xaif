<?php
namespace eBizIndia;

function autoload($class){
	$class_path=dirname(__FILE__).'/../cls/';
	$class=$class_path.str_replace('\\', '/', str_replace(__NAMESPACE__.'\\','',$class));
	// echo $class,'<br>';
	if(file_exists($class.'.php') && is_readable($class.'.php')){
		require_once $class.'.php';		
	}
	

}
spl_autoload_register('\eBizIndia\autoload');

?>