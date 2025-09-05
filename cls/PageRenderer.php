<?php
namespace eBizIndia;
use \Exception;
class PageRenderer{

	private $templates;
	private $base_template;
	private $base_template_data;
	private $theme_include_path;
	private $templates_include_path;
	private $javascript_files_before_slash_head;
	private $javascript_files_before_slash_body;
	private $css_files;
	
	function __construct(){
		
		$this->templates=array(); // an array
		
		$this->base_template='';
		$this->base_template_data=array();
		
		$this->body_template='';
		$this->body_template_data=array();
		
		
		$this->theme_include_path='';
		
		$this->templates_include_path='';
		
		$this->javascript_files_before_slash_body=array();
		
		$this->javascript_files_before_slash_head=array();
		
		$this->css_files=array();
		
	}
	function __get($name){
		return $this->{$name};
	}
	function setThemesDirFullPhysicalPath($theme_include_path){
		
		$this->theme_include_path=$theme_include_path;
		$this->templates_include_path=$theme_include_path.'templates/';
		
	}
	
	function registerTemplate($template_file_with_path,$template_data,$template_name=''){
		// $template_file_with_path should be the template file with full physical path
		// $template_data should be an associative array
		// $usefor is the name to access the template and data
		
		if($template_name=='')
			$template_name=pathinfo($template_file_with_path,PATHINFO_FILENAME);
			
		if($template_name!=''){
			$this->templates[$template_name]['file']=$template_file_with_path;
			$this->templates[$template_name]['data']=$template_data;
			return true;
		}	
			
		return false;
		
	}
	
	
	function registerBaseTemplate($template_file_with_path,$template_data){
		if(!file_exists($template_file_with_path))
			return false;
		$this->base_template=$template_file_with_path;
		$this->base_template_data=$template_data;
		return true;
	}
	function updateBaseTemplateData($data_array,$replace=false){
		if(is_array($data_array) && !empty($data_array)){
			if($replace === true)	
				$this->base_template_data=array_replace_recursive($this->base_template_data,$data_array);
			else
				$this->base_template_data=array_merge_recursive($this->base_template_data,$data_array);
		}
		
	}
	
	
	function registerBodyTemplate($template_file_with_path,$template_data){
		if(!file_exists($template_file_with_path))
			return false;
		$this->body_template=$template_file_with_path;
		$this->body_template_data=$template_data;
		return true;
	}
	
	function updateBodyTemplateData($data_array,$replace=false){
		// print_r($data_array);
		// echo "<br>****<br>";

		if(is_array($data_array) && !empty($data_array)){
			if($replace === true)	
				$this->body_template_data=array_replace_recursive($this->body_template_data,$data_array);
			else
				$this->body_template_data=array_merge_recursive($this->body_template_data,$data_array);
		}

		// print_r($this->body_template_data);

		
	}
	
	
	
	function addJavascript($js_file_urls,$location='BEFORE_SLASH_HEAD'){  // allowed location values BEFORE_SLASH_HEAD, BEFORE_SLASH_BODY
		
		if(!is_array($js_file_urls)){
			$js_file_urls=array($js_file_urls);
		}
		
		
		if($location=='BEFORE_SLASH_BODY'){
			$this->javascript_files_before_slash_body=array_unique(array_merge($this->javascript_files_before_slash_body,$js_file_urls));
		}else{	
			$this->javascript_files_before_slash_head=array_unique(array_merge($this->javascript_files_before_slash_head,$js_file_urls));
		}	
		
	}
	
	
	function addCss($css_file_url){
		
		if(is_array($css_file_url)){
			$this->css_files=array_unique(array_merge($this->css_files,$css_file_url));
		}else{
			$this->css_files[]=$css_file_url;
		}
		
	}
	
	
	private function getTemplate($template_index){
	
		require_once $this->templates[$template_index]['file'];
		
		
	}
	
	function isTemplateRegistered($template_index){
		
		if(file_exists($this->templates[$template_index]['file']))
			return true;
		return false;	
		
	}
	

	function renderPage(){
		
		require_once $this->base_template;
		
	}

	function fetchContent($template_file='', $data_for_fetch_template=array()){
		$this->body_template_data['fetch_template_data']=$data_for_fetch_template;
		
		ob_start();

		if($template_file!='')
			require $template_file;
		else
			require $this->body_template;
		$content = ob_get_contents();

		ob_end_clean();
		unset($this->body_template_data['fetch_template_data']);
		return $content;
		
	}


}



?>