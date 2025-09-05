<?php
$page='event-registrations';
require_once 'inc.php';


$template_type='';
$page_title = 'Event Registrations'.CONST_TITLE_AFX;
$page_description = 'Member\'s can register for the available events.';
$body_template_file = CONST_THEMES_TEMPLATE_INCLUDE_PATH . 'event-registrations.tpl';
$body_template_data = array();
$page_renderer->registerBodyTemplate($body_template_file,$body_template_data);
$email_pattern="/^\w+([.']?-*\w+)*@\w+([.-]?\w+)*(\.\w{2,4})+$/i";
$user_date_display_format_for_storage = 'd-m-Y';
$can_add = true; 
$can_edit = false;
$_cu_role = $loggedindata[0]['profile_details']['assigned_roles'][0]['role'];
$default_retry_cnt = 3;

$rec_fields = [
	'event_id'=>'', 
	'no_of_tickets'=>'', 
];

if(isset($_GET['mode']) && $_GET['mode']=='pmtresp' && !empty($_GET['payment_request_id']) && !empty($_GET['payment_id']) && !empty($_GET['payment_status'])){
	// payment status returned from instamojo 
	// \eBizIndia\ErrorHandler::logError(['$pmt_resp'=>$_GET]);
	$pmt_obj = new \eBizIndia\Payment(CONST_INSTAMOJO_CREDS);
	$res = $pmt_obj->handlePaymentRespose($_GET);
	if($res[0]==1){
		$panel_heading = 'Payment Status Unknown';
		$event_reg_msg = 'The payment status is unknown or the payment URL was invalid. <br>Please contact the Admins for any clarification.';
		$event_reg_msg_class = 'booking_failure_msg';

	}else if($res[0]==2){
		$panel_heading = 'Invalid Payment Request';
		$event_reg_msg = 'The payment request was invalid. <br>Please contact the Admins for any clarification.';
		$event_reg_msg_class = 'booking_failure_msg';

	}else{
		$event_reg_obj = new \eBizIndia\EventRegistration($res[1]);
		$event_reg_details = $event_reg_obj->getDetails();
		$thanks_msg = '';
		if($res[0]==3){
			$panel_heading = 'Payment Request Already Completed';
			$event_reg_msg = 'The payment for the event registration ID '.\eBizIndia\_esc($event_reg_details[0]['booking_id'], true).' has already been completed earlier. <br>Please contact the Admins for any clarification.';
			$event_reg_msg_class = 'booking_failure_msg';

		}else {
			if($_GET['payment_status']=='Credit'){
				
				if($res[0]==4 || $res[0]==8){
					$panel_heading = 'Registration Not Confirmed.';
					$event_reg_msg = 'The registration for the event <strong>'.\eBizIndia\_esc($event_reg_details[0]['ev_name'], true).'</strong> is yet to be confirmed due to failure in receiving the payment status.<br>If your account has already been debited then contact the Admins with the payment details for the registration ID <strong>'.\eBizIndia\_esc($event_reg_details[0]['booking_id'], true).'</strong> before retrying.';
					$event_reg_msg_class = 'booking_failure_msg';
					// Alert the authorities that the payment was successful but the payment status could not be updated in the event registration record
					$recp = [CONST_ERROR_ALERT_RECP ];
					$email_data = [
						'msg' => 'The payment for an event registration as per the details given below was successful but the payment details could not be updated in the backend. As a result the registration has not been marked as confirmed.',
						'ev_name' => \eBizIndia\_esc($event_reg_details[0]['ev_name'], true),
						'mem_name' => \eBizIndia\_esc($loggedindata[0]['profile_details']['name'], true),
						'booking_id' => \eBizIndia\_esc($event_reg_details[0]['booking_id'], true),
						'booking_date' => date('d-M-Y', strtotime($event_reg_details[0]['registered_on'])),
						'no_of_tickets' => $event_reg_details[0]['no_of_tickets'],
						'amount_payable' => $event_reg_details[0]['total_amount'],
						'pmtg_payment_req_id' => $_GET['payment_request_id'],
						'pmtg_payment_id' => $_GET['payment_id'],
						'pmtg_payment_status' => $_GET['payment_status'],
						'booking_details_page' => CONST_APP_ABSURL.'/event-bookings.php#mode=view&recid='.urlencode($event_reg_details[0]['id']),
						'from_name' => CONST_MAIL_SENDERS_NAME,

					];
					$event_reg_obj->alertEventRegistrationError($email_data, $recp);
				}else{
					$panel_heading = 'Registration Confirmed!';
					$event_reg_msg_class = 'booking_success_msg';
					if($event_reg_details[0]['no_of_tickets']>1){
						$event_reg_msg = $event_reg_details[0]['no_of_tickets'].' persons have been successfully registered for the event <strong>'.\eBizIndia\_esc($event_reg_details[0]['ev_name'], true).'</strong>.<br>Your registration ID is <strong>'.\eBizIndia\_esc($event_reg_details[0]['booking_id'], true).'</strong>.';
					}else{
						$event_reg_msg = 'Registration for one person for the event <strong>'.\eBizIndia\_esc($event_reg_details[0]['ev_name'], true).'</strong> was successful.<br>Your registration ID is <strong>'.\eBizIndia\_esc($event_reg_details[0]['booking_id'], true).'</strong>.';
					}
					$ev_period = '';
					if($event_reg_details[0]['ev_start_dt'] === $event_reg_details[0]['ev_end_dt']){
						$ev_period = date('d-M-Y', strtotime($event_reg_details[0]['ev_start_dt']));
					}else{
						$ev_period = date('d-M-Y', strtotime($event_reg_details[0]['ev_start_dt'])). ' to '.date('d-M-Y', strtotime($event_reg_details[0]['ev_end_dt']));
					}

					// $bid_encoded = \eBizIndia\base64UrlEncode($event_reg_details[0]['booking_id']);
					// $hash = password_hash($bid_encoded.CONST_SECRET_ACCESS_KEY, PASSWORD_DEFAULT);
					// $str_for_qr = $bid_encoded.'.'.\eBizIndia\base64UrlEncode($hash);
					$qr_file_name = 'evqr-'.$event_reg_details[0]['id'].'.png';
					$qr_file_name_path = CONST_EVENT_IMG_DIR_PATH . 'qr-codes/'.$qr_file_name;
					$qr_code_for_email = [];
					if(\eBizIndia\QRCodeGen::createPng($event_reg_details[0]['qr_code'], $qr_file_name_path, CONST_QRCODE_PARAMS['size'], CONST_QRCODE_PARAMS['margin'], $event_reg_details[0]['qr_code'])){
							if(ENABLE_EVREG_MSG_OVER_WHATSAPP == 1 && !empty($loggedindata[0]['profile_details']['mobile'])){
								$aisensy = new \eBizIndia\AISensy(AISENSY_API_KEY, 2);
								$aisensy->resetOverrideRecipient(); 
								$aisensy->setOverrideRecipient(CONST_WA_OVERRIDE);
								$media = [
									'url' => CONST_THEMES_CUSTOM_IMAGES_PATH.'event/qr-codes/'.$qr_file_name.'?_='.mt_rand(),
									'filename' => $qr_file_name,
								];
								$wa_msg_vars = [
									$loggedindata[0]['profile_details']['name'], 
									$event_reg_details[0]['ev_name'], 
									$ev_period,
									$event_reg_details[0]['time_text'],
									str_replace("\n", " ", $event_reg_details[0]['ev_venue']),
									$event_reg_details[0]['booking_id'].' dt. '.date('d-M-Y', strtotime($event_reg_details[0]['registered_on'])), // 
									$event_reg_details[0]['no_of_tickets'],
									'&#8377; '.$event_reg_details[0]['total_amount'],
								];
								$resp = $aisensy->sendCampaignMessage(AISENSY_EVENT_REG_CAMPAIGN, $loggedindata[0]['profile_details']['mobile'], $wa_msg_vars, $media);
								\eBizIndia\ErrorHandler::logError(['Paid event','WA template params:'.print_r($wa_msg_vars, true),'WA media:'.print_r($media, true),'WA resp: '.$resp]);
							}
							$qr_code_for_email = [
								'file_name_path' => $qr_file_name_path,
								'image_name' => $qr_file_name,
								'type' => 'image/png',
							];
					}
					
					// Send booking confirmation email to the member
					$recp = ['to'=>[$loggedindata[0]['profile_details']['email']] ];
					$email_data = [
						'ev_name' => \eBizIndia\_esc($event_reg_details[0]['ev_name'], true),
						'ev_venue' => nl2br(\eBizIndia\_esc($event_reg_details[0]['ev_venue'], true)),
						'mem_name' => \eBizIndia\_esc($loggedindata[0]['profile_details']['name'], true),
						'booking_id' => \eBizIndia\_esc($event_reg_details[0]['booking_id'], true),
						'booking_date' => date('d-M-Y', strtotime($event_reg_details[0]['registered_on'])),
						'ev_dt_period' => \eBizIndia\_esc($ev_period, true),
						'ev_time_text' => \eBizIndia\_esc($event_reg_details[0]['time_text'], true),
						'no_of_tickets' => $event_reg_details[0]['no_of_tickets'],
						'amount_paid' => $event_reg_details[0]['total_amount'],
						'booking_details_page' => CONST_APP_ABSURL.'/event-registrations.php#mode=view&recid='.urlencode($res[1]),
						'from_name' => CONST_MAIL_SENDERS_NAME,
						'offer' => $event_reg_details[0]['offer']==='EB'?'Early Bird':'',

					];
					if(!empty($qr_code_for_email))
						$email_data['qr_code']= $qr_code_for_email;
					$event_reg_obj->sendTicketBookingConfirmationEmail($email_data, $recp);
					///////////////////////


					if($res[0]==5 || $res[0]==6 || $res[0]==7){
						// Alert the authorities that the payment details could not be updated in the event registration record due to failure in fetching the same from the API
						$recp = [CONST_ERROR_ALERT_RECP ];
						$email_data = [
							'msg' => 'The payment for an event registration as per the details given below was successful but the payment details could not be fetched from the Instamojo API. As a result the same was not recorded in the backend.',
							'ev_name' => \eBizIndia\_esc($event_reg_details[0]['ev_name'], true),
							'mem_name' => \eBizIndia\_esc($loggedindata[0]['profile_details']['name'], true),
							'booking_id' => \eBizIndia\_esc($event_reg_details[0]['booking_id'], true),
							'booking_date' => date('d-M-Y', strtotime($event_reg_details[0]['registered_on'])),
							'no_of_tickets' => $event_reg_details[0]['no_of_tickets'],
							'amount_payable' => $event_reg_details[0]['total_amount'],
							'pmtg_payment_req_id' => $_GET['payment_request_id'],
							'pmtg_payment_id' => $_GET['payment_id'],
							'pmtg_payment_status' => $_GET['payment_status'],
							'booking_details_page' => CONST_APP_ABSURL.'/event-bookings.php#mode=view&recid='.urlencode($event_reg_details[0]['id']),
							'from_name' => CONST_MAIL_SENDERS_NAME,

						];
						$event_reg_obj->alertEventRegistrationError($email_data, $recp);
					}

				}
			}else{
				$event_reg_msg_class = 'booking_failure_msg';
				$panel_heading = 'Registration Failed';
				$event_reg_msg = 'Registration for the event <strong>'.\eBizIndia\_esc($event_reg_details[0]['ev_name'], true).'</strong> has failed due to payment failure.<br>Please <a href="'.CONST_APP_ABSURL.'/event-registrations.php" >try again</a> after some time.';

				if($res[0]==4 || $res[0]==8){
					// Alert the authorities that the payment failed but the failure status could not be updated in the event registration record
					$recp = [CONST_ERROR_ALERT_RECP ];
					$email_data = [
						'msg' => 'The payment for an event registration as per the details given below has failed but the details could not be updated in the backend due to server error.',
						'ev_name' => \eBizIndia\_esc($event_reg_details[0]['ev_name'], true),
						'mem_name' => \eBizIndia\_esc($loggedindata[0]['profile_details']['name'], true),
						'booking_id' => \eBizIndia\_esc($event_reg_details[0]['booking_id'], true),
						'booking_date' => date('d-M-Y', strtotime($event_reg_details[0]['registered_on'])),
						'no_of_tickets' => $event_reg_details[0]['no_of_tickets'],
						'amount_payable' => $event_reg_details[0]['total_amount'],
						'pmtg_payment_req_id' => $_GET['payment_request_id'],
						'pmtg_payment_id' => $_GET['payment_id'],
						'pmtg_payment_status' => $_GET['payment_status'],
						'booking_details_page' => CONST_APP_ABSURL.'/event-bookings.php#mode=view&recid='.urlencode($event_reg_details[0]['id']),
						'from_name' => CONST_MAIL_SENDERS_NAME,
						
					];
					$event_reg_obj->alertEventRegistrationError($email_data, $recp);
				}else if($res[0]==5 || $res[0]==6 || $res[0]==7){
					// Alert the authorities that the payment failure details could not be updated in the event registration record due to failure in fetching the same from the API
					$recp = [CONST_ERROR_ALERT_RECP ];
					$email_data = [
						'msg' => 'The payment for an event registration as per the details given below has failed but the details could not be fetched via the Instamojo API and thus were not updated in the backend.',
						'ev_name' => \eBizIndia\_esc($event_reg_details[0]['ev_name'], true),
						'mem_name' => \eBizIndia\_esc($loggedindata[0]['profile_details']['name'], true),
						'booking_id' => \eBizIndia\_esc($event_reg_details[0]['booking_id'], true),
						'booking_date' => date('d-M-Y', strtotime($event_reg_details[0]['registered_on'])),
						'no_of_tickets' => $event_reg_details[0]['no_of_tickets'],
						'amount_payable' => $event_reg_details[0]['total_amount'],
						'pmtg_payment_req_id' => $_GET['payment_request_id'],
						'pmtg_payment_id' => $_GET['payment_id'],
						'pmtg_payment_status' => $_GET['payment_status'],
						'booking_details_page' => CONST_APP_ABSURL.'/event-bookings.php#mode=view&recid='.urlencode($event_reg_details[0]['id']),
						'from_name' => CONST_MAIL_SENDERS_NAME,
						
					];
					$event_reg_obj->alertEventRegistrationError($email_data, $recp);
				}
			}

		}
	} 
	$_SESSION['pmt_resp_result'] = ['panel_heading'=>$panel_heading, 'event_reg_msg' => $event_reg_msg, 'event_reg_msg_class' => $event_reg_msg_class ];
	header('HTTP/1.0 301 Moved Permanently', true, 301);
	header('Location:?#mode=regthanks');
	exit;

}elseif(isset($_SESSION['pmt_resp_result']) && is_array($_SESSION['pmt_resp_result'])){
	$page_renderer->updateBodyTemplateData($_SESSION['pmt_resp_result']);
	unset($_SESSION['pmt_resp_result']);

}else if(filter_has_var(INPUT_POST,'mode') && $_POST['mode']=='createrec'){
	$result=array('error_code'=>0,'message'=>[], 'elemid'=>array(), 'other_data'=>[]);

	if($can_add===false){
		$result['error_code']=403;
		$result['message']="Sorry, you are not authorised to perfom this action.";
	}else{
		$data=array();
		$data = \eBizIndia\trim_deep(\eBizIndia\striptags_deep(array_intersect_key($_POST, $rec_fields ) ));
		
		// fetch the already booked tickets count for the event, for this member
		$options = [];
		$options['fieldstofetch']= ['ev_id', 'member_id', 'bookings', 'tot_tickets' ];
		$options['filters'] = [
			['field'=>'ev_id', 'type'=>'EQUAL', 'value'=>(int)$data['event_id']],
			['field'=>'member_id', 'type'=>'EQUAL', 'value'=>(int)$loggedindata[0]['profile_details']['id']],
			['field'=>'reg_status', 'type'=>'EQUAL', 'value'=>'Confirmed'],
		];
		$tkt_booked=\eBizIndia\EventRegistration::getEventWiseSummaryList($options);
		$tkts_already_booked = $tkt_booked[0]['tot_tickets']??0;

		$reg_obj = new \eBizIndia\EventRegistration();
		$validation_res = $reg_obj->validate($data, 'add', ['tkts_already_booked'=>$tkts_already_booked]);
		if($validation_res['error_code']>0){
			$result = $validation_res;
		} else {
			// $result['$validation_res'] = $validation_res;
			// $_SESSION['create_rec_result'] = $result;
			// header("Location:?");
			// exit;
			
			if($_POST['offer']=='EB'){
				$ev_obj = new \eBizIndia\Event($data['event_id']);
				$ev_details = $ev_obj->getDetails();
				if(!empty($ev_details))
					$ev_details = $ev_details[0];
				$offer_details = \eBizIndia\EventRegistration::isEarlyBirdOfferApplicable($ev_details);
			}else{
				$offer_details = ['offer'=>'', 'offer_tkt_price'=>''];
			}
			
			if(empty($offer_details)){
				$result['error_code'] = 9;
				$result['message'] = 'Some error occurred while verifying the early bird offer details.';
			}else if($offer_details['offer']!=$_POST['offer']){
				$result['error_code'] = 10;
				$result['message'] = 'Apparently the ticket price has changed. Please try again.';
			}else{
				$tm = time();
				$created_on = date('Y-m-d H:i:s', $tm);
				$ip = \eBizIndia\getRemoteIP();
				$data['member_id'] = $loggedindata[0]['profile_details']['id'];
				$data['registered_on'] = $created_on;
				$data['registered_from'] = $ip;
				$data['event_start_dt'] = $validation_res['event_details'][0]['start_dt'];
				$data['event_end_dt'] = $validation_res['event_details'][0]['end_dt'];
				if($offer_details['offer']!='')
					$data['offer'] = $offer_details['offer'];
				$data['price_per_tkt'] = $offer_details['offer']!=''?intval($offer_details['offer_tkt_price']):$validation_res['event_details'][0]['tkt_price'];
				$data['gst_perc'] = $validation_res['event_details'][0]['gst_perc'];
				$data['conv_fee'] = $validation_res['event_details'][0]['conv_fee'];
				$data['total_amount'] = $reg_obj->calculateBookingAmount($data['no_of_tickets'], $data['price_per_tkt'], $data['gst_perc'], $data['conv_fee']);
				// if($data['total_amount']>0)
				// 	$data['payment_mode'] = 'Cash';
				// $data['amount_paid'] = $data['total_amount'];
				// $data['paid_on'] = date('Y-m-d');
				if($data['price_per_tkt']<=0){
					$data['reg_status'] = 'Confirmed'; // set confirmed for Free tickets
					$data['payment_status'] = 'Free';
				}else{
					$data['reg_status'] = 'Pending'; // Will be updated to confirmed after a successful payment
				}
				
				
				try{
					$conn = \eBizIndia\PDOConn::getInstance();
					$conn->beginTransaction();
					$unique_qr_code = '';
					$error_details_to_log['mode'] = 'createrec';
					$error_details_to_log['part'] = 'Create a new event registration.';
					$max_tries = $default_retry_cnt; $try_after = 100000; // micro sec
					while($max_tries>0){
						$max_tries--;
						$booking_id = $reg_obj->generateBookingId(date('d-m-Y', $tm));
						if(empty($booking_id)){
							if($max_tries<=0)
								throw new Exception('Error generating event registration ID.');
							usleep($try_after); // wait before retrying
						}else{
							$data['booking_id'] = $booking_id;
							$unique_qr_code = \eBizIndia\EventRegistration::generateUniqueQRCode(5, false); // 5 chars, case insensitive string
							if(empty($unique_qr_code)){
								if($max_tries<=0)
									throw new Exception('Event registration failed. Error generating QR Code.',1002);
								usleep($try_after); // wait before retrying
								continue;
							}
							$data['qr_code'] = $unique_qr_code;
							$rec_id=$reg_obj->saveDetails($data);
							if($rec_id===false){
								if($max_tries<=0)
									throw new Exception('Error occurred while processing the event registration request.', 1003);
								usleep($try_after); // wait before retrying
							}else{
								$max_tries = -1;
							}
						}
					}

					$result['error_code']=0;
					$result['message']='Registration successful.';
					$result['other_data']['no_of_tickets'] = $data['no_of_tickets'];
					$result['other_data']['ev_name_disp'] = \eBizIndia\_esc($validation_res['event_details'][0]['name'], true);
					$result['other_data']['booking_id'] = \eBizIndia\_esc($booking_id, true);
					

					$recp = ['to'=>[$loggedindata[0]['profile_details']['email']] ];
					// $recp = ['to'=>['nishant@ebizindia.com', 'arun@ebizindia.com'] ];
					$ev_period = '';
					if($validation_res['event_details'][0]['start_dt'] === $validation_res['event_details'][0]['end_dt']){
						$ev_period = date('d-M-Y', strtotime($validation_res['event_details'][0]['start_dt']));
					}else{
						$ev_period = date('d-M-Y', strtotime($validation_res['event_details'][0]['start_dt'])). ' to '.date('d-M-Y', strtotime($validation_res['event_details'][0]['end_dt']));
					}
					if($data['price_per_tkt']<=0){
						$qr_file_name = 'evqr-'.$rec_id.'.png';
						$qr_file_name_path = CONST_EVENT_IMG_DIR_PATH . 'qr-codes/'.$qr_file_name;
						
						if(!\eBizIndia\QRCodeGen::createPng($unique_qr_code, $qr_file_name_path, CONST_QRCODE_PARAMS['size'], CONST_QRCODE_PARAMS['margin'], $unique_qr_code)){
							throw new Exception("Event registration failed. Error occurred while generating the QR code.", 1001);
						}
						$conn->commit();
						// If booking is free for an event then payment process won't be required so send the booking confirmation email now
																
						if(ENABLE_EVREG_MSG_OVER_WHATSAPP == 1 && !empty($loggedindata[0]['profile_details']['mobile'])){
							$aisensy = new \eBizIndia\AISensy(AISENSY_API_KEY, 2);
							$aisensy->resetOverrideRecipient(); 
							$aisensy->setOverrideRecipient(CONST_WA_OVERRIDE);
							$media = [
								'url' => CONST_THEMES_CUSTOM_IMAGES_PATH.'event/qr-codes/'.$qr_file_name.'?_='.mt_rand(),
								'filename'=>$qr_file_name,
							];
							$wa_msg_vars = [
								$loggedindata[0]['profile_details']['name'], 
								$validation_res['event_details'][0]['name'], 
								$ev_period,
								$validation_res['event_details'][0]['time_text'],
								trim(str_replace("\n", " ", $validation_res['event_details'][0]['venue'])),
								$booking_id.' dt. '.date('d-M-Y', $tm), // 
								$data['no_of_tickets'],
								'Free', 
							];
							$resp = $aisensy->sendCampaignMessage(AISENSY_EVENT_REG_CAMPAIGN, $loggedindata[0]['profile_details']['mobile'], $wa_msg_vars, $media);
							\eBizIndia\ErrorHandler::logError(['WA template params:'.print_r($wa_msg_vars, true),'WA media:'.print_r($media, true),'WA resp: '.$resp]);
						}

						$email_data = [
							'ev_name' => \eBizIndia\_esc($validation_res['event_details'][0]['name'], true),
							'ev_venue' => nl2br(\eBizIndia\_esc($validation_res['event_details'][0]['venue'], true)),
							'mem_name' => \eBizIndia\_esc($loggedindata[0]['profile_details']['name'], true),
							'booking_id' => \eBizIndia\_esc($booking_id, true),
							'booking_date' => date('d-M-Y', $tm),
							'ev_dt_period' => \eBizIndia\_esc($ev_period, true),
							'ev_time_text' => \eBizIndia\_esc($validation_res['event_details'][0]['time_text'], true),
							'no_of_tickets' => $data['no_of_tickets'],
							'amount_paid' => $data['total_amount'],
							'booking_details_page' => CONST_APP_ABSURL.'/event-registrations.php#mode=view&recid='.urlencode($rec_id),
							'qr_code' => [
								'file_name_path' => $qr_file_name_path,
								'image_name' => $qr_file_name,
								'type' => 'image/png',
							],
							'offer' => $data['offer']==='EB'?'Early Bird':'',
							'from_name' => CONST_MAIL_SENDERS_NAME,

						];
						$reg_obj->sendTicketBookingConfirmationEmail($email_data, $recp);
					}else{
						// get the payment request link
						$max_tries = $default_retry_cnt; $try_after = 200000; // micro sec
						while($max_tries>0){
							$max_tries--;
							try{
								if(empty($pmt_obj))
									$pmt_obj = new \eBizIndia\Payment(CONST_INSTAMOJO_CREDS, 'event_reg', $rec_id);
								$pmt_req = $pmt_obj->generatePaymentReq([
									'amount'=>$data['total_amount'],
									'purpose'=>$validation_res['event_details'][0]['name'].' ('.$booking_id.')',
									'buyer_name'=>$loggedindata[0]['profile_details']['name'],
									'email'=>$loggedindata[0]['profile_details']['email'],
									'phone'=>$loggedindata[0]['profile_details']['mobile'],
									'redirect_url'=>CONST_APP_ABSURL."/event-registrations.php?mode=pmtresp",
									// 'expires_at' => gmdate('Y-m-dTH:i:s',time()+300), // 5 minutes
								]);
								$max_tries=-1;
							}catch(\Exception $e){ 
								if($max_tries<=0){
									\eBizIndia\ErrorHandler::logError(['$pmt_obj: '.var_export($pmt_obj, true)], $e);
									throw $e;
								}
								usleep($try_after); // wait before retrying
							}
						}

						$max_tries = $default_retry_cnt; $try_after = 100000; // micro sec
						while($max_tries>0){
							$max_tries--;
							if($pmt_obj->recordPaymentRequest()){
								$max_tries = -1; // everything OK, no more tries are required
								$conn->commit();
								$result['other_data']['pmt_req'] = $pmt_req['longurl'];
							}else{
								if($max_tries<=0){
									$result['error_code']=8;
									$result['message']='Event registration failed due to error in initiating the payment process.';
									throw new Exception('Error initiating the payment process. System failed while trying to record the payment request details in the DB.');
								}
								usleep($try_after); // wait before retrying
							}
						}
					}

				}catch(\Exception $e){
					$last_error = \eBizIndia\PDOConn::getLastError();
					$code = $e->getCode();
					if($result['error_code']==0){
						$result['message']='Event registration failed due to error in reaching the payment gateway.';
						if(is_a($e, '\Instamojo\Exceptions\AuthenticationException')){
							$result['error_code']=3; 
							\eBizIndia\ErrorHandler::logError(['Error generating payment request.', 'Instamojo Exception class: \Instamojo\Exceptions\AuthenticationException'], $e);
						}else if(is_a($e, '\Instamojo\Exceptions\ActionForbiddenException')){
							$result['error_code']=4; 
							\eBizIndia\ErrorHandler::logError(['Error generating payment request.', 'Instamojo Exception class: \Instamojo\Exceptions\ActionForbiddenException'], $e);
						}else if(is_a($e, '\Instamojo\Exceptions\InvalidRequestException')){
							$result['error_code']=5; 
							\eBizIndia\ErrorHandler::logError(['Error generating payment request.', 'Instamojo Exception class: \Instamojo\Exceptions\InvalidRequestException'], $e);
						}else if(is_a($e, '\Instamojo\Exceptions\MissingParameterException')){
							$result['error_code']=6; 
							\eBizIndia\ErrorHandler::logError(['Error generating payment request.', 'Instamojo Exception class: \Instamojo\Exceptions\MissingParameterException'], $e);
						}else if(is_a($e, '\Instamojo\Exceptions\ApiException')){
							$result['error_code']=7; 
							\eBizIndia\ErrorHandler::logError(['Error generating payment request.', 'Instamojo Exception class: \Instamojo\Exceptions\ApiException'], $e);
						}else if(in_array($code, [1001,1002,1003]) ){
							$result['error_code']=$e->getCode(); 
							$result['message']=$e->getMessage(); 
							// \eBizIndia\ErrorHandler::logError(['Error booking event tickets.', $e->getMessage()], $e);
						}else{
							$result['error_code']=1; // DB error
							$result['message']="Event registration failed due to server error.";
							\eBizIndia\ErrorHandler::logError(['Error generating payment request.', 'Exception class: '.get_class($e)], $e); 
						}
					}
					$error_details_to_log['exception object class'] = get_class($e); 
					$error_details_to_log['member_data'] = array_intersect_key($loggedindata[0]['profile_details'], ['id'=>'', 'name'=>'', 'user_acnt_id'=>'']);
					$error_details_to_log['result'] = $result;
					\eBizIndia\ErrorHandler::logError($error_details_to_log, $e);
					if($conn && $conn->inTransaction()){
						$conn->rollBack();
						\eBizIndia\Helper::resetAutoID(CONST_TBL_PREFIX . 'event_registrations');
					}
					 
				}

			}


			
		}
	}


	$_SESSION['create_rec_result'] = $result;
	header("Location:?");
	exit;

}elseif(isset($_SESSION['create_rec_result']) && is_array($_SESSION['create_rec_result'])){
	header("Content-Type: text/html; charset=UTF-8");
	echo "<script type='text/javascript' >\n";
	echo "parent.evregfuncs.handleAddRecResponse(".json_encode($_SESSION['create_rec_result']).");\n";
	echo "</script>";
	unset($_SESSION['create_rec_result']);
	exit;

}elseif(filter_has_var(INPUT_POST,'mode') && $_POST['mode']=='getEventDetails'){
	$result=array();
	$error=0; // no error
	
	if($_POST['id']==''){
		$error=1; // Record ID missing

	}else{
		$ev_id = (int)$_POST['id'];
		$obj = new \eBizIndia\Event($ev_id);
		$recorddetails = $obj->getDetails();

		if($recorddetails===false){
			$error=2; // db error
		}elseif(count($recorddetails)==0){
			$error=3; // Rec ID does not exist
		}else{
			$recorddetails=$recorddetails[0];
			// $early_bird_applicable = false;
			// if($recorddetails['early_bird']=='y'){
			// 	if(!empty($recorddetails['early_bird_end_dt'])){
			// 		$today = strtotime(date('Y-m-d'));
			// 		$early_bird_end_dt_tm = strtotime($recorddetails['early_bird_end_dt']);
			// 		if($early_bird_end_dt_tm>=$today){
			// 			$early_bird_applicable = true;
			// 		}
			// 	}

			// 	if(!empty($recorddetails['early_bird_max_cnt'])){
			// 		// get the confirmed counts
			// 		$options = [];
			// 		$options['fieldstofetch']= ['ev_id', 'bookings', 'tot_tickets'];
			// 		$options['filters'] = [
			// 			['field'=>'ev_id', 'type'=>'EQUAL', 'value'=>$ev_id],
			// 			['field'=>'reg_status', 'type'=>'EQUAL', 'value'=>'Confirmed'],
			// 		];
			// 		$confirmed_tkts = \eBizIndia\EventRegistration::getEventWiseSummaryList($options);
			// 		if($confirmed_tkts===false){
			// 			$error = 4; // Error fetching the already booked tickets count
			// 		}else{
			// 			if($recorddetails['early_bird_max_cnt']>$confirmed_tkts[0]['tot_tickets']){
			// 				// Still a few early bird tickets are available
			// 				$early_bird_applicable = true;
			// 			}
			// 		}
			// 	}else if($recorddetails['early_bird_max_cnt']===0){
			// 		// Count of 0 has explicitly been set, maybe to close the EB offer, so the EB offer is not applicable anymore even if an EB end date is available and is within the allowed range.
			// 		$early_bird_applicable = false;
			// 	}

			// 	// $recorddetails['intval_early_bird_max_cnt'] = intval($recorddetails['early_bird_max_cnt']);

			// }

			// $recorddetails['offer'] = $early_bird_applicable===true?'EB':'';
			// $recorddetails['offer_tkt_price'] = $early_bird_applicable===true?$recorddetails['early_bird_tkt_price']:'';

			$offer_details = \eBizIndia\EventRegistration::isEarlyBirdOfferApplicable($recorddetails);
			if(!empty($offer_details))
				$recorddetails = array_merge($recorddetails, $offer_details);
			else
				$error = 4; // Error fetching the already booked tickets count
			
			if($error===0){
				$edit_restricted_fields = [];

				$recorddetails['name_disp'] = \eBizIndia\_esc($recorddetails['name'], true);
				
				$recorddetails['dsk_img_url'] = CONST_EVENT_IMG_URL_PATH.$recorddetails['dsk_img'];
				$pic_size = getimagesize(CONST_EVENT_IMG_DIR_PATH.$recorddetails['dsk_img']);
				$recorddetails['dsk_img_org_width'] = $pic_size[0];

				$recorddetails['mob_img_url'] = CONST_EVENT_IMG_URL_PATH.$recorddetails['mob_img'];
				$pic_size = getimagesize(CONST_EVENT_IMG_DIR_PATH.$recorddetails['mob_img']);
				$recorddetails['mob_img_org_width'] = $pic_size[0];

				$recorddetails['description_disp'] = nl2br(\eBizIndia\_esc($recorddetails['description'], true));
				$recorddetails['venue_disp'] = nl2br(\eBizIndia\_esc($recorddetails['venue'], true));
				if($recorddetails['start_dt']==$recorddetails['end_dt'])
					$recorddetails['period_disp'] = \eBizIndia\_esc(date('d-M-Y', strtotime($recorddetails['start_dt'])), true);
				else
					$recorddetails['period_disp'] = \eBizIndia\_esc(date('d-M-Y', strtotime($recorddetails['start_dt'])) .' To '. date('d-M-Y', strtotime($recorddetails['end_dt']) ), true);

				$recorddetails['time_text_disp'] = \eBizIndia\_esc($recorddetails['time_text']??'', true);

				$recorddetails['reg_end_dt_disp'] = $recorddetails['reg_end_dt']==''?'':\eBizIndia\_esc(date('d-M-Y', strtotime($recorddetails['reg_end_dt'])), true);
				$today = new DateTime();
				$recorddetails['reg_allowed'] = ( $recorddetails['active'] =='y' && $recorddetails['reg_active'] =='y' && $recorddetails['reg_start_dt'] !='' && (new DateTime($recorddetails['reg_start_dt'].' 00:00:00'))<=$today && ( $recorddetails['reg_end_dt']=='' ||   (new DateTime($recorddetails['reg_end_dt'].' 23:59:59'))>=$today ) );

				$recorddetails['max_tkts_allowed'] = $recorddetails['max_tkt_per_person'];

				$options = [];
				$options['fieldstofetch']= ['ev_id', 'member_id', 'bookings', 'tot_tickets' ];
				$options['filters'] = [
					['field'=>'ev_id', 'type'=>'EQUAL', 'value'=>(int)$_POST['id']],
					['field'=>'member_id', 'type'=>'EQUAL', 'value'=>(int)$loggedindata[0]['profile_details']['id']],
					['field'=>'reg_status', 'type'=>'EQUAL', 'value'=>'Confirmed'],
				];

				$tkt_booked=\eBizIndia\EventRegistration::getEventWiseSummaryList($options);
				$recorddetails['tkts_already_booked'] = $tkt_booked[0]['tot_tickets']??0;

			}

		}
	}

	$result[0]=$error;
	$result[1]=$recorddetails;
		
	echo json_encode($result);

	exit;

}elseif(filter_has_var(INPUT_POST,'mode') && $_POST['mode']=='getRecordDetails'){
	$result=array();
	$error=0; // no error
	$can_edit = true;
	
	if($_POST['recordid']==''){
		$error=1; // Record ID missing

	}else{
		$obj = new \eBizIndia\EventRegistration((int)$_POST['recordid'], (int) $loggedindata[0]['profile_details']['id']);
		$recorddetails = $obj->getDetails();

		if($recorddetails===false){
			$error=2; // db error
		}elseif(count($recorddetails)==0 || $recorddetails[0]['reg_status']!=='Confirmed'){
			$error=3; // Rec ID does not exist
		}else{
			$recorddetails=$recorddetails[0];
			$edit_restricted_fields = [];

			$recorddetails['name_disp'] = \eBizIndia\_esc($recorddetails['ev_name'], true);
			$recorddetails['description_disp'] = nl2br(\eBizIndia\_esc($recorddetails['ev_description'], true));
			$recorddetails['venue_disp'] = nl2br(\eBizIndia\_esc($recorddetails['ev_venue'], true));
			
			if($recorddetails['ev_start_dt']==$recorddetails['ev_end_dt'])
				$recorddetails['period_disp'] = \eBizIndia\_esc(date('d-M-Y', strtotime($recorddetails['ev_start_dt'])), true);
			else
				$recorddetails['period_disp'] = \eBizIndia\_esc(date('d-M-Y', strtotime($recorddetails['ev_start_dt'])) .' To '. date('d-M-Y', strtotime($recorddetails['ev_end_dt']) ), true);

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
		}
	}

	$result[0]=$error;
	$result[1]['can_edit'] = $can_edit;
	$result[1]['cuid'] = $loggedindata[0]['id'];  // This is the auto id of the table users and not member
	$result[1]['record_details']=$recorddetails;
	$result[1]['edit_restricted_fields']=$edit_restricted_fields;
	
	echo json_encode($result);

	exit;

}elseif(filter_has_var(INPUT_POST,'mode') && $_POST['mode']=='getBookings'){
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
				if(!in_array($field, ['booking_id','ev_name', 'ev_description', 'ev_venue', 'ev_falls_in_period']))
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

				$disp_value = $field=='ev_falls_in_period'?date('d-M-Y', strtotime($value[0])).' TO '.date('d-M-Y', strtotime($value[1])):$value;
				
				if($field=='booking_id')
					$fltr_text = 'Registration ID ';
				else if($field=='ev_name')
					$fltr_text = 'Event\'s name ';
				else if($field=='ev_description')
					$fltr_text = 'Event\'s description ';
				else if($field=='ev_venue')
					$fltr_text = 'Event\'s venue address ';
				else if($field=='ev_falls_in_period')
					$fltr_text = 'Event falls in the period  ';
				else 
					$fltr_text = ucfirst($field).' ';
				
				if($field=='ev_falls_in_period'){
					if($value[0]!='' && $value[1]==''){
						$disp_value = date('d-M-Y', strtotime($value[0]));
						$fltr_text = 'Event ends on or after  ';
					}else if($value[0]=='' && $value[1]!=''){
						$disp_value = date('d-M-Y', strtotime($value[1]));
						$fltr_text = 'Event starts on or before  ';
					}
				}else{
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


				$filtertext[]='<span class="searched_elem"  >'.$fltr_text.'  <b>'.\eBizIndia\_esc($disp_value, true).'</b><span class="remove_filter" data-fld="'.$field.'"  >X</span> </span>';
			}
			$result[1]['filtertext'] = implode($filtertext);
		}
	}

	// if($_cu_role!=='ADMIN') // Allow only one's own registrations to be listed
		$options['filters'][] = [ 'field'=> 'member_id', 'type'=>'EQUAL', 'value'=> $loggedindata[0]['profile_details']['id'] ];
		$options['filters'][] = [ 'field'=> 'reg_status', 'type'=>'EQUAL', 'value'=> 'Confirmed' ]; // Only confirmed bookings should be shown

	$tot_rec_options = [
		'fieldstofetch'=>['recordcount'],
		'filters' => [
			[ 'field'=> 'member_id', 'type'=>'EQUAL', 'value'=> $loggedindata[0]['profile_details']['id'] ],
			[ 'field'=> 'reg_status', 'type'=>'EQUAL', 'value'=> 'Confirmed' ], // Only confirmed bookings should be shown
		],
	];

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
			'ev_name',
			'no_of_tickets',
			'registered_on',
			'ev_start_dt',
			'total_amount',
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

}elseif(filter_has_var(INPUT_POST,'mode') && $_POST['mode']=='getList'){ // list of events available for booking tickets
	$result=array(0,array()); // error code and list html
	$show_dnd_status = true;
	$options=[];
	$options['filters'] = [
		// ['field'=>'reg_active_on_date', 'type'=>'', 'value'=> date('Y-m-d')],
		// ['field'=>'reg_active', 'type'=>'EQUAL', 'value'=> 'y'],
		['field'=>'active', 'type'=>'EQUAL', 'value'=> 'y'],
	];

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
			// $options['filters']=[];
			foreach($searchdata as $filter){
				$field = (string)$filter['searchon'];
				if(!in_array($field, ['name', 'description', 'venue']))
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

				$disp_value = $field=='ev_falls_in_period'?date('d-M-Y', strtotime($value[0])).' TO '.date('d-M-Y', strtotime($value[1])):$value;
				
				if($field=='name')
					$fltr_text = 'Event\'s name ';
				else if($field=='description')
					$fltr_text = 'Event\'s description ';
				else if($field=='venue')
					$fltr_text = 'Event\'s venue address ';
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


				$filtertext[]='<span class="searched_elem"  >'.$fltr_text.'  <b>'.\eBizIndia\_esc($disp_value, true).'</b><span class="remove_filter" data-fld="'.$field.'"  >X</span> </span>';
			}
			$result[1]['filtertext'] = implode($filtertext);
		}
	}

	$tot_rec_options = [
		'fieldstofetch'=>['recordcount'],
		'filters' => [
			// ['field'=>'reg_active_on_date', 'type'=>'', 'value'=> date('Y-m-d')],
			// ['field'=>'reg_active', 'type'=>'EQUAL', 'value'=> 'y'],
			['field'=>'active', 'type'=>'EQUAL', 'value'=> 'y'],
		],
		
	];

	$options['fieldstofetch'] = ['recordcount'];

	// get total emp count
	$tot_rec_cnt = \eBizIndia\Event::getList($tot_rec_options); 
	$result[1]['tot_rec_cnt'] = $tot_rec_cnt[0]['recordcount'];

	$recordcount = \eBizIndia\Event::getList($options);
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
			'name',
			'dsk_img',
			'mob_img',
			'description',
			'venue',
			'start_dt',
			'end_dt',
			'time_text',
			'tkt_price',
			'reg_active',
			'reg_start_dt',
			'reg_end_dt',
		];
		$options['resourceonly'] = true;

		if(isset($_POST['sortdata']) && $_POST['sortdata']!=''){
			$options['order_by']=[];
			$sortdata=json_decode($_POST['sortdata'],true);
			foreach($sortdata as $sort_param){

				$options['order_by'][]=array('field'=>$sort_param['sorton'],'type'=>$sort_param['sortorder']);

			}
		}

		$res=\eBizIndia\Event::getList($options);
		if(!$res){
			$error = 1;
		}else{
			$records = [];
			$temp_ev_ids = [];
			while ($row = $res->fetch(\PDO::FETCH_ASSOC)) {
				$records[$row['id']] = $row;
				$records[$row['id']]['tot_tickets'] = 0;
				$records[$row['id']]['bookings'] = 0;
				$temp_ev_ids[] = $row['id'];
			}
			$res->closeCursor();

			if(!empty($records)){
				// Now get the number of bookings and tickets already booked by the logged in member for each event in the events list retrieved above
				$options = [];
				$options['filters'] = [

					['field'=>'ev_id', 'type'=>'IN', 'value'=> $temp_ev_ids ],
					['field'=>'member_id', 'type'=>'EQUAL', 'value'=> $loggedindata[0]['profile_details']['id'] ],
					['field'=>'reg_status', 'type'=>'EQUAL', 'value'=> 'Confirmed' ],

				];
				$options['fieldstofetch'] = ['ev_id', 'tot_tickets', 'bookings'];
				$options['resourceonly'] = true;
				$res_bookings = \eBizIndia\EventRegistration::getEventWiseSummaryList($options);
				if(!$res_bookings){
					$error = 1;
				}else{
					while ($row = $res_bookings->fetch(\PDO::FETCH_ASSOC)) {
						if(isset($records[$row['ev_id']]))
							$records[$row['ev_id']]['tot_tickets'] = $row['tot_tickets'];
							$records[$row['ev_id']]['bookings'] = $row['bookings'];
					}	
					$res_bookings->closeCursor();
					unset($temp_ev_ids);
				}

				$records = array_values($records);
			}
		}

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

$dom_ready_data['event-registrations']=array(
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

$additional_body_template_data = ['can_add'=>$can_add, 'field_meta' => CONST_FIELD_META, 'events' => $events ];

$page_renderer->updateBodyTemplateData($additional_body_template_data);

$page_renderer->updateBaseTemplateData($additional_base_template_data);
$page_renderer->addCss(\scriptProviderFuncs\getCss($page));
$js_files=\scriptProviderFuncs\getJavascripts($page);
$page_renderer->addJavascript($js_files['BSH'],'BEFORE_SLASH_HEAD');
$page_renderer->addJavascript($js_files['BSB'],'BEFORE_SLASH_BODY');
$page_renderer->renderPage();

?>
