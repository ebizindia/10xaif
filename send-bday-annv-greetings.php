<?php
require_once 'inc-oth.php';
if(CONST_SEND_BDAY_ANNV_GREET===false)
	die;

$override_recp = !empty(CONST_EMAIL_OVERRIDE)?explode(',',CONST_EMAIL_OVERRIDE):[];

$extra_data = [
	'recp' => [],
	'from' => CONST_MAIL_SENDERS_EMAIL,
	'from_name' => CONST_MAIL_SENDERS_NAME,
	'cc' => [],
];	
$email_vars = ['{{FNAME}}'];
$greetings = [
	'bday' => [
		'status' => 1, // 0 to disable
		'sub' => 'Happy Birthday, {{FNAME}}!',
		'htmlmsg' =>"<p><strong>Happy Birthday, {{FNAME}}!</strong></p> <p>Wishing you a day filled with joy, laughter, and warm memories.</p> <p>We wish you a ton of success in all you do! 💐🎂👍🏻</p> <p>Team Nopany Alumni Association</p>",
		'textmsg' =>"Happy Birthday, {{FNAME}}!\n\nWishing you a day filled with joy, laughter, and warm memories.\n\nWe wish you a ton of success in all you do! 💐🎂👍🏻\n\nTeam Nopany Alumni Association",
	],
	'annv' => [
		'status' => 1, // 0 to disable
		'sub' => 'Happy Anniversary, {{FNAME}}',
		'htmlmsg' =>"<p><strong>Happy Anniversary, {{FNAME}}!</strong></p> <p>Warmest wishes from your Nopany Alumni family!</p> <p>May your special day overflow with joy, laughter, and cherished memories you create together. We wish you continued love and happiness as you navigate life's journey as a couple. Here's to many more years of love and laughter! 💐🎂👍🏻</p> <p>Team Nopany Alumni Association</p>",
		'textmsg' =>"Happy Anniversary, {{FNAME}}!\n\nWarmest wishes from your Nopany Alumni family!\n\nMay your special day overflow with joy, laughter, and cherished memories you create together. We wish you continued love and happiness as you navigate life's journey as a couple. Here's to many more years of love and laughter! 💐🎂👍🏻\n\nTeam Nopany Alumni Association",
	],

];

$list_type = '';
if($greetings['bday']['status'] === 1 && $greetings['annv']['status'] === 1 )
	$list_type = 'both';
else if($greetings['bday']['status'] === 1)
	$list_type = 'bday';
else if($greetings['annv']['status'] === 1 )
	$list_type = 'annv';
else
	die;
$today=date('Y-m-d');
$bday_annv_list = \eBizIndia\Member::getBdayAnnvOndate($today, 'y', '', [], $list_type);  /// send the greetings irrespective of the DND status but only to active members
if(empty($bday_annv_list))
	die('No Birthdays and anniversaries today');

$obj = new \eBizIndia\Mailer(true, ['use_default'=>CONST_USE_SERVERS_DEFAULT_SMTP]); // Will use server's default email settings
$obj->CharSet = 'UTF-8'; // required for including unicode emojis in the emil content
$obj->resetOverrideEmails(); 
$obj->setOverrideEmail($override_recp);

if(ENABLE_WHATSAPP_MSG == 1){
	$aisensy = new \eBizIndia\AISensy(AISENSY_API_KEY, 2);
	$aisensy->resetOverrideRecipient(); 
	$aisensy->setOverrideRecipient(CONST_WA_OVERRIDE);
}

foreach ($bday_annv_list as $mem) {
	$send_bday = $send_annv = false;
	if($mem['type']==='both'){
		$send_bday = $send_annv = true;
	}else if($mem['type']==='bday'){
		$send_bday = true;
	}else if($mem['type']==='annv'){
		$send_annv = true;
	}

	$extra_data['recp'] = [$mem['email']];
	$extra_data['cc'] = [];
	
	$email_vars_data = [$mem['fname']];
	if($send_bday){
		$gd = $greetings['bday'];
		$email_data = [];
		$email_data['subject'] = CONST_MAIL_SUBJECT_PREFIX.' '.str_replace($email_vars, $email_vars_data, $gd['sub']);
		$email_data['html_message'] = str_replace($email_vars, $email_vars_data, $gd['htmlmsg']);
		$email_data['text_message'] = str_replace($email_vars, $email_vars_data, $gd['textmsg']);
		echo $mem['email'].':  ';
		var_dump($obj->sendEmail($email_data, $extra_data));
		echo $mem['mobile'].':  ';
		if(ENABLE_WHATSAPP_MSG == 1)
			var_dump($aisensy->sendCampaignMessage(AISENSY_BDAY_CAMPAIGN, $mem['mobile'], [$mem['fname']]));
	}

	if($send_annv){
		$gd = $greetings['annv'];
		$email_data = [];
		$email_data['subject'] = CONST_MAIL_SUBJECT_PREFIX.' '.str_replace($email_vars, $email_vars_data, $gd['sub']);
		$email_data['html_message'] = str_replace($email_vars, $email_vars_data, $gd['htmlmsg']);
		$email_data['text_message'] = str_replace($email_vars, $email_vars_data, $gd['textmsg']);
		echo $mem['email'].':  ';
		var_dump($obj->sendEmail($email_data, $extra_data));
		echo $mem['mobile'].':  ';
		if(ENABLE_WHATSAPP_MSG == 1)
			var_dump($aisensy->sendCampaignMessage(AISENSY_ANN_CAMPAIGN, $mem['mobile'], [$mem['fname']]));
	}
	echo '<br>';

}