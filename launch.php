<?php
exit;
require_once 'inc-oth.php';
$img_uri = CONST_APP_ASSETS_ROOT_URI.'/images/email/';
$allowedmobile = [
    
    
];

$override_emailids = ['nishant@ebizindia.com']; //,'arun@ebizindia.com'];

$obj = new \eBizIndia\Mailer(true, ['use_default'=>CONST_USE_SERVERS_DEFAULT_SMTP]);
$obj->CharSet = 'UTF-8';
$obj->Encoding = 'base64'; // default for phpmailer is 8bit which can be problematic for utf-8 data so setting base64
$obj->resetOverrideEmails(); 
// $obj->setOverrideEmail($override_emailids); // uncomment to apply the email override 

$options = [];
$options['filters'] = [
    ['field'=>'active', 'type'=>'EQUAL', 'value'=>'y'],
    ['field'=>'email', 'type'=>'NOT_IN', 'value'=> [ 'shubham@chemtexlimited.com', 'Shikha@timesfurnishing.com', 'Kanoianushree@gmail.com', 'nupur.chokhani@gmail.com', 'Nikhil@vyana.in', 'eteebajaj@gmail.com', 'suchi.jj@gmail.com' ]],
    // ['field'=>'id', 'type'=>'EQUAL', 'value'=>2],
];
$options['fieldstofetch'] = ['id', 'fname', 'mobile', 'email', 'user_acnt_id'];
// $options['order_by'] = [
//     ['field'=>'name', 'type'=>'ASC'],
//     ['field'=>'id', 'type'=>'ASC'],
// ];
$page = (int)$_GET['p'];
if($page>0){
    $options['page'] = $page;
    $options['recs_per_page'] = 100;
}

$member_list = \eBizIndia\Member::getList($options);

// \eBizIndia\_p($member_list);
// exit;

$usercls=new \eBizIndia\User();
 
$extra_data['from']  = CONST_MAIL_SENDERS_EMAIL;
$extra_data['from_name'] = CONST_MAIL_SENDERS_NAME;
$cnt=0;
foreach ($member_list as $member) {
    $cnt++;
    $name = $member['fname'];
    $email = $member['email'];
    $id = $member['user_acnt_id'];
    $mobile = $member['mobile'];
    echo '<br>',$cnt,' | ',$mobile,' | ',$name,' | ',$email;
    if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)){
        echo ' | Invalid or missing email ID<br>';
        continue;
    }
    
    // if($id==2){
    //     echo ' | Skipped<br>';
    //     continue;
    // }

    $extra_data['recp'] = [$email];
    $extra_data['cc'] = [];
    
    $password = \eBizIndia\generatePassword();
    // $email_content = "<p>Dear ".\eBizIndia\_esc($name,true).",</p><p>We are thrilled to announce the launch of our secure, online members' directory for the Young Indians, Kolkata.</p><p>This comprehensive directory includes the details of all our members. We are confident that this will significantly enhance your ability to connect with fellow members and foster collaboration opportunities within the community. Please update your details by logging in so that your friends can find you.</p><p>To access the directory, please follow these steps:</p><p>Visit <a href='".CONST_APP_ABSURL."'>".CONST_APP_ABSURL."</a></p><p>Enter your email ID: ".\eBizIndia\_esc($email,true)."</p><p>Use this randomly generated password: ".\eBizIndia\_esc($password,true)."</p><p>For your convenience, please check the \"Remember Me\" option to avoid entering your login details each time, provided you are using a trusted computer or mobile device.</p> <p>Click on the Login button.</p><p>We hope you find this tool valuable!</p><p>Best regards,<br>".\eBizIndia\_esc(CONST_MAIL_SENDERS_NAME, true)."</p>";
    $email_content = '<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Welcome!</title>
</head>
<body>
<table style="width:100%; max-width:600px; border-collapse:collapse;font-family:Arial, Helvetica, sans-serif;font-size:14px;">
    <tr>
        <td style="padding:5px 5px 25px;">Dear '.\eBizIndia\_esc($name,true).',</td>
    </tr>
    <tr>
        <td style="padding:5px 5px 15px;">We are thrilled to launch our Young Indians Offical Directory.</td>
    </tr>
    <tr>
        <td style="padding:5px 5px 15px;">
            <table>
                <tr>
                    <td style="padding:2px 0 2px; vertical-align:middle;"><img src="'.$img_uri.'green-tick.png" style="margin-right:5px;float:left;">Secure</td>
                </tr>
                <tr>
                    <td style="padding:2px 0 2px; vertical-align:middle;"><img src="'.$img_uri.'green-tick.png" style="margin-right:5px;float:left;">Comprehensive and Valuable</td>
                </tr>
                <tr>
                    <td style="padding:2px 0 2px; vertical-align:middle;"><img src="'.$img_uri.'green-tick.png" style="margin-right:5px;float:left;">Tech-enabled</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td style="padding:5px 0px 15px;">We are confident that this will significantly enhance your ability to connect with fellow members and foster collaboration opportunities within the community.</td>
    </tr>
    <tr>
        <td style="padding:5px 0px 15px;">Please update your details by logging in so that your friends can find you.</td>
    </tr>
    <tr>
        <td style="padding:5px 0px 15px;">To access the directory, please follow these steps:</td>
    </tr>
    <tr>
        <td style="padding:5px 0 10px;">
            <table>
                <tr>
                    <td style="padding:2px 0 2px;">
                        1) Visit <a href="'.CONST_APP_ABSURL.'">'.\eBizIndia\_esc(CONST_APP_ABSURL, true).'</a>
                    </td>
                <tr>
                </tr>
                    <td style="padding:2px 0 2px;">
                        2) Enter your email ID: '.\eBizIndia\_esc($email,true).'
                    </td>
                <tr>
                </tr>   
                    <td style="padding:2px 0 2px;">
                        3) Use this randomly generated password: '.\eBizIndia\_esc($password,true).'
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td style="padding:5px 5px 15px;">For your convenience, please check the &quot;Remember Me&quot; option to avoid entering your login details each time, provided you are using a trusted computer or mobile device.</td>
    </tr>
    <tr>
        <td style="padding:5px 5px 15px;">Click on the Login button to get in.</td>
    </tr>
    <tr>
        <td style="padding:5px 5px 15px;">See you on-board!</td>
    </tr>
    <tr>
        <td style="padding:5px;">Best, <br>'.\eBizIndia\_esc(CONST_MAIL_SENDERS_NAME, true).'</td>
    </tr>
</table>
</body>
</html>';

    $login_account_data = ['username' => $email, 'password' =>password_hash($password, PASSWORD_BCRYPT)];
    $login_res = $usercls->saveUserDetails($login_account_data, $id);

    if ($login_res === true) {
        $email_data['subject'] = CONST_MAIL_SUBJECT_PREFIX." Welcome to Young Indians Kolkata Members' Directory";
        $email_data['html_message'] = $email_content;
        if($obj->sendEmail($email_data, $extra_data)){
            echo ' | email sent<br>';
        }else{
            echo ' | email sending failed<br>';
        }
        usleep(50000); // 50 milisec
        
        // var_dump($aisensy->sendCampaignMessage('nr_launch_api', $mobile, [$name, CONST_APP_ABSURL,$mobile,$password]));
       
    }else{
        echo ' | Username and password update failed<br>';
    }

}

?>
