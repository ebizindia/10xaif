<?php
$page='feedbacktoadmin';
require_once 'inc.php';

$template_type='';
$page_title = 'Send Feedback To Admin'.CONST_TITLE_AFX;
$page_description = 'One can send a text feedback message to the Admin.';
$body_template_file = CONST_THEMES_TEMPLATE_INCLUDE_PATH . 'feedback.tpl';
$body_template_data = array();
$page_renderer->registerBodyTemplate($body_template_file,$body_template_data);

if(filter_has_var(INPUT_POST,'mode') && $_POST['mode']=='sendfeedback'){
	$result=array('error_code'=>0,'message'=>[], 'other_data'=>[]);

	try {
		$options = [];
		$options['filters'][] = ['field'=>'role', 'type'=>'EQUAL', 'value'=>'ADMIN'];
		$options['filters'][] = ['field'=>'profile_type', 'type'=>'EQUAL', 'value'=>'member'];
		$options['fieldstofetch'] = ['id','username', 'profile_id'];
		$admin_users = $usercls->getList($options);
		
		if(empty($admin_users)){
			$result['error_code'] = 3;
			$result['message'] = "The feedback could not be submitted as there are no active recipients.";
			throw new Exception("Error Processing Request", 1);
		}
		$options = [];
		$options['filters'] = [
			['field'=>'id', 'type'=>'IN', 'value'=>array_column($admin_users, 'profile_id')],
			['field'=>'active', 'type'=>'EQUAL', 'value'=>'y']
		];
		$options['fieldstofetch'] = ['id', 'name', 'email'];
		$recp_users = \eBizIndia\Member::getList($options);
		if(empty($recp_users)){
			$result['error_code'] = 4;
			$result['message'] = "The feedback could not be submitted as there are no active recps.";
			throw new Exception("Error Processing Request", 1);
		}


		$ip = \eBizIndia\getRemoteIP();
		$feedback_msg = trim(\eBizIndia\striptags_deep($_POST['msg_body']));
		$extra_data['from'] = CONST_MAIL_SENDERS_EMAIL;
		$extra_data['from_name'] = CONST_MAIL_SENDERS_NAME;
		$extra_data['reply_to'] = explode(",",trim($loggedindata[0]['profile_details']['email']));
		$extra_data['cc'] = [];

		if(!empty(CONST_EMAIL_OVERRIDE))
			$extra_data['recp'] = explode(',',CONST_EMAIL_OVERRIDE);
		else{
			$extra_data['recp'] = array_column($recp_users, 'email');
		}

		$feedback_data['subject'] =  CONST_MAIL_SUBJECT_PREFIX." Feedback from ".trim($loggedindata[0]['profile_details']['name']);
		$feedback_data['html_message'] = "<p>Hello,</p>";
		$feedback_data['html_message'] .= "<p>The following feedback has been submitted by ".\eBizIndia\_esc($loggedindata[0]['profile_details']['name'], true)." .</p>"; 
		$feedback_data['html_message'] .= "<p><table  border='1' style='border-collapse:collapse;min-width:400px;max-width:700px;' cellpadding='5'><tr><td valign='top'>".nl2br(\eBizIndia\_esc($feedback_msg, true))."</td></tr>";
		$feedback_data['html_message'] .= "<tr><td  valign='top' ><b>Date & Time:</b>&nbsp;".date("d-m-Y h:i A",time())."</td></tr>" ;
		$feedback_data['html_message'] .= "<tr><td valign='top' ><b>IP Address:</b>&nbsp;<a href='http://whois.domaintools.com/".$ip."'  rel='noopener'  >".\eBizIndia\_esc($ip, true)."</a></td></tr></table></p>" ;
		$feedback_data['html_message'] .="<p>Regards,<br>".CONST_MAIL_SENDERS_NAME."</p>";
		$feedback_email = new \eBizIndia\Mailer(true, ['use_default'=>CONST_USE_SERVERS_DEFAULT_SMTP]); // Will use server's default email settings
		$msg_encoding = mb_detect_encoding($feedback_msg);
		$sub_encoding = mb_detect_encoding($feedback_data['subject']);
		if(strcasecmp($msg_encoding, 'UTF-8')===0 || strcasecmp($sub_encoding, 'UTF-8')===0){
			$feedback_email->CharSet = 'UTF-8';
			$feedback_email->Encoding = 'base64'; // default for phpmailer is 8bit which can be problematic for utf-8 data so setting base64
		}
		$feedback_email->resetOverrideEmails(); // becuase the overide email is being set in the recp var above
		$feedback_email->sendEmail($feedback_data, $extra_data);
		$result['error_code'] = 0;
		$result['message'] = "Your feedback has been successfully submitted.";
		
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
	echo "parent.feedbackfuncs.sendFeedbackResp(".json_encode($_SESSION['create_rec_result']).");\n";
	echo "</script>";
	unset($_SESSION['create_rec_result']);
	exit;

}

$dom_ready_data['feedbacktoadmin']=array(
								'feedback_max_chars' => CONST_FIELD_META['feedback_to_admin']['len'],
							);

$additional_base_template_data = array(
										'page_title' => $page_title,
										'page_description' => $page_description,
										'template_type'=>$template_type,
										'dom_ready_code'=>\scriptProviderFuncs\getDomReadyJsCode($page,$dom_ready_data),
										'other_js_code'=>$jscode,
										'module_name' => $page
									);


$page_renderer->updateBaseTemplateData($additional_base_template_data);
$page_renderer->addCss(\scriptProviderFuncs\getCss($page));
$js_files=\scriptProviderFuncs\getJavascripts($page);
$page_renderer->addJavascript($js_files['BSH'],'BEFORE_SLASH_HEAD');
$page_renderer->addJavascript($js_files['BSB'],'BEFORE_SLASH_BODY');
$page_renderer->renderPage();

?>
