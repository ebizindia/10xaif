<?php
$page='members';
require_once 'inc.php';
$template_type='';
$page_title = 'Members'.CONST_TITLE_AFX;
$page_description = 'One can manage users (member) of the system.';
$body_template_file = CONST_THEMES_TEMPLATE_INCLUDE_PATH . 'users.tpl';
$body_template_data = array();
$page_renderer->registerBodyTemplate($body_template_file,$body_template_data);
$email_pattern="/^\w+([.']?-*\w+)*@\w+([.-]?\w+)*(\.\w{2,4})+$/i";
$user_date_display_format_for_storage = 'd-m-Y';
$default_pswd = 'xyz123';
$profile_type = 'member';
$exp_dt = date('Y-03-31');
$exp_dt = ((new DateTime($exp_dt)) >  (new DateTime(date('Y-m-d'))) )?$exp_dt:date('Y-m-d', strtotime('+1 YEAR', strtotime($exp_dt)));
$self_edit = $others_edit = $can_add = false; // $can_edit = false;
$_cu_role = $loggedindata[0]['profile_details']['assigned_roles'][0]['role'];
// if($loggedindata[0]['profile_details']['assigned_roles'][0]['role']=='ADMIN')
// 	$can_edit = true;

if(CONST_MEM_PROF_EDIT_RESTC[$_cu_role]['self'][0]===true)
	$self_edit = true;
if(CONST_MEM_PROF_EDIT_RESTC[$_cu_role]['others'][0]===true)
	$can_add = $others_edit = true;

$member_fields = [
	'title'=>'', 
	'fname'=>'', 
	'mname'=>'', 
	'lname'=>'', 
	'email'=>'', 
	'secondary_email'=>'', 
	'mobile'=>'', 
	'mobile2'=>'', 
	'edu_qual'=>'', 
	'linkedin_accnt'=>'', 
	'x_accnt'=>'', 
	'fb_accnt'=>'',
	'website'=>'',
	'gender'=>'', 
	'blood_grp'=>'', 
	// 'batch_no'=>'', 
	'dob'=>'', 
	'annv'=>'', 
	'spouse_name'=>'',
	'spouse_gender' => '',
	'spouse_dob' => '',
	'spouse_whatapp'=> '',
	'spouse_email' => '',
	'spouse_profession' => '',
	'spouse_children' => '',
	// 'marital_status'=>'', 
	'residence_city'=>'', 
	'residence_state'=>'', 
	'residence_country'=>'', 
	'residence_pin'=>'', 
	'residence_addrline1'=>'', 
	'residence_addrline2'=>'', 
	'residence_addrline3'=>'',
	'residence_phone'=>'',
	'residence_fax'=>'',
	'work_type'=>'', 
	'work_ind'=>'', 
	'work_company'=>'', 
	'designation'=>'', 
	'work_city'=>'', 
	'work_state'=>'', 
	'work_country'=>'', 
	'work_pin'=>'', 
	'work_addrline1'=>'', 
	'work_addrline2'=>'', 
	'work_addrline3'=>'',
	'work_phone'=>'',
	'work_phoneepabx'=>'',
	'work_fax'=>'',
	'work_secretary_name'=>'',
	'work_secretary_mobile'=>'',
	'work_secretary_email'=>'',
	'membership_no'=>'',
	'groups'=>'',
	'desig_in_assoc'=>'',
	'password'=>'',
	'role'=>'',
	'status'=>'',
	'dnd'=>'',
	// 'hashtags'=>'',
	'remarks'=>'',
	'sector'=>'',

	'membership_fee' => '',
	'payment_status' => '',
	'payment_mode' => '',
	'payment_txn_ref' => '',
	'payment_instrument_type' => '',
	'payment_instrument' => '',
	'paid_on' => '',
	'joining_dt' => '',
	// 'exp_dt' => '',
	 
];

