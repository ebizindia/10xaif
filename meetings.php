<?php
$page='meetings';
require_once 'inc.php';
require_once CONST_CLASS_DIR.'mpdf/autoload.php';
$template_type='';
$page_title = 'Meetings'.CONST_TITLE_AFX;
$page_description = 'View and Add Meeting';
$body_template_file = CONST_THEMES_TEMPLATE_INCLUDE_PATH . 'meetings.tpl';
$body_template_data = array();
$page_renderer->registerBodyTemplate($body_template_file,$body_template_data);
$email_pattern="/^\w+([.']?-*\w+)*@\w+([.-]?\w+)*(\.\w{2,4})+$/i";
$user_date_display_format_for_storage = 'd-m-Y';
$can_add = $can_edit = true; 
$_cu_role = $loggedindata[0]['profile_details']['assigned_roles'][0]['role'];



$rec_fields = [
	'meet_date'=>'',
	'meet_date_to'=>'',
 	'meet_time'=>'',
 	'meet_title'=>'',
 	'venue'=>'',
 	'minutes'=>'',
 	'active'=>'',
 	'created_on'=>'',
 	'created_by'=>'',
 	'created_from'=>'',
 	'updated_on'=>'',
 	'updated_by'=>'',
 	'updated_from'=>'',
];

