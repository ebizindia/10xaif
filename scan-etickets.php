<?php
// WISH LIST
// Guest entry allowed - y/n - in the event master

$page='scan-tkt';
require_once 'inc.php';
$_cu_role = $loggedindata[0]['profile_details']['assigned_roles'][0]['role'];
$template_type='';
$page_title = 'Scan e-Tickets'.CONST_TITLE_AFX;
$page_description = 'Scan the QR Code to allow entry to the event venue.';
$body_template_file = CONST_THEMES_TEMPLATE_INCLUDE_PATH . 'scan-etickets.tpl';
$body_template_data = array();
$page_renderer->registerBodyTemplate($body_template_file,$body_template_data);
$can_add = true;

$allowed_fields = [
	'tkt_code' => '',
	'no_of_guests' => '',
];

$cash_refund_allowed_for_cards = ['active'];


if(filter_has_var(INPUT_POST,'mode') && $_POST['mode']=='getencc'){
	// Generate and return enocoded card code. This will help in verifying the card code submitted by the vendor collect payemtn form against the one scanned by the vendor
	// If required, will be done later 

}else if(filter_has_var(INPUT_POST,'mode') && $_POST['mode']=='gettktdtls'){
	// get the latest issued instance of the card
	$today_tm = strtotime(date('Y-m-d'));
	$result=['error_code'=>0, 'message'=>'', 'details'=>[]];
	try{
		$options = [];
		$options['filters'] = [
			['field'=>'qr_code', 'type'=>'EQUAL', 'value'=>$_POST['qr_code']],
		];
		$options['order_by'] = [
			['field'=>'registered_on', 'type'=>'DESC'],
		];
		$options['fieldstofetch'] = [
			'id',
			'booking_id',
			'qr_code',
			'event_id',
			'no_of_tickets',
			'ev_name',
			'reg_status',
			'ev_active',
			'ev_start_dt',
			'ev_end_dt',
			'mem_name',
		];
		$options['page'] = 1;
		$options['recs_per_page'] = 1;
		$tkt_details = \eBizIndia\EventRegistration::getList($options);
		// \eBizIndia\_p($tkt_details);
		if($tkt_details===false){
			$result['error_code'] = 1001;
			$result['message'] = 'Error occurred while fetching the ticket details.';
		}else if(empty($tkt_details)){
			$result['error_code'] = 1002;
			$result['message'] = 'Invalid ticket.';
		}else if($tkt_details[0]['reg_status']!=='Confirmed'){
			$result['error_code'] = 1003;
			$result['message'] = 'The ticket has not been confirmed yet.';
		}else if($tkt_details[0]['ev_active']!=='y'){
			$result['error_code'] = 1004;
			$result['message'] = 'Guests entry is not allowed now.';
		}/*else if(strtotime($tkt_details[0]['ev_start_dt'])>$today_tm || strtotime($tkt_details[0]['ev_end_dt'])<$today_tm){
			$result['error_code'] = 1005;
			$result['message'] = 'Not a valid ticket for today\'s event';
		}*/else{
			$attended = \eBizIndia\EventRegistration::getAttendedData($tkt_details[0]['id']);
			
			if($attended===false){
				$result['error_code'] = 1006;
				$result['message'] = 'Error occurred while fetching the attendee count.';
			}else{
				$tkt_details[0]['attended'] = $attended[0]['attended'];
				$result['details'] = $tkt_details[0];
			}
		}

	}catch (\Exception $e) {
		\eBizIndia\ErrorHandler::logError([],$e);
		$result['error_code'] = $e->getCode();
		$result['message'] = $e->getMessage();
	}

	echo json_encode($result);
	exit;

}else if(filter_has_var(INPUT_POST,'mode') && $_POST['mode']=='enterevent'){
	$result=array('error_code'=>0,'message'=>'', 'elemid'=>array(), 'other_data'=>[]);
	$data=array();
	$data = \eBizIndia\trim_deep(\eBizIndia\striptags_deep(array_intersect_key($_POST, $allowed_fields)));
	if(empty($data) || empty($data['tkt_code'])){
		$result['error_code'] = 1002;
		$result['message'] = 'Please rescan the QR code.';
	}else if(empty($data['no_of_guests'])){
		$result['error_code'] = 1002;
		$result['message'] = 'Please provide the number of guests.';
		$result['error_fields'][]= "#add_form_field_noofguests";
	}else{	
		try {
			$conn = \eBizIndia\PDOConn::getInstance();
			$conn->beginTransaction();
			$today_tm = strtotime(date('Y-m-d'));
			$options = [];
			$options['filters'] = [
				['field'=>'qr_code', 'type'=>'EQUAL', 'value'=>$data['tkt_code']],
			];
			$options['order_by'] = [
				['field'=>'registered_on', 'type'=>'DESC'],
			];
			$options['fieldstofetch'] = [
				'id',
				'booking_id',
				'qr_code',
				'event_id',
				'no_of_tickets',
				'ev_name',
				'reg_status',
				'ev_active',
				'ev_start_dt',
				'ev_end_dt',
				'mem_name',
			];
			$options['page'] = 1;
			$options['recs_per_page'] = 1;
			$tkt_details = \eBizIndia\EventRegistration::getList($options);
			// \eBizIndia\_p($tkt_details);
			if($tkt_details===false)
				throw new Exception('Error occurred while fetching the registration details.', 1003);
			if(empty($tkt_details))
				throw new Exception('Invalid registration.', 1004);
			if($tkt_details[0]['reg_status']!=='Confirmed')
				throw new Exception('Entry is not allowed on an unconfirmed event registration.', 1005);
			if($tkt_details[0]['ev_active']!=='y')
				throw new Exception('Guests entry is not allowed now.', 1006);
			if(strtotime($tkt_details[0]['ev_start_dt'])>$today_tm || strtotime($tkt_details[0]['ev_end_dt'])<$today_tm)
				throw new Exception('Not a valid registration for today\'s event', 1006);

			$attended = \eBizIndia\EventRegistration::getAttendedData($tkt_details[0]['id']);
			if(empty($attended))
				throw new Exception('Error occurred while fetching the attendee count.', 1007);

			$guests_allowed_cnt = $tkt_details[0]['no_of_tickets'] - ((int)($attended[0]['attended']??0));

			if($guests_allowed_cnt<=0){
				throw new Exception('Sorry, the maximum limit of '.\eBizIndia\_esc($tkt_details[0]['no_of_tickets'], true).' has been exhaused for this registration.', 1008);
			}else if(((int)$data['no_of_guests'])>$guests_allowed_cnt){
				throw new Exception('Sorry, the number of persons exceeds the max allowed limit.', 1009);
			}

			// throw new Exception('No error, but stop here.', 1011);
			
			$evreg = new \eBizIndia\EventRegistration($tkt_details[0]['id']);
			$attendee_data = [
				'persons_allowed' => [$guests_allowed_cnt, 'int'],
				'persons_entered' => [$data['no_of_guests'], 'int'],
				'entry_on' => [date('Y-m-d H:i:s'), 'str'],
				'entry_by' => [$loggedindata[0]['id'], 'int'],
				'entry_from' => [\eBizIndia\getRemoteIP(), 'str'],
			];
			if(!$evreg->createAttendedEntry($attendee_data))
				throw new Exception('Sorry, some error occurred while accepting the entry. Try again after sometime', 1010);
			$conn->commit();

			$result['error_code'] = 0;
			$result['message'] = 'The entry for '.$data['no_of_guests'].($data['no_of_guests']>1?' persons':' person').' was successful';

		} catch (\Exception $e) {
			\eBizIndia\ErrorHandler::logError([],$e);
			$result['error_code'] = $e->getCode();
			$result['message'] = $e->getMessage();
			if($conn->inTransaction())
				$conn->rollback();
		}
		
	}
	
	$_SESSION['create_rec_result'] = $result;
	header("Location:?");
	exit;

}elseif(isset($_SESSION['create_rec_result']) && is_array($_SESSION['create_rec_result'])){
	header("Content-Type: text/html; charset=UTF-8");
	echo "<script type='text/javascript' >\n";
	echo "parent.scantktfuncs.handleSaveRecResp(".json_encode($_SESSION['create_rec_result']).");\n";
	echo "</script>";
	unset($_SESSION['create_rec_result']);
	exit;

}

$dom_ready_data[$page]=array();

$additional_base_template_data = array(
										'page_title' => $page_title,
										'page_description' => $page_description,
										'template_type'=>$template_type,
										'dom_ready_code'=>\scriptProviderFuncs\getDomReadyJsCode($page,$dom_ready_data),
										'other_js_code'=>$jscode,
										'module_name' => $page
									);
// $parameters = parse_ini_file(CONST_INCLUDES_DIR.'params.ini', true);	
// $event_id = $parameters['EVENTS']['RUNNING_EVENT_ID'];

// $additional_body_template_data = ['vendor_name'=>$vendor_details['name'], 'running_event_id'=>$event_id];
// $page_renderer->updateBodyTemplateData($additional_body_template_data);

$page_renderer->updateBaseTemplateData($additional_base_template_data);
$page_renderer->addCss(\scriptProviderFuncs\getCss($page));
$js_files=\scriptProviderFuncs\getJavascripts($page);
$page_renderer->addJavascript($js_files['BSH'],'BEFORE_SLASH_HEAD');
$page_renderer->addJavascript($js_files['BSB'],'BEFORE_SLASH_BODY');
$page_renderer->renderPage();

?>
