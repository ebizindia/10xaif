<?php
$page='mem-regs';
require_once 'inc.php';
$template_type='';
$page_title = 'Manage Registrations'.CONST_TITLE_AFX;
$page_description = 'One can approve/disapprove member registrations.';
$body_template_file = CONST_THEMES_TEMPLATE_INCLUDE_PATH . 'mem-regs.tpl';
$body_template_data = array();
$page_renderer->registerBodyTemplate($body_template_file,$body_template_data);
$email_pattern="/^\w+([.']?-*\w+)*@\w+([.-]?\w+)*(\.\w{2,4})+$/i";
$user_date_display_format_for_storage = 'd-m-Y';
$default_pswd = 'xyz123';
$profile_type = 'member';
$self_edit = $others_edit = $can_add = false; // $can_edit = false;
$_cu_role = $loggedindata[0]['profile_details']['assigned_roles'][0]['role'];

if(CONST_MEM_PROF_EDIT_RESTC[$_cu_role]['others'][0]===true)
	$others_edit = true;

$member_fields = [
	'title'=>'', 
	'fname'=>'', 
	'mname'=>'', 
	'lname'=>'', 
	'email'=>'', 
	'mobile'=>'', 
	'mobile2'=>'', 
	'edu_qual'=>'', 
	'linkedin_accnt'=>'', 
	'x_accnt'=>'', 
	'fb_accnt'=>'',
	'website'=>'',
	'gender'=>'', 
	'blood_grp'=>'', 
	'batch_no'=>'', 
	'dob'=>'', 
	'annv'=>'', 
	// 'marital_status'=>'', 
	'residence_city'=>'', 
	'residence_state'=>'', 
	'residence_country'=>'', 
	'residence_pin'=>'', 
	'residence_addrline1'=>'', 
	'residence_addrline2'=>'', 
	'residence_addrline3'=>'',
	'work_type'=>'', 
	// 'work_ind'=>'', 
	'work_company'=>'', 
	'designation'=>'', 
	'work_city'=>'', 
	'work_state'=>'', 
	'work_country'=>'', 
	'work_pin'=>'', 
	'work_addrline1'=>'', 
	'work_addrline2'=>'', 
	'work_addrline3'=>'',
	'status'=>'',
	'dnd'=>'',
	// 'hashtags'=>'',
	'status_remarks' => '',
	'sector' => '',

	'membership_fee' => '',
	'payment_status' => '',
	'payment_mode' => '',
	'payment_txn_ref' => '',
	'payment_instrument_type' => '',
	'payment_instrument' => '',
	'paid_on' => '',
];

