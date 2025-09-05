<?php
$page='sectors';
require_once 'inc.php';
$template_type='';
$page_title = 'Manage Sectors List'.CONST_TITLE_AFX;
$page_description = 'One can manage the sectors list.';
$body_template_file = CONST_THEMES_TEMPLATE_INCLUDE_PATH . 'sectors.tpl';
$body_template_data = array();
$page_renderer->registerBodyTemplate($body_template_file,$body_template_data);
$can_add = $can_edit = true; 
$_cu_role = $loggedindata[0]['profile_details']['assigned_roles'][0]['role'];

$rec_fields = [
	'sector'=>'', 
	'active' => '',
];

if(filter_has_var(INPUT_POST,'mode') && $_POST['mode']=='createrec'){
	$result=array('error_code'=>0,'message'=>[], 'elemid'=>array(), 'other_data'=>[]);
	
	if($can_add===false){
		$result['error_code']=403;
		$result['message']="Sorry, you are not authorised to perfom this action.";
	}else{

		$data=array();
		$sectors = trim($_POST['sector']);
		
		// $other_data['field_meta'] = CONST_FIELD_META;

		
		if(empty($sectors)){
			$result['error_code'] = 2;
			$result['message'] = 'Sector is required.';
			$result['error_fields'][]="#add_form_field_sector";
		} else {
			$result['other_data']['sectors_prev'] = $sectors;
			$sectors = \eBizIndia\trim_deep(\eBizIndia\striptags_deep(preg_split("/(\r?\n)+/", $sectors)));
			$invalid_sector = array_filter($sectors, function($sec){
				return mb_strlen($sec)>100;
			});
			$valid_sectors = array_filter($sectors, function($sec){
				return $sec!='';
			});
			$result['other_data']['sectors'] = $sectors;
			if(!empty($invalid_sector)){
				$result['error_code'] = 2;
				$result['message'] = 'One or more of the sector values exceed the allowed number of characters.';
				$result['error_fields'][]="#add_form_field_sector";
			}else if(empty($valid_sectors)){
				$result['error_code'] = 2;
				$result['message'] = 'Please enter one or moe valid sector values.';
				$result['error_fields'][]="#add_form_field_sector";
			}else{
				$created_on = date('Y-m-d H:i:s');
				$ip = \eBizIndia\getRemoteIP();
				$data['created_on'] = $created_on;
				$data['created_by'] = $loggedindata[0]['id'];
				$data['created_from'] = $ip;
				try{
					$res = \eBizIndia\MemberSector::add($valid_sectors);
					if(empty($res)){
						throw new Exception('Error adding sectors.');
					}
					$result['error_code'] = 0;
					$result['message'] = count($valid_sectors)>1?'The sectors were added successfully.':'The sector was added successfully.';
				}catch(\Exception $e){
					$last_error = \eBizIndia\PDOConn::getLastError();
					if($result['error_code']==0){
						$result['error_code']=1; // DB error
						$result['message']="The sectors could not be added due to server error.";
					}
					$error_details_to_log['result'] = $result;
					\eBizIndia\ErrorHandler::logError($error_details_to_log, $e);
				}

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
		$result['message']="Sorry, you are not authorised to update the sectors.";
	}else {
		$data=array();
		$recordid=(int)$_POST['recordid']; 
		// data validation
		if($recordid == ''){
			$result['error_code']=2;
			$result['message'][]="Invalid sector reference.";
		}else{
			$options= [];
			$options['filters'] = [
				['field'=>'id', 'type'=>'EQUAL', 'value'=> $recordid],
			];
			$recorddetails  = \eBizIndia\MemberSector::getList($options);
			if($recorddetails===false){
				$result['error_code']=1;
				$result['message'][]="Failed to verify the sector details due to server error.";
				$result['error_fields'][]="#edit_form_field_sector";
			}elseif(empty($recorddetails)){
				// Sector with this ID does not exist
				$result['error_code']=3;
				$result['message'][]="The sector you are trying to modify was not found.";
				$result['error_fields'][]="#edit_form_field_sector";
			}else{
				$edit_restricted_fields = [];
				$rec_fields = array_diff_key($rec_fields, array_fill_keys($edit_restricted_fields, '')); // removing the edit restricted fields from the list of fields
				$data = \eBizIndia\trim_deep(\eBizIndia\striptags_deep(array_intersect_key($_POST, $rec_fields)));
				if($data['sector']==''){
					$result['error_code']=2;
					$result['message'][]="Sector value is required.";
					$result['error_fields'][]="#edit_form_field_sector";
				} elseif(mb_strlen($data['sector'])>100){
					$result['error_code']=2;
					$result['message'][]="Sector value exceeds the allowed number of characters.";
					$result['error_fields'][]="#edit_form_field_sector";
				} elseif($data['active']!='y' && $data['active']!='n'){
					$result['error_code']=2;
					$result['message'][]="Please select a status for the sector value.";
					$result['error_fields'][]="input[name=status]:eq(0)";
				} else {
					$result['other_data']['post'] = $data;
					$data_to_update = [];
					foreach($rec_fields as $fld=>$val){
						if($data[$fld]!==$recorddetails[0][$fld]){
							$data_changed = true;
							$data_to_update[$fld] = $data[$fld];
							
						}
					}

					try{
						if(!empty($data_to_update)){
							// Initialize with a common success message and code
							
							if(!\eBizIndia\MemberSector::update($data_to_update, $recordid))
								throw new Exception('Error updating the sector.');
							
							$result['error_code']=0;
							$result['message']='The changes have been saved.';
							
						}else{
							$result['error_code']=4;
							$result['message']='There were no changes to save.';
						}
					}catch(\Exception $e){
						$last_error = \eBizIndia\PDOConn::getLastError();
						$result['error_code']=1;
						if($last_error[1] == 1062){
							$result['message'] = "Process failed. A sector with this name already exists.";
						}else{
							$result['message']="The sector could not be updated due to server error.";
						}			
						$error_details_to_log['last_error'] = $last_error;
						$error_details_to_log['result'] = $result;
						\eBizIndia\ErrorHandler::logError($error_details_to_log, $e);
					}
				
				}
			}

		}

	}

	$_SESSION['update_rec_result']=$result;

	header("Location:?");
	exit;

}elseif(isset($_SESSION['update_rec_result']) && is_array($_SESSION['update_rec_result'])){
	header("Content-Type: text/html; charset=UTF-8");
	echo "<script type='text/javascript' >\n";
	echo "parent.sectorfuncs.handleUpdateRecResponse(".json_encode($_SESSION['update_rec_result']).");\n";
	echo "</script>";
	unset($_SESSION['update_rec_result']);
	exit;

}elseif(isset($_SESSION['create_rec_result']) && is_array($_SESSION['create_rec_result'])){
	header("Content-Type: text/html; charset=UTF-8");
	echo "<script type='text/javascript' >\n";
	echo "parent.sectorfuncs.handleAddRecResponse(".json_encode($_SESSION['create_rec_result']).");\n";
	echo "</script>";
	unset($_SESSION['create_rec_result']);
	exit;

}elseif(filter_has_var(INPUT_POST,'mode') && $_POST['mode']=='deleteSector'){
	$result=array();
	$error=0; // no error
	if($can_add===false){
		$result['error_code']=403;
		$result['message']="Sorry, you are not authorised to perfom this action.";
	}else if($_POST['rec_id']==''){
		$result['error_code']=2;
		$result['message']="The sector ID reference was not found.";
	}else{
		if(\eBizIndia\MemberSector::delete([$_POST['rec_id']])){
			$result['error_code']=0;
			$result['message']="The sector was deleted successfully.";
		}else{
			$last_error = \eBizIndia\PDOConn::getLastError();
			if($last_error[1]==1451 || $last_error[1]==1452 ){
				$result['error_code']=1;
				$result['message']="The sector could not be deleted as it is use in one more member profiles.";
			}else{
				$result['error_code']=1;
				$result['message']="The sector could not be deleted due to server error.";
			}
		}
	}

	echo json_encode($result);

	exit;

}elseif(filter_has_var(INPUT_POST,'mode') && $_POST['mode']=='getRecordDetails'){
	$result=array();
	$error=0; // no error
	$can_edit = true;
	
	if($_POST['recordid']==''){
		$error=1; // Record ID missing

	}else{
		$options= [];
		$options['filters'] = [
			['field'=>'id', 'type'=>'EQUAL', 'value'=> $_POST['recordid']],
		];
		$recorddetails  = \eBizIndia\MemberSector::getList($options);

		if($recorddetails===false){
			$error=2; // db error
		}elseif(count($recorddetails)==0){
			$error=3; // Rec ID does not exist
		}else{
			$recorddetails=$recorddetails[0];
			$recorddetails['sector_disp'] = \eBizIndia\_esc($recorddetails['sector'], true);
			$edit_restricted_fields = [];
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

				if($field=='sector')
					$fltr_text = 'Sector name ';
				else 
					$fltr_text = ucfirst($field).' ';
				
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
				

				$filtertext[]='<span class="searched_elem"  >'.$fltr_text.'  <b>'.\eBizIndia\_esc($value, true).'</b><span class="remove_filter" data-fld="'.$field.'"  >X</span> </span>';
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
	$tot_rec_cnt = \eBizIndia\MemberSector::getList($tot_rec_options); 
	$result[1]['tot_rec_cnt'] = $tot_rec_cnt[0]['recordcount'];

	// $recordcount=$usercls->getList($options);
	$recordcount = \eBizIndia\MemberSector::getList($options);
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

		$records=\eBizIndia\MemberSector::getList($options);
		
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

$dom_ready_data['sectors']=array(
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


$additional_body_template_data = ['can_add'=>$can_add];

$page_renderer->updateBodyTemplateData($additional_body_template_data);

$page_renderer->updateBaseTemplateData($additional_base_template_data);
$page_renderer->addCss(\scriptProviderFuncs\getCss($page));
$js_files=\scriptProviderFuncs\getJavascripts($page);
$page_renderer->addJavascript($js_files['BSH'],'BEFORE_SLASH_HEAD');
$page_renderer->addJavascript($js_files['BSB'],'BEFORE_SLASH_BODY');
$page_renderer->renderPage();

?>
