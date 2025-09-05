<?php
namespace eBizIndia;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


class Mailer extends PHPMailer {

	private $override_email;
	private $allow_bck_bcc;
	private $replacement_vars;
	private $place_holder_data;
	public function __construct($throw_exception = TRUE, $smtp_info = NULL){
		$this->override_email = [];
		$this->allow_bck_bcc = true;
		parent::__construct($throw_exception);

		if(is_array($smtp_info)){
			$this->isSMTP();
			$this->SMTPAuth = TRUE;

			if($smtp_info['use_default']){
				if(CONST_SMTP_SECURE == 1)
					$this->SMTPSecure='ssl';
				else{
					$this->SMTPSecure=false;
					$this->SMTPAutoTLS = false;
				}
				$this->Host = CONST_SMTP_HOST;
				$this->Port = CONST_SMTP_PORT;
				$this->Username = CONST_SMTP_USER;
				$this->Password = CONST_SMTP_PASSWORD;
			}else if($smtp_info['host'] != '' && $smtp_info['port'] != '' && $smtp_info['username'] != '' && $smtp_info['password'] != ''){
				if($smtp_info['smtp_secure'] == 1){
					$this->SMTPSecure = 'ssl';
				}
				else{
					$this->SMTPSecure=false;
					$this->SMTPAutoTLS = false;
				}
				$this->Host = $smtp_info['host'];
				$this->Port = $smtp_info['port'];
				$this->Username = $smtp_info['username'];
				$this->Password = $smtp_info['password'];
			}else{
				$this->isMail();
			}
		}else{
			$this->isMail();
		}
		
	}

	function __set($name, $value){
		if($name == 'allow_bck_bcc'){
			$value = !!$value;
			$this->{$name} = $value;
		}
	}


	function setOverrideEmail($email_address){
		if(!empty($email_address)){
			if(!is_array($email_address))
				$this->override_email[] = $email_address;
			else
				$this->override_email = array_merge($this->override_email,$email_address);

			$this->override_email = array_values(array_unique($this->override_email));
		}else{
			$this->override_email = [];
		}

	}

	function resetOverrideEmails(){

		$this->override_email = [];

	}

	public function clearAllRecipients(){
		parent::clearAllRecipients();
		// if($this->allow_bck_bcc && !empty(CONST_EMAIL_BACKUP_BCC)){
		// 	if(!is_array(CONST_EMAIL_BACKUP_BCC))
		// 		$this->addBCC(CONST_EMAIL_BACKUP_BCC);
		// 	else{
		// 		foreach (CONST_EMAIL_BACKUP_BCC as $recp_one) {
		// 			$this->addBCC($recp_one);
		// 		}
		// 	}
		// }
	}


	public function setPlaceHoldersAndData($vars_n_data){
		$this->replacement_vars = $vars_n_data;
	}

	public function resetPlaceholderVars(){
		$this->replacement_vars = [];
	}

	private function replacePlaceholders($str){
		
		foreach ($this->replacement_vars as $key => $val) {
			$key = str_replace(['$','+','?','*','^','(',')','[',']','|'], ['\$','\+','\?','\*','\^','\(','\)','\[','\]','\|'], $key);
			$str = preg_replace("/$key/", $val, $str);
		}
		return $str;
	}

	public function sendEmail($message_data, $other_data){

		$email_subject = $this->replacePlaceholders($message_data['subject']);
		$email_message_html = $this->replacePlaceholders($message_data['html_message']);
		$email_message_text = $this->replacePlaceholders((empty($message_data['text_message']))?'To view the message, please use an HTML compatible email viewer!':$message_data['text_message']);
		
		try{
			$this->clearAllRecipients();
			$this->From = $other_data['from'];
			$this->FromName = $other_data['from_name'];
			if(empty($this->override_email)){
				foreach ($other_data['recp'] as $recp_one) {
					$this->AddAddress($recp_one);
				}
				if(!empty($other_data['cc'])){
					foreach ($other_data['cc'] as $cc) {
						if(is_array($cc))
							$this->AddCC($cc['email'], $cc['name']??'');
						else
							$this->AddCC($cc);
					}
				}
				if(!empty($other_data['bcc'])){
					foreach ($other_data['bcc'] as $bcc) {
						if(is_array($bcc))
							$this->addBCC($bcc['email'], $bcc['name']??'');
						else
							$this->addBCC($bcc);
					}
				}
			}else{
				foreach ($this->override_email as $recp_one) {
					$this->AddAddress($recp_one);
				}
			}

			if(!empty($other_data['reply_to'])){
				if(!is_array($other_data['reply_to'])){
					$other_data['reply_to'] = explode(',',$other_data['reply_to']);	
				}	
				foreach ($other_data['reply_to'] as $replyto_one) {
					$this->addReplyTo($replyto_one);
				}
				
			}
			$this->Subject = $email_subject;
			$this->Body = $email_message_html;
			$this->IsHTML(TRUE);
			$this->AltBody = $email_message_text;
			$attch_cnt = count($message_data['attachments']??[]);
			for($i=0;$i<$attch_cnt;$i++)
				$this->AddAttachment($message_data['attachments'][$i]['attachment_filenamepath'],$message_data['attachments'][$i]['attachment_name'],$message_data['attachments'][$i]['encoding'],$message_data['attachments'][$i]['contenttype']);      // attachment
			
			$embed_cnt = count($message_data['inlineimages']??[]);
			for($i=0;$i<$embed_cnt;$i++)
				$this->AddEmbeddedImage($message_data['inlineimages'][$i]['image_filenamepath'],$message_data['inlineimages'][$i]['image_identifier'],$message_data['inlineimages'][$i]['image_name'],'base64',$message_data['inlineimages'][$i]['image_type']??'image/jpeg');      // embed

			$this->send();
		}catch(Exception $ex){
			ErrorHandler::logError([], $ex);
			return false;
		}
		return true;
	}

}

?>