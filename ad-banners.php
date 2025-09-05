<?php
$page='ad-banners';
require_once 'inc.php';
$template_type='';
$page_title = 'Manage Sponsor Ads'.CONST_TITLE_AFX;
$page_description = 'One can manage sponsors\' Ad Banners.';
$body_template_file = CONST_THEMES_TEMPLATE_INCLUDE_PATH . 'ad-banners.tpl';
$body_template_data = array();
$page_renderer->registerBodyTemplate($body_template_file,$body_template_data);
$email_pattern="/^\w+([.']?-*\w+)*@\w+([.-]?\w+)*(\.\w{2,4})+$/i";
$user_date_display_format_for_storage = 'd-m-Y';
$can_add = $can_edit = true; 
$_cu_role = $loggedindata[0]['profile_details']['assigned_roles'][0]['role'];

$rec_fields = [
	'name'=>'', 
	'target_link'=>'', 
	'start_dt'=>'', 
	'end_dt'=>'', 
	'active' => '',
];

if(filter_has_var(INPUT_POST,'mode') && $_POST['mode']=='createrec'){
	$result=array('error_code'=>0,'message'=>[], 'elemid'=>array(), 'other_data'=>[]);
	
	if($can_add===false){
		$result['error_code']=403;
		$result['message']="Sorry, you are not authorised to perfom this action.";
	}else{

		$data=array();
		$data = \eBizIndia\trim_deep(\eBizIndia\striptags_deep(array_intersect_key($_POST, $rec_fields)));
		$other_data['field_meta'] = CONST_FIELD_META;
		$other_data['dsk_img'] = $_FILES['dsk_img'];
		$other_data['mob_img'] = $_FILES['mob_img'];
		$ad_obj = new \eBizIndia\AdBanner();	
		$validation_res = $ad_obj->validate($data, 'add', $other_data);
		if($validation_res['error_code']>0){
			$result = $validation_res;
		} else {
			$created_on = date('Y-m-d H:i:s');
			$ip = \eBizIndia\getRemoteIP();
			$data['created_on'] = $created_on;
			$data['created_by'] = $loggedindata[0]['id'];
			$data['created_from'] = $ip;
			try{
				$conn = \eBizIndia\PDOConn::getInstance();
				$conn->beginTransaction();
				$error_details_to_log['mode'] = 'createAdBanner';
				$error_details_to_log['part'] = 'Create a new Ad banner.';
				$rec_id=$ad_obj->saveDetails($data);
				if($rec_id===false)
					throw new Exception('Error creating a new Ad Banner.');

				$image_names_to_update = [];
				$banner_img_res = $ad_obj->uploadBannerImage($rec_id, $_FILES['dsk_img']['name'], $_FILES['dsk_img']['tmp_name'], 'dsk');
				if(empty($banner_img_res)){
					$result['error_code']=3;
					$result['message']='The Ad Banner could not be created as the banner image for desktop screens could not be uploaded.';
					throw new Exception('Error creating a new Ad Banner.');
				}else{
					$image_names_to_update['dsk_img'] = $banner_img_res['gen_file_name'];
				}

				$banner_img_res = $ad_obj->uploadBannerImage($rec_id, $_FILES['mob_img']['name'], $_FILES['mob_img']['tmp_name'], 'mob');
				if(empty($banner_img_res)){
					$result['error_code']=3;
					$result['message']='The Ad Banner could not be created as the banner image for mobile screens could not be uploaded.';
					throw new Exception('Error creating a new Ad Banner.');
				}else{
					$image_names_to_update['mob_img'] = $banner_img_res['gen_file_name'];
				}

				if(!$ad_obj->saveDetails($image_names_to_update, $rec_id)){
					unlink(CONST_AD_BANNER_IMG_DIR_PATH.$image_names_to_update['dsk_img']);
					unlink(CONST_AD_BANNER_IMG_DIR_PATH.$image_names_to_update['mob_img']);
					$result['error_code']=4;
					$result['message']='The Ad Banner could not be created as the banner images could not be registered with the banner.';
					throw new Exception('Error creating a new Ad Banner.');
				}

				$result['error_code']=0;
				$result['message']='The Ad banner <b>'.\eBizIndia\_esc($data['name']).'</b> has been created.';
				$conn->commit();

			}catch(\Exception $e){
				$last_error = \eBizIndia\PDOConn::getLastError();
				if($result['error_code']==0){
					$result['error_code']=1; // DB error
					$result['message']="The Ad Banner could not be created due to server error.";
				}
				$error_details_to_log['member_data'] = $member_data;
				$error_details_to_log['login_account_data'] = $login_account_data;
				$error_details_to_log['result'] = $result;
				\eBizIndia\ErrorHandler::logError($error_details_to_log, $e);
				if($conn && $conn->inTransaction())
					$conn->rollBack();
			}
		}
	}


	$_SESSION['create_rec_result'] = $result;
	header("Location:?");
	exit;

}elseif(filter_has_var(INPUT_POST,'mode') && $_POST['mode']=='updaterec'){
	$result=array('error_code'=>0,'message'=>[],'other_data'=>[]);
	if($can_edit===false){
		$result['error_code']=403;
		$result['message']="Sorry, you are not authorised to update the Ad Banners.";
	}else {
		$data=array();
		$recordid=(int)$_POST['recordid']; 
		// data validation
		if($recordid == ''){
			$result['error_code']=2;
			$result['message'][]="Invalid Ad banner reference.";
		}else{
			$ad_obj = new \eBizIndia\AdBanner($recordid);
			$recorddetails  = $ad_obj->getDetails();
			if($recorddetails===false){
				$result['error_code']=1;
				$result['message'][]="Failed to verify the Ad Banner details due to server error.";
				$result['error_fields'][]="#add_form_field_name";
			}elseif(empty($recorddetails)){
				// Ad banner with this ID does not exist
				$result['error_code']=3;
				$result['message'][]="The Ad banner you are trying to modify was not found.";
				$result['error_fields'][]="#add_form_field_name";
			}else{
				$edit_restricted_fields = [];
				
				$rec_fields = array_diff_key($rec_fields, array_fill_keys($edit_restricted_fields, '')); // removing the edit restricted fields from the list of fields
				
				$data = \eBizIndia\trim_deep(\eBizIndia\striptags_deep(array_intersect_key($_POST, $rec_fields)));
				$other_data['field_meta'] = CONST_FIELD_META;
				$other_data['recorddetails'] = $recorddetails[0];
				$other_data['dsk_img'] = !in_array('dsk_img', $edit_restricted_fields)?$_FILES['dsk_img']:[];
				$other_data['mob_img'] = !in_array('mob_img', $edit_restricted_fields)?$_FILES['mob_img']:[];
				$other_data['edit_restricted_fields'] = $edit_restricted_fields;
				$validation_res = $ad_obj->validate($data, 'update', $other_data); 
				if($validation_res['error_code']>0){
					$result = $validation_res;
				} else {
					$data_to_update = [];
					$curr_dttm = date('Y-m-d H:i:s');
					$ip = \eBizIndia\getRemoteIP();
										
					foreach($rec_fields as $fld=>$val){
						if($data[$fld]!==$recorddetails[0][$fld]){
							$data_changed = true;
							$data_to_update[$fld] = $data[$fld];
							
						}
					}


					try{
						if(!empty($data_to_update) || (!in_array('dsk_img', $edit_restricted_fields) && $_FILES['dsk_img']['error']===0) || (!in_array('mob_img', $edit_restricted_fields) && $_FILES['mob_img']['error']===0) ){
							// Initialize with a common success message and code
							$result['error_code'] = 0;
							$result['message']='The changes have been saved.';

							$data_to_update['updated_on'] = $curr_dttm;
							$data_to_update['updated_by'] = $loggedindata[0]['id'];
							$data_to_update['updated_from'] = $ip;

							if($_FILES['dsk_img']['error']===0){
								$banner_img_res = $ad_obj->uploadBannerImage($recordid, $_FILES['dsk_img']['name'], $_FILES['dsk_img']['tmp_name'], 'dsk');
								if(empty($banner_img_res)){
									$result['error_code']=3;
									$result['message']='The Ad Banner could not be updated as the banner image for desktop screens could not be uploaded.';
									throw new Exception('Error creating a new Ad Banner.');
								}else{
									$data_to_update['dsk_img'] = $banner_img_res['gen_file_name'];
								}
							}

							if($_FILES['mob_img']['error']===0){
								$banner_img_res = $ad_obj->uploadBannerImage($recordid, $_FILES['mob_img']['name'], $_FILES['mob_img']['tmp_name'], 'mob');
								if(empty($banner_img_res)){
									$result['error_code']=3;
									$result['message']='The Ad Banner could not be updated as the banner image for mobile screens could not be uploaded.';
									if(!empty($data_to_update['dsk_img']))
										unlink(CONST_AD_BANNER_IMG_DIR_PATH.$data_to_update['dsk_img']);
									throw new Exception('Error creating a new Ad Banner.');
								}else{
									$data_to_update['mob_img'] = $banner_img_res['gen_file_name'];
								}
							}

							if(!$ad_obj->updateDetails($data_to_update)){
								if(!empty($data_to_update['dsk_img']))
									unlink(CONST_AD_BANNER_IMG_DIR_PATH.$data_to_update['dsk_img']);
								if(!empty($data_to_update['mob_img']))
									unlink(CONST_AD_BANNER_IMG_DIR_PATH.$data_to_update['mob_img']);
								$result['error_code']=4;
								$result['message']='The Ad Banner could not be created as the banner images could not be registered with the banner.';
								throw new Exception('Error creating a new Ad Banner.');
							}else{
								// remove the old images
								if(!empty($data_to_update['dsk_img'])){
									unlink(CONST_AD_BANNER_IMG_DIR_PATH.$recorddetails[0]['dsk_img']);
									$result['other_data']['dsk_img_url'] = CONST_AD_BANNER_IMG_URL_PATH.$data_to_update['dsk_img'];
									$pic_size = getimagesize(CONST_AD_BANNER_IMG_DIR_PATH.$data_to_update['dsk_img']);
									$result['other_data']['dsk_img_org_width'] = $pic_size[0];
								}
								if(!empty($data_to_update['mob_img'])){
									unlink(CONST_AD_BANNER_IMG_DIR_PATH.$recorddetails[0]['mob_img']);
									$result['other_data']['mob_img_url'] = CONST_AD_BANNER_IMG_URL_PATH.$data_to_update['mob_img'];
									$pic_size = getimagesize(CONST_AD_BANNER_IMG_DIR_PATH.$data_to_update['mob_img']);
									$result['other_data']['mob_img_org_width'] = $pic_size[0];
								}


								
							}

							$result['error_code']=0;
							$result['message']='The changes have been saved.';
							
						}else{
							$result['error_code']=4;
							$result['message']='There were no changes to save.';
						}
					}catch(\Exception $e){
						$last_error = \eBizIndia\PDOConn::getLastError();
												
						$error_details_to_log['login_account_data'] = $login_account_data;
						$error_details_to_log['result'] = $result;
						\eBizIndia\ErrorHandler::logError($error_details_to_log, $e);
					}
				
				}
			}

		}

	}

	$_SESSION['update_user_result']=$result;

	header("Location:?");
	exit;

}elseif(isset($_SESSION['update_user_result']) && is_array($_SESSION['update_user_result'])){
	header("Content-Type: text/html; charset=UTF-8");
	echo "<script type='text/javascript' >\n";
	echo "parent.adfuncs.handleUpdateRecResponse(".json_encode($_SESSION['update_user_result']).");\n";
	echo "</script>";
	unset($_SESSION['update_user_result']);
	exit;

}elseif(isset($_SESSION['create_rec_result']) && is_array($_SESSION['create_rec_result'])){
	header("Content-Type: text/html; charset=UTF-8");
	echo "<script type='text/javascript' >\n";
	echo "parent.adfuncs.handleAddRecResponse(".json_encode($_SESSION['create_rec_result']).");\n";
	echo "</script>";
	unset($_SESSION['create_rec_result']);
	exit;

}elseif(filter_has_var(INPUT_POST,'mode') && $_POST['mode']=='getRecordDetails'){
	$result=array();
	$error=0; // no error
	$can_edit = true;
	
	if($_POST['recordid']==''){
		$error=1; // Record ID missing

	}else{
		$obj = new \eBizIndia\AdBanner((int)$_POST['recordid']);
		$recorddetails = $obj->getDetails();

		if($recorddetails===false){
			$error=2; // db error
		}elseif(count($recorddetails)==0){
			$error=3; // Rec ID does not exist
		}else{
			$recorddetails=$recorddetails[0];
			$edit_restricted_fields = [];

			$recorddetails['name_disp'] = \eBizIndia\_esc($recorddetails['name_disp'], true);
			
			$recorddetails['dsk_img_url'] = CONST_AD_BANNER_IMG_URL_PATH.$recorddetails['dsk_img'];
			$pic_size = getimagesize(CONST_AD_BANNER_IMG_DIR_PATH.$recorddetails['dsk_img']);
			$recorddetails['dsk_img_org_width'] = $pic_size[0];

			$recorddetails['mob_img_url'] = CONST_AD_BANNER_IMG_URL_PATH.$recorddetails['mob_img'];
			$pic_size = getimagesize(CONST_AD_BANNER_IMG_DIR_PATH.$recorddetails['mob_img']);
			$recorddetails['mob_img_org_width'] = $pic_size[0];
		}
	}

	$result[0]=$error;
	$result[1]['can_edit'] = $can_edit;
	$result[1]['cuid'] = $loggedindata[0]['id'];  // This is the auto id of the table users and not member
	$result[1]['record_details']=$recorddetails;
	$result[1]['edit_restricted_fields']=$edit_restricted_fields;
	
	echo json_encode($result);

	exit;

}elseif(filter_has_var(INPUT_POST,'mode') && $_POST['mode']=='getList'){
	$result=array(0,array()); // error code and list html
	$show_dnd_status = true;
	$options=[];
	$options['filters']=[];

	$filterparams=array();
	$sortparams=array();

	$pno=(isset($_POST['pno']) && $_POST['pno']!='' && is_numeric($_POST['pno']))?$_POST['pno']:((isset($_GET['pno']) && $_GET['pno']!='' && is_numeric($_GET['pno']))?$_GET['pno']:1);
	$recsperpage=(isset($_POST['recsperpage']) && $_POST['recsperpage']!='' && is_numeric($_POST['recsperpage']))?$_POST['recsperpage']:((isset($_GET['recsperpage']) && $_GET['recsperpage']!='' && is_numeric($_GET['recsperpage']))?$_GET['recsperpage']:CONST_RECORDS_PER_PAGE);

	$filtertext = [];
	if(filter_has_var(INPUT_POST, 'searchdata') && $_POST['searchdata']!=''){
		$searchdata=json_decode($_POST['searchdata'],true);
		if(!is_array($searchdata)){
			$error=2; // invalid search parameters
		}else if(!empty($searchdata)){
			$options['filters']=[];
			foreach($searchdata as $filter){
				$field=$filter['searchon'];

				if(array_key_exists('searchtype',$filter)){
					$type=$filter['searchtype'];

				}else{
					$type='';

				}

				if(array_key_exists('searchtext', $filter))
					$value= \eBizIndia\trim_deep($filter['searchtext']);
				else
					$value='';

				$options['filters'][] = array('field'=>$field,'type'=>$type,'value'=>$value);

				if($field=='name')
					$fltr_text = 'Name ';
				else if($field=='target_link')
					$fltr_text = 'Target URL ';
				else if($field=='falls_in_period')
					$fltr_text = 'Falls in the period  ';
				else 
					$fltr_text = ucfirst($field).' ';
				
				if($field!=='falls_in_period'){
					switch($type){
						case 'CONTAINS':
							$fltr_text .= 'has ';	break;
						case 'EQUAL':
							$fltr_text .= 'is ';	break;
						case 'STARTS_WITH':
							$fltr_text .= 'starts with ';	break;
						case 'AFTER':
							$fltr_text .= 'after ';	break;
					}
				}

				$disp_value = $field=='falls_in_period'?date('d-M-Y', strtotime($value[0])).' TO '.date('d-M-Y', strtotime($value[1])):$value;

				$filtertext[]='<span class="searched_elem"  >'.$fltr_text.'  <b>'.\eBizIndia\_esc($disp_value, true).'</b><span class="remove_filter" data-fld="'.$field.'"  >X</span> </span>';
			}
			$result[1]['filtertext'] = implode($filtertext);
		}
	}

	$tot_rec_options = [
		'fieldstofetch'=>['recordcount'],
		'filters' => [],
	];

	$options['fieldstofetch'] = ['recordcount'];

	// get total emp count
	$tot_rec_cnt = \eBizIndia\AdBanner::getList($tot_rec_options); 
	$result[1]['tot_rec_cnt'] = $tot_rec_cnt[0]['recordcount'];

	// $recordcount=$usercls->getList($options);
	$recordcount = \eBizIndia\AdBanner::getList($options);
	$recordcount = $recordcount[0]['recordcount'];
	$paginationdata=\eBizIndia\getPaginationData($recordcount,$recsperpage,$pno,CONST_PAGE_LINKS_COUNT);
	$result[1]['paginationdata']=$paginationdata;


	if($recordcount>0){
		$noofrecords=$paginationdata['recs_per_page'];
		unset($options['fieldstofetch']);
		$options['page'] = $pno;
		$options['recs_per_page'] = $noofrecords;

		if(isset($_POST['sortdata']) && $_POST['sortdata']!=''){
			$options['order_by']=[];
			$sortdata=json_decode($_POST['sortdata'],true);
			foreach($sortdata as $sort_param){

				$options['order_by'][]=array('field'=>$sort_param['sorton'],'type'=>$sort_param['sortorder']);

			}
		}

		$records=\eBizIndia\AdBanner::getList($options);
		
		if($records===false){
			$error=1; // db error
		}else{
			$result[1]['list']=$records;
		}
	}

	$result[0]=$error;
	$result[1]['reccount']=$recordcount;

	if($_POST['listformat']=='html'){

		$get_list_template_data=array();
		$get_list_template_data['mode']=$_POST['mode'];
		$get_list_template_data[$_POST['mode']]=array();
		$get_list_template_data[$_POST['mode']]['error']=$error;
		$get_list_template_data[$_POST['mode']]['records']=$records;
		$get_list_template_data[$_POST['mode']]['records_count']=count($records??[]);
		$get_list_template_data[$_POST['mode']]['cu_id']=$loggedindata[0]['id'];
		$get_list_template_data[$_POST['mode']]['filtertext']=$result[1]['filtertext'];
		$get_list_template_data[$_POST['mode']]['filtercount']=count($filtertext);
		$get_list_template_data[$_POST['mode']]['tot_col_count']=count($records[0]??[])+1; // +1 for the action column

		$paginationdata['link_data']="";
		$paginationdata['page_link']='#';//"users.php#pno=<<page>>&sorton=".urlencode($options['order_by'][0]['field'])."&sortorder=".urlencode($options['order_by'][0]['type']);
		$get_list_template_data[$_POST['mode']]['pagination_html']=$page_renderer->fetchContent(CONST_THEMES_TEMPLATE_INCLUDE_PATH.'pagination-bar.tpl',$paginationdata);

		$get_list_template_data['logged_in_user']=$loggedindata[0];
		
		$page_renderer->updateBodyTemplateData($get_list_template_data);
		$result[1]['list']=$page_renderer->fetchContent();

	}

	echo json_encode($result,JSON_HEX_TAG);
	exit;

}

$dom_ready_data['users']=array(
								'field_meta' => CONST_FIELD_META,
							);

$additional_base_template_data = array(
										'page_title' => $page_title,
										'page_description' => $page_description,
										'template_type'=>$template_type,
										'dom_ready_code'=>\scriptProviderFuncs\getDomReadyJsCode($page,$dom_ready_data),
										'other_js_code'=>$jscode,
										'module_name' => $page
									);


$additional_body_template_data = ['can_add'=>$can_add, 'field_meta' => CONST_FIELD_META ];

$page_renderer->updateBodyTemplateData($additional_body_template_data);

$page_renderer->updateBaseTemplateData($additional_base_template_data);
$page_renderer->addCss(\scriptProviderFuncs\getCss($page));
$js_files=\scriptProviderFuncs\getJavascripts($page);
$page_renderer->addJavascript($js_files['BSH'],'BEFORE_SLASH_HEAD');
$page_renderer->addJavascript($js_files['BSB'],'BEFORE_SLASH_BODY');
$page_renderer->renderPage();

?>