// PDF Download Handler
// PDF Download Handler - Place this at the very beginning of the file, right after require_once 'inc.php';
if(filter_has_var(INPUT_GET, 'mode') && $_GET['mode'] === 'downloadagenda' && filter_has_var(INPUT_GET, 'recid')){
	$meeting_id = (int)$_GET['recid'];
	
	if($meeting_id <= 0){
		header("HTTP/1.1 400 Bad Request");
		echo "Invalid meeting ID";
		exit;
	}
	
	try {
		// Clean any output buffers
		if (ob_get_level()) {
			ob_end_clean();
		}
		
		$meet_obj = new \eBizIndia\Meeting($meeting_id);
		$meeting_details = $meet_obj->getDetails();
		
		if($meeting_details === false || empty($meeting_details)){
			header("HTTP/1.1 404 Not Found");
			echo "Meeting not found";
			exit;
		}
		
		$meeting_data = $meeting_details[0];
		$agenda_details = $meet_obj->getSessionDetails();
		
		// Include mPDF library - adjust path as needed
		
		
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8',
			'format' => 'A4',
			'orientation' => 'P',
			'margin_left' => 15,
			'margin_right' => 15,
			'margin_top' => 16,
			'margin_bottom' => 16
		]);
		
		// Generate PDF content
		$pdf_content = generateMeetingAgendaPDF($meeting_data, $agenda_details);
		
		$mpdf->WriteHTML($pdf_content);
		
		// Clear any output buffers again before sending PDF
		if (ob_get_level()) {
			ob_end_clean();
		}
		
		// Set proper headers
		header('Content-Type: application/pdf');
		header('Content-Disposition: attachment; filename="Meeting_Agenda_' . date('Y-m-d', strtotime($meeting_data['meet_date'])) . '_' . $meeting_id . '.pdf"');
		header('Cache-Control: private, max-age=0, must-revalidate');
		header('Pragma: public');
		
		$mpdf->Output('Meeting-Agenda.php', 'D'); // I = Inline/Download
		exit;
		
	} catch(\Exception $e) {
		// Clear output buffer on error too
		if (ob_get_level()) {
			ob_end_clean();
		}
		
		$error_details_to_log['function'] = 'PDF Generation';
		$error_details_to_log['meeting_id'] = $meeting_id;
		$error_details_to_log['error'] = $e->getMessage();
		\eBizIndia\ErrorHandler::logError($error_details_to_log, $e);
		
		header("HTTP/1.1 500 Internal Server Error");
		header('Content-Type: text/plain');
		echo "Error generating PDF: " . $e->getMessage();
		exit;
	}
}else if(filter_has_var(INPUT_POST,'mode') && $_POST['mode']=='createrec'){
	$result=array('error_code'=>0,'message'=>[], 'elemid'=>array(), 'other_data'=>[]);

	if($can_add===false){
		$result['error_code']=403;
		$result['message']="Sorry, you are not authorised to perfom this action.";
	}else{

		$data=array();
		$data = \eBizIndia\trim_deep(\eBizIndia\striptags_deep(array_intersect_key($_POST, array_diff_key($rec_fields, ['venue'=>'', 'minutes'=>'','meet_title'=>'']) )));
		$data['meet_title'] = trim($_POST['meet_title']);
		$data['venue'] = trim($_POST['venue']);
		$data['minutes'] = trim($_POST['minutes']);
		$data['meet_date'] = trim($_POST['meet_date']);
		$data['meet_date_to'] = trim($_POST['meet_date_to']);
		$data['meet_time'] = trim($_POST['meet_time']);

		

		$meet_obj = new \eBizIndia\Meeting();	
		$validation_res = $meet_obj->validate($data, 'add', $other_data);
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
				$error_details_to_log['mode'] = 'addMeeting';
				$error_details_to_log['part'] = 'Add a new meeting.';
				$rec_id=$meet_obj->saveDetails($data);
				if($rec_id===false)
					throw new Exception('Error adding a new Meeting.');

				$agendas=[];
				foreach($_POST['time_from'] as $key=>$time_from)
				{
					$session_meet_date=$_POST['session_meet_date'][$key];
					$time_to=$_POST['time_to'][$key];
					$topic=$_POST['topic'][$key];
					$id=$_POST['agenda_id'][$key];
					$agendas[]=compact('session_meet_date','time_to','time_from','topic','id');
				}
				if($meet_obj->saveAgendaDetails($agendas,$rec_id)===false)
					throw new Exception('Error adding a new Meeting.');
				

				$result['error_code']=0;
				$result['message']='The meeting <b>'.\eBizIndia\_esc($data['name']).'</b> has been created.';
				$conn->commit();

			}catch(\Exception $e){
				$last_error = \eBizIndia\PDOConn::getLastError();
				if($result['error_code']==0){
					$result['error_code']=1; // DB error
					$result['message']="The meeting could not be added due to server error.";
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
		$result['message']="Sorry, you are not authorised to update the Events.";
	}else {
		$data=array();
		$recordid=(int)$_POST['recordid']; 
		// data validation
		if($recordid == ''){
			$result['error_code']=2;
			$result['message'][]="Invalid meeting reference.";
		}else{
			$meet_obj = new \eBizIndia\Meeting($recordid);
			$recorddetails  = $meet_obj->getDetails();
			if($recorddetails===false){
				$result['error_code']=1;
				$result['message'][]="Failed to verify the Meeting details due to server error.";
				$result['error_fields'][]="#meet_date";
			}elseif(empty($recorddetails)){
				// event with this ID does not exist
				$result['error_code']=3;
				$result['message'][]="The meeting you are trying to modify was not found.";
				$result['error_fields'][]="#meet_date";
			}else{
				$edit_restricted_fields = [];
				
				$rec_fields = array_diff_key($rec_fields, array_fill_keys($edit_restricted_fields, '')); // removing the edit restricted fields from the list of fields
				
				$data = \eBizIndia\trim_deep(\eBizIndia\striptags_deep(array_intersect_key($_POST, array_diff_key($rec_fields, ['venue'=>'', 'minutes'=>'','meet_title'=>'']) ) ));
				if(array_key_exists('minutes', $rec_fields) && array_key_exists('minutes', $_POST))
					$data['minutes'] = trim($_POST['minutes']);
				if(array_key_exists('venue', $rec_fields) && array_key_exists('venue', $_POST))
					$data['venue'] = trim($_POST['venue']);
				if(array_key_exists('meet_title', $rec_fields) && array_key_exists('meet_title', $_POST))
					$data['meet_title'] = trim($_POST['meet_title']);

				
				

				$other_data['field_meta'] = CONST_FIELD_META;
				$other_data['recorddetails'] = $recorddetails[0];
				$other_data['edit_restricted_fields'] = $edit_restricted_fields;
				$validation_res = $meet_obj->validate($data, 'update', $other_data); 
				if($validation_res['error_code']>0){
					$result = $validation_res;
				} else {
					$data_to_update = [];
					$curr_dttm = date('Y-m-d H:i:s');
					$ip = \eBizIndia\getRemoteIP();
										
					foreach($rec_fields as $fld=>$val){
						 if($data[$fld]!=$recorddetails[0][$fld]){
							$data_changed = true;
							$data_to_update[$fld] = $data[$fld];
							
						}
					}

					 // Other update logic here
                	$agendas_updated = false;

	                // Get saved agendas
	                $saved_agendas = $meet_obj->getSessionDetails(); // Fetch saved agenda records

	                // Prepare submitted agendas
	                $submitted_agendas = [];
	                foreach ($_POST['time_from'] as $key => $time_from) {
	                    $session_meet_date = $_POST['session_meet_date'][$key];
	                    $time_to = $_POST['time_to'][$key];
	                    $topic = $_POST['topic'][$key];
	                    $id = $_POST['agenda_id'][$key];
	                    $submitted_agendas[] = compact('session_meet_date','time_to', 'time_from', 'topic', 'id');
	                }

	                // Compare submitted agendas with saved agendas
	                if ($saved_agendas !== $submitted_agendas) {
	                    $agendas_updated = true;
	                }

	                // If agendas are updated, call saveAgendaDetails
	              /*  if ($agendas_updated) {
	                    if ($meet_obj->saveAgendaDetails($submitted_agendas, $recordid) === false) {
	                        $result['error_code'] = 5;
	                        $result['message'] = "Error saving agenda details.";
	                        throw new Exception('Error saving agenda details.');
	                    }
	                } */




					try{
						if(!empty($data_to_update || $agendas_updated===true) ){
							// Initialize with a common success message and code
							$result['error_code'] = 0;
							$result['message']='The changes have been saved.';

							$data_to_update['updated_on'] = $curr_dttm;
							$data_to_update['updated_by'] = $loggedindata[0]['id'];
							$data_to_update['updated_from'] = $ip;

							if(!$meet_obj->updateDetails($data_to_update)){
								$result['error_code']=4;
								$result['message']='The Meeting could not be updated due to error in saving the changes.';
								throw new Exception('Error updating the  Meeting.');
							}

							if ($meet_obj->saveAgendaDetails($submitted_agendas, $recordid) === false) {
	                        $result['error_code'] = 5;
	                        $result['message'] = "Error saving agenda details.";
	                        throw new Exception('Error saving agenda details.');
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

}elseif(filter_has_var(INPUT_POST,'mode') && $_POST['mode']=='deleterec'){
	
	$result=array('error_code'=>0,'message'=>[],'other_data'=>[]);
	if($can_edit===false){
		$result['error_code']=403;
		$result['message']="Sorry, you are not authorised to update the Events.";
	}else {
		$data=array();
		$recordid=(int)$_POST['recordid']; 
		// data validation
		if($recordid == ''){
			$result['error_code']=2;
			$result['message'][]="Invalid meeting reference.";
		}else{
			$meet_obj = new \eBizIndia\Meeting($recordid);
			$recorddetails  = $meet_obj->getDetails();
			if($recorddetails===false){
				$result['error_code']=1;
				$result['message'][]="Failed to verify the Meeting details due to server error.";
				$result['error_fields'][]="#meet_date";
			}elseif(empty($recorddetails)){
				// event with this ID does not exist
				$result['error_code']=3;
				$result['message'][]="The meeting you are trying to delete was not found.";
				$result['error_fields'][]="#meet_date";
			}else{
				
				if($validation_res['error_code']>0){
					$result = $validation_res;
				} else {
					

					try{
						
							if ($meet_obj->deleteMeeting($recordid) === false) {
	                        $result['error_code'] = 5;
	                        $result['message'] = "Error to delete meeting record.";
	                        throw new Exception('Error to delete meeting record.');
	                        }
							

							$result['error_code']=0;
							$result['message']='Meeting details have been deleted.';
							
						
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

	echo json_encode($result);

	exit;

}elseif(filter_has_var(INPUT_POST, 'mode') && $_POST['mode'] === 'printagenda') {
  
        header("Location:?id=$meeting_id");
        exit;
  
}elseif(isset($_SESSION['update_user_result']) && is_array($_SESSION['update_user_result'])){
	header("Content-Type: text/html; charset=UTF-8");
	echo "<script type='text/javascript' >\n";
	echo "parent.meetfuncs.handleUpdateRecResponse(".json_encode($_SESSION['update_user_result']).");\n";
	echo "</script>";
	unset($_SESSION['update_user_result']);
	exit;

}elseif(isset($_SESSION['create_rec_result']) && is_array($_SESSION['create_rec_result'])){
	header("Content-Type: text/html; charset=UTF-8");
	echo "<script type='text/javascript' >\n";
	echo "parent.meetfuncs.handleAddRecResponse(".json_encode($_SESSION['create_rec_result']).");\n";
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
		$meet_obj = new \eBizIndia\Meeting((int)$_POST['recordid']);
		$recorddetails = $meet_obj->getDetails();

		if($recorddetails===false){
			$error=2; // db error
		}elseif(count($recorddetails)==0){
			$error=3; // Rec ID does not exist
		}else{
			$recorddetails=$recorddetails[0];
			$edit_restricted_fields = [];

			//$recorddetails['name_disp'] = \eBizIndia\_esc($recorddetails['name_disp'], true);
			
			//$recorddetails['dsk_img_url'] = CONST_EVENT_IMG_URL_PATH.$recorddetails['dsk_img'];
			//$pic_size = getimagesize(CONST_EVENT_IMG_DIR_PATH.$recorddetails['dsk_img']);
			//$recorddetails['dsk_img_org_width'] = $pic_size[0];

			//$recorddetails['mob_img_url'] = CONST_EVENT_IMG_URL_PATH.$recorddetails['mob_img'];
			////$pic_size = getimagesize(CONST_EVENT_IMG_DIR_PATH.$recorddetails['mob_img']);
			//$recorddetails['mob_img_org_width'] = $pic_size[0];

			//$recorddetails['booking_link'] = CONST_APP_ABSURL.'/event/'.urlencode($_POST['recordid']);


			$meet_obj = new \eBizIndia\Meeting((int)$_POST['recordid']);
			$sessionrecdetails = $meet_obj->getSessionDetails();
			//print_r($sessionrecdetails);
			//exit;
		}
	}

	$result[0]=$error;
	$result[1]['can_edit'] = $can_edit;
	$result[1]['today'] = date('Y-m-d');
	$result[1]['cuid'] = $loggedindata[0]['id'];  // This is the auto id of the table users and not member
	$result[1]['record_details']=$recorddetails;
	$result[1]['agenda_record_details']=$sessionrecdetails;
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
				
				$disp_value = $field=='falls_in_period'?date('d-M-Y', strtotime($value[0])).' TO '.date('d-M-Y', strtotime($value[1])):$value;

				if($field=='meet_date')
					$fltr_text = 'Date ';
				else if($field=='meet_date_to')
					$fltr_text = 'Date ';
				else if($field=='meet_title')
					$fltr_text = 'Title ';
				else if($field=='venue')
					$fltr_text = 'Venue ';
				else if($field=='falls_in_period')
					$fltr_text = 'Falls in the period  ';
				else 
					$fltr_text = ucfirst($field).' ';
				
				


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
	$tot_rec_cnt = \eBizIndia\Meeting::getList($tot_rec_options); 
	$result[1]['tot_rec_cnt'] = $tot_rec_cnt[0]['recordcount'];

	$recordcount = \eBizIndia\Meeting::getList($options);
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

		$records=\eBizIndia\Meeting::getList($options);
		
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


$dom_ready_data['events']=array(
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


// PDF Generation Function
// PDF Generation Function - Place this before the final template rendering code
function generateMeetingAgendaPDF($meeting_data, $agenda_details) {
    // Escape data properly
    $meeting_title = htmlspecialchars($meeting_data['meet_title'] ?? '', ENT_QUOTES, 'UTF-8');
    $venue = htmlspecialchars($meeting_data['venue'] ?? '', ENT_QUOTES, 'UTF-8');
    $meet_time = htmlspecialchars($meeting_data['meet_time'] ?? '', ENT_QUOTES, 'UTF-8');
    
    // Format dates
    $meet_date_from = !empty($meeting_data['meet_date']) ? date('d-M-Y', strtotime($meeting_data['meet_date'])) : '';
    $meet_date_to = !empty($meeting_data['meet_date_to']) ? date('d-M-Y', strtotime($meeting_data['meet_date_to'])) : '';
    
    $date_range = $meet_date_from;
    if ($meet_date_to && $meet_date_from !== $meet_date_to) {
        $date_range .= ' to ' . $meet_date_to;
    }
    
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Meeting Agenda</title>
        <style>
            body { 
                font-family: Arial, sans-serif; 
                font-size: 12px; 
                line-height: 1.4; 
                margin: 0;
                padding: 20px;
            }
            .header { 
                text-align: center; 
                margin-bottom: 30px; 
                border-bottom: 2px solid #333; 
                padding-bottom: 15px; 
            }
            .header h1 { 
                color: #333; 
                margin: 0; 
                font-size: 24px; 
                font-weight: bold;
            }
            .header h2 { 
                color: #666; 
                margin: 5px 0 0 0; 
                font-size: 16px; 
                font-weight: normal;
            }
            .meeting-info { 
                margin-bottom: 25px; 
            }
            .meeting-info table { 
                width: 100%; 
                border-collapse: collapse; 
                border: 1px solid #ddd;
            }
            .meeting-info td { 
                padding: 10px 15px; 
                border: 1px solid #ddd; 
                vertical-align: top;
            }
            .meeting-info .label { 
                background-color: #f5f5f5; 
                font-weight: bold; 
                width: 30%; 
            }
            .agenda-section { 
                margin-top: 25px; 
            }
            .agenda-section h3 { 
                color: #333; 
                border-bottom: 1px solid #ccc; 
                padding-bottom: 5px; 
                font-size: 16px;
                margin-bottom: 15px;
            }
            .agenda-table { 
                width: 100%; 
                border-collapse: collapse; 
                margin-top: 15px; 
                border: 1px solid #ddd;
            }
            .agenda-table th, .agenda-table td { 
                border: 1px solid #ddd; 
                padding: 10px; 
                text-align: left; 
                vertical-align: top;
            }
            .agenda-table th { 
                background-color: #f8f9fa; 
                font-weight: bold; 
            }
            .session-date { width: 20%; }
            .session-time { width: 25%; }
            .session-topic { width: 55%; }
            .footer { 
                margin-top: 30px; 
                text-align: center; 
                font-size: 10px; 
                color: #666; 
                border-top: 1px solid #ddd;
                padding-top: 15px;
            }
            .no-agenda {
                text-align: center;
                color: #666;
                font-style: italic;
                padding: 20px;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>Meeting Agenda</h1>';
            
    if (!empty($meeting_title)) {
        $html .= '<h2>' . $meeting_title . '</h2>';
    }
    
    $html .= '</div>
        
        <div class="meeting-info">
            <table>
                <tr>
                    <td class="label">Meeting Title:</td>
                    <td>' . $meeting_title . '</td>
                </tr>
                <tr>
                    <td class="label">Venue:</td>
                    <td>' . $venue . '</td>
                </tr>
                <tr>
                    <td class="label">Meeting Date:</td>
                    <td>' . $date_range . '</td>
                </tr>
                <tr>
                    <td class="label">Meeting Time:</td>
                    <td>' . (!empty(trim($meet_time)) ? $meet_time : '<em style="color: #999;">Not specified</em>') . '</td>
                </tr>
            </table>
        </div>';
        
    if (!empty($agenda_details)) {
        $html .= '
        <div class="agenda-section">
            <h3>Session Details</h3>
            <table class="agenda-table">
                <thead>
                    <tr>
                        <th class="session-date">Date</th>
                        <th class="session-time">Time</th>
                        <th class="session-topic">Topic</th>
                    </tr>
                </thead>
                <tbody>';
                
        foreach ($agenda_details as $session) {
            $session_date = '';
            if (!empty($session['session_meet_date'])) {
                $session_date = date('d-M-Y', strtotime($session['session_meet_date']));
            }
            
            $session_time = '';
            if (!empty($session['time_from'])) {
                $session_time = htmlspecialchars($session['time_from'], ENT_QUOTES, 'UTF-8');
                if (!empty($session['time_to'])) {
                    $session_time .= ' - ' . htmlspecialchars($session['time_to'], ENT_QUOTES, 'UTF-8');
                }
            }
            
            $topic = htmlspecialchars($session['topic'] ?? '', ENT_QUOTES, 'UTF-8');
            
            $html .= '
                    <tr>
                        <td class="session-date">' . $session_date . '</td>
                        <td class="session-time">' . $session_time . '</td>
                        <td class="session-topic">' . $topic . '</td>
                    </tr>';
        }
        
        $html .= '
                </tbody>
            </table>
        </div>';
    } else {
        $html .= '
        <div class="agenda-section">
            <h3>Session Details</h3>
            <div class="no-agenda">No agenda sessions have been scheduled for this meeting.</div>
        </div>';
    }
    
    $html .= '
        <div class="footer">
            <p>Generated on ' . date('d-M-Y H:i:s') . '</p>
        </div>
    </body>
    </html>';
    
    return $html;
}
?>
