<?php
$page='event-bookings';
require_once 'inc.php';
$template_type='';
$page_title = 'Event Registrations'.CONST_TITLE_AFX;
$page_description = 'Admins can view the registrations for the various events.';
$body_template_file = CONST_THEMES_TEMPLATE_INCLUDE_PATH . 'event-bookings.tpl';
$body_template_data = array();
$page_renderer->registerBodyTemplate($body_template_file,$body_template_data);
$email_pattern="/^\w+([.']?-*\w+)*@\w+([.-]?\w+)*(\.\w{2,4})+$/i";
$user_date_display_format_for_storage = 'd-m-Y';
$can_add = false; 
$can_edit = false;
$_cu_role = $loggedindata[0]['profile_details']['assigned_roles'][0]['role'];

if(filter_has_var(INPUT_POST,'mode') && $_POST['mode']=='getRecordDetails'){
	$result=array();
	$error=0; // no error
	$can_edit = true;
	
	if($_POST['recordid']==''){
		$error=1; // Record ID missing

	}else{
		$obj = new \eBizIndia\EventRegistration((int)$_POST['recordid']);
		$recorddetails = $obj->getDetails();

		if($recorddetails===false){
			$error=2; // db error
		}elseif(count($recorddetails)==0){
			$error=3; // Rec ID does not exist
		}else{
			$recorddetails=$recorddetails[0];

			$attended = \eBizIndia\EventRegistration::getAttendedData($recorddetails['id']);

			// \eBizIndia\_p($attended);

			if(empty($attended)){
				$error=2; // db error
			}else{
				$edit_restricted_fields = [];

				$recorddetails['attended'] = $attended[0]['attended']??0;

				$recorddetails['name_disp'] = \eBizIndia\_esc($recorddetails['ev_name'], true);
				$recorddetails['description_disp'] = nl2br(\eBizIndia\_esc($recorddetails['ev_description'], true));
				$recorddetails['venue_disp'] = nl2br(\eBizIndia\_esc($recorddetails['ev_venue'], true));
				
				if($recorddetails['ev_start_dt']==$recorddetails['ev_end_dt'])
					$recorddetails['period_disp'] = \eBizIndia\_esc(date('d-M-Y', strtotime($recorddetails['ev_start_dt'])), true);
				else
					$recorddetails['period_disp'] = \eBizIndia\_esc(date('d-M-Y', strtotime($recorddetails['ev_start_dt'])) .' To '. date('d-M-Y', strtotime($recorddetails['ev_end_dt']) ), true);

				$recorddetails['mem_name_disp'] = \eBizIndia\_esc($recorddetails['mem_name'], true);
				$recorddetails['memno_disp'] = \eBizIndia\_esc($recorddetails['mem_membership_no'], true);

				// $recorddetails['period_disp'] = \eBizIndia\_esc(date('d-M-Y', strtotime($recorddetails['ev_start_dt'])) .' To '. date('d-M-Y', strtotime($recorddetails['ev_end_dt']) ), true);
				$recorddetails['reg_end_dt_disp'] = $recorddetails['ev_reg_end_dt']==''?'':\eBizIndia\_esc(date('d-M-Y', strtotime($recorddetails['ev_reg_end_dt'])), true);
				$recorddetails['time_text_disp'] = \eBizIndia\_esc($recorddetails['ev_time_text']??'', true);
				$recorddetails['booking_date']  = date('d-M-Y', strtotime($recorddetails['registered_on']));
				
				$recorddetails['dsk_img_url'] = CONST_EVENT_IMG_URL_PATH.$recorddetails['ev_dsk_img'];
				$pic_size = getimagesize(CONST_EVENT_IMG_DIR_PATH.$recorddetails['ev_dsk_img']);
				$recorddetails['dsk_img_org_width'] = $pic_size[0];

				$recorddetails['mob_img_url'] = CONST_EVENT_IMG_URL_PATH.$recorddetails['ev_mob_img'];
				$pic_size = getimagesize(CONST_EVENT_IMG_DIR_PATH.$recorddetails['ev_mob_img']);
				$recorddetails['mob_img_org_width'] = $pic_size[0];

				// fetch the payment details no matter it was successful or it failed
				$fields_to_fetch = [
					'pmt_req_status',
					'pmt_req_amount',
					'pmt_amount',
					'pmt_fees',
					'pmt_total_taxes',
					'pmt_instrument_type',
					'pmt_billing_instrument',
					'pmt_bank_reference_number',
					'pmt_failure_reason',
					'pmt_failure_msg',
					'pmt_completed_at',
				];
				try{
					$pmt_obj = new \eBizIndia\Payment(CONST_INSTAMOJO_CREDS, 'event_reg', $_POST['recordid']);
					$payment_details = $pmt_obj->getPaymentReqDetails('',[],$fields_to_fetch);

					if($payment_details===false){
						$error=2; // db error
					}else{
						$recorddetails['payment_details'] = !empty($payment_details)?$payment_details:[];

					}

				}catch(Exception $e){
					$error=2; // db error
				}

			}


		}
	}

	$result[0]=$error;
	$result[1]['can_edit'] = $can_edit;
	$result[1]['cuid'] = $loggedindata[0]['id'];  // This is the auto id of the table users and not member
	$result[1]['record_details']=$recorddetails;
	$result[1]['edit_restricted_fields']=$edit_restricted_fields;
	
	echo json_encode($result);

	exit;

}elseif(filter_has_var(INPUT_GET,'mode') && $_GET['mode']==='export'){
// 	if(strcasecmp($_cu_role, 'ADMIN')!==0){
// 		header('HTTP/1.0 403 Forbidden', true, 403);
// 		die;
// 	}

	$comp_export_fields = [
	  'booking_id'=> 'Registration ID',	
	  'ev_name' => 'Event Name',
	  'mem_name'=>'Member Name',
	  'mem_email'=>'Member Email',
	  'mem_mobile' =>'Member Mobile Number',
	  'mem_work_company' =>'Member Work Company',
	  'registered_on' =>'Registered On',
	  'reg_status' =>'Registration Status',
	  'no_of_tickets' =>'Persons',
	  'attended' =>'Attended (As on '.date('d-m-Y g:i a').')',
	  'no_show' =>'No Show (As on '.date('d-m-Y g:i a').')',
	  'payment_status' =>'Payment Status',
	  'price_per_tkt'=> 'Price Per Person',
	  'offer'=> 'Offer',
	  'total_amount'=> 'Amount',
	  'payment_mode'=>'Payment Mode'
	 	
	];

	$options=[];
	//echo $_GET['recid'];die;
 		$options['filters'][] = ['field'=>'event_id', 'type'=>'EQUAL', 'value'=>$_GET['recid']];
	//$options['filters']=[];
	if(filter_has_var(INPUT_GET, 'searchdata') && $_GET['searchdata']!=''){
		$searchdata=json_decode($_GET['searchdata'],true);
		if(is_array($searchdata) && !empty($searchdata)){
			$options['filters']=[];
			foreach($searchdata as $filter){
				$field=$filter['searchon'];

				if(array_key_exists('searchtype',$filter)){
					$type=$filter['searchtype'];

				}else{
					$type='';

				}

				if(array_key_exists('searchtext', $filter))
				
					$value=$filter['searchtext'];
				else
					$value='';

				$options['filters'][] = array('field'=>$field,'type'=>$type,'value'=>$value);
			}
		}
	}

	if(filter_has_var(INPUT_GET, 'sortdata') && $_GET['sortdata']!=''){
		$options['order_by']=[];
		$sortdata=json_decode($_GET['sortdata'],true);
		foreach($sortdata as $sort_param){
			$options['order_by'][]=array('field'=>$sort_param['sorton'],'type'=>$sort_param['sortorder']);
		}
	}

	$options['fieldstofetch'] = array_values(array_diff(array_keys($comp_export_fields), ['no_show']));

	$records=\eBizIndia\EventRegistration::getList($options);
	if($records===false){
		header('HTTP/1.0 500 Internal Server Error', true, 500);
		die;
	}else if(empty($records)){
		header('HTTP/1.0 204 No Content', true, 204);
		die;
	}else{
		// if(!defined('CONST_COMP_EXPORT_FLDS') || empty(CONST_COMP_EXPORT_FLDS) || !is_array(CONST_COMP_EXPORT_FLDS)){
		// 	header('HTTP/1.0 412 Precondition Failed', true, 412);
		// 	die;
		// }
		
		ob_clean();
		header('Content-Description: File Transfer');
	    header('Content-Type: application/csv');
	    header("Content-Disposition: attachment; filename=registrations.csv");
	    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	    $fh = fopen('php://output', 'w');
	    if(!$fh){
	    	header('HTTP/1.0 500 Internal Server Error', true, 500);
	    	die;
	    }
	    $col_headers = array_values($comp_export_fields);
	    $data_row_flds = array_fill_keys(array_keys($comp_export_fields), '');
	    fputcsv($fh, $col_headers);
	    foreach ($records as $rec) {
			$data_row = array_intersect_key(array_replace($data_row_flds, $rec), $data_row_flds);
			if(isset($data_row['no_show']) && isset($data_row['attended']) && isset($data_row['no_of_tickets']))
				$data_row['no_show'] = $data_row['no_of_tickets'] - $data_row['attended'];
			if(isset($data_row['price_per_tkt']) && $data_row['price_per_tkt']==0)
				$data_row['price_per_tkt'] = 'Free';
			if(isset($data_row['offer']) && $data_row['offer']=='EB')
				$data_row['offer'] = 'Early Bird';
			fputcsv($fh, array_values($data_row));
		}
		ob_flush();
		fclose($fh);
		die;
	}


}elseif(filter_has_var(INPUT_POST,'mode') && $_POST['mode']=='getSummaryList'){
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
				$field = (string)$filter['searchon'];
				if(!in_array($field, ['ev_name']))
					continue; // restricting the search on a predefined list of fields

				if(array_key_exists('searchtype',$filter)){
					$type= (string)$filter['searchtype'];

				}else{
					$type='';

				}

				if(array_key_exists('searchtext', $filter))
					$value= \eBizIndia\trim_deep($filter['searchtext']);
				else
					$value='';

				$options['filters'][] = array('field'=>$field,'type'=>$type,'value'=>$value);

				if($field=='ev_name')
					$fltr_text = 'Event\'s name ';
				else if($field=='registered_on')
					$fltr_text = 'Registrations summary for  ';
				else 
					$fltr_text = ucfirst($field).' ';
				
				if($field!=='registered_on'){
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
					$disp_value = $value;
				}else{
					$disp_value = date('d-M-Y', strtotime($value[0])).' and '.date('d-M-Y', strtotime($value[1]));
				}

				$filtertext[]='<span class="searched_elem"  >'.$fltr_text.'  <b>'.\eBizIndia\_esc($disp_value, true).'</b><span class="remove_filter" data-fld="'.$field.'"  >X</span> </span>';
			}
			$result[1]['filtertext'] = implode($filtertext);
		}
	}

	$tot_rec_options = [
		'fieldstofetch'=>['recordcount'],
		'filters' => [
		],
	];

	$options['fieldstofetch'] = ['recordcount'];

	// get total emp count
	$tot_rec_cnt = \eBizIndia\EventRegistration::getEventWiseSummaryList($tot_rec_options); 
	$result[1]['tot_rec_cnt'] = $tot_rec_cnt[0]['recordcount'];

	$recordcount = \eBizIndia\EventRegistration::getEventWiseSummaryList($options);
	$recordcount = $recordcount[0]['recordcount'];
	$paginationdata=\eBizIndia\getPaginationData($recordcount,$recsperpage,$pno,CONST_PAGE_LINKS_COUNT);
	$result[1]['paginationdata']=$paginationdata;


	if($recordcount>0){
		$noofrecords=$paginationdata['recs_per_page'];
		unset($options['fieldstofetch']);
		$options['page'] = $pno;
		$options['recs_per_page'] = $noofrecords;
		$options['cuml_fig_confirmed_only'] = true; // consider only the confirmed registrations while fetching the cumulative figures for no of bookings, no of tickets and amount
		
		if(isset($_POST['sortdata']) && $_POST['sortdata']!=''){
			$options['order_by']=[];
			$sortdata=json_decode($_POST['sortdata'],true);
			foreach($sortdata as $sort_param){

				$options['order_by'][]=array('field'=>$sort_param['sorton'],'type'=>$sort_param['sortorder']);

			}
		}

		$records=\eBizIndia\EventRegistration::getEventWiseSummaryList($options);

		// print_r($records); exit;
		
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
		$get_list_template_data['country_code']= CONST_COUNTRY_CODE;
		
		$page_renderer->updateBodyTemplateData($get_list_template_data);
		$result[1]['list']=$page_renderer->fetchContent();

	}

	echo json_encode($result,JSON_HEX_TAG);
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
				$field = (string)$filter['searchon'];
				if(!in_array($field, ['booking_id', 'mem_name', 'mem_membership_no', 'ev_name', 'registered_on']))
					continue; // restricting the search on a predefined list of fields

				if(array_key_exists('searchtype',$filter)){
					$type= (string)$filter['searchtype'];

				}else{
					$type='';

				}

				if(array_key_exists('searchtext', $filter))
					$value= \eBizIndia\trim_deep($filter['searchtext']);
				else
					$value='';

				$options['filters'][] = array('field'=>$field,'type'=>$type,'value'=>$value);

				if($field=='booking_id')
					$fltr_text = 'Registration ID ';
				else if($field=='mem_name')
					$fltr_text = 'Member\'s name ';
				else if($field=='mem_membership_no')
					$fltr_text = 'Membership no. ';
				else if($field=='ev_name')
					$fltr_text = 'Event\'s name ';
				else if($field=='registered_on')
					$fltr_text = 'Registered between  ';
				else 
					$fltr_text = ucfirst($field).' ';
				
				if($field!=='registered_on'){
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
					$disp_value = $value;
				}else{
					$disp_value = date('d-M-Y', strtotime($value[0])).' and '.date('d-M-Y', strtotime($value[1]));
				}

				$filtertext[]='<span class="searched_elem"  >'.$fltr_text.'  <b>'.\eBizIndia\_esc($disp_value, true).'</b><span class="remove_filter" data-fld="'.$field.'"  >X</span> </span>';
			}
			$result[1]['filtertext'] = implode($filtertext);
		}
	}

	$tot_rec_options = [
		'fieldstofetch'=>['recordcount'],
		'filters' => [
		],
	];

	$bookings_for_event = '';
	if(!empty($_POST['recid'])){
		$tot_rec_options['filters'][] = ['field'=>'event_id', 'type'=>'EQUAL', 'value'=>$_POST['recid']];
		$options['filters'][] = ['field'=>'event_id', 'type'=>'EQUAL', 'value'=>$_POST['recid']];

		$ev_obj = new \eBizIndia\Event($_POST['recid']);
		$ev_details = $ev_obj->getDetails();
		$bookings_for_event = $ev_details[0]['name'];
	}

	$options['fieldstofetch'] = ['recordcount'];

	// get total emp count
	$tot_rec_cnt = \eBizIndia\EventRegistration::getList($tot_rec_options); 
	$result[1]['tot_rec_cnt'] = $tot_rec_cnt[0]['recordcount'];

	$recordcount = \eBizIndia\EventRegistration::getList($options);
	$recordcount = $recordcount[0]['recordcount'];
	$paginationdata=\eBizIndia\getPaginationData($recordcount,$recsperpage,$pno,CONST_PAGE_LINKS_COUNT);
	$result[1]['paginationdata']=$paginationdata;


	if($recordcount>0){
		$noofrecords=$paginationdata['recs_per_page'];
		unset($options['fieldstofetch']);
		$options['page'] = $pno;
		$options['recs_per_page'] = $noofrecords;
		$options['fieldstofetch'] = [
			'id',
			'booking_id',
			'event_id',
			'member_id',
			'mem_name',
			'mem_email',
			'mem_membership_no',
			'mem_mobile',
			'ev_name',
			'ev_start_dt',
			'no_of_tickets',
			'registered_on',
			'total_amount',
			'amount_paid',
			'payment_mode',
			'reg_status',
			'payment_status',
			'attended',
		];

		if(isset($_POST['sortdata']) && $_POST['sortdata']!=''){
			$options['order_by']=[];
			$sortdata=json_decode($_POST['sortdata'],true);
			foreach($sortdata as $sort_param){

				$options['order_by'][]=array('field'=>$sort_param['sorton'],'type'=>$sort_param['sortorder']);

			}
		}

		$records=\eBizIndia\EventRegistration::getList($options);

		// print_r($records); exit;
		
		if($records===false){
			$error=1; // db error
		}else{
			$result[1]['list']=$records;
		}
	}

	$result[0]=$error;
	$result[1]['reccount']=$recordcount;
	$result[1]['bookings_for_event']=$bookings_for_event;

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
		$get_list_template_data['country_code']= CONST_COUNTRY_CODE;
		
		$page_renderer->updateBodyTemplateData($get_list_template_data);
		$result[1]['list']=$page_renderer->fetchContent();

	}

	echo json_encode($result,JSON_HEX_TAG);
	exit;

}


$dom_ready_data['event-bookings']=array(
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

$options = [];
$options['filters'] = [
	['field'=>'reg_active_on_date', 'type'=>'', 'value'=> date('Y-m-d')],
	['field'=>'reg_active', 'type'=>'EQUAL', 'value'=> 'y'],
	['field'=>'active', 'type'=>'EQUAL', 'value'=> 'y'],
];
$events = \eBizIndia\Event::getList($options);

$additional_body_template_data = ['can_add'=>$can_add, 'field_meta' => CONST_FIELD_META, 'events' => $events, 'country_code' => CONST_COUNTRY_CODE ];

$page_renderer->updateBodyTemplateData($additional_body_template_data);

$page_renderer->updateBaseTemplateData($additional_base_template_data);
$page_renderer->addCss(\scriptProviderFuncs\getCss($page));
$js_files=\scriptProviderFuncs\getJavascripts($page);
$page_renderer->addJavascript($js_files['BSH'],'BEFORE_SLASH_HEAD');
$page_renderer->addJavascript($js_files['BSB'],'BEFORE_SLASH_BODY');
$page_renderer->renderPage();

?>
