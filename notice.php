<?php
$page='noticetomem';
require_once 'inc.php';
$template_type='';
$page_title = 'Send Notice To Members'.CONST_TITLE_AFX;
$page_description = 'One can send a text notice to the members.';
$body_template_file = CONST_THEMES_TEMPLATE_INCLUDE_PATH . 'notice.tpl';
$body_template_data = array();
$page_renderer->registerBodyTemplate($body_template_file,$body_template_data);

$options = [];
$options['filters'] = [];
$options['filters'][] = ['field'=>'active', 'type'=>'EQUAL', 'value'=>'y'];
$groups = \eBizIndia\MemberGroup::getList($options);

$options = [];
$options['filters'] = [];
$options['fieldstofetch']= ['id', 'name', 'email' ];
$options['filters'][] = ['field'=>'active', 'type'=>'EQUAL', 'value'=>'y'];
$members = \eBizIndia\Member::getList($options);

if(filter_has_var(INPUT_POST,'mode') && $_POST['mode']=='sendnotice'){
	$result=array('error_code'=>0,'message'=>'Sending blocked. Development under process...', 'other_data'=>[]);
	$file_upload_errors = [
	    0 => 'There is no error, the file uploaded with success',
	    1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
	    2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
	    3 => 'The uploaded file was only partially uploaded',
	    4 => 'No file was uploaded',
	    6 => 'Missing a temporary folder',
	    7 => 'Failed to write file to disk.',
	    8 => 'A PHP extension stopped the file upload.',
	];
	$rep_vars=['name', 'fname', 'lname', 'email', 'password', 'membership_no', 'batch_no'];
	$test_mode = $_POST['msg_test_notice'] == 1;
	try {
		$groups[] = ['id'=>'Others','grp'=>'Others']; // For handling cases where the members do not have any membership type assigned
		$attachments = [];
		/* 
		if(empty($_POST['groups'])){
			$result['error_code']=2;
			$result['message']="Please select one or more groups to send the notice to.";
			$result['error_fields'][] = 'input[id^=add_form_field_groups_]:eq(0)';
			throw new Exception("Error Processing Request", 1);
			
		}else if(!empty(array_diff($_POST['groups'], array_column($groups, 'id') )) ){
			$result['error_code']=2;
			$result['message']="Please select one or more valid groups to send the notice to.";
			$result['error_fields'][] = 'input[id^=add_form_field_groups_]:eq(0)';
			throw new Exception("Error Processing Request", 1); */

	   if(empty($_POST['members'])){
			$result['error_code']=2;
			$result['message']="Please select one or more member to send the notice to.";
			$result['error_fields'][] = 'input[id^=add_form_field_member_]:eq(0)';
			throw new Exception("Error Processing Request", 1);
			
		}else if(!empty(array_diff($_POST['members'], array_column($members, 'id') )) ){
			$result['error_code']=2;
			$result['message']="Please select one or more valid member to send the notice to.";
			$result['error_fields'][] = 'input[id^=add_form_field_member_]:eq(0)';
			throw new Exception("Error Processing Request", 1);
			
		}else if($_FILES['attachment']['error']>0 && $_FILES['attachment']['error']!=4){
			$result['error_code']=2;
			$result['message'] = 'Sending of the notice failed. '.$file_upload_errors[$_FILES['attachment']['error']];
			$result['error_fields'][] = '#add_form_field_attachment';
			throw new Exception("Error Processing Request", 1);
			
		}else if($_FILES['attachment']['error']==0){
			$file_ext = strtolower(pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION));
			if(empty($file_ext) || !in_array($file_ext, CONST_FIELD_META['notice_to_mem']['attachment_types'])){
				$result['error_code']=2;
				$result['message']="The selected attachment is not among one of the allowed file types.";
				$result['error_fields'][] = '#add_form_field_attachment';
				throw new Exception("Error Processing Request", 1);
				
			}else if(!in_array($_FILES['attachment']['type'], CONST_MIME_TYPES[$file_ext]??[] )){
				$result['error_code']=2;
				$result['message']="The selected attachment is not a valid file type.";
				$result['error_fields'][] = '#add_form_field_attachment';
				throw new Exception("Error Processing Request", 1);
				
			}

			$attachments=[
				[
					'attachment_filenamepath'=>$_FILES['attachment']['tmp_name'],
					'attachment_name' => $_FILES['attachment']['name'],
					'encoding' => 'base64',
					'contenttype' => $_FILES['attachment']['type']
				]
			];
		}
		
		if(ENABLE_WHATSAPP_MSG == 1 && !empty($_POST['send_via_wa']) ){
			$rep_vals=explode("\n", trim(\eBizIndia\striptags_deep($_POST['msg_replacements'])));
			//print_r($rep_vals);
			foreach($rep_vals as &$v){
				$val=strtolower(trim($v));
				if($val[0]=='$'){
					if(!in_array(substr($val, 1), $rep_vars)){
						$result['error_code']=3;
						$result['message']="Replacement variable $val does not match.";
						$result['error_fields'][] = '#add_form_field_msgreplacements';
						throw new Exception("Error Processing Request", 1);
					}
					$v=$val;
				}
			}
			$aisensy = new \eBizIndia\AISensy(AISENSY_API_KEY, 2);
			//print_r($rep_vals);die();
		}

		$options = [];
		if($test_mode)
			$options['filters'][] = ['field'=>'id', 'type'=>'EQUAL', 'value'=>$loggedindata[0]['profile_details']['id']];
		else{
			$options['filters'] = [
				['field'=>'id', 'type'=>'IN', 'value'=>$_POST['members']],
			];	
		}
		$options['filters'][] = ['field'=>'active', 'type'=>'EQUAL', 'value'=>'y'];
		$options['fieldstofetch'] = ['id', 'name', 'email', 'membership_no', 'batch_no', 'password', 'fname', 'lname', 'mobile'];

		$recp_users = \eBizIndia\Member::getList($options);

		
		if(empty($recp_users)){
			$result['error_code'] = 4;
			$result['message'] = "The notice could not be sent as there are no active members under the selected groups.";
			throw new Exception("Error Processing Request", 1);
		}

		$ip = \eBizIndia\getRemoteIP();
		$feedback_msg = trim($_POST['msg_body']);
		$feedback_sub = trim(\eBizIndia\striptags_deep($_POST['msg_sub']));
		$extra_data['from'] = CONST_MAIL_SENDERS_EMAIL;
		$extra_data['from_name'] = CONST_MAIL_SENDERS_NAME;
		$extra_data['reply_to'] = CONST_MAIL_REPLYTO_EMAIL; //explode(",",trim($loggedindata[0]['profile_details']['email']));
		$extra_data['cc'] = $feedback_data = [];
		$feedback_data['subject'] =  CONST_MAIL_SUBJECT_PREFIX.' '.$feedback_sub;
		$feedback_data['html_message'] = $feedback_msg;
		$feedback_data['attachments'] = $attachments;

		$override_recp = !empty(CONST_EMAIL_OVERRIDE)?explode(',',CONST_EMAIL_OVERRIDE):[];

		$result['error_code'] = 0;
		$result['message'] = "The notice has been sent successfully.";
		$feedback_email = new \eBizIndia\Mailer(true, ['use_default'=>CONST_USE_SERVERS_DEFAULT_SMTP]); // Will use server's default email settings
		$msg_encoding = mb_detect_encoding($feedback_msg);
		$sub_encoding = mb_detect_encoding($feedback_sub);
		if(strcasecmp($msg_encoding, 'UTF-8')===0 || strcasecmp($sub_encoding, 'UTF-8')===0){
			$feedback_email->CharSet = 'UTF-8';
			$feedback_email->Encoding = 'base64'; // default for phpmailer is 8bit which can be problematic for utf-8 data so setting base64
		}
		$mails_sent = 0;
		foreach ($recp_users as $recp) {
			if(!empty($override_recp))
				$extra_data['recp'] = $override_recp;
			else{
				$extra_data['recp'] = [$recp['email']];
			}
			$feedback_email->resetOverrideEmails(); // becuase the overide email is being set in the recp var above
			$placeholders = [];
			foreach (CONST_NOTICE_EMAIL_VARS as $key => $fld) {
				$placeholders[$key] = $recp[$fld]??'';
			}
			$feedback_email->setPlaceHoldersAndData($placeholders);
			
			if($feedback_email->sendEmail($feedback_data, $extra_data))
				$mails_sent+=1;

			if(ENABLE_WHATSAPP_MSG == 1 && !empty($_POST['send_via_wa'])){
				$template_params=[];
				foreach($rep_vals as &$v){
					if($v[0]=='$'){
						$template_params[] = $recp[substr($v, 1)];
					}else{
						$template_params[] = $v;
					}		
				}
				//$aisensy->resetOverrideRecipient(); 
				//$aisensy->setOverrideRecipient('8013132921');
				// echo 'send text to '.$recp['mobile'];
				$res=$aisensy->sendCampaignMessage($_POST['msg_campaign'], $recp['mobile'], $template_params);
			}
			if($test_mode){
				//print_r($res);
				//die('aaa');
				break;
			}


		}
		if($mails_sent>0){
			$result['error_code'] = 0;
			$result['message'] = "The notice has been sent successfully.";

		}else{
			throw new Exception("Error Processing Request", 1);
			
		}

	} catch (\Exception $e) {
		$result['e'] = $e->getMessage();
		if($result['error_code']==0){
			$result['error_code'] = 1;
			$result['message'] = "The feedback could not be submitted due to error in sending emails.";

		}
	}
	$_SESSION['create_rec_result']=$result;
	header("Location:?");
	exit;

}elseif(isset($_SESSION['create_rec_result']) && is_array($_SESSION['create_rec_result'])){
	header("Content-Type: text/html; charset=UTF-8");
	echo "<script type='text/javascript' >\n";
	echo "parent.noticefuncs.sendNoticeResp(".json_encode($_SESSION['create_rec_result']).");\n";
	echo "</script>";
	unset($_SESSION['create_rec_result']);
	exit;

}

$dom_ready_data['noticetomem']=array(
								'attachment_types' => CONST_FIELD_META['notice_to_mem']['attachment_types'],
							);

$additional_base_template_data = array(
										'page_title' => $page_title,
										'page_description' => $page_description,
										'template_type'=>$template_type,
										'dom_ready_code'=>\scriptProviderFuncs\getDomReadyJsCode($page,$dom_ready_data),
										'other_js_code'=>$jscode,
										'module_name' => $page
									);

$additional_body_template_data = [
	'attachment_types' => CONST_FIELD_META['notice_to_mem']['attachment_types'],
	'groups' => $groups,
	'members' =>$members
];
$page_renderer->updateBodyTemplateData($additional_body_template_data);
$page_renderer->updateBaseTemplateData($additional_base_template_data);
$page_renderer->addCss(\scriptProviderFuncs\getCss($page));
$js_files=\scriptProviderFuncs\getJavascripts($page);
$page_renderer->addJavascript($js_files['BSH'],'BEFORE_SLASH_HEAD');
$page_renderer->addJavascript($js_files['BSB'],'BEFORE_SLASH_BODY');
$page_renderer->renderPage();

?>