if(filter_has_var(INPUT_POST,'mode') && $_POST['mode']=='updateUser'){
	$result=array('error_code'=>0,'message'=>[],'other_data'=>[]);
	if($others_edit===false){
		$result['error_code']=403;
		$result['message']="Sorry, you are not authorised to process new registration requests.";
	}else {
		$data=array();
		$recordid=(int)$_POST['recordid']; // member_regs table's id
		// data validation
		if($recordid == ''){
			$result['error_code']=2;
			$result['message'][]="Invalid record ID.";
		}else{
			$options=[];
			$options['filters']=[];
			$options['filters'][]=['field'=>'id','type'=>'EQUAL','value'=>$recordid];
			$options['fieldstofetch'] = ['*'];
			$recorddetails = \eBizIndia\Member::getRegistrations($options);
			if($recorddetails===false){
				$result['error_code']=1;
				$result['message'][]="Failed to verify the user details due to server error.";
				$result['error_fields'][]="#add_form_field_name";
			}elseif(empty($recorddetails)){
				// member record with this ID does not exist
				$result['error_code']=3;
				$result['message'][]="The user account was not found.";
				$result['error_fields'][]="#add_form_field_name";
			}elseif( $recorddetails[0]['status'] ==='Approved' ){
				// Approved registrations cannot be updated
				$result['error_code']=403;
				$result['message']="Sorry, an approved registration cannot be modified. If required you may update the details in the members' directory.";
			}else{
				$edit_restricted_fields = [];
				if($recorddetails[0]['payment_status']=='Paid'){
					// Already in paid status so ignore the payment related fields, if any
					$edit_restricted_fields[] = 'payment_status'; 
					$edit_restricted_fields[] = 'payment_mode'; 
					$edit_restricted_fields[] = 'membership_fee'; 
					$edit_restricted_fields[] = 'payment_txn_ref'; 
					$edit_restricted_fields[] = 'payment_instrument_type'; 
					$edit_restricted_fields[] = 'payment_instrument'; 
					$edit_restricted_fields[] = 'paid_on'; 
				}
				
				$member_fields = array_diff_key($member_fields, array_fill_keys($edit_restricted_fields, '')); // removing the edit restricted fields from the list of fields
				
				$data = \eBizIndia\trim_deep(\eBizIndia\striptags_deep(array_intersect_key($_POST, $member_fields)));
				if(($data['dnd']??'')!=='y')
					$data['dnd'] = 'n'; 
				$data['sector'] = (array)$data['sector'];
				$other_data['field_meta'] = CONST_FIELD_META;
				$other_data['gender'] = array_column(\eBizIndia\enums\Gender::cases(), 'value');	
				$other_data['blood_grps'] = array_keys(CONST_BLOOD_GRPS);
				$other_data['loggedindata'] = $loggedindata[0];
				$other_data['recorddetails'] = $recorddetails[0];
				$other_data['profile_pic'] = !in_array('profile_pic', $edit_restricted_fields)?$_FILES['profile_pic']:[];
				$other_data['edit_restricted_fields'] = $edit_restricted_fields;
				$member_obj = new \eBizIndia\Member();	
				$validation_res = $member_obj->validate($data, 'regupdate', $other_data); 
				if($validation_res['error_code']>0){
					$result = $validation_res;
				} else {
					$curr_dttm = date('Y-m-d H:i:s');
					$ip = \eBizIndia\getRemoteIP();
					$login_account_data = $member_data = $reg_data = [];
					
					$email_changed = $gender_changed = $status_changed = $sector_changed = false;
					foreach($member_fields as $fld=>$val){
						if($fld=='email' && $data['email']!==$recorddetails[0]['email']){
							$email_changed = true;
							$reg_data['email'] = $data['email'];
							
						}else if($fld=='status'){
							if($data['status']!==$recorddetails[0]['status']){
								$status_changed = true;
								// if($data['status']==='Approved'){
								// 	$login_account_data['username'] = $data['email'];
								// }
							}
						}else if($fld=='sector' && (!empty($recorddetails[0]['sector_names']) || !empty($data['sector']))  ){
							$assigned_sector_ids = !empty($recorddetails[0]['assigned_sectors'])?array_column($recorddetails[0]['assigned_sectors'], 'id'):[];
							$mem_sectors_tmp1 = array_diff($assigned_sector_ids, $data['sector']??[]);
							$mem_sectors_tmp2 = array_diff($data['sector']??[], $assigned_sector_ids );

							if(!empty($mem_sectors_tmp1) || !empty($mem_sectors_tmp2) ){
								$sector_changed = true;
								$mem_sectors = $data['sector']??[];
							}

						}else if($data[$fld]!==$recorddetails[0][$fld]){
							if($fld=='status_remarks' && $data['status']=='New')
								continue; // ignore the status remarks field;
							$reg_data[$fld] = $data[$fld];
							if($fld==='gender')
								$gender_changed = true;
						}
						
					}


					$delete_profile_pic = false;
					if(!in_array('profile_pic', $edit_restricted_fields) && $_POST['delete_profile_pic']==1 && $recorddetails[0]['profile_pic']!=''){
						$reg_data['profile_pic'] = '';
						$delete_profile_pic = true;
					}
					
					try{
						$conn = \eBizIndia\PDOConn::getInstance();
						if(!empty($reg_data) ||  $status_changed || $sector_changed || (!in_array('profile_pic', $edit_restricted_fields) && $_FILES['profile_pic']['error']===0) ){
							// Initialize with a common success message and code
							$result['error_code'] = 0;
							$result['message']='The changes have been saved.';

							$conn->beginTransaction();
							if(!empty($reg_data)){
								// put the membership fee into the amount paid column if the payment status has been manually changed to Paid
								if(($reg_data['payment_status']??'')=='Paid'){
									$reg_data['amount_paid'] = $data['membership_fee'];
									if(($reg_data['paid_on']??'')!='')
										$reg_data['paid_on'] .= ' 00:00:00';
								}

								$reg_data['updated_at'] = $curr_dttm;
								$reg_data['updated_by'] = $loggedindata[0]['id'];
								$reg_data['updated_from'] = $ip;
								
							}

							if(!empty($sector_changed)){
								if(!empty($mem_sectors)){
									if(!\eBizIndia\MemberSector::assignRevokeFromReg($recordid, $mem_sectors))
										throw new Exception('The changes could not be saved due to error in saving the sector.');
								}else{
									if(!\eBizIndia\MemberSector::assignRevokeFromReg($recordid))
										throw new Exception('The changes could not be saved due to error in saving the sector.');
								}

								// The updation meta info should be set even if only the sector is being changed
								$reg_data['updated_at'] = $curr_dttm;
								$reg_data['updated_by'] = $loggedindata[0]['id'];
								$reg_data['updated_from'] = $ip;
								
							}


							// Update the record without the changed status, if the new status is Approved
							if($status_changed && $data['status']==='Disapproved'){
								$reg_data['status'] = 'Disapproved';
								$reg_data['disapproved_on'] = $curr_dttm;
								$reg_data['disapproved_by'] = $loggedindata[0]['id'];
								$reg_data['disapproved_from_ip'] = $ip;
							}
							
							if(!empty($reg_data)){
								$error_details_to_log['mode'] = 'updateUser';
								$error_details_to_log['part'] = 'Update the member record.';
								$res = $member_obj->registerMember($reg_data, $recordid);
								if($res===false){
									$result['error_code']=11; 
									$result['message'] = 'Process failed. The changes could not be saved.';
									throw new Exception('Error updating the registration record.');
								}

								$conn->commit(); // The modifications done in the registration record has been committed, except the status, if any

								// if($reg_data['status']==='Disapproved'){
								// 	$result['message'] = 'The member registration request has been disapproved.';
								// 	// Send Disapproval intimation to the member
								// 	$email_recp = [$data['email']];
								// 	$email_data = [
								// 		'name' => trim($data['fname'].' '.$data['mname'].' '.$data['lname']),
								// 		'from_name' => CONST_MAIL_SENDERS_NAME,
								// 	];
								// 	$member_obj->sendRegistrationDisapprovalEmail($email_data, $recp); // notify the member
								// }
							}

							// Profile image processing
							if(!in_array('profile_pic', $edit_restricted_fields) && $_FILES['profile_pic']['error']===0){
								if(file_exists(CONST_PROFILE_IMG_DIR_PATH.$recorddetails[0]['profile_pic'])){
									$new_name = CONST_PROFILE_IMG_DIR_PATH.uniqid().'.'.pathinfo($recorddetails[0]['profile_pic'], PATHINFO_EXTENSION);
									rename(CONST_PROFILE_IMG_DIR_PATH.$recorddetails[0]['profile_pic'], $new_name);
								}
								$profile_pic_res = $member_obj->uploadProflieImage($recordid, $_FILES['profile_pic']['name'], $_FILES['profile_pic']['tmp_name'], 'reg');
								if(empty($profile_pic_res)){
									$result['message'] .= "<span style='color:#ff3333;'  > The profile pic could not be processed.</span>";
									if(!empty($new_name)){
										rename($new_name, CONST_PROFILE_IMG_DIR_PATH.$recorddetails[0]['profile_pic']);
									}
								}else{
									$profile_pic_data = [
										'profile_pic'=>$profile_pic_res['dp_file_name']
									];
									if(empty($reg_data)){
										$profile_pic_data['updated_at'] = $curr_dttm;	
										$profile_pic_data['updated_by'] = $loggedindata[0]['id'];
										$profile_pic_data['updated_from'] = \eBizIndia\getRemoteIP();
									}
									
									if(!$member_obj->registerMember($profile_pic_data, $recordid)){
										$result['message'] .= "<span style='color:#ff3333;'  > The profile pic could not be saved.</span>";
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
								$result['other_data']['placeholder_image'] = (isset($reg_data['gender']) && $reg_data['gender']==='F' || $recorddetails[0]['gender']==='F')?CONST_NOIMAGE_F_FILE:CONST_NOIMAGE_M_FILE;
							}else if($gender_changed && $recorddetails[0]['profile_pic']==''){
								$result['other_data']['placeholder_image'] = (isset($reg_data['gender']) && $reg_data['gender']==='F')?CONST_NOIMAGE_F_FILE:CONST_NOIMAGE_M_FILE;
							}

							//////////////////////


							if($status_changed && $data['status']==='Approved'){
								// start another transaction
								if(!$conn->inTransaction())
									$conn->beginTransaction();

								if($email_changed){
									$email_to_check = $reg_data['email'];
								}else{
									$email_to_check = $recorddetails[0]['email'];
								}
								// check the email address for uniqueness against the users table
								$username_check = $usercls->usernameExists($email_to_check);
								if($username_check===false){
									$result['error_code']=5; // DB error
									$result['message'] = 'Process failed. The system could not determine if the email address is already registered.';
									throw new Exception("Error approving the registration");
								}
								if(!empty($username_check)){
									$result['error_code']=6; 
									$result['message'] = 'Process failed. A member with the given email address already exists.';
									throw new Exception("Process failed. A member with the given email address already exists.");
								}
								// check the email adddress for uniqueness against the members' list
								$options = [];
								$options['fieldstofetch'] = ['recordcount'];
								$options['filters']=[];
								$options['filters'][]=['field'=>'email','type'=>'EQUAL','value'=>$email_to_check];
								$email_check=$member_obj->getList($options);
								if($email_check===false){
									$result['error_code']=7; 
									$result['message'] = 'Process failed. The system could not determine if the email address is already registered.';
									throw new Exception("Error approving the registration");
								}
								if($email_check[0]['recordcount']>0){
									$result['error_code']=8; 
									$result['message'] = 'Process failed. A member with the given email address already exists.';
									throw new Exception("Process failed. A member with the given email address already exists.");
								}


								// Create a new member record from the registration record
								$other_data = [
									'created_at' => $curr_dttm,
									'created_by' => $loggedindata[0]['id'],
									'created_from' => $ip,
									'joining_dt' => date('Y-m-d'),
									'membership_no' => $member_obj->generateMembershipNo(),
								];

								$mem_id = $member_obj->addToMembersDirectory($recordid, $other_data);
								if(empty($mem_id)){
									$result['error_code']=11; 
									$result['message'] = 'The changes, if any, were saved to the registered profile but the approval failed.';
									throw new Exception('Error creating the member record.', 11); 
								}

								if(!\eBizIndia\MemberGroup::assignRevokeGroups($mem_id, [1])){
									$result['error_code']=17; 
									$result['message'] = 'The changes, if any, were saved to the registered profile but the approval failed.';
									throw new Exception('Error occurred while setting the default group for the new profile.', 17); 
								} // Assigning the default group Student

								
								if(!empty($data['sector']) && !\eBizIndia\MemberSector::assignRevoke($mem_id, $data['sector'])){
									$result['error_code']=18; 
									$result['message'] = 'The changes, if any, were saved to the registered profile but the approval failed.';
									throw new Exception('Error occurred while setting the sector for the new profile.', 18); 
								} // Assigning the sectors

								// Update the profile pic image in the member record
								$mem_data = [];
								if(!empty($profile_pic_data)){
									$profile_pic = $profile_pic_data['profile_pic'];
								}else if(!$delete_profile_pic){
									$profile_pic = $recorddetails[0]['profile_pic'];
								}
								if(!empty($profile_pic) && file_exists(CONST_PROFILE_IMG_DIR_PATH.$profile_pic) ){
									$mem_data['profile_pic'] = preg_replace("/reg-\d+/", $mem_id, $profile_pic);
									if(!$member_obj->saveDetails($mem_data, $mem_id)){
										$result['error_code']=12; 
										$result['message'] = 'The changes, if any, were saved to the registered profile but the approval failed.';
										throw new Exception('Error setting the profile pic in the member record.', 12); // 10 indicates that
									}
									copy(CONST_PROFILE_IMG_DIR_PATH.$profile_pic, CONST_PROFILE_IMG_DIR_PATH.$mem_data['profile_pic']);
								}

								// \eBizIndia\ErrorHandler::logError(['$profile_pic: '.$profile_pic, '$mem_data: '.print_r($mem_data, true)]);

								// Mark the registration record as approved and set the member record id too
								$appr_data = [];	
								$appr_data['status'] = 'Approved';
								$appr_data['approved_on'] = $curr_dttm;
								$appr_data['approved_by'] = $loggedindata[0]['id'];
								$appr_data['approved_from_ip'] = $ip;
								$appr_data['mem_id'] = $mem_id;
								
								$res = $member_obj->registerMember($appr_data, $recordid);
								if($res===false){
									$result['error_code']=13; 
									$result['message'] = 'The changes, if any, were saved to the registered profile but the approval failed.';
									throw new Exception('Error updating the reg record.', 13); 
								}
								
								// \eBizIndia\ErrorHandler::logError(['$appr_data: '.print_r($appr_data, true)]);

								$default_pswd = \eBizIndia\generatePassword();
								// Create a new user record for the new member record so as to give login access
								$login_account_data = [
									'username' => strtolower($data['email']),
									'password' => password_hash($default_pswd, PASSWORD_BCRYPT),
									'profile_type' => $profile_type,
									'profile_id' => $mem_id,
									'createdOn' => $curr_dttm,
									'createdBy' => $loggedindata[0]['id'],
									'createdFrom' => $ip,
								];
								$login_rec_id = $usercls->saveUserDetails($login_account_data);	
								if($login_rec_id===false){
									$result['error_code']=14; 
									$result['message'] = 'The changes, if any, were saved to the registered profile but the approval failed.';
									throw new Exception('Error imparting login access to the member record.', 14);
								}

								// \eBizIndia\ErrorHandler::logError(['$login_account_data: '.print_r($login_account_data, true)]);

								$roles = $usercls->getRoles('REGULAR','',1); // only for member users
								if(empty($roles)){
									$result['error_code']=15; 
									$result['message'] = 'The changes, if any, were saved to the registered profile but the approval failed.';
									throw new Exception('Error fetching the role ID for the new member record.');
								}

								$roleids_to_assign = [$roles[0]['role_id']];
								if(!$usercls->assignRolesToUsers($login_rec_id,$roleids_to_assign)){
									$result['error_code']=16; 
									$result['message'] = 'The changes, if any, were saved to the registered profile but the approval failed.';
									throw new Exception('User could not be created due to error in saving the member role.');
								}

								$result['message'] = 'The member profile has been approved successfully.';
								// Send Approval email to the member
								$email_recp = ['to'=>[$data['email']], 'cc'=>CONST_REG_APPROVAL_RECP['cc']??[]]; // cc: .
								$email_data = [
									'name' => trim($data['fname'].' '.$data['mname'].' '.$data['lname']),
									'email' => $data['email'],
									'org_name' => CONST_ORG_DISP_NAME,
									'password' => $default_pswd,
									'login_url' => CONST_APP_ABSURL,
									'from_name' => CONST_MAIL_SENDERS_NAME,
								];
								$member_obj->sendRegistrationApprovalEmail($email_data, $email_recp); // notify the member with cc to authorities

								if(ENABLE_WHATSAPP_MSG == 1){
									$aisensy = new \eBizIndia\AISensy(AISENSY_API_KEY, 2);
									$template_params=[$email_data['name'], $email_data['email'], $default_pswd];
									$res=$aisensy->sendCampaignMessage(AISENSY_MEM_APPR_CAMPAIGN, $data['mobile'], $template_params);
									//print_r($res);die();
								}

							}
							
							$conn->commit();

							$result['other_data']['title']=$data['title'];
							$result['other_data']['mobile'] = $data['mobile'];
							$result['other_data']['mobile2'] = $data['mobile2'];
							$result['other_data']['name']=$recorddetails[0]['name'];
							$result['other_data']['payment_status'] = !empty($reg_data['payment_status'])?$reg_data['payment_status']:$recorddetails[0]['payment_status'];

						
						}else{
							$result['error_code']=4;
							$result['message']='There were no changes to save.';
						}
					}catch(\Exception $e){
						$last_error = \eBizIndia\PDOConn::getLastError();
						if(!empty($mem_data['profile_pic']) && file_exists(CONST_PROFILE_IMG_DIR_PATH.$mem_data['profile_pic']))
							unlink(CONST_PROFILE_IMG_DIR_PATH.$mem_data['profile_pic']);
						
						$error_details_to_log['reg_data'] = $reg_data;
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
	echo "parent.memregfuncs.handleUpdateUserResponse(".json_encode($_SESSION['update_user_result']).");\n";
	echo "</script>";
	unset($_SESSION['update_user_result']);
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
		$options['fieldstofetch'] = ['*'];
		$recorddetails = \eBizIndia\Member::getRegistrations($options);
		if($recorddetails===false){
			$error=2; // db error
		}elseif(count($recorddetails)==0){
			$error=3; // User ID does not exist
		}else{
			$recorddetails=$recorddetails[0];

			// fetch the payment details no matter it was successful or it failed
			$fields_to_fetch = [
				'pmt_failure_reason',
				'pmt_failure_msg',
				'pmt_completed_at',
			];
			$pmt_obj = new \eBizIndia\Payment(CONST_INSTAMOJO_CREDS, 'mem_reg', $_POST['recordid']);
			$payment_details = $pmt_obj->getPaymentReqDetails('',[],$fields_to_fetch);

			if($payment_details===false){
				$error=2; // db error
			}else{
				$edit_restricted_fields = [];
				$recorddetails['payment_details'] = $payment_details??[];

				if($recorddetails['status']==='New' || $recorddetails['status']==='Disapproved'){
					// editing other recortds is allowed
					$edit_restricted_fields = []; 
					$can_edit = true;
				}

				if($can_edit===false){
					// $enum_obj = \eBizIndia\enums\MaritalStatus::tryFrom($recorddetails['marital_status']);
					// $recorddetails['marital_status_view'] = !empty($enum_obj)?$enum_obj->label():'';

					$enum_obj = \eBizIndia\enums\Gender::tryFrom($recorddetails['gender']);
					$recorddetails['gender_view'] = !empty($enum_obj)?$enum_obj->label():'';

					$enum_obj = \eBizIndia\enums\RegStatus::tryFrom($recorddetails['status']);
					$recorddetails['status_view'] = !empty($enum_obj)?$enum_obj->label():'';

					if($recorddetails['dob']!='')
						$recorddetails['dob_view'] = date('d-M-Y', strtotime($recorddetails['dob']));
					if($recorddetails['annv']!='')
						$recorddetails['annv_view'] = date('d-M-Y', strtotime($recorddetails['annv']));

					if($recorddetails['blood_grp']!='')
						$recorddetails['blood_grp_view'] = CONST_BLOOD_GRPS[$recorddetails['blood_grp']]??'';

					if($recorddetails['status']!='New')
						$recorddetails['status_remarks_view'] = nl2br(htmlentities($recorddetails['status_remarks']));
				}

				if ($recorddetails['paid_on'] != '') {
					$recorddetails['paid_on_dt'] = date('Y-m-d', strtotime($recorddetails['paid_on']));
					$recorddetails['paid_on_dt_view'] = date('d-M-Y', strtotime($recorddetails['paid_on']));
				} else {
					$recorddetails['paid_on_dt'] = $recorddetails['paid_on_dt_view'] = '';
				}

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

				$tmp = array_column($recorddetails['assigned_sectors'], 'id');
				$recorddetails['assigned_sector_ids'] = empty($tmp)? []: array_map('strval', $tmp);

			}


		}
	}

	$result[0]=$error;
	$result[1]['can_edit'] = $can_edit;
	$result[1]['show_dnd'] = $show_others_dnd_status;
	$result[1]['cuid'] = $loggedindata[0]['id'];  // This is the auto id of the table users and not member
	$result[1]['record_details']=$recorddetails;
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
					$value=\eBizIndia\trim_deep($filter['searchtext']);
				else
					$value='';

				$options['filters'][] = array('field'=>$field,'type'=>$type,'value'=>$value);

				if($field=='name')
					$fltr_text = 'Name ';
				else if($field=='status')
					$fltr_text = 'Status ';
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

				$filtertext[]='<span class="searched_elem"  >'.$fltr_text.'  <b>'.\eBizIndia\_esc($value, true).'</b><span class="remove_filter" data-fld="'.$field.'"  >X</span> </span>';
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
	$tot_rec_cnt = \eBizIndia\Member::getRegistrations($tot_rec_options); 
	$result[1]['tot_rec_cnt'] = $tot_rec_cnt[0]['recordcount'];

	// $recordcount=$usercls->getList($options);
	$recordcount = \eBizIndia\Member::getRegistrations($options);
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

		$records=\eBizIndia\Member::getRegistrations($options);
		
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
// $work_company = \eBizIndia\Helper::getMasterList('work_company');
// if(empty($work_company))
$work_company = [];

$user_levels = []; //$usercls->getUserLevelsList();

$sectors = \eBizIndia\MemberSector::getList();

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
								'work_company' => json_encode($work_company),
								'field_meta' => CONST_FIELD_META,
							);

// $jscode .= "const can_edit = ".($can_edit===true?'true;':'false;').';'.PHP_EOL;
$jscode .= "const country_code = \"".CONST_COUNTRY_CODE.'";'.PHP_EOL;

$additional_base_template_data = array(
										'page_title' => $page_title,
										'page_description' => $page_description,
										'template_type'=>$template_type,
										'dom_ready_code'=>\scriptProviderFuncs\getDomReadyJsCode($page,$dom_ready_data),
										'other_js_code'=>$jscode,
										'module_name' => $page
									);


$additional_body_template_data = ['user_roles'=>$user_roles,   'user_levels'=>$user_levels,  'pg_list'=>$pg_list,'salutation'=>$salutation_list,'default_pswd'=>$default_pswd, 'can_add'=>$can_add, 'country_code'=>CONST_COUNTRY_CODE, 'profile_pic_file_types'=>CONST_FIELD_META['profile_pic']['file_types'], 'allow_export'=>($_cu_role==='ADMIN'?true:false), 'blood_grps' => CONST_BLOOD_GRPS, 'field_meta' => CONST_FIELD_META, 'sectors' => $sectors ];

$page_renderer->updateBodyTemplateData($additional_body_template_data);

$page_renderer->updateBaseTemplateData($additional_base_template_data);
$page_renderer->addCss(\scriptProviderFuncs\getCss($page));
$js_files=\scriptProviderFuncs\getJavascripts($page);
$page_renderer->addJavascript($js_files['BSH'],'BEFORE_SLASH_HEAD');
$page_renderer->addJavascript($js_files['BSB'],'BEFORE_SLASH_BODY');
$page_renderer->renderPage();

?>
