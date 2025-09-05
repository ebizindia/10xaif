<?php
$page='manage-offers';
require_once 'inc.php';
$template_type='';
$page_title = 'Epic Offers Master'.CONST_TITLE_AFX;
$page_description = 'One can manage epic Offers.';
$body_template_file = CONST_THEMES_TEMPLATE_INCLUDE_PATH . 'manage-offers.tpl';
$body_template_data = array();
$page_renderer->registerBodyTemplate($body_template_file,$body_template_data);
$can_add = $can_edit = true; 
$discount_offer = new \eBizIndia\DiscountOffer();	

$rec_fields = [
	'category_id'=>'', 
	'title'=>'', 
	'description'=>'', 
	// 'company' => '',
	'offer_url' => '',
	// 'address'=>'', 
	// 'display_start_date' =>'',
	// 'display_end_date' =>'',
	'valid_upto'=>'', 
	//'dsk_img'=>'',
	// 'mob_img'=>'',
	'active' => '',
];

if(filter_has_var(INPUT_POST,'mode') && $_POST['mode']=='create'){
	$result=[
		'error_code'=>0,
		'message'=>[],
		'elemid'=>[],
		'other_data'=>[]
	];
	
	if($can_add===false){
		$result['error_code']=403;
		$result['message']="Sorry, you are not authorised to perfom this action.";
	}else{

		$data = \eBizIndia\trim_deep(\eBizIndia\striptags_deep(array_intersect_key($_POST, $rec_fields)));
		
		// Force predefined dates as these fields have been removed from the interface
		$data['display_start_date'] = '2024-12-01'; 
		$data['display_end_date'] = '2099-12-31';

		$other_data = [
			'field_meta' => CONST_FIELD_META,
			// 'dsk_img' => $_FILES['dsk_img'],
			'mob_img' => $_FILES['mob_img'],
			'mou' => $_FILES['mou'],
		];
		$validation_res = $discount_offer->validate($data, 'add', $other_data);
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
				$error_details_to_log['mode'] = 'create-discount-offer';
				$error_details_to_log['part'] = 'Create a new Discount Offer.';
				$rec_id=$discount_offer->save($data);
				if($rec_id===false)
					throw new Exception('Error creating a new epic Offer.');

				$file_names_to_update = [];
				if($_FILES['mou']['name']){
					$mou_file_res = $discount_offer->uploadOfferMOU($rec_id, $_FILES['mou']['name'], $_FILES['mou']['tmp_name']);
					if(empty($mou_file_res)){
						$result['error_code']=3;
						$result['message']='The epic Offer could not be created as the mou could not be uploaded.';
						throw new Exception('Error creating a new epic Offer.');
					}else{
						$file_names_to_update['mou'] = $mou_file_res['gen_file_name'];
						$file_names_to_update['mou_org_file_name'] = $_FILES['mou']['name'];
					}
				}


				// if($_FILES['dsk_img']['name']){
				// 	$banner_img_res = $discount_offer->uploadOfferImage($rec_id, $_FILES['dsk_img']['name'], $_FILES['dsk_img']['tmp_name'], 'dsk');
				// 	if(empty($banner_img_res)){
				// 		$result['error_code']=3;
				// 		$result['message']='The Discount Offer could not be created as the banner image for desktop screens could not be uploaded.';
				// 		throw new Exception('Error creating a new Discount Offer.');
				// 	}else{
				// 		$file_names_to_update['dsk_img'] = $banner_img_res['gen_file_name'];
				// 	}
				// }

				if($_FILES['mob_img']['name']){
					$banner_img_res = $discount_offer->uploadOfferImage($rec_id, $_FILES['mob_img']['name'], $_FILES['mob_img']['tmp_name'], 'mob');
					if(empty($banner_img_res)){
						$result['error_code']=3;
						$result['message']='The epic Offer could not be created as the image could not be uploaded.';
						throw new Exception('Error creating a new epic Offer.');
					}else{
						$file_names_to_update['mob_img'] = $banner_img_res['gen_file_name'];
					}
				}
				if(count($file_names_to_update)>0){
					if(!$discount_offer->save($file_names_to_update, $rec_id)){
						// unlink(CONST_DISCOUNT_OFFER_IMG_DIR_PATH.$file_names_to_update['dsk_img']);
						unlink(CONST_DISCOUNT_OFFER_IMG_DIR_PATH.$file_names_to_update['mob_img']);
						unlink(CONST_DISCOUNT_OFFER_MOU_DIR_PATH.$file_names_to_update['mou']);
						$result['error_code']=4;
						$result['message']='The Epic Offer could not be created as the image and/or MOU document could not be uploaded.';
						throw new Exception('Error creating a new Epic Offer.');
					}
				}

				$result['error_code']=0;
				$result['message']='The Epic Offer <b>'.\eBizIndia\_esc($data['title'], true).'</b> has been created.';
				$conn->commit();

			}catch(\Exception $e){var_dump($e);die();
				if($result['error_code']==0){
					$result['error_code']=1; // DB error
					$result['message']="The Epic Offer could not be created due to server error.".$e->getMessage();
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


	$_SESSION['create_d_o_rec_result'] = $result;
	header("Location:?");
	exit;

}elseif(filter_has_var(INPUT_POST,'mode') && $_POST['mode']=='update'){
	$result=['error_code'=>0,'message'=>[],'other_data'=>[]];
	if($can_edit===false){
		$result['error_code']=403;
		$result['message']="Sorry, you are not authorised to update the Epic Offers.";
	}else {
		$data=[];
		$rec_id=(int)$_POST['rec_id']; 
		if($rec_id == ''){
			$result['error_code']=2;
			$result['message'][]="Invalid Epic Offer reference.";
		}else{
			$discount_offer = new \eBizIndia\DiscountOffer($rec_id);
			$record_details  = $discount_offer->getDetails();
			if($record_details===false){
				$result['error_code']=1;
				$result['message'][]="Failed to verify the Epic Offer details due to server error.";
				$result['error_fields'][]="#add_form_field_title";
			}elseif(empty($record_details)){
				// Discount Offer with this ID does not exist
				$result['error_code']=3;
				$result['message'][]="The Epic Offer you are trying to modify was not found.";
				$result['error_fields'][]="#add_form_field_title";
			}else{
				$edit_restricted_fields = [];
				
				$rec_fields = array_diff_key($rec_fields, array_fill_keys($edit_restricted_fields, '')); // removing the edit restricted fields from the list of fields
				
				$data = \eBizIndia\trim_deep(\eBizIndia\striptags_deep(array_intersect_key($_POST, $rec_fields)));
				$other_data['field_meta'] = CONST_FIELD_META;
				$other_data['record_details'] = $record_details[0];
				// $other_data['dsk_img'] = !in_array('dsk_img', $edit_restricted_fields)?$_FILES['dsk_img']:[];
				$other_data['mob_img'] = !in_array('mob_img', $edit_restricted_fields)?$_FILES['mob_img']:[];
				$other_data['mou'] = !in_array('mou', $edit_restricted_fields)?$_FILES['mou']:[];
				$other_data['edit_restricted_fields'] = $edit_restricted_fields;
				$delete_dsk_img = $delete_mob_img = $delete_mou = false;
				// if(!in_array('dsk_img', $edit_restricted_fields) && $_POST['delete_dsk_img']==1 && $record_details[0]['dsk_img']!=''){
				// 	$data['disk_img'] = '';
				// 	$delete_dsk_img = true;
				// 	$rec_fields['dsk_img']='';
				// }
				if(!in_array('mou', $edit_restricted_fields) && $_POST['delete_mou_file']==1 && $record_details[0]['mou']!=''){
					$data['mou'] = '';
					$delete_mou = true;
					$rec_fields['mou']='';
					$rec_fields['mou_org_file_name']='';
				}
				if(!in_array('mob_img', $edit_restricted_fields) && $_POST['delete_mob_img']==1 && $record_details[0]['mob_img']!=''){
					$data['mob_img'] = '';
					$delete_mob_img = true;
					$rec_fields['mob_img']='';
				}
				$validation_res = $discount_offer->validate($data, 'update', $other_data); 
				if($validation_res['error_code']>0){
					$result = $validation_res;
				} else {
					$data_to_update = [];
					$curr_dttm = date('Y-m-d H:i:s');
					$ip = \eBizIndia\getRemoteIP();
					
					foreach($rec_fields as $fld=>$val){
						if($fld=='category_id'){
							if($data[$fld]!=$record_details[0][$fld]){
								$data_changed = true;
								$data_to_update[$fld] = $data[$fld];
							}
						}else if(($data[$fld]??'')!==($record_details[0][$fld]??'') ){
							$data_changed = true;
							$data_to_update[$fld] = $data[$fld];
						}
					}
					try{
						// || (!in_array('dsk_img', $edit_restricted_fields) && $_FILES['dsk_img']['error']===0) 
						if(!empty($data_to_update) || (!in_array('mob_img', $edit_restricted_fields) && $_FILES['mob_img']['error']===0)  || (!in_array('mou', $edit_restricted_fields) && $_FILES['mou']['error']===0) ){
							// Initialize with a common success message and code
							$result['error_code'] = 0;
							$result['message']='The changes have been saved.';

							$data_to_update['updated_on'] = $curr_dttm;
							$data_to_update['updated_by'] = $loggedindata[0]['id'];
							$data_to_update['updated_from'] = $ip;

							// if($_FILES['dsk_img']['error']===0){
							// 	$dsk_img_res = $discount_offer->uploadOfferImage($rec_id, $_FILES['dsk_img']['name'], $_FILES['dsk_img']['tmp_name'], 'dsk');
							// 	if(empty($dsk_img_res)){
							// 		$result['error_code']=3;
							// 		$result['message']='The Discount Offer could not be updated as the offer image for desktop screens could not be uploaded.';
							// 		throw new Exception('Error updating Discount Offer.');
							// 	}else{
							// 		$data_to_update['dsk_img'] = $dsk_img_res['gen_file_name'];
							// 	}
							// }

							if($_FILES['mou']['error']===0){
								$mou_res = $discount_offer->uploadOfferMOU($rec_id, $_FILES['mou']['name'], $_FILES['mou']['tmp_name']);
								if(empty($mou_res)){
									$result['error_code']=3;
									$result['message']='The Epic Offer could not be updated as the MOU document could not be uploaded.';
									if(!empty($data_to_update['mou']))
										unlink(CONST_DISCOUNT_OFFER_MOU_DIR_PATH.$data_to_update['mou']);
									throw new Exception('Error updating Epic Offer.');
								}else{
									$data_to_update['mou'] = $mou_res['gen_file_name'];
									$data_to_update['mou_org_file_name'] = $_FILES['mou']['name'];
								}
							}

							if($_FILES['mob_img']['error']===0){
								$mob_img_res = $discount_offer->uploadOfferImage($rec_id, $_FILES['mob_img']['name'], $_FILES['mob_img']['tmp_name'], 'mob');
								if(empty($mob_img_res)){
									$result['error_code']=3;
									$result['message']='The Epic Offer could not be updated as the offer image for mobile screens could not be uploaded.';
									if(!empty($data_to_update['mob_img']))
										unlink(CONST_DISCOUNT_OFFER_IMG_DIR_PATH.$data_to_update['mob_img']);
									throw new Exception('Error updating Epic Offer.');
								}else{
									$data_to_update['mob_img'] = $mob_img_res['gen_file_name'];
								}
							}

							if(!$discount_offer->update($data_to_update)){
								// if(!empty($data_to_update['dsk_img']))
								// 	unlink(CONST_DISCOUNT_OFFER_IMG_DIR_PATH.$data_to_update['dsk_img']);
								if(!empty($data_to_update['mob_img']))
									unlink(CONST_DISCOUNT_OFFER_IMG_DIR_PATH.$data_to_update['mob_img']);
								if(!empty($data_to_update['mou']))
									unlink(CONST_DISCOUNT_OFFER_MOU_DIR_PATH.$data_to_update['mou']);
								$result['error_code']=4;
								$result['message']='The Epic Offer could not be created as the offer images and/or the MOU document could not be uploaded.';
								throw new Exception('Error updating Epic Offer.');
							}else{
								// remove the old files
								
								// if(!empty($data_to_update['dsk_img'])){
								// 	$result['other_data']['dsk_img_url'] = CONST_DISCOUNT_OFFER_IMG_URL_PATH.$data_to_update['dsk_img'];
								// 	$pic_size = getimagesize(CONST_DISCOUNT_OFFER_IMG_DIR_PATH.$data_to_update['dsk_img']);
								// 	$result['other_data']['dsk_img_org_width'] = $pic_size[0];
								// }else if($record_details[0]['dsk_img']!='' && !$delete_dsk_img){
								// 	$result['other_data']['dsk_img_url'] = CONST_DISCOUNT_OFFER_IMG_URL_PATH.$record_details[0]['dsk_img'];
								// 	$pic_size = getimagesize(CONST_DISCOUNT_OFFER_IMG_DIR_PATH.$record_details[0]['dsk_img']);
								// 	$result['other_data']['dsk_img_org_width'] = $pic_size[0];
								// }
								// if($delete_dsk_img)
								// 	unlink(CONST_DISCOUNT_OFFER_IMG_DIR_PATH.$record_details[0]['dsk_img']);


								if(!empty($data_to_update['mou'])){
									unlink(CONST_DISCOUNT_OFFER_MOU_DIR_PATH.$record_details[0]['mou']);
									$result['other_data']['mou_url'] = CONST_DISCOUNT_OFFER_MOU_URL_PATH.$data_to_update['mou'];
									$result['other_data']['mou_org_file_name'] = $data_to_update['mou_org_file_name'];
								}else if($record_details[0]['mou']!='' && !$delete_mou){
									$result['other_data']['mou_url'] = CONST_DISCOUNT_OFFER_MOU_URL_PATH.$record_details[0]['mou'];
									$result['other_data']['mou_org_file_name'] = $record_details[0]['mou_org_file_name'];
								}
								if($delete_mou)
									unlink(CONST_DISCOUNT_OFFER_MOU_DIR_PATH.$record_details[0]['mou']);


								if(!empty($data_to_update['mob_img'])){
									unlink(CONST_DISCOUNT_OFFER_IMG_DIR_PATH.$record_details[0]['mob_img']);
									$result['other_data']['mob_img_url'] = CONST_DISCOUNT_OFFER_IMG_URL_PATH.$data_to_update['mob_img'];
									$pic_size = getimagesize(CONST_DISCOUNT_OFFER_IMG_DIR_PATH.$data_to_update['mob_img']);
									$result['other_data']['mob_img_org_width'] = $pic_size[0];
								}else if($record_details[0]['mob_img']!='' && !$delete_mob_img){
									$result['other_data']['mob_img_url'] = CONST_DISCOUNT_OFFER_IMG_URL_PATH.$record_details[0]['mob_img'];
									$pic_size = getimagesize(CONST_DISCOUNT_OFFER_IMG_DIR_PATH.$record_details[0]['mob_img']);
									$result['other_data']['mob_img_org_width'] = $pic_size[0];
								}
								if($delete_mob_img)
									unlink(CONST_DISCOUNT_OFFER_IMG_DIR_PATH.$record_details[0]['mob_img']);

							}

							$result['error_code']=0;
							$result['message']='The changes have been saved.';
							
						}else{
							$result['error_code']=4;
							$result['message']='There were no changes to save.';
						}
					}catch(\Exception $e){
						$error_details_to_log['login_account_data'] = $login_account_data;
						$error_details_to_log['result'] = $result;
						\eBizIndia\ErrorHandler::logError($error_details_to_log, $e);
					}
				
				}
			}
		}
	}
	$_SESSION['update_d_o_rec_result']=$result;
	header("Location:?");
	exit;

}elseif(isset($_SESSION['update_d_o_rec_result']) && is_array($_SESSION['update_d_o_rec_result'])){
	header("Content-Type: text/html; charset=UTF-8");
	echo "<script type='text/javascript' >\n";
	echo "parent.ManageOffer.handleUpdateDOResponse(".json_encode($_SESSION['update_d_o_rec_result']).");\n";
	echo "</script>";
	unset($_SESSION['update_d_o_rec_result']);
	exit;

}elseif(isset($_SESSION['create_d_o_rec_result']) && is_array($_SESSION['create_d_o_rec_result'])){
	header("Content-Type: text/html; charset=UTF-8");
	echo "<script type='text/javascript' >\n";
	echo "parent.ManageOffer.handleAddDOResponse(".json_encode($_SESSION['create_d_o_rec_result']).");\n";
	echo "</script>";
	unset($_SESSION['create_d_o_rec_result']);
	exit;

}elseif(filter_has_var(INPUT_POST,'mode') && $_POST['mode']=='getRecordDetails'){
	$result=[];
	$error=0; // no error
	$can_edit = true;
	
	if($_POST['rec_id']==''){
		$error=1; // Record ID missing
	}else{
		$discount_offer = new \eBizIndia\DiscountOffer((int)$_POST['rec_id']);
		$record_details = $discount_offer->getDetails();
		if($record_details===false){
			$error=2; // db error
		}elseif(count($record_details)==0){
			$error=3; // Rec ID does not exist
		}else{
			$record_details=$record_details[0];
			$edit_restricted_fields = [];

			// if($record_details['dsk_img']){
			// 	$record_details['dsk_img_url'] = CONST_DISCOUNT_OFFER_IMG_URL_PATH.$record_details['dsk_img'];
			// 	$pic_size = getimagesize(CONST_DISCOUNT_OFFER_IMG_DIR_PATH.$record_details['dsk_img']);
			// 	$record_details['dsk_img_org_width'] = $pic_size[0];
			// }

			if($record_details['mob_img']){
				$record_details['mob_img_url'] = CONST_DISCOUNT_OFFER_IMG_URL_PATH.$record_details['mob_img'];
				$pic_size = getimagesize(CONST_DISCOUNT_OFFER_IMG_DIR_PATH.$record_details['mob_img']);
				$record_details['mob_img_org_width'] = $pic_size[0];
			}

			if(!empty($record_details['mou']) ){
				$record_details['mou_url'] = CONST_DISCOUNT_OFFER_MOU_URL_PATH.$record_details['mou'];
			}

			
		}
	}

	$result[0]=$error;
	$result[1]['can_edit'] = $can_edit;
	$result[1]['cuid'] = $loggedindata[0]['id'];  // This is the auto id of the table users and not member
	$result[1]['record_details']=$record_details;
	$result[1]['edit_restricted_fields']=$edit_restricted_fields;
	echo json_encode($result);
	exit;

}elseif(filter_has_var(INPUT_POST,'mode') && $_POST['mode']=='getList'){
	$result=[0,[]]; // error code and list html
	$error=0; // no error
	$show_dnd_status = true;
	$options=['filters' => []];

	$pno=(isset($_POST['pno']) && $_POST['pno']!='' && is_numeric($_POST['pno']))?$_POST['pno']:((isset($_GET['pno']) && $_GET['pno']!='' && is_numeric($_GET['pno']))?$_GET['pno']:1);
	$recs_per_page=(isset($_POST['recs_per_page']) && $_POST['recs_per_page']!='' && is_numeric($_POST['recs_per_page']))?$_POST['recs_per_page']:((isset($_GET['recs_per_page']) && $_GET['recs_per_page']!='' && is_numeric($_GET['recs_per_page']))?$_GET['recs_per_page']:CONST_RECORDS_PER_PAGE);

	$filter_text = [];
	if(filter_has_var(INPUT_POST, 'search_data') && $_POST['search_data']!=''){
		$search_data=json_decode($_POST['search_data'],true);
		if(!is_array($search_data)){
			$error=2; // invalid search parameters
		}else if(!empty($search_data)){
			$options['filters']=[];
			foreach($search_data as $filter){
				$field=$filter['search_on'];

				if(array_key_exists('search_type',$filter)){
					$type=$filter['search_type'];
				}else{
					$type='';
				}

				if(array_key_exists('search_text', $filter))
					$value= \eBizIndia\trim_deep($filter['search_text']);
				else
					$value='';

				$options['filters'][] = array('field'=>$field,'type'=>$type,'value'=>$value);

				switch($field){
					/*case 'validity_period':
						if($value[0] && $value[1]){
							$fltr_text='Valid between ';
							$disp_value=date('d-M-Y', strtotime($value[0])).' and '.date('d-M-Y', strtotime($value[1]));
						}else if($value[0]){
							$fltr_text='Valid on or after ';
							$disp_value=date('d-M-Y', strtotime($value[0]));
						}else if($value[1]){
							$fltr_text='Valid on or before ';
							$disp_value=date('d-M-Y', strtotime($value[1]));
						}
						break;*/
					case 'category_id':
						$fltr_text='Category is ';
						$value[0]=strtolower($value[0]);
						$disp_value=$filter['disp_text']??'';
						break;
					case 'active':
						$fltr_text='Status is ';
						$value[0]=strtolower($value[0]);
						$disp_value=$value[0]=='y'?'Active':'Inactive';
						break;
						
					// case 'display_period':
					// 	if($value[0] && $value[1]){
					// 		$fltr_text='Display between ';
					// 		$disp_value=date('d-M-Y', strtotime($value[0])).' and '.date('d-M-Y', strtotime($value[1]));
					// 	}else if($value[0]){
					// 		$fltr_text='Display on or after ';
					// 		$disp_value=date('d-M-Y', strtotime($value[0]));
					// 	}else if($value[1]){
					// 		$fltr_text='Display on or before ';
					// 		$disp_value=date('d-M-Y', strtotime($value[1]));
					// 	}
					// 	/*switch($type){
					// 		case 'INTERSECTS':
					// 			$disp_value='Display between '.date('d-M-Y', strtotime($value[0])).' and '.date('d-M-Y', strtotime($value[1]));
					// 			break;
					// 		case 'ON_OR_BEFORE':
					// 			$disp_value='Display from '.date('d-M-Y', strtotime($value[0]));
					// 			break;
					// 		case 'ON_OR_AFTER':
					// 			$disp_value='Display till '.date('d-M-Y', strtotime($value[0]));
					// 			break;
					// 	}*/
					// 	break;
					default:
						
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
						$disp_value=$value;
						break;
				}

				//$disp_value = $field=='falls_in_period'?date('d-M-Y', strtotime($value[0])).' TO '.date('d-M-Y', strtotime($value[1])):$value;

				$filter_text[]='<span class="searched_elem">'.$fltr_text.'  <b>'.\eBizIndia\_esc($disp_value, true).'</b><span class="remove_filter" data-fld="'.$field.'"  >X</span> </span>';
			}
			$result[1]['filter_text'] = implode($filter_text);
		}
	}
	if($_SESSION['dev_mode']==1)
		print_r($options);
	$tot_rec_options = [
		'fields_to_fetch'=>['record_count'],
		'filters' => [],
	];

	$options['fields_to_fetch'] = ['record_count'];

	// get total emp count
	$tot_rec_cnt = \eBizIndia\DiscountOffer::getList($tot_rec_options); 
	$result[1]['tot_rec_cnt'] = $tot_rec_cnt[0]['record_count'];

	// $record_count=$usercls->getList($options);
	$record_count = \eBizIndia\DiscountOffer::getList($options);
	$record_count = $record_count[0]['record_count'];
	$pagination_data=\eBizIndia\getPaginationData($record_count,$recs_per_page,$pno,CONST_PAGE_LINKS_COUNT);
	$result[1]['pagination_data']=$pagination_data;


	if($record_count>0){
		$noofrecords=$pagination_data['recs_per_page'];
		unset($options['fields_to_fetch']);
		$options['page'] = $pno;
		$options['recs_per_page'] = $noofrecords;

		if(isset($_POST['sort_data']) && $_POST['sort_data']!=''){
			$options['order_by']=[];
			$sort_data=json_decode($_POST['sort_data'],true);
			foreach($sort_data as $sort_param){
				$options['order_by'][]=['field'=>$sort_param['sort_on'],'type'=>$sort_param['sort_order']];

			}
		}

		$records=\eBizIndia\DiscountOffer::getList($options);
		//var_dump($records);die();
		if($records===false){
			$error=1; // db error
		}else{
			$result[1]['list']=$records;
		}
	}

	$result[0]=$error;
	$result[1]['rec_count']=$record_count;

	if($_POST['list_format']=='html'){

		$get_list_template_data=array();
		$get_list_template_data['mode']=$_POST['mode'];
		$get_list_template_data[$_POST['mode']]=array();
		$get_list_template_data[$_POST['mode']]['error']=$error;
		$get_list_template_data[$_POST['mode']]['records']=$records;
		$get_list_template_data[$_POST['mode']]['records_count']=count($records??[]);
		$get_list_template_data[$_POST['mode']]['cu_id']=$loggedindata[0]['id'];
		$get_list_template_data[$_POST['mode']]['filter_text']=$result[1]['filter_text'];
		$get_list_template_data[$_POST['mode']]['filter_count']=count($filter_text);
		$get_list_template_data[$_POST['mode']]['tot_col_count']=count($records[0]??[])+1; // +1 for the action column

		$pagination_data['link_data']="";
		$pagination_data['page_link']='#';//"users.php#pno=<<page>>&sort_on=".urlencode($options['order_by'][0]['field'])."&sort_order=".urlencode($options['order_by'][0]['type']);
		$get_list_template_data[$_POST['mode']]['pagination_html']=$page_renderer->fetchContent(CONST_THEMES_TEMPLATE_INCLUDE_PATH.'pagination-bar.tpl',$pagination_data);

		//$get_list_template_data['logged_in_user']=$loggedindata[0];
		
		$page_renderer->updateBodyTemplateData($get_list_template_data);
		$result[1]['list']=$page_renderer->fetchContent();
	}
	echo json_encode($result,JSON_HEX_TAG);
	exit;
}

$dom_ready_data['users']=['field_meta' => CONST_FIELD_META];

$additional_base_template_data = [
									'page_title' => $page_title,
									'page_description' => $page_description,
									'template_type'=>$template_type,
									'dom_ready_code'=>\scriptProviderFuncs\getDomReadyJsCode($page,$dom_ready_data),
									'other_js_code'=>$jscode,
									'module_name' => $page
								];

$disc_offer_cats = \eBizIndia\DiscountOfferCat::getList();
// \eBizIndia\_p($disc_offer_cats);
// exit;


$additional_body_template_data = ['can_add'=>$can_add, 'field_meta' => CONST_FIELD_META, 'disc_offer_cats'=>$disc_offer_cats ];

$page_renderer->updateBodyTemplateData($additional_body_template_data);

$page_renderer->updateBaseTemplateData($additional_base_template_data);
$page_renderer->addCss(\scriptProviderFuncs\getCss($page));
$js_files=\scriptProviderFuncs\getJavascripts($page);
$page_renderer->addJavascript($js_files['BSH'],'BEFORE_SLASH_HEAD');
$page_renderer->addJavascript($js_files['BSB'],'BEFORE_SLASH_BODY');
$page_renderer->renderPage();