if(filter_has_var(INPUT_POST,'mode') && $_POST['mode']=='createUser'){
	$result=array('error_code'=>0,'message'=>[], 'elemid'=>array(), 'other_data'=>['new_roles'=>[]]);
	$result['other_data']['post'] = $_POST;
	// $_SESSION['create_user_result'] = $result;
	// header("Location:?");
	// exit;
	
	if($can_add===false){
		$result['error_code']=403;
		$result['message']="Sorry, you are not authorised to perfom this action.";
	}else{

		$data=array();
		$data = \eBizIndia\trim_deep(\eBizIndia\striptags_deep(array_intersect_key($_POST, $member_fields)));
		if(isset($data['membership_no']))
			$data['membership_no'] = strtoupper($data['membership_no']);
		$data['dateDisplayFormat'] = $user_date_display_format_for_storage; 
		if(!empty($data['sector']))
			$data['sector'] = (array)$data['sector'];
		
		$other_data['field_meta'] = CONST_FIELD_META;
		$other_data['roles'] = array_column(\eBizIndia\enums\Role::cases(), 'value');
		$other_data['gender'] = array_column(\eBizIndia\enums\Gender::cases(), 'value');
		$other_data['blood_grps'] = array_keys(CONST_BLOOD_GRPS);
		$other_data['profile_pic'] = $_FILES['profile_pic'];
		$member_obj = new \eBizIndia\Member();	
		$validation_res = $member_obj->validate($data, 'add', $other_data);
		if($validation_res['error_code']>0){
			$result = $validation_res;
		} else {
			$username = $data['email'];
			$options=[];
			$options['filters']=[];
			$options['filters'][]=['field'=>'username','type'=>'EQUAL','value'=>$username];
			$res_user=$usercls->getList($options);
			$error_details_to_log = [];
			if($res_user===false){
				$result['error_code']=1; // DB error
				$result['message']="Member could not be added due to server error.";

				$error_details_to_log['mode'] = 'createUser';
				$error_details_to_log['part'] = 'fetch user details for given email address';
				$error_details_to_log['func resp'] = 'boolean false';
				$error_details_to_log['result'] = $result;

			}elseif($res_user===null){
				$result['error_code']=1; // DB error
				$result['message']="Member could not be added due to server error.";

				$error_details_to_log['mode'] = 'createUser';
				$error_details_to_log['part'] = 'fetch user details for given email address';
				$error_details_to_log['func resp'] = 'null';
				$error_details_to_log['result'] = $result;

			}elseif(!empty($res_user)){
				$result['error_code']=1; // DB error
				$result['message']="A member with the email address ".\eBizIndia\_esc($data['email'], true)."  already exists.";

				$error_details_to_log['mode'] = 'createUser';
				$error_details_to_log['part'] = 'fetch user details for given email address';
				$error_details_to_log['func resp'] = 'non empty array';
				$error_details_to_log['result'] = $result;

			}else{
				
				$options=[];
				$options['filters']=[];
				$options['filters'][]=['field'=>'email','type'=>'EQUAL','value'=>$data['email']];
				$res_user=\eBizIndia\Member::getList($options);
				$error_details_to_log = [];
				if($res_user===false){
					$result['error_code']=1; // DB error
					$result['message']="Member could not be added due to server error.";

					$error_details_to_log['mode'] = 'createUser';
					$error_details_to_log['part'] = 'fetch member details for given email address';
					$error_details_to_log['func resp'] = 'boolean false';
					$error_details_to_log['result'] = $result;

				}elseif($res_user===null){
					$result['error_code']=1; // DB error
					$result['message']="Member could not be added due to server error.";

					$error_details_to_log['mode'] = 'createUser';
					$error_details_to_log['part'] = 'fetch member details for given email address';
					$error_details_to_log['func resp'] = 'null';
					$error_details_to_log['result'] = $result;

				}elseif(!empty($res_user)){
					$result['error_code']=1; // DB error
					$result['message']="A member with the email address ".\eBizIndia\_esc($data['email'], true)."  already exists.";

					$error_details_to_log['mode'] = 'createUser';
					$error_details_to_log['part'] = 'fetch member details for given email address';
					$error_details_to_log['func resp'] = 'non empty array';
					$error_details_to_log['result'] = $result;

				}else{

					$created_at = date('Y-m-d H:i:s');
					$ip = \eBizIndia\getRemoteIP();
					$default_pswd = \eBizIndia\generatePassword();
					$login_account_data = [
						'username' => strtolower($data['email']),
						'password' => password_hash(empty($data['password'])?$default_pswd:$data['password'], PASSWORD_BCRYPT),
						'profile_type' => $profile_type,
						'profile_id' => null,
						'status' => $data['status']==='y'?1:0,
						'createdOn' => $created_at,
						'createdBy' => $loggedindata[0]['id'],
						'createdFrom' => $ip,
					];	

					$member_data = array_diff_key($data, ['password'=>'', 'role'=>'', 'status'=>'', 'dateDisplayFormat'=>'', 'groups'=>'', 'sector'=>'' ]);
					
					// $member_data['joining_dt'] = date('Y-m-d');
					// $member_data['membership_no'] = $member_obj->generateMembershipNo();
					$member_data['active'] = $data['status'];
					$member_data['created_at'] = $created_at;
					$member_data['created_by'] = $loggedindata[0]['id'];
					$member_data['created_from'] = $ip;

					// payment info
					if( ($member_data['payment_status']??'')=='Paid'){
						// As of now member addition without any payment details should be allowed in case the payment details will be updated later
						$member_data['paid_on'] .= ' 00:00:00'; // as only date is being submitted but the DB expects a datetime value
						$member_data['amount_paid'] = $member_data['membership_fee']; // for consisstency sake just copying the same amount for manual entry 
						$member_data['membership_fee_gst_rate'] = CONST_MEM_REG_FEE_GST;
					}

					try{
						$conn = \eBizIndia\PDOConn::getInstance();
						$conn->beginTransaction();
						$error_details_to_log['mode'] = 'createUser';
						$error_details_to_log['part'] = 'Create a member record.';
						$rec_id=$member_obj->saveDetails($member_data);
						if($rec_id===false)
							throw new Exception('Error creating a member record.');

						$error_details_to_log['mode'] = 'createUser';
						$error_details_to_log['part'] = 'Create a login account for the member record.';
						$login_account_data['profile_id'] = $rec_id;
						$login_rec_id=$usercls->saveUserDetails($login_account_data);	
						if($login_rec_id===false)
							throw new Exception('Error setting up a login account for a new member record.');

						$error_details_to_log['mode'] = 'createUser';
						$error_details_to_log['part'] = 'Fetch the details for the default role for the member type users.';
						$roles = $usercls->getRoles($data['role'],'',1); // only for member users
						if(empty($roles))
							throw new Exception('Error fetching the role ID for the new member record.');

						$error_details_to_log['mode'] = 'createUser';
						$error_details_to_log['part'] = 'Assign the selected role to the member\'s user account.';
						$roleids_to_assign = [$roles[0]['role_id']];
						if(!$usercls->assignRolesToUsers($login_rec_id,$roleids_to_assign)){
							throw new Exception('User could not be created due to error in saving the member role.');
						}


						$options = [];
						$options['filters'] = [];
						$options['filters'][] = ['field'=>'active', 'type'=>'EQUAL', 'value'=>'y'];
						$groups_tmp = \eBizIndia\MemberGroup::getList($options);
						if(!empty($groups_tmp))
							$groups_tmp = array_map('strtolower', array_column($groups_tmp, 'grp'));
						if(!empty($data['groups'])){
							// look for new groups
							$new_groups = $group_ids_to_assign = [];
							foreach ($data['groups'] as $value) {
								if($value!==''){
									if(preg_match("/\D+/", $value)){
										$new_groups[] = $value;
									}else{
										$group_ids_to_assign[] = (int)$value;
									}

								}
							}

							if(!empty($new_groups)){
								$new_grp_ids = \eBizIndia\MemberGroup::addGroup($new_groups);
								if($new_grp_ids===false)
									throw new Exception('member could not be created due to error in saving the groups.');
								$group_ids_to_assign = array_merge($group_ids_to_assign, $new_grp_ids);
							}

							\eBizIndia\MemberGroup::assignRevokeGroups($rec_id, $group_ids_to_assign);
						}else{
							\eBizIndia\MemberGroup::assignRevokeGroups($rec_id);
						}


						if(!empty($data['sector'])){
							if(!\eBizIndia\MemberSector::assignRevoke($rec_id, $data['sector']))
								throw new Exception('member could not be created due to error in saving the sector.');
						}

						$result['error_code']=0;
						$result['message']='The member <b>'.\eBizIndia\_esc($data['fname']).'</b> has been created.';
						$conn->commit();

						// return latest groups list
						$options = [];
						$options['filters'] = [];
						$options['filters'][] = ['field'=>'active', 'type'=>'EQUAL', 'value'=>'y'];
						$groups = \eBizIndia\MemberGroup::getList($options);
						$result['other_data']['groups'] = $groups;
						$result['other_data']['default_groups'] = [1]; // Student

						if($_FILES['profile_pic']['error']===0){
							$profile_pic_res = $member_obj->uploadProflieImage($rec_id, $_FILES['profile_pic']['name'], $_FILES['profile_pic']['tmp_name']);
							if(empty($profile_pic_res)){
								$result['message'] .= "<span style='color:#ff3333;'  > The profile pic could not be processed.</span>";
							}else{
								if(!$member_obj->saveDetails(['profile_pic'=>$profile_pic_res['dp_file_name']], $rec_id)){
									$result['message'] .= "<span style='color:#ff3333;'  > The profile pic could not be registered.</span>";
									unlink(CONST_PROFILE_IMG_DIR_PATH.$profile_pic_res['dp_file_name']);
								}
							}
						}

						$recp = ['to'=>[$data['email']], 'cc'=>CONST_REG_APPROVAL_RECP['cc']??[] ];
						$email_data = [
							'name' => preg_replace("/\s+/", ' ', $data['fname']. ' ' .$data['mname']. ' ' .$data['lname']),
							'email' => $data['email'],
							'password' => empty($data['password'])?$default_pswd:$data['password'],
							'org_name' => CONST_ORG_DISP_NAME,
							'login_url' => CONST_APP_ABSURL,
							'from_name' => CONST_MAIL_SENDERS_NAME,

						];
						$member_obj->sendNewRegistrationEmail($email_data, $recp);
					}catch(\Exception $e){
						$last_error = \eBizIndia\PDOConn::getLastError();
						$result['error_code']=1; // DB error
						if($last_error[1] == 1062){
							$result['message'] = "Process failed. Please make sure the membership no is not in use by some other member.";
						}else{
							$result['message']="The member could not be added due to server error.";
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
		}
	}


	$_SESSION['create_user_result'] = $result;
	header("Location:?");
	exit;

}elseif(filter_has_var(INPUT_POST,'mode') && $_POST['mode']=='updateUser'){

   // print_r($_POST);
	$result=array('error_code'=>0,'message'=>[],'other_data'=>['new_roles'=>[], 'roleids_for_showing_selected'=>[]]);
	if($others_edit===false && $self_edit==false){
		$result['error_code']=403;
		$result['message']="Sorry, you are not authorised to perfom this action.";
	}else {
		$data=array();
		$recordid=(int)$_POST['recordid']; // member table's id
		// data validation
		if($recordid == ''){
			$result['error_code']=2;
			$result['message'][]="Invalid record ID.";

		}else{
			$options=[];
			$options['filters']=[];
			$options['filters'][]=['field'=>'id','type'=>'EQUAL','value'=>$recordid];
			$options['fieldstofetch'] = ['*'];
			$recorddetails = \eBizIndia\Member::getList($options);
			if($recorddetails===false){
				$result['error_code']=1;
				$result['message'][]="Failed to verify the user details due to server error.";
				$result['error_fields'][]="#add_form_field_name";
			}elseif(empty($recorddetails)){
				// member record with this ID does not exist
				$result['error_code']=3;
				$result['message'][]="The user account was not found.";
				$result['error_fields'][]="#add_form_field_name";
			}elseif( ($loggedindata[0]['id'] === $recorddetails[0]['user_acnt_id'] && $self_edit===false) || ($loggedindata[0]['id'] !== $recorddetails[0]['user_acnt_id'] && $others_edit===false) ){
				// self edit and others edit not allowed
				$result['error_code']=403;
				$result['message']="Sorry, you are not authorised to perfom this action.";
			}else{
				$edit_restricted_fields = [];
				if($self_edit===true && $loggedindata[0]['id'] === $recorddetails[0]['user_acnt_id']){
					// editing of one's own profile is allowed and the user is trying to do so remove the edit restricted fields from the allowed fields list
					$edit_restricted_fields = CONST_MEM_PROF_EDIT_RESTC[$_cu_role]['self'][1];
				}else if($others_edit===true && $loggedindata[0]['id'] !== $recorddetails[0]['user_acnt_id']){
					$edit_restricted_fields = CONST_MEM_PROF_EDIT_RESTC[$_cu_role]['others'][1];
				}

				if($_cu_role!=='ADMIN'){ // Only admins are allowed to edit the payment details, though till the status is not Paid
					// Already in paid status so ignore the payment related fields, if any
					$edit_restricted_fields[] = 'payment_status'; 
					$edit_restricted_fields[] = 'paid_on'; 
					$edit_restricted_fields[] = 'payment_mode'; 
					$edit_restricted_fields[] = 'membership_fee'; 
					$edit_restricted_fields[] = 'payment_txn_ref'; 
					$edit_restricted_fields[] = 'payment_instrument_type'; 
					$edit_restricted_fields[] = 'payment_instrument'; 
				}
				
				$member_fields = array_diff_key($member_fields, array_fill_keys($edit_restricted_fields, '')); // removing the edit restricted fields from the list of fields
				
				$data = \eBizIndia\trim_deep(\eBizIndia\striptags_deep(array_intersect_key($_POST, $member_fields)));
				if(isset($data['membership_no']))
					$data['membership_no'] = strtoupper($data['membership_no']);
				
				if(($data['dnd']??'')!=='y')
					$data['dnd'] = 'n'; 
					
				if(!empty($data['sector'])){
				    $data['sector'] = (array)$data['sector'];
				    
				}else{
					$data['sector']=null;
				}
			    
				    
				    
				$other_data['field_meta'] = CONST_FIELD_META;
				$other_data['roles'] = array_column(\eBizIndia\enums\Role::cases(), 'value');
				$other_data['gender'] = array_column(\eBizIndia\enums\Gender::cases(), 'value');	
				$other_data['blood_grps'] = array_keys(CONST_BLOOD_GRPS);
				$other_data['loggedindata'] = $loggedindata[0];
				$other_data['recorddetails'] = $recorddetails[0];
				$other_data['profile_pic'] = !in_array('profile_pic', $edit_restricted_fields)?$_FILES['profile_pic']:[];
				$other_data['edit_restricted_fields'] = $edit_restricted_fields;
				$member_obj = new \eBizIndia\Member();	
				$validation_res = $member_obj->validate($data, 'update', $other_data); 
				if($validation_res['error_code']>0){
					$result = $validation_res;
				} else {
					$curr_dttm = date('Y-m-d H:i:s');
					$login_account_data = $member_data = [];
					if(array_key_exists('password', $member_fields) && !empty($data['password'])){	
						$login_account_data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
					}
					if($loggedindata[0]['id'] !== $recorddetails[0]['user_acnt_id'] && array_key_exists('status', $member_fields) &&  $data['status']!==$recorddetails[0]['active']){	
						$login_account_data['status'] = $data['status']==='y'?1:0;
					}
					
					$email_changed = $role_changed = $gender_changed = $groups_changed = $sector_changed = false;
					foreach($member_fields as $fld=>$val){
						if($fld=='password')
							continue;
						if($fld=='email' && $data['email']!==$recorddetails[0]['email']){
							$email_changed = true;
							$member_data['email'] = $login_account_data['username'] = $data['email'];
						}else if($fld=='groups' && (!empty($recorddetails[0]['grp_names']) || !empty($data['groups']))  ){
							$assigned_grp_ids = !empty($recorddetails[0]['assigned_grps'])?array_column($recorddetails[0]['assigned_grps'], 'id'):[];
							$mem_groups_tmp1 = array_diff($assigned_grp_ids, $data['groups']??[]);
							$mem_groups_tmp2 = array_diff($data['groups']??[], $assigned_grp_ids );

							if(!empty($mem_groups_tmp1) || !empty($mem_groups_tmp2) ){
								$groups_changed = true;
								$mem_groups = $data['groups']??[];
							}

						}else if($fld=='sector' && (!empty($recorddetails[0]['sector_names']) || !empty($data['sector']))  ){
							$assigned_sector_ids = !empty($recorddetails[0]['assigned_sectors'])?array_column($recorddetails[0]['assigned_sectors'], 'id'):[];
							$mem_sectors_tmp1 = array_diff($assigned_sector_ids, $data['sector']??[]);
							$mem_sectors_tmp2 = array_diff($data['sector']??[], $assigned_sector_ids );

							if(!empty($mem_sectors_tmp1) || !empty($mem_sectors_tmp2) ){
								$sector_changed = true;
								$mem_sectors = $data['sector']??[];
							}

						}else if($fld=='role'){
							if($data['role']!=$recorddetails[0]['assigned_roles'][0]['role'])
								$role_changed = $data['role'];
						}else if($fld=='status'){
							if($data['status']!==$recorddetails[0]['active'] && $loggedindata[0]['id'] !== $recorddetails[0]['user_acnt_id'])
								$member_data['active'] = $data[$fld]!='y'?'n':'y';
						}else if($data[$fld]!==$recorddetails[0][$fld]){
							if($fld!='spouse_gender' || $data[$fld]!==null){
								$member_data[$fld] = $data[$fld];
								if($fld==='gender')
									$gender_changed = true;

							}
						}
					}
					
					if(!empty($login_account_data) || $role_changed){
						$login_account_data['lastUpdatedOn'] = $curr_dttm;
						$login_account_data['lastUpdatedBy'] = $loggedindata[0]['id'];
						$login_account_data['lastUpdatedFrom'] = \eBizIndia\getRemoteIP();
					}




					$delete_profile_pic = false;
					if(!in_array('profile_pic', $edit_restricted_fields) && $_POST['delete_profile_pic']==1 && $recorddetails[0]['profile_pic']!=''){
						$member_data['profile_pic'] = '';
						$delete_profile_pic = true;
					}
					
					try{
						$conn = \eBizIndia\PDOConn::getInstance();
						if(!empty($member_data) || !empty($login_account_data) || !empty($role_changed) || !empty($groups_changed) || !empty($sector_changed) || (!in_array('profile_pic', $edit_restricted_fields) && $_FILES['profile_pic']['error']===0) ){
							$conn->beginTransaction();
							if(!empty($member_data)){

								// payment info
								if(($member_data['payment_status']??'')=='Paid'){
									// If the payment status is marked as Paid then set the following
									$member_data['paid_on'] .= ' 00:00:00'; // as only date is being submitted but the DB expects a datetime value
									$member_data['amount_paid'] = $member_data['membership_fee']; // for consisstency sake just copying the same amount for manual entry 
									$member_data['membership_fee_gst_rate'] = CONST_MEM_REG_FEE_GST;
								}

								if($email_changed){
									// check the email address for uniqueness
									$username_check = $usercls->usernameExists($member_data['email'], [$recorddetails[0]['user_acnt_id']]);
									// $result['$username_check'] = $username_check;
									if($username_check===false)
										throw new Exception("Error updating the member record");
									if(!empty($username_check))
										throw new Exception("Process failed. A member with the given email address already exists.");
								}

								$member_data['updated_at'] = $curr_dttm;
								$member_data['updated_by'] = $loggedindata[0]['id'];
								$member_data['updated_from'] = \eBizIndia\getRemoteIP();
								$error_details_to_log['mode'] = 'updateUser';
								$error_details_to_log['part'] = 'Update the member record.';
								$member_obj = new \eBizIndia\Member();
								$res = $member_obj->saveDetails($member_data, $recordid);
								if($res===false)
									throw new Exception('Error updating the member record.');
							}

							if(!empty($login_account_data)){
								$error_details_to_log['mode'] = 'updateUser';
								$error_details_to_log['part'] = 'Update the login account for the member record.';
								$login_res = $usercls->saveUserDetails($login_account_data, $recorddetails[0]['user_acnt_id']);	
								if($login_res===false)
									throw new Exception('Error updating the login account for the member record.');
							}

							if(!empty($role_changed)){
								$roles = $usercls->getRoles($role_changed,'',1); // only for member users
								if(empty($roles))
									throw new Exception('Error fetching the role ID for the new member record.');

								$error_details_to_log['mode'] = 'updateUser';
								$error_details_to_log['part'] = 'Assign new role to the member\'s user account.';
								if(!$usercls->revokeUserRoles($recorddetails[0]['user_acnt_id'])){
									throw new Exception('Member could not be created due to error in updating the member role.');
								}
								$roleids_to_assign = [$roles[0]['role_id']];
								if(!$usercls->assignRolesToUsers($recorddetails[0]['user_acnt_id'],$roleids_to_assign)){
									throw new Exception('Member could not be created due to error in saving the member role.');
								}

							}

							if(!empty($groups_changed)){
								$options = [];
								$options['filters'] = [];
								$options['filters'][] = ['field'=>'active', 'type'=>'EQUAL', 'value'=>'y'];
								$groups_tmp = \eBizIndia\MemberGroup::getList($options);
								if(!empty($groups_tmp))
									$groups_tmp = array_map('strtolower', array_column($groups_tmp, 'grp'));
								if(!empty($mem_groups)){
									// look for new groups
									$new_groups = $group_ids_to_assign = [];
									foreach ($mem_groups as $value) {
										if($value!==''){
											if(preg_match("/\D+/", $value)){
												$new_groups[] = $value;
											}else{
												$group_ids_to_assign[] = (int)$value;
											}

										}
									}

									if(!empty($new_groups)){
										$new_grp_ids = \eBizIndia\MemberGroup::addGroup($new_groups);
										if($new_grp_ids===false)
											throw new Exception('Member profile could not be update due to error in saving the groups.');
										$group_ids_to_assign = array_merge($group_ids_to_assign, $new_grp_ids);
									}

									\eBizIndia\MemberGroup::assignRevokeGroups($recordid, $group_ids_to_assign);
								}else{
									\eBizIndia\MemberGroup::assignRevokeGroups($recordid);
								}

								$result['other_data']['selected_groups'] = array_map('strval', $group_ids_to_assign??[]);
							}else{
								$result['other_data']['selected_groups'] = array_map('strval', array_column($recorddetails[0]['assigned_grps'], 'id'));
							}


							if(!empty($sector_changed)){
								if(!empty($mem_sectors)){
									if(!\eBizIndia\MemberSector::assignRevoke($recordid, $mem_sectors))
										throw new Exception('the changes could not be saved due to error in saving the sector.');
								}else{
									if(!\eBizIndia\MemberSector::assignRevoke($recordid))
										throw new Exception('the changes could not be saved due to error in saving the sector.');
								}

								$result['other_data']['selected_sectors'] = array_map('strval', $mem_sectors??[]);
							}else{
								$result['other_data']['selected_sectors'] = array_map('strval', array_column($recorddetails[0]['assigned_sectors'], 'id'));
							}


							if($loggedindata[0]['id']==$recorddetails[0]['user_acnt_id']){
								// If the user is editing his own account
								$userdata = $usercls->refreshLoggedInUserData();
								if(!$userdata){
									throw new Exception('Error updating the member record.');
								}
								if(isset($_COOKIE['loggedin_user']) && !empty($login_account_data['password']) ){
									setcookie('loggedin_user',base64_encode($userdata[0]['username'].$login_account_data['password']),time()+(30*24*60*60),CONST_APP_PATH_FROM_ROOT.'/',$_SERVER['HTTP_HOST'],false,true);
								}
							}

							$result['error_code']=0;
							$result['message']='The changes have been saved.';
							$result['other_data']['recordid']=$recorddetails[0]['user_acnt_id']; // id of the user table for the record which was edited
							$result['other_data']['loggedin_user_id']=$loggedindata[0]['id']; // logged in user's id as there in the users table
							$result['other_data']['profile_details']=$userdata[0]['profile_details'];
							$result['other_data']['title']=$data['title'];
							$result['other_data']['mobile'] = $data['mobile'];
							$result['other_data']['mobile2'] = $data['mobile2'];
							$result['other_data']['name']=$recorddetails[0]['name'];
							$conn->commit();

							if(!in_array('profile_pic', $edit_restricted_fields) && $_FILES['profile_pic']['error']===0){
								if(file_exists(CONST_PROFILE_IMG_DIR_PATH.$recorddetails[0]['profile_pic'])){
									$new_name = CONST_PROFILE_IMG_DIR_PATH.uniqid().'.'.pathinfo($recorddetails[0]['profile_pic'], PATHINFO_EXTENSION);
									rename(CONST_PROFILE_IMG_DIR_PATH.$recorddetails[0]['profile_pic'], $new_name);
								}
								$profile_pic_res = $member_obj->uploadProflieImage($recordid, $_FILES['profile_pic']['name'], $_FILES['profile_pic']['tmp_name']);
								if(empty($profile_pic_res)){
									$result['message'] .= "<span style='color:#ff3333;'  > The profile pic could not be processed.</span>";
									if(!empty($new_name)){
										rename($new_name, CONST_PROFILE_IMG_DIR_PATH.$recorddetails[0]['profile_pic']);
									}
								}else{
									$profile_pic_data = [
										'profile_pic'=>$profile_pic_res['dp_file_name']
									];
									if(empty($member_data)){
										$profile_pic_data['updated_at'] = $curr_dttm;	
										$profile_pic_data['updated_by'] = $loggedindata[0]['id'];
										$profile_pic_data['updated_from'] = \eBizIndia\getRemoteIP();
									}
									
									if(!$member_obj->saveDetails($profile_pic_data, $recordid)){
										$result['message'] .= "<span style='color:#ff3333;'  > The profile pic could not be registered.</span>";
										unlink(CONST_PROFILE_IMG_DIR_PATH.$profile_pic_res['dp_file_name']);
										if(!empty($new_name)){
											rename($new_name, CONST_PROFILE_IMG_DIR_PATH.$recorddetails[0]['profile_pic']);
										}
									}else{
										if(!empty($new_name)){
											// Delete the previously uploaded image file
											unlink($new_name);
										}
										$profile_pic_size = getimagesize(CONST_PROFILE_IMG_DIR_PATH.$profile_pic_res['dp_file_name']);
										$result['other_data']['profile_pic_max_width'] = CONST_PROFILE_IMG_DIM['dw'];
										$result['other_data']['profile_pic_org_width'] = $profile_pic_size[0];
										$result['other_data']['profile_pic_url'] = CONST_PROFILE_IMG_URL_PATH.$profile_pic_res['dp_file_name'];
									}
								}
							}else if($delete_profile_pic && file_exists(CONST_PROFILE_IMG_DIR_PATH.$recorddetails[0]['profile_pic'])){
								unlink(CONST_PROFILE_IMG_DIR_PATH.$recorddetails[0]['profile_pic']);
								$result['other_data']['profile_pic_deleted'] = 1;
								$result['other_data']['placeholder_image'] = ($member_data['gender']??'')==='F'?CONST_NOIMAGE_F_FILE:CONST_NOIMAGE_M_FILE;
							}else if($gender_changed && $recorddetails[0]['profile_pic']==''){
								$result['other_data']['placeholder_image'] = ($member_data['gender']??'')==='F'?CONST_NOIMAGE_F_FILE:CONST_NOIMAGE_M_FILE;
							}

							// Alert the admins if a regular user updates his profile
							if($_cu_role==='REGULAR' && $loggedindata[0]['profile_details']['id'] === $recorddetails[0]['id']){
								$options = [];
								$options['filters'] = [
									['field'=>'role', 'type'=>'EQUAL', 'value'=>'ADMIN'],
									['field'=>'active', 'type'=>'EQUAL', 'value'=>'y']
								];
								$options['fieldstofetch'] = ['id', 'name', 'email'];
								$recp_users = \eBizIndia\Member::getList($options);
								if(!empty($recp_users)){
									$recp = ['to'=>array_column($recp_users, 'email')];
									$email_data = [
										'name' => trim($data['fname'].' '.$data['mname'].' '.$data['lname']),
										'email' => $data['email'],
										'membership_no' => $recorddetails[0]['membership_no'],
										'gender' => $data['gender'],
										'profile_url' => CONST_APP_ABSURL.'/users.php#mode=view&recid='.$recordid,
										'from_name' => CONST_MAIL_SENDERS_NAME,
									];
									$member_obj->profileUpdatedNotification($email_data, $recp); // notify the authorities
								}
							}




							// return latest groups list
							$options = [];
							$options['filters'] = [];
							$options['filters'][] = ['field'=>'active', 'type'=>'EQUAL', 'value'=>'y'];
							$groups = \eBizIndia\MemberGroup::getList($options);
							$result['other_data']['groups'] = $groups;

						}else{
							$result['error_code']=4;
							$result['message']='There were no changes to save.';
						}
					}catch(\Exception $e){
						$result['error_code']=5; // DB error
						$last_error = \eBizIndia\PDOConn::getLastError();
						if($last_error[1] == 1062){
							$result['message'] = "Process failed. Please make sure the membership no. is not in use by some other member.";
						}else{
							$result['message']= $e->getMessage();
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

		}

	}

	$_SESSION['update_user_result']=$result;

	header("Location:?");
	exit;

}elseif(isset($_SESSION['update_user_result']) && is_array($_SESSION['update_user_result'])){
	header("Content-Type: text/html; charset=UTF-8");
	echo "<script type='text/javascript' >\n";
	echo "parent.usersfuncs.handleUpdateUserResponse(".json_encode($_SESSION['update_user_result']).");\n";
	echo "</script>";
	unset($_SESSION['update_user_result']);
	exit;

}elseif(isset($_SESSION['create_user_result']) && is_array($_SESSION['create_user_result'])){
	header("Content-Type: text/html; charset=UTF-8");
	echo "<script type='text/javascript' >\n";
	echo "parent.usersfuncs.handleAddUserResponse(".json_encode($_SESSION['create_user_result']).");\n";
	echo "</script>";
	unset($_SESSION['create_user_result']);
	exit;

}elseif(filter_has_var(INPUT_POST,'mode') && $_POST['mode']=='reqcontact'){
	// Send contact request to the DND member on behalf of the logged in REGULAR user	
	$result=array('error_code'=>0,'message'=>[], 'error_field'=>'#reqcontact_msg', 'other_data'=>[]);
	if($_cu_role=='REGULAR'){

		if(empty($_POST['to_mem_id'])){
			$result['error_code']=2;
			$result['message'][]="The recipient could not be identified.";
		}else if(empty($_POST['msg'])){
			$result['error_code']=2;
			$result['message'][]="Please write your message in the message box.";
		}else{
			$options=[];
			$options['filters']=[];
			$options['filters'][]=['field'=>'id','type'=>'EQUAL','value'=>(int)$_POST['to_mem_id']];
			$options['fieldstofetch'] = ['id', 'email', 'mobile', 'fname', 'dnd', 'active', 'user_acnt_status'];
			$recorddetails = \eBizIndia\Member::getList($options);
			if($recorddetails===false){
				$result['error_code']=2;
				$result['message'][]="The recipient member\'s details could not be retrieved.";
			}elseif(count($recorddetails)==0){
				$result['error_code']=2;
				$result['message'][]="The recipient member was not found.";
			}elseif($recorddetails[0]['active']!='y' || $recorddetails[0]['user_acnt_status']!='1'){
				$result['error_code']=2;
				$result['message'][]="The recipient member was not found.";
			}else{
				$recorddetails=$recorddetails[0];
				if($recorddetails['dnd']=='y'){
					$recp = ['to'=>[$recorddetails['email']], 'cc'=>[] ];
					$email_data = [
						'req_for' => $recorddetails['fname'], // recipient's name
						'req_from' => $loggedindata[0]['profile_details']['name'], // sender's name
						'membership_no' => $loggedindata[0]['profile_details']['membership_no'], 
						'email' => $loggedindata[0]['profile_details']['email'],
						'mobile' => $loggedindata[0]['profile_details']['mobile'],
						'msg' => trim($_POST['msg']),
						'org_name' => CONST_ORG_DISP_NAME,
						'link_to_profile' => CONST_APP_ABSURL.'/users.php#mode=view&recid='.$loggedindata[0]['profile_details']['id'], // sender's profile
						'from_name' => CONST_MAIL_SENDERS_NAME,

					];
					$member_obj = new \eBizIndia\Member();
					if($member_obj->sendContactRequestEmail($email_data, $recp)){
						$result['error_code']=0;
						$result['message'][]="Your contact request was sent successfully.";
					}else{
						$result['error_code']=4;
						$result['message'][]="Your contact request could not be sent due to some error.";
					}

				}else{
					$result['error_code']=5;
					$result['message'][]="Contact request to this member is not allowed.";
				}
			}
		}

			
	}else{
		$result['error_code']=2;
		$result['message'][]="This feature is not available for your profile.";
	}

	$_SESSION['req_contact_result']=$result;

	header("Location:?");
	exit;

}elseif(isset($_SESSION['req_contact_result']) && is_array($_SESSION['req_contact_result'])){
	header("Content-Type: text/html; charset=UTF-8");
	echo "<script type='text/javascript' >\n";
	echo "parent.usersfuncs.handleReqContactResponse(".json_encode($_SESSION['req_contact_result']).");\n";
	echo "</script>";
	unset($_SESSION['req_contact_result']);
	exit;

}elseif(filter_has_var(INPUT_POST,'mode') && $_POST['mode']=='getRecordDetails'){
	$result=array();
	$error=0; // no error
	$can_edit = false;
	$show_others_dnd_status = true;

	if($_POST['recordid']==''){
		$error=1; // Record ID missing

	}else{
		$options=[];
		$options['filters']=[];
		$options['filters'][]=['field'=>'id','type'=>'EQUAL','value'=>(int)$_POST['recordid']];
		if($_cu_role === 'REGULAR'){
			// Allow only active records, to be retrieved by REGULAR type members
			if($loggedindata[0]['profile_details']['id']!==((int)$_POST['recordid'])){
				$options['filters'][] = [
					'field' => 'active',
					'type' => 'EQUAL',
					'value' => 'y'
				];
				// $options['filters'][] = [
				// 	'field' => 'dnd',
				// 	'type' => 'EQUAL',
				// 	'value' => 'n'
				// ];

				// $options['filters'][] = [
				// 	'field' => 'active_dndno_or_id',
				// 	'type' => 'EQUAL',
				// 	'value' => [$_POST['recordid']]
				// ];
			}
			
			$show_others_dnd_status = false;
		}
		$options['fieldstofetch'] = ['*'];
		$recorddetails = \eBizIndia\Member::getList($options);
		if($recorddetails===false){
			$error=2; // db error
		}elseif(count($recorddetails)==0){
			$error=3; // User ID does not exist
		}else{
			$recorddetails=$recorddetails[0];

			$edit_restricted_fields = [];
			if($self_edit===true && $loggedindata[0]['id'] === $recorddetails['user_acnt_id']){
				// editing of one's own profile is allowed and the user is opening his own profile
				$edit_restricted_fields = CONST_MEM_PROF_EDIT_RESTC[$_cu_role]['self'][1];
				$can_edit = true;
			}else if($others_edit===true && $loggedindata[0]['id'] !== $recorddetails['user_acnt_id']){
				// editing other recortds is allowed and the user is opening someone else's profile
				$edit_restricted_fields = CONST_MEM_PROF_EDIT_RESTC[$_cu_role]['others'][1];
				$can_edit = true;
			}

			if($recorddetails['paid_on']!=''){
				$recorddetails['paid_on_dt'] = date('Y-m-d', strtotime($recorddetails['paid_on']));
				$recorddetails['paid_on_dt_view'] = date('d-M-Y', strtotime($recorddetails['paid_on']));
			}else{
				$recorddetails['paid_on_dt'] = $recorddetails['paid_on_dt_view'] = '';
			}
			
			// if($recorddetails['exp_dt']!=''){
			//     $recorddetails['exp_dt_view'] = date('d-M-Y', strtotime($recorddetails['exp_dt']));
			// }


			// if($can_edit===false){
				// $enum_obj = \eBizIndia\enums\MaritalStatus::tryFrom($recorddetails['marital_status']);
				// $recorddetails['marital_status_view'] = !empty($enum_obj)?$enum_obj->label():'';

				$enum_obj = \eBizIndia\enums\Gender::tryFrom($recorddetails['gender']);
				$recorddetails['gender_view'] = !empty($enum_obj)?$enum_obj->label():'';

				if($_cu_role==='ADMIN'){ // non admins will not see the role and status
					$enum_obj = \eBizIndia\enums\Role::tryFrom($recorddetails['assigned_roles'][0]['role']);
					$recorddetails['role_view'] = !empty($enum_obj)?$enum_obj->label():'';

					if($recorddetails['active']==='y')
						$recorddetails['status_view'] = 'Active';
					else
						$recorddetails['status_view'] = 'Inactive';
				}
								
				if($recorddetails['dob']!='')
					$recorddetails['dob_view'] = date('jS M', strtotime($recorddetails['dob']));
				if($recorddetails['annv']!='')
					$recorddetails['annv_view'] = date('jS M', strtotime($recorddetails['annv']));

				if($recorddetails['spouse_dob']!='')
					$recorddetails['spouse_dob_view'] = date('jS M', strtotime($recorddetails['spouse_dob']));

				if($recorddetails['blood_grp']!='')
					$recorddetails['blood_grp_view'] = CONST_BLOOD_GRPS[$recorddetails['blood_grp']]??'';
				
				
			// }

			if($recorddetails['dnd']==='y')
				$recorddetails['dnd_view'] = 'Yes';
			else
				$recorddetails['dnd_view'] = 'No';

			if($recorddetails['profile_pic']!=''){
				$recorddetails['profile_pic_url'] = CONST_PROFILE_IMG_URL_PATH.$recorddetails['profile_pic'];
				$profile_pic_size = getimagesize(CONST_PROFILE_IMG_DIR_PATH.$recorddetails['profile_pic']);
				$recorddetails['profile_pic_max_width'] = CONST_PROFILE_IMG_DIM['dw'];
				$recorddetails['profile_pic_org_width'] = $profile_pic_size[0];
			}
			else
				$recorddetails['profile_pic_url'] = ($recorddetails['gender']==='F')?CONST_NOIMAGE_F_FILE:CONST_NOIMAGE_M_FILE;
			$recorddetails['joining_dt_view'] = $recorddetails['joining_dt']!=''?date('d-M-Y', strtotime($recorddetails['joining_dt'])):'';
			$tmp = array_column($recorddetails['assigned_sectors'], 'id');
			$recorddetails['assigned_sector_ids'] = empty($tmp)? []: array_map('strval', $tmp);
			$recorddetails['remarks_view'] = nl2br(\eBizIndia\_esc($recorddetails['remarks'], true));
			$recorddetails['spouse_children_view'] = nl2br(\eBizIndia\_esc($recorddetails['spouse_children'], true));
			$recorddetails['spouse_profession_view'] = nl2br(\eBizIndia\_esc($recorddetails['spouse_profession'], true));
		}

		if($_cu_role==='ADMIN' || $loggedindata[0]['profile_details']['id'] == $recorddetails['id']){
			// These data are required only for the edit screen and only self editing and editing by ADMINs are allowed
			// return latest groups list
			$groups = \eBizIndia\MemberGroup::getList();
			$result[1]['groups'] = $groups;
			$tmp = array_column($recorddetails['assigned_grps'], 'id');
			$result[1]['assigned_grp_ids'] = empty($tmp)? []: array_map('strval', $tmp);

		}

	}

	$result[0]=$error;
	$result[1]['allow_detail_view'] = true; //($_cu_role==='REGULAR' && $recorddetails['dnd']==='y' && !$can_edit)?false:true; // flag to check if opening the details of the member record is allowed to the logged in user or not
	$result[1]['is_admin'] = $_cu_role==='ADMIN'?true:false;
	$result[1]['can_edit'] = $can_edit;
	$result[1]['show_dnd'] = $show_others_dnd_status;
	$result[1]['cuid'] = $loggedindata[0]['id'];  // This is the auto id of the table users and not member
	$result[1]['record_details']=filterData($recorddetails,'mem_view',['loggedindata'=>$loggedindata[0],'cu_role'=>$_cu_role]);
	$result[1]['edit_restricted_fields']=$edit_restricted_fields;

	
	echo json_encode($result);

	exit;

}elseif(filter_has_var(INPUT_GET,'mode') && $_GET['mode']==='export'){
	if(strcasecmp($_cu_role, 'ADMIN')!==0){
		header('HTTP/1.0 403 Forbidden', true, 403);
		die;
	}
	$options=[];
	$options['filters']=[];
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
					$value=trim($filter['searchtext']);
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

	$records=\eBizIndia\Member::getList($options);
	
	if($records===false){
		header('HTTP/1.0 500 Internal Server Error', true, 500);
		die;
	}else if(empty($records)){
		header('HTTP/1.0 204 No Content', true, 204);
		die;
	}else{
		if(!defined('CONST_MEM_EXPORT_FLDS') || empty(CONST_MEM_EXPORT_FLDS) || !is_array(CONST_MEM_EXPORT_FLDS)){
			header('HTTP/1.0 412 Precondition Failed', true, 412);
			die;
		}
		ob_clean();
		header('Content-Description: File Transfer');
	    header('Content-Type: application/csv');
	    header("Content-Disposition: attachment; filename=members.csv");
	    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	    $fh = fopen('php://output', 'w');
	    if(!$fh){
	    	header('HTTP/1.0 500 Internal Server Error', true, 500);
	    	die;
	    }
	    $col_headers = array_values(CONST_MEM_EXPORT_FLDS);
	    $data_row_flds = array_fill_keys(array_keys(CONST_MEM_EXPORT_FLDS), '');
	    fputcsv($fh, $col_headers);
	    foreach ($records as $rec) {
			$data_row = array_intersect_key(array_replace($data_row_flds, $rec), $data_row_flds);
			$data_row['role'] = $rec['assigned_roles'][0]['role'];	
			$tmp = \eBizIndia\enums\Gender::tryFrom($data_row['gender']);
			$data_row['gender'] = !empty($tmp)?$tmp->label():'';		
			// $tmp = \eBizIndia\enums\MaritalStatus::tryFrom($data_row['marital_status']);
			// $data_row['marital_status'] = !empty($tmp)?$tmp->label():'';	
			$data_row['active'] = $data_row['active']=='y'?'Active':'Inactive';	
			if(array_key_exists('dnd', $data_row_flds))
				$data_row['dnd'] = $data_row['dnd']=='y'?'Yes':'';	
			if(array_key_exists('groups', $data_row_flds))
				$data_row['groups'] = implode(', ',$rec['grp_names']??[]);	
			if(array_key_exists('sectors', $data_row_flds))
				$data_row['sectors'] = implode(', ',$rec['sector_names']??[]);	
			
			
			fputcsv($fh, array_values($data_row));
		}
		ob_flush();
		fclose($fh);
		die;
	}


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
					$value=trim($filter['searchtext']);
				else
					$value='';

				$options['filters'][] = array('field'=>$field,'type'=>$type,'value'=>$value);

				if($field=='mob')
					$fltr_text = 'Mobile number ';
				else if($field=='batch_no')
					$fltr_text = 'Batch ';
				else if($field=='membership_no')
					$fltr_text = 'Membership no. ';
				else if($field=='residence_city')
					$fltr_text = 'Residence city ';
				else if($field=='residence_country')
					$fltr_text = 'Residence country ';
				else if($field=='blood_grp')
					$fltr_text = 'Blood group ';
				else if($field=='work_type')
					$fltr_text = 'Business/Profession details ';
				else if($field=='work_ind')
					$fltr_text = 'Business/Profession details ';
				else if($field=='work_company')
					$fltr_text = 'Company ';
				// else if($field=='joining_dt')
				// 	$fltr_text = 'Joined ';
				else if($field=='sector_id')
					$fltr_text = 'Sector ';
				else if($field=='grp_id')
					$fltr_text = 'Group ';
				else 
					$fltr_text = ucfirst($field).' ';
				
				switch($type){
					case 'CONTAINS':
						$fltr_text .= 'has ';	break;
					case 'EQUAL':
						$fltr_text .= $field=='joining_dt'?'on ':'is ';	break;
					case 'STARTS_WITH':
						$fltr_text .= 'starts with ';	break;
					case 'AFTER':
						$fltr_text .= 'after ';	break;
				}

				$disp_value = !empty($filter['disp_text'])?$filter['disp_text']:($field=='joining_dt'?date('d-M-Y', strtotime($value)):$value);

				$filtertext[]='<span class="searched_elem"  >'.$fltr_text.'  <b>'.\eBizIndia\_esc($disp_value, true).'</b><span class="remove_filter" data-fld="'.$field.'"  >X</span> </span>';
			}
			$result[1]['filtertext'] = implode(' ',$filtertext);
		}
	}

	$tot_rec_options = [
		'fieldstofetch'=>['recordcount'],
		'filters' => [],
	];

	if($_cu_role === 'REGULAR'){
		// Allow only active records to be listed for REGULAR type members
		
		// $tot_rec_options['filters'][] = $options['filters'][] = [
		// 	'field' => 'active_dndno_or_id',
		// 	'type' => 'EQUAL',
		// 	'value' => [$loggedindata[0]['profile_details']['id']]
		// ];
		$options['filters'][] = $tot_rec_options['filters'][] = [
			'field' => 'active',
			'type' => 'EQUAL',
			'value' => 'y'
		]; 
		$show_dnd_status = false;
	}

	$options['fieldstofetch'] = ['recordcount'];

	// get total emp count
	$tot_rec_cnt = \eBizIndia\Member::getList($tot_rec_options); 
	$result[1]['tot_rec_cnt'] = $tot_rec_cnt[0]['recordcount'];

	// $recordcount=$usercls->getList($options);
	$recordcount = \eBizIndia\Member::getList($options);
	$recordcount = $recordcount[0]['recordcount'];
	$paginationdata=\eBizIndia\getPaginationData($recordcount,$recsperpage,$pno,CONST_PAGE_LINKS_COUNT);
	$result[1]['paginationdata']=$paginationdata;


	if($recordcount>0){
		$noofrecords=$paginationdata['recs_per_page'];
		$options['fieldstofetch'] = ['id', 'name', 'batch_no', 'email', 'mobile', 'dnd', 'role', 'active', 'profile_pic', 'gender', 'work_company', 'membership_no', 'user_acnt_id'];
		$options['page'] = $pno;
		$options['recs_per_page'] = $noofrecords;

		if(isset($_POST['sortdata']) && $_POST['sortdata']!=''){
			$options['order_by']=[];
			$sortdata=json_decode($_POST['sortdata'],true);
			foreach($sortdata as $sort_param){

				$options['order_by'][]=array('field'=>$sort_param['sorton'],'type'=>$sort_param['sortorder']);

				if($sort_param['sorton']=='batch_no')
					$options['order_by'][]=array('field'=>'name','type'=>'ASC');

			}
		}

		$records=\eBizIndia\Member::getList($options);
		
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
		$get_list_template_data[$_POST['mode']]['self_edit']=$self_edit;
		$get_list_template_data[$_POST['mode']]['others_edit']=$others_edit;
		$get_list_template_data[$_POST['mode']]['cu_id']=$loggedindata[0]['id'];
		$get_list_template_data[$_POST['mode']]['show_dnd_status']=$show_dnd_status;
		$get_list_template_data[$_POST['mode']]['filtertext']=$result[1]['filtertext'];
		$get_list_template_data[$_POST['mode']]['filtercount']=count($filtertext);
		$get_list_template_data[$_POST['mode']]['tot_col_count']=count($records[0]??[])+1; // +1 for the action column

		$paginationdata['link_data']="";
		$paginationdata['page_link']='#';//"users.php#pno=<<page>>&sorton=".urlencode($options['order_by'][0]['field'])."&sortorder=".urlencode($options['order_by'][0]['type']);
		$get_list_template_data[$_POST['mode']]['pagination_html']=$page_renderer->fetchContent(CONST_THEMES_TEMPLATE_INCLUDE_PATH.'pagination-bar.tpl',$paginationdata);

		$get_list_template_data['logged_in_user']=$loggedindata[0];
		$get_list_template_data['country_code'] = CONST_COUNTRY_CODE;
		$get_list_template_data['cu_role'] = $_cu_role;


		$page_renderer->updateBodyTemplateData($get_list_template_data);
		$result[1]['list']=$page_renderer->fetchContent();

	}

	echo json_encode($result,JSON_HEX_TAG);
	exit;

}

$salutation_list=[];
foreach($_salutations as $val){

	$salutation_list[] = $val['text'];

}

$admin_menu_obj = new \eBizIndia\AdminMenu();
$user_roles = $admin_menu_obj->getUserRoles('',false);

$user_roles_for_select_list = [];
foreach($user_roles as $role){

	$user_roles_for_select_list[] = ['id'=>$role['role_id'], 'text'=>$role['role_name'], 'for'=>$role['role_for']];
}

$designations = \eBizIndia\Helper::getMasterList('designations');
if(empty($designations))
	$designations = [];
// $mem_types = \eBizIndia\Helper::getMasterList('mem_types');
// if(empty($mem_types))
// 	$mem_types = [];
$cities = \eBizIndia\Helper::getMasterList('cities');
if(empty($cities))
	$cities = [];
$states = \eBizIndia\Helper::getMasterList('states');
if(empty($states))
	$states = [];
$countries = \eBizIndia\Helper::getMasterList('countries');
if(empty($countries))
	$countries = [];
$work_type = \eBizIndia\Helper::getMasterList('work_type');
if(empty($work_type))
	$work_type = [];
$work_ind = \eBizIndia\Helper::getMasterList('work_ind');
if(empty($work_ind))
	$work_ind = [];
$work_company = \eBizIndia\Helper::getMasterList('work_company');
if(empty($work_company))
 	$work_company = [];

$options = [];
// $options['filters'] = [];
// $options['filters'][] = ['field'=>'active', 'type'=>'EQUAL', 'value'=>'y'];
$groups = \eBizIndia\MemberGroup::getList($options);

if(!empty($groups)){
	array_multisort(array_column($groups, 'grp'), SORT_NATURAL, SORT_ASC, $groups);
}

$sectors = \eBizIndia\MemberSector::getList();



$user_levels = []; //$usercls->getUserLevelsList();

$dom_ready_data['users']=array(
								'salutation'=>json_encode($salutation_list),
								'user_roles_list'=>json_encode($user_roles_for_select_list),
								'user_levels'=>json_encode($user_levels),
								'designations' => json_encode($designations),
								// 'mem_types' => json_encode($mem_types),
								'cities' => json_encode($cities),
								'states' => json_encode($states),
								'countries' => json_encode($countries),
								'work_type' => json_encode($work_type),
								'work_ind' => json_encode($work_ind),
								'field_meta' => CONST_FIELD_META,
							);

// $jscode .= "const can_edit = ".($can_edit===true?'true;':'false;').';'.PHP_EOL;
$jscode .= "const country_code = \"".CONST_COUNTRY_CODE.'";'.PHP_EOL."const def_exp_dt = new Date(\"$exp_dt\");".PHP_EOL;

$additional_base_template_data = array(
										'page_title' => $page_title,
										'page_description' => $page_description,
										'template_type'=>$template_type,
										'dom_ready_code'=>\scriptProviderFuncs\getDomReadyJsCode($page,$dom_ready_data),
										'other_js_code'=>$jscode,
										'module_name' => $page
									);


$additional_body_template_data = ['user_roles'=>$user_roles,   'user_levels'=>$user_levels,  'pg_list'=>$pg_list,'salutation'=>$salutation_list,'default_pswd'=>$default_pswd, 'can_add'=>$can_add, 'country_code'=>CONST_COUNTRY_CODE, 'profile_pic_file_types'=>CONST_FIELD_META['profile_pic']['file_types'], 'allow_export'=>($_cu_role==='ADMIN'?true:false), 'blood_grps' => CONST_BLOOD_GRPS , 'field_meta' => CONST_FIELD_META, 'groups' => $groups, 'sectors' => $sectors, 'is_admin'=>$_cu_role=='ADMIN'?true:false];

$page_renderer->updateBodyTemplateData($additional_body_template_data);

$page_renderer->updateBaseTemplateData($additional_base_template_data);
$page_renderer->addCss(\scriptProviderFuncs\getCss($page));
$js_files=\scriptProviderFuncs\getJavascripts($page);
$page_renderer->addJavascript($js_files['BSH'],'BEFORE_SLASH_HEAD');
$page_renderer->addJavascript($js_files['BSB'],'BEFORE_SLASH_BODY');
$page_renderer->renderPage();

function filterData($data, $for='', $other_info = []){
	switch ($for) {
		case 'mem_view':
			$flds_to_remove = [];
			if($other_info['loggedindata']['profile_details']['id']!=$data['id'] && $other_info['cu_role']!='ADMIN' && $data['dnd']=='y'){
				$flds_to_remove['email'] = $flds_to_remove['mobile'] = $flds_to_remove['mobile2'] = '';
			}
			if($other_info['cu_role']!='ADMIN'){
				$flds_to_remove['membership_fee'] = $flds_to_remove['membership_fee_gst_rate'] = $flds_to_remove['membership_type'] = $flds_to_remove['paid_on'] = $flds_to_remove['paid_on_dt'] = $flds_to_remove['paid_on_dt_view'] = $flds_to_remove['payment_instrument'] = $flds_to_remove['payment_instrument_type'] = $flds_to_remove['payment_mode'] = $flds_to_remove['payment_status'] = $flds_to_remove['payment_txn_ref'] = $flds_to_remove['remarks'] = '';

			}
			$data = array_diff_key($data, $flds_to_remove);
			break;
	}
	return $data;
}

?>
