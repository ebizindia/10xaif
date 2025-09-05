<?php
namespace eBizIndia;
// mb_http_output("UTF-8");
// ob_start("mb_output_handler");
// require_once "../config.php";
// error_reporting(E_ALL); ini_set('display_errors',1);
// require_once CONST_INCLUDES_DIR."/ebiz-autoload.php";
// echo 'start<br>';
// PDOConn::connectToDB('mysql');
class Member{
	
	protected $user_acnt_obj;

	public function __construct($user_acnt_obj=null){
		$this->user_acnt_obj = $user_acnt_obj;
	}

	public function getProfile($user_acnt_id){
		if(empty($user_acnt_id))
			return false;
		$options = [];
		$options['filters'] = [
			[ 'field' => 'user_acnt_id', 'type' => 'EQUAL', 'value' => $user_acnt_id ]
		];
		$profile = self::getList($options);
		return $profile;
	} 

	public function validate($data, $mode='add', $other_data=[]){
		$result['error_code'] = 0;
		$restricted_fields = $other_data['edit_restricted_fields']??[];
		$file_upload_errors = [
		    0 => 'There is no error, the file uploaded with success',
		    1 => 'The uploaded file exceeds the allowed max size of '.ini_get('upload_max_filesize'),
		    2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
		    3 => 'The uploaded file was only partially uploaded',
		    4 => 'No file was uploaded',
		    6 => 'Missing a temporary folder',
		    7 => 'Failed to write file to disk.',
		    8 => 'A PHP extension stopped the file upload.',
		];
		if(!in_array('profile_pic', $restricted_fields) && $other_data['profile_pic']['error']>0 && $other_data['profile_pic']['error']!=4){
			$result['error_code']=2;
			$result['message'] = 'Process failed as the profile pic could not be uploaded.'.$file_upload_errors[$other_data['profile_pic']['error']];
			$result['error_fields'][] = '#add_form_field_profilepic';
			
		}else if(!in_array('profile_pic', $restricted_fields) && $other_data['profile_pic']['error']==0){
			$file_ext = strtolower(pathinfo($other_data['profile_pic']['name'], PATHINFO_EXTENSION));
			if(empty($file_ext) || !in_array($file_ext, CONST_FIELD_META['profile_pic']['file_types'])){
				$result['error_code']=2;
				$result['message']="The selected file is not among one of the allowed file types.";
				$result['error_fields'][] = '#add_form_field_profilepic';
				
			}else if(!in_array($other_data['profile_pic']['type'], CONST_MIME_TYPES[$file_ext]??[] )){
				$result['error_code']=2;
				$result['message']="The selected profile image is not a valid file type.";
				$result['error_fields'][] = '#add_form_field_profilepic';
			}
		}

		if($result['error_code'] == 0){
			if(!in_array('title', $restricted_fields) && $data['title'] == ''){
				$result['error_code']=2;
				$result['message'][]="Salutation is required.";
				$result['error_fields'][]="#add_form_field_title";
			}else if(!in_array('fname', $restricted_fields) && $data['fname'] == ''){
				$result['error_code']=2;
				$result['message'][]="First name is required.";
				$result['error_fields'][]="#add_form_field_fname";
			}else if(!in_array('fname', $restricted_fields) && !empty($other_data['field_meta']['name']['regex']) && !preg_match($other_data['field_meta']['name']['regex'], $data['fname'])) {
				$result['error_code']=2;
				$result['message'][]="First name contains invalid characters.";
				$result['error_fields'][]="#add_form_field_fname";
			}else if(!empty($other_data['field_meta']['name']['regex']) && $data['mname']!='' && !preg_match($other_data['field_meta']['name']['regex'], $data['mname'])) {
				$result['error_code']=2;
				$result['message'][]="Middle name contains invalid characters.";
				$result['error_fields'][]="#add_form_field_mname";
			}else if($mode=='reg' &&  !in_array('lname', $restricted_fields) && $data['lname'] == ''){
				$result['error_code']=2;
				$result['message'][]="Surname is required.";
				$result['error_fields'][]="#add_form_field_lname";
			}else if(!empty($other_data['field_meta']['name']['regex']) && $data['lname']!='' && !preg_match($other_data['field_meta']['name']['regex'], $data['lname'])) {
				$result['error_code']=2;
				$result['message'][]="Surname contains invalid characters.";
				$result['error_fields'][]="#add_form_field_lname";
			}else if(!in_array('email', $restricted_fields) && $data['email'] == ''){
				$result['error_code']=2;
				$result['message'][]="Email is required.";
				$result['error_fields'][]="#add_form_field_email";
			}else if(!in_array('email', $restricted_fields) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)){
				$result['error_code']=2;
				$result['message'][]="Please enter a valid email address.";
				$result['error_fields'][]="#add_form_field_email";
			}else if($mode=='reg' &&  !in_array('mobile', $restricted_fields) && $data['mobile']=='') {
				$result['error_code']=2;
				$result['message'][]="WhatsApp number is required.";
				$result['error_fields'][]="#add_form_field_mobile";
			}else if(!in_array('mobile', $restricted_fields) && !empty($other_data['field_meta']['mobile']['regex']) && $data['mobile']!='' && !preg_match($other_data['field_meta']['mobile']['regex'], $data['mobile'])) {
				$result['error_code']=2;
				$result['message'][]="WhatsApp number is invalid.";
				$result['error_fields'][]="#add_form_field_mobile";
			}else if(!in_array('mobile2', $restricted_fields) && !empty($other_data['field_meta']['mobile']['regex']) && $data['mobile2']!='' && !preg_match($other_data['field_meta']['mobile']['regex'], $data['mobile2'])) {
				$result['error_code']=2;
				$result['message'][]="Alternate mobile number is invalid.";
				$result['error_fields'][]="#add_form_field_mobile2";
			}else if($mode=='reg' &&  $data['gender']=='') {
				$result['error_code']=2;
				$result['message'][]="Gender is required.";
				$result['error_fields'][]="#add_form_field_gender_M";
			}else if($data['gender']!='' && !in_array($data['gender'], $other_data['gender']) ) {
				$result['error_code']=2;
				$result['message'][]="Gender is invalid.";
				$result['error_fields'][]="#add_form_field_gender_M";
			}else if($data['blood_grp']!='' && !in_array($data['blood_grp'], $other_data['blood_grps']) ) {
				$result['error_code']=2;
				$result['message'][]="Blood group is invalid.";
				$result['error_fields'][]="#add_form_field_bloodgrp";
			
			}else if($data['dob']!='' && !isDateValid($data['dob'])){
				$result['error_code']=2;
				$result['message'][]="Date of birth is invalid.";
				$result['error_fields'][]="#add_form_field_dob_picker";
			}else if($mode=='reg' &&  $data['residence_city']=='') {
				$result['error_code']=2;
				$result['message'][]="The residence city is required.";
				$result['error_fields'][]="#add_form_field_rescity";
			}else if(!empty($other_data['field_meta']['city']['len']) && $data['residence_city']!='' && mb_strlen($data['residence_city'])>$other_data['field_meta']['city']['len'] ) {
				$result['error_code']=2;
				$result['message'][]="The residence city exceeds the allowed number of characters.";
				$result['error_fields'][]="#add_form_field_rescity";
			}else if(!empty($other_data['field_meta']['state']['len']) && $data['residence_state']!='' && mb_strlen($data['residence_state'])>$other_data['field_meta']['state']['len'] ) {
				$result['error_code']=2;
				$result['message'][]="The residence state exceeds the allowed number of characters.";
				$result['error_fields'][]="#add_form_field_resstate";
			}else if(!empty($other_data['field_meta']['country']['len']) && $data['residence_country']!='' && mb_strlen($data['residence_country'])>$other_data['field_meta']['country']['len'] ) {
				$result['error_code']=2;
				$result['message'][]="The residence country exceeds the allowed number of characters.";
				$result['error_fields'][]="#add_form_field_rescountry";
			}else if(!empty($other_data['field_meta']['pin']['len']) && $data['residence_pin']!='' && mb_strlen($data['residence_pin'])>$other_data['field_meta']['pin']['len'] ) {
				$result['error_code']=2;
				$result['message'][]="The residence pin exceeds the allowed number of characters.";
				$result['error_fields'][]="#add_form_field_respin";
			}else if(!empty($other_data['field_meta']['addrline']['len']) && $data['residence_addrline1']!='' && mb_strlen($data['residence_addrline1'])>$other_data['field_meta']['addrline']['len'] ) {
				$result['error_code']=2;
				$result['message'][]="The residence address line 1 exceeds the allowed number of characters.";
				$result['error_fields'][]="#add_form_field_resaddrline1";
			}else if(!empty($other_data['field_meta']['addrline']['len']) && $data['residence_addrline2']!='' && mb_strlen($data['residence_addrline2'])>$other_data['field_meta']['addrline']['len'] ) {
				$result['error_code']=2;
				$result['message'][]="The residence address line 2 exceeds the allowed number of characters.";
				$result['error_fields'][]="#add_form_field_resaddrline2";
			}else if(!empty($other_data['field_meta']['addrline']['len']) && $data['residence_addrline3']!='' && mb_strlen($data['residence_addrline3'])>$other_data['field_meta']['addrline']['len'] ) {
				$result['error_code']=2;
				$result['message'][]="The residence address line 3 exceeds the allowed number of characters.";
				$result['error_fields'][]="#add_form_field_resaddrline3";
			}else if(!empty($other_data['field_meta']['work_type']['len']) && $data['work_type']!='' && mb_strlen($data['work_type'])>$other_data['field_meta']['work_type']['len'] ) {
				$result['error_code']=2;
				$result['message'][]="The Business/Professional details exceeds the allowed number of characters.";
				$result['error_fields'][]="#add_form_field_worktype";
			}else if(!empty($other_data['field_meta']['work_company']['len']) && $data['work_company']!='' && mb_strlen($data['work_company'])>$other_data['field_meta']['work_company']['len'] ) {
				$result['error_code']=2;
				$result['message'][]="The company name exceeds the allowed number of characters.";
				$result['error_fields'][]="#add_form_field_workcompany";
			}else if(!empty($other_data['field_meta']['city']['len']) && $data['work_city']!='' && mb_strlen($data['work_city'])>$other_data['field_meta']['city']['len'] ) {
				$result['error_code']=2;
				$result['message'][]="The work city exceeds the allowed number of characters.";
				$result['error_fields'][]="#add_form_field_workcity";
			}else if(!empty($other_data['field_meta']['state']['len']) && $data['work_state']!='' && mb_strlen($data['work_state'])>$other_data['field_meta']['state']['len'] ) {
				$result['error_code']=2;
				$result['message'][]="The work state exceeds the allowed number of characters.";
				$result['error_fields'][]="#add_form_field_workstate";
			}else if(!empty($other_data['field_meta']['country']['len']) && $data['work_country']!='' && mb_strlen($data['work_country'])>$other_data['field_meta']['country']['len'] ) {
				$result['error_code']=2;
				$result['message'][]="The work country exceeds the allowed number of characters.";
				$result['error_fields'][]="#add_form_field_workcountry";
			}else if(!empty($other_data['field_meta']['pin']['len']) && $data['work_pin']!='' && mb_strlen($data['work_pin'])>$other_data['field_meta']['pin']['len'] ) {
				$result['error_code']=2;
				$result['message'][]="The work pin exceeds the allowed number of characters.";
				$result['error_fields'][]="#add_form_field_workpin";
			}else if(!empty($other_data['field_meta']['addrline']['len']) && $data['work_addrline1']!='' && mb_strlen($data['work_addrline1'])>$other_data['field_meta']['addrline']['len'] ) {
				$result['error_code']=2;
				$result['message'][]="The work address line 1 exceeds the allowed number of characters.";
				$result['error_fields'][]="#add_form_field_workaddrline1";
			}else if(!empty($other_data['field_meta']['addrline']['len']) && $data['work_addrline2']!='' && mb_strlen($data['work_addrline2'])>$other_data['field_meta']['addrline']['len'] ) {
				$result['error_code']=2;
				$result['message'][]="The work address line 2 exceeds the allowed number of characters.";
				$result['error_fields'][]="#add_form_field_workaddrline2";
			}else if(!empty($other_data['field_meta']['addrline']['len']) && $data['work_addrline3']!='' && mb_strlen($data['work_addrline3'])>$other_data['field_meta']['addrline']['len'] ) {
				$result['error_code']=2;
				$result['message'][]="The work address line 3 exceeds the allowed number of characters.";
				$result['error_fields'][]="#add_form_field_workaddrline3";
			}else if($mode!=='reg' && $mode!=='regupdate' && !empty($other_data['field_meta']['mem_no']['regex']) && $data['membership_no']!='' && !preg_match($other_data['field_meta']['mem_no']['regex'], $data['membership_no'])) {
				$result['error_code']=2;
				$result['message'][]="Membership number in invalid.";
				$result['error_fields'][]="#add_form_field_memno";
			}else if($mode!=='reg' && $mode!=='regupdate' && !empty($other_data['field_meta']['mem_no']['len']) && $data['membership_no']!='' && mb_strlen($data['membership_no'])> $other_data['field_meta']['mem_no']['len'] ) {
				$result['error_code']=2;
				$result['message'][]="Membership number exceeds the allowed number of characters.";
				$result['error_fields'][]="#add_form_field_memno";
			}else if($mode!=='reg' && $mode!=='regupdate' && !in_array('role', $restricted_fields) && $data['role']==''){
				$result['error_code']=2;
				$result['message'][]="Role is required.";
				$result['error_fields'][]="#add_form_field_role_REGULAR";
			}else if($mode!=='reg' && $mode!=='regupdate' && !in_array('role', $restricted_fields) && !in_array($data['role'], $other_data['roles'])){
				$result['error_code']=2;
				$result['message'][]="Invalid role selected.";
				$result['error_fields'][]="#add_form_field_role_REGULAR";
			}else if($mode!=='reg' && $mode!=='regupdate' && !empty($data['password']) && !empty($other_data['field_meta']['pswd']['len']) && mb_strlen($data['password'])>!empty($other_data['field_meta']['pswd']['len'])){
				$result['error_code']=2;
				$result['message'][]="The password cannot be longer than ".$other_data['field_meta']['pswd']['len']." characters.";
				$result['error_fields'][]="#add_form_field_password";

			}else if($mode!=='reg' && $mode!=='regupdate' &&  ($mode=='add' || !in_array('status', $restricted_fields) ) &&  $data['status']==''){
				$result['error_code']=2;
				$result['message'][]="Member status is required.";
				$result['error_fields'][]="#add_form_field_status";

			}else if($mode=='reg' &&  $data['ref1_name'] == ''){
				$result['error_code']=2;
				$result['message'][]="The name of your first reference is required.";
				$result['error_fields'][]="#add_form_field_ref1name";
			}else if($mode=='reg' && !empty($other_data['field_meta']['name']['regex']) && !preg_match($other_data['field_meta']['name']['regex'], $data['ref1_name'])) {
				$result['error_code']=2;
				$result['message'][]="The name of your first reference contains invalid characters.";
				$result['error_fields'][]="#add_form_field_ref1name";
			}else if($mode=='reg' &&  $data['ref1_batch']=='') {
				$result['error_code']=2;
				$result['message'][]="The batch number of your first reference is required.";
				$result['error_fields'][]="#add_form_field_ref1batch";
			}else if($mode=='reg' && !empty($other_data['field_meta']['batch_no']['regex']) && !preg_match($other_data['field_meta']['batch_no']['regex'], $data['ref1_batch'])) {
				$result['error_code']=2;
				$result['message'][]="The batch no should be the class X year - between "._esc($other_data['field_meta']['batch_no']['min'], true)." and "._esc($other_data['field_meta']['batch_no']['max'], true);
				$result['error_fields'][]="#add_form_field_ref1batch";
			}else if($mode=='reg' && ($data['ref1_batch']<$other_data['field_meta']['batch_no']['min'] || $data['ref1_batch']>$other_data['field_meta']['batch_no']['max']) ) {
				$result['error_code']=2;
				$result['message'][]="The batch no should be the class X year - between "._esc($other_data['field_meta']['batch_no']['min'], true)." and "._esc($other_data['field_meta']['batch_no']['max'], true);
				$result['error_fields'][]="#add_form_field_ref1batch";
			}else if($mode=='reg' &&  $data['ref1_mobile']=='') {
				$result['error_code']=2;
				$result['message'][]="The mobile number of your first reference is required.";
				$result['error_fields'][]="#add_form_field_ref1mobile";
			}else if($mode=='reg' &&  !empty($other_data['field_meta']['mobile']['regex']) && $data['mobile']!='' && !preg_match($other_data['field_meta']['mobile']['regex'], $data['ref1_mobile'])) {
				$result['error_code']=2;
				$result['message'][]="The mobile number of your first reference is invalid.";
				$result['error_fields'][]="#add_form_field_ref1mobile";
			}else if($mode=='reg' &&  $data['ref2_name'] == ''){
				$result['error_code']=2;
				$result['message'][]="The name of your second reference is required.";
				$result['error_fields'][]="#add_form_field_ref2name";
			}else if($mode=='reg' && !empty($other_data['field_meta']['name']['regex']) && !preg_match($other_data['field_meta']['name']['regex'], $data['ref2_name'])) {
				$result['error_code']=2;
				$result['message'][]="The name of your second reference contains invalid characters.";
				$result['error_fields'][]="#add_form_field_ref2name";
			}else if($mode=='reg' &&  $data['ref2_batch']=='') {
				$result['error_code']=2;
				$result['message'][]="The batch number of your first reference is required.";
				$result['error_fields'][]="#add_form_field_ref2batch";
			}else if($mode=='reg' && !empty($other_data['field_meta']['batch_no']['regex']) && !preg_match($other_data['field_meta']['batch_no']['regex'], $data['ref2_batch'])) {
				$result['error_code']=2;
				$result['message'][]="The batch no should be the class X year - between "._esc($other_data['field_meta']['batch_no']['min'], true)." and "._esc($other_data['field_meta']['batch_no']['max'], true);
				$result['error_fields'][]="#add_form_field_ref2batch";
			}else if($mode=='reg' && ($data['ref2_batch']<$other_data['field_meta']['batch_no']['min'] || $data['ref2_batch']>$other_data['field_meta']['batch_no']['max']) ) {
				$result['error_code']=2;
				$result['message'][]="The batch no should be the class X year - between "._esc($other_data['field_meta']['batch_no']['min'], true)." and "._esc($other_data['field_meta']['batch_no']['max'], true);
				$result['error_fields'][]="#add_form_field_ref2batch";
			}else if($mode=='reg' &&  $data['ref1_mobile']=='') {
				$result['error_code']=2;
				$result['message'][]="The mobile number of your second reference is required.";
				$result['error_fields'][]="#add_form_field_ref2mobile";
			}else if($mode=='reg' &&  !empty($other_data['field_meta']['mobile']['regex']) && $data['mobile']!='' && !preg_match($other_data['field_meta']['mobile']['regex'], $data['ref2_mobile'])) {
				$result['error_code']=2;
				$result['message'][]="The mobile number of your second reference is invalid.";
				$result['error_fields'][]="#add_form_field_ref2mobile";
			}

		}
		return $result;
	}

	public static function getList($options=[]){
		$data = [];
		$fields_mapper = $fields_mapper1 = [];
		$fields_mapper1['*'] = 'T1.*, r.role_name as role, ur.role_id as role_id, g.grp as grp, mg.grp_id as grp_id, sec.sector as sector, msec.sector_id as sector_id';
		$fields_mapper1['id']='T1.id';
		$fields_mapper1['title']='T1.title';
		$fields_mapper1['fname']="T1.fname";
		$fields_mapper1['mname']="T1.mname";
		$fields_mapper1['lname']="T1.lname";
		$fields_mapper1['name']="T1.name";
		$fields_mapper1['email']="T1.email";
		$fields_mapper1['secondary_email']="T1.secondary_email";
		$fields_mapper1['mobile']="T1.mobile";
		$fields_mapper1['mobile2']="T1.mobile2";
		$fields_mapper1['edu_qual']="T1.edu_qual";
		$fields_mapper1['work_company']='T1.work_company';
		$fields_mapper1['designation']='T1.designation';
		$fields_mapper1['linkedin_accnt']='T1.linkedin_accnt';
		$fields_mapper1['x_accnt']='T1.x_accnt';
		$fields_mapper1['fb_accnt']='T1.fb_accnt';
		$fields_mapper1['website']='T1.website';
		$fields_mapper1['gender']="T1.gender";
		$fields_mapper1['blood_grp']="T1.blood_grp";
		$fields_mapper1['dob']="T1.dob";
		$fields_mapper1['annv']="T1.annv";
		$fields_mapper1['spouse_name']="T1.spouse_name";
		$fields_mapper1['spouse_gender']="T1.spouse_gender";
		$fields_mapper1['spouse_dob']="T1.spouse_dob";
		$fields_mapper1['spouse_whatapp']="T1.spouse_whatapp";
		$fields_mapper1['spouse_email']="T1.spouse_email";
		$fields_mapper1['spouse_profession']="T1.spouse_profession";
		$fields_mapper1['spouse_children']="T1.spouse_children";
	    $fields_mapper1['marital_status']="T1.marital_status";
		$fields_mapper1['desig_in_assoc']="T1.desig_in_assoc";
		$fields_mapper1['residence_city']="T1.residence_city";
		$fields_mapper1['residence_state']="T1.residence_state";
		$fields_mapper1['membership_no']="T1.membership_no";
		$fields_mapper1['membership_type']="T1.membership_type";
		$fields_mapper1['batch_no']="T1.batch_no";
		$fields_mapper1['profile_pic']="T1.profile_pic";
		$fields_mapper1['active']="T1.active";
		$fields_mapper1['dnd']="T1.dnd";
		$fields_mapper1['hashtags']="T1.hashtags";
		$fields_mapper1['remarks']="T1.remarks";
		$fields_mapper1['joining_dt']="T1.joining_dt";
		$fields_mapper1['exp_dt']="T1.exp_dt";
		$fields_mapper1['user_acnt_id']="T1.user_acnt_id";
		$fields_mapper1['username']="T1.username";
		$fields_mapper1['user_acnt_status']="T1.user_acnt_status";
		$fields_mapper1['membership_fee']='T1.membership_fee';
		$fields_mapper1['membership_fee_gst_rate']='T1.membership_fee_gst_rate';
		$fields_mapper1['payment_mode']='T1.payment_mode';
		$fields_mapper1['payment_status']='T1.payment_status';
		$fields_mapper1['payment_txn_ref']='T1.payment_txn_ref';
		$fields_mapper1['payment_instrument_type']='T1.payment_instrument_type';
		$fields_mapper1['payment_instrument']='T1.payment_instrument';
		$fields_mapper1['paid_on']='T1.paid_on';
		$fields_mapper1['role']='r.role_name';
		$fields_mapper1['role_id']='ur.role_id';
		$fields_mapper1['grp']='g.grp';
		$fields_mapper1['grp_id']='mg.grp_id';
		$fields_mapper1['sector']='sec.sector';
		$fields_mapper1['sector_id']='msec.sector_id';
		
		


		$fields_mapper['*'] = "mem.id as id, COALESCE(mem.title,'') as title, COALESCE(mem.fname,'') as fname, COALESCE(mem.mname,'') as mname, COALESCE(mem.lname,'') as lname, mem.name as name, mem.email as email, mem.secondary_email as secondary_email, COALESCE(mem.mobile, '') as mobile, COALESCE(mem.mobile2, '') as mobile2, COALESCE(mem.edu_qual, '') as edu_qual, COALESCE(mem.linkedin_accnt, '') as linkedin_accnt, COALESCE(mem.x_accnt, '') as x_accnt, COALESCE(mem.fb_accnt, '') as fb_accnt, COALESCE(mem.website, '') as website, COALESCE(mem.gender, '') as gender, COALESCE(mem.blood_grp, '') as blood_grp, COALESCE(mem.dob, '') as dob, COALESCE(mem.annv, '') as annv, COALESCE(mem.marital_status, '') as marital_status, COALESCE(mem.desig_in_assoc, '') as desig_in_assoc, COALESCE(mem.residence_city,'') as residence_city, COALESCE(mem.residence_state,'') as residence_state, COALESCE(mem.residence_country,'') as residence_country, COALESCE(mem.residence_pin,'') as residence_pin, COALESCE(mem.residence_addrline1,'') as residence_addrline1, COALESCE(mem.residence_addrline2,'') as residence_addrline2, COALESCE(mem.residence_addrline3,'') as residence_addrline3, COALESCE(mem.residence_phone,'') as residence_phone, COALESCE(mem.residence_fax,'') as residence_fax, COALESCE(mem.work_type,'') as work_type, COALESCE(mem.work_ind,'') as work_ind, COALESCE(mem.work_company,'') as work_company, COALESCE(mem.designation,'') as designation, COALESCE(mem.work_city,'') as work_city, COALESCE(mem.work_state,'') as work_state, COALESCE(mem.work_country,'') as work_country, COALESCE(mem.work_pin,'') as work_pin, COALESCE(mem.work_addrline1,'') as work_addrline1, COALESCE(mem.work_addrline2,'') as work_addrline2, COALESCE(mem.work_addrline3,'') as work_addrline3, COALESCE(mem.work_phone,'') as work_phone,  COALESCE(mem.work_phoneepabx,'') as work_phoneepabx,  COALESCE(mem.work_fax,'') as work_fax, COALESCE(mem.work_secretary_name,'') as work_secretary_name, COALESCE(mem.work_secretary_mobile,'') as work_secretary_mobile, COALESCE(mem.work_secretary_email,'') as work_secretary_email, COALESCE(mem.membership_no,'') as membership_no, COALESCE(mem.membership_type,'') as membership_type, COALESCE(mem.batch_no,'') as batch_no, COALESCE(mem.profile_pic,'') as profile_pic, mem.active as active, mem.dnd as dnd, COALESCE(mem.hashtags,'') as hashtags, u.id as user_acnt_id, u.username as username, u.status as user_acnt_status, COALESCE(mem.joining_dt,'') as joining_dt, COALESCE(mem.membership_fee, '') as membership_fee, COALESCE(mem.membership_fee_gst_rate, '') as membership_fee_gst_rate, COALESCE(mem.payment_mode, '') as payment_mode, COALESCE(mem.payment_status, '') as payment_status, COALESCE(mem.payment_txn_ref, '') as payment_txn_ref, COALESCE(mem.payment_instrument_type, '') as payment_instrument_type, COALESCE(mem.payment_instrument, '') as payment_instrument, COALESCE(mem.paid_on, '') as paid_on, COALESCE(mem.remarks,'') as remarks, COALESCE(mem.spouse_name, '') as spouse_name, COALESCE(mem.spouse_gender, '') as spouse_gender, COALESCE(mem.spouse_dob, '') as spouse_dob, COALESCE(mem.spouse_whatapp, '') as spouse_whatapp, COALESCE(mem.spouse_email, '') as spouse_email, COALESCE(mem.spouse_profession, '') as spouse_profession, COALESCE(mem.spouse_children, '') as spouse_children ,COALESCE(mem.exp_dt, '') as exp_dt ";



		$fields_mapper['recordcount']='count(distinct(mem.id))';
		$fields_mapper['id']="mem.id";
		$fields_mapper['title']='COALESCE(mem.title,"")';
		$fields_mapper['fname']='COALESCE(mem.fname,"")';
		$fields_mapper['mname']='COALESCE(mem.mname, "")';
		$fields_mapper['lname']='COALESCE(mem.lname, "")';
		$fields_mapper['name']='mem.name';
		$fields_mapper['email']='mem.email';
		$fields_mapper['secondary_email']='mem.secondary_email';
		$fields_mapper['mobile']='COALESCE(mem.mobile, "")';
		$fields_mapper['mobile2']='COALESCE(mem.mobile2, "")';
		$fields_mapper['edu_qual']='COALESCE(mem.edu_qual, "")';
		$fields_mapper['linkedin_accnt']='COALESCE(mem.linkedin_accnt, "")';
		$fields_mapper['x_accnt']='COALESCE(mem.x_accnt, "")';
		$fields_mapper['fb_accnt']='COALESCE(mem.fb_accnt, "")';
		$fields_mapper['website']='COALESCE(mem.website, "")';
		$fields_mapper['gender']='COALESCE(mem.gender, "")';
		$fields_mapper['blood_grp']='COALESCE(mem.blood_grp, "")';
		$fields_mapper['dob']='COALESCE(mem.dob, "")';
		$fields_mapper['annv']='COALESCE(mem.annv, "")';
		$fields_mapper['spouse_name']='COALESCE(mem.spouse_name, "")';
		$fields_mapper['spouse_gender']='COALESCE(mem.spouse_gender, "")';
		$fields_mapper['spouse_dob']='COALESCE(mem.spouse_dob, "")';
		$fields_mapper['spouse_whatapp']='COALESCE(mem.spouse_whatapp, "")';
		$fields_mapper['spouse_email']='COALESCE(mem.spouse_email, "")';
		$fields_mapper['spouse_profession']='COALESCE(mem.spouse_profession, "")';
		$fields_mapper['spouse_children']='COALESCE(mem.spouse_children, "")';
		$fields_mapper['marital_status']='COALESCE(mem.marital_status, "")';
		$fields_mapper['desig_in_assoc']='COALESCE(mem.desig_in_assoc, "")';
		$fields_mapper['residence_city']='COALESCE(mem.residence_city,"")';
		$fields_mapper['residence_state']='COALESCE(mem.residence_state,"")';
		$fields_mapper['residence_pin']='COALESCE(mem.residence_pin,"")';
		$fields_mapper['residence_country']='COALESCE(mem.residence_country,"")';
		$fields_mapper['residence_addrline1']='COALESCE(mem.residence_addrline1,"")';
		$fields_mapper['residence_addrline2']='COALESCE(mem.residence_addrline2,"")';
		$fields_mapper['residence_addrline3']='COALESCE(mem.residence_addrline3,"")';
		$fields_mapper['residence_fax']='COALESCE(mem.residence_fax,"")';
		$fields_mapper['residence_phone']='COALESCE(mem.residence_phone,"")';
		$fields_mapper['work_city']='COALESCE(mem.work_city,"")';
		$fields_mapper['work_state']='COALESCE(mem.work_state,"")';
		$fields_mapper['work_pin']='COALESCE(mem.work_pin,"")';
		$fields_mapper['work_country']='COALESCE(mem.work_country,"")';
		$fields_mapper['work_addrline1']='COALESCE(mem.work_addrline1,"")';
		$fields_mapper['work_addrline2']='COALESCE(mem.work_addrline2,"")';
		$fields_mapper['work_addrline3']='COALESCE(mem.work_addrline3,"")';
		$fields_mapper['work_phone']='COALESCE(mem.work_phone,"")';
		$fields_mapper['work_phoneepabx']='COALESCE(mem.work_phoneepabx,"")';
		$fields_mapper['work_fax']='COALESCE(mem.work_fax,"")';
		$fields_mapper['work_secretary_name']='COALESCE(mem.work_secretary_name,"")'; 
		$fields_mapper['work_secretary_mobile']='COALESCE(mem.work_secretary_mobile,"")'; 
		$fields_mapper['work_secretary_email']='COALESCE(mem.work_secretary_email,"")'; 
		$fields_mapper['work_type']='COALESCE(mem.work_type,"")';
		$fields_mapper['work_ind']='COALESCE(mem.work_ind,"")';
		$fields_mapper['work_company']='COALESCE(mem.work_company,"")';
		$fields_mapper['designation']='COALESCE(mem.designation,"")';
		$fields_mapper['membership_no']='COALESCE(mem.membership_no,"")';
		$fields_mapper['membership_type']='COALESCE(mem.membership_type,"")';
		$fields_mapper['batch_no']='COALESCE(mem.batch_no,"")';
		$fields_mapper['profile_pic']='COALESCE(mem.profile_pic,"")';
		$fields_mapper['active']='mem.active';
		$fields_mapper['dnd']='mem.dnd';
		$fields_mapper['hashtags']='COALESCE(mem.hashtags, "")';
		$fields_mapper['remarks']='COALESCE(mem.remarks, "")';
		$fields_mapper['joining_dt']='COALESCE(mem.joining_dt, "")';
		$fields_mapper['exp_dt']='COALESCE(mem.exp_dt, "")';
		$fields_mapper['user_acnt_id']='u.id';
		$fields_mapper['username']='u.username';
		$fields_mapper['user_acnt_status']='u.status';
		$fields_mapper['membership_fee']='COALESCE(mem.membership_fee,"")';
		$fields_mapper['membership_fee_gst_rate']='COALESCE(mem.membership_fee_gst_rate,"")';
		$fields_mapper['payment_mode']='COALESCE(mem.payment_mode,"")';
		$fields_mapper['payment_status']='COALESCE(mem.payment_status,"")';
		$fields_mapper['payment_txn_ref']='COALESCE(mem.payment_txn_ref,"")';
		$fields_mapper['payment_instrument_type']='COALESCE(mem.payment_instrument_type,"")';
		$fields_mapper['payment_instrument']='COALESCE(mem.payment_instrument,"")';
		$fields_mapper['paid_on']='COALESCE(mem.paid_on,"")';
		
		$where_clause=[];

		$str_params_to_bind=[];
		$int_params_to_bind=[];

		if( array_key_exists('filters',$options) && is_array($options['filters']) ){
			$field_counter=0;
			foreach($options['filters'] as $filter){
				++$field_counter;
				switch ($filter['field']) {
					case 'batch_no':
					case 'user_acnt_id':
					case 'id':
						switch($filter['type']){
							case 'IN':
								if(is_array($filter['value'])){
									$place_holders=[];
									$k=0;
									foreach($filter['value'] as $userid){
										$k++;
										$place_holders[]=":whr".$field_counter."_userid_{$k}_";
										$int_params_to_bind[":whr".$field_counter."_userid_{$k}_"]=$userid;
									}
									$where_clause[] = $fields_mapper[$filter['field']].' in('.implode(',',$place_holders).') ';

								}
								break;

							case 'NOT_IN':
								if(is_array($filter['value'])){
									$place_holders=[];
									$k=0;
									foreach($filter['value'] as $userid){
										$k++;
										$place_holders[]=":whr".$field_counter."_userid_{$k}_";
										$int_params_to_bind[":whr".$field_counter."_userid_{$k}_"]=$userid;
									}
									$where_clause[] = $fields_mapper[$filter['field']].' not in('.implode(',',$place_holders).') ';

								}
								break;

							case 'NOT_EQUAL':
								$userid=(is_array($filter['value']))?$filter['value'][0]:$filter['value'];
								$where_clause[] = $fields_mapper[$filter['field']].'!=:whr'.$field_counter.'_userid';
								$int_params_to_bind[':whr'.$field_counter.'_userid']=$userid;
								break;

							default:
								$userid=(is_array($filter['value']))?$filter['value'][0]:$filter['value'];
								$where_clause[] = $fields_mapper[$filter['field']].'=:whr'.$field_counter.'_userid';
								$int_params_to_bind[':whr'.$field_counter.'_userid']=$userid;
						}

						break;



					case 'role_id':
						switch($filter['type']){
							case 'IN':
								if(is_array($filter['value'])){
									$place_holders=[];
									$k=0;
									foreach($filter['value'] as $roleid){
										$k++;
										$place_holders[]=":whr".$field_counter."_roleid_{$k}_";
										$int_params_to_bind[":whr".$field_counter."_roleid_{$k}_"]=$roleid;
									}
									$where_clause[]=' ur1.role_id in('.implode(',',$place_holders).') ';

								}
								break;

							case 'NOT_IN':
								if(is_array($filter['value'])){
									$place_holders=[];
									$k=0;
									foreach($filter['value'] as $roleid){
										$k++;
										$place_holders[]=":whr".$field_counter."_roleid_{$k}_";
										$int_params_to_bind[":whr".$field_counter."_roleid_{$k}_"]=$roleid;
									}
									$where_clause[]=' ur1.role_id not in('.implode(',',$place_holders).') ';

								}
								break;

							case 'NOT_EQUAL':
								$roleid=(is_array($filter['value']))?$filter['value'][0]:$filter['value'];
								$where_clause[]=' ur1.role_id!=:whr'.$field_counter.'_roleid';
								$int_params_to_bind[':whr'.$field_counter.'_roleid']=$roleid;
								break;

							default:
								$roleid=(is_array($filter['value']))?$filter['value'][0]:$filter['value'];
								$where_clause[]=' ur1.role_id=:whr'.$field_counter.'_roleid';
								$int_params_to_bind[':whr'.$field_counter.'_roleid']=$roleid;
						}

						break;

					case 'grp_id':
						switch($filter['type']){
							case 'IN':
								if(is_array($filter['value'])){
									$place_holders=[];
									$k=0;
									foreach($filter['value'] as $grp_id){
										$k++;
										$place_holders[]=":whr".$field_counter."_grpid_{$k}_";
										$int_params_to_bind[":whr".$field_counter."_grpid_{$k}_"]=$grp_id;
									}
									$where_clause[]=' mg1.grp_id in('.implode(',',$place_holders).') ';

								}
								break;

							case 'NOT_IN':
								if(is_array($filter['value'])){
									$place_holders=[];
									$k=0;
									foreach($filter['value'] as $grp_id){
										$k++;
										$place_holders[]=":whr".$field_counter."_grpid_{$k}_";
										$int_params_to_bind[":whr".$field_counter."_grpid_{$k}_"]=$grp_id;
									}
									$where_clause[]=' mg1.grp_id not in('.implode(',',$place_holders).') ';

								}
								break;

							case 'NOT_EQUAL':
								$grp_id=(is_array($filter['value']))?$filter['value'][0]:$filter['value'];
								$where_clause[]=' mg1.grp_id!=:whr'.$field_counter.'_grpid';
								$int_params_to_bind[':whr'.$field_counter.'_grpid']=$grp_id;
								break;

							default:
								$grp_id=(is_array($filter['value']))?$filter['value'][0]:$filter['value'];
								$where_clause[]=' mg1.grp_id=:whr'.$field_counter.'_grpid';
								$int_params_to_bind[':whr'.$field_counter.'_grpid']=$grp_id;
						}

						break;

					case 'grp':
						$grp=(is_array($filter['value']))?$filter['value'][0]:$filter['value'];
						$where_clause[] = " g1.grp like :whr".$field_counter."_grp_";
						switch($filter['type']){
							case 'CONTAINS':
								$str_params_to_bind[':whr'.$field_counter.'_grp_']="%$grp%";
								break;
							case 'STARTS_WITH':
								$str_params_to_bind[':whr'.$field_counter.'_grp_']="$grp%";
								break;
							case 'ENDS_WITH':
								$str_params_to_bind[':whr'.$field_counter.'_grp_']="%$grp";
								break;
							case 'EQUAL':
							default:
								$str_params_to_bind[':whr'.$field_counter.'_grp_']="$grp";
								break;
						}

						break;	

					case 'sector_id':
						switch($filter['type']){
							case 'IN':
								if(is_array($filter['value'])){
									$place_holders=[];
									$k=0;
									foreach($filter['value'] as $sector_id){
										$k++;
										$place_holders[]=":whr".$field_counter."_sectorid_{$k}_";
										$int_params_to_bind[":whr".$field_counter."_sectorid_{$k}_"]=$sector_id;
									}
									$where_clause[]=' msec1.sector_id in('.implode(',',$place_holders).') ';

								}
								break;

							case 'NOT_IN':
								if(is_array($filter['value'])){
									$place_holders=[];
									$k=0;
									foreach($filter['value'] as $sector_id){
										$k++;
										$place_holders[]=":whr".$field_counter."_sectorid_{$k}_";
										$int_params_to_bind[":whr".$field_counter."_sectorid_{$k}_"]=$sector_id;
									}
									$where_clause[]=' msec1.sector_id not in('.implode(',',$place_holders).') ';

								}
								break;

							case 'NOT_EQUAL':
								$sector_id=(is_array($filter['value']))?$filter['value'][0]:$filter['value'];
								$where_clause[]=' msec1.sector_id!=:whr'.$field_counter.'_sectorid';
								$int_params_to_bind[':whr'.$field_counter.'_sectorid']=$sector_id;
								break;

							default:
								$sector_id=(is_array($filter['value']))?$filter['value'][0]:$filter['value'];
								$where_clause[]=' msec1.sector_id=:whr'.$field_counter.'_sectorid';
								$int_params_to_bind[':whr'.$field_counter.'_sectorid']=$sector_id;
						}

						break;

					case 'sector':
						$sector=(is_array($filter['value']))?$filter['value'][0]:$filter['value'];
						$where_clause[] = " sec1.sector like :whr".$field_counter."_sector_";
						switch($filter['type']){
							case 'CONTAINS':
								$str_params_to_bind[':whr'.$field_counter.'_sector_']="%$sector%";
								break;
							case 'STARTS_WITH':
								$str_params_to_bind[':whr'.$field_counter.'_sector_']="$sector%";
								break;
							case 'ENDS_WITH':
								$str_params_to_bind[':whr'.$field_counter.'_sector_']="%$sector";
								break;
							case 'EQUAL':
							default:
								$str_params_to_bind[':whr'.$field_counter.'_sector_']="$sector";
								break;
						}

						break;	

					case 'blood_grp':
					case 'email':
					case 'secondary_email':
					case 'name':
					case 'membership_no':
					case 'mobile':
					case 'mobile2':
					case 'residence_city':
					case 'residence_country':
					case 'work_company':
					case 'work_type':
						$fld = in_array($filter['field'], ['blood_grp', 'mobile', 'mobile2', 'residence_city', 'residence_country', 'work_company', 'work_type', 'membership_no'])?$filter['field']:$fields_mapper[$filter['field']];
						if(($filter['type']=='IN' || $filter['type']=='NOT_IN') && is_array($filter['value'])){
							$place_holders=[];
							$k=0;
							foreach($filter['value'] as $srch_val){
								$k++;
								$place_holders[]=":whr".$field_counter."_srch_{$k}_";
								$str_params_to_bind[":whr".$field_counter."_srch_{$k}_"]=$srch_val;
							}
							if($filter['type']=='NOT_IN'){
								$where_clause[] = ' ( '.$fld.' is NULL OR '.$fld.' not in('.implode(',',$place_holders).') ) ';
							}else	
								$where_clause[] = $fld.' in('.implode(',',$place_holders).') ';
						}else{
							$nm=(is_array($filter['value']))?$filter['value'][0]:$filter['value'];
							$where_clause[] = $fld." like :whr".$field_counter."_nm";
							switch($filter['type']){
								case 'CONTAINS':
									$str_params_to_bind[':whr'.$field_counter.'_nm']="%$nm%";
									break;
								case 'STARTS_WITH':
									$str_params_to_bind[':whr'.$field_counter.'_nm']="$nm%";
									break;
								case 'ENDS_WITH':
									$str_params_to_bind[':whr'.$field_counter.'_nm']="%$nm";
									break;
								case 'EQUAL':
								default:
									$str_params_to_bind[':whr'.$field_counter.'_nm']="$nm";
									break;
							}
							
						}


						break;

					case 'joining_dt':
						$dt=(is_array($filter['value']))?$filter['value'][0]:$filter['value'];
						$fld = 'joining_dt';
						switch($filter['type']){
							case 'AFTER':
								$where_clause[] = $fld." > :whr".$field_counter."_jdt";	
								$str_params_to_bind[':whr'.$field_counter.'_jdt'] = $dt;
								break;
							case 'BEFORE':
								$where_clause[] = $fld." < :whr".$field_counter."_jdt";	
								$str_params_to_bind[':whr'.$field_counter.'_jdt'] = $dt;
								break;
							case 'EQUAL':
							default:
								$where_clause[] = $fld." = :whr".$field_counter."_jdt";	
								$str_params_to_bind[':whr'.$field_counter.'_jdt'] = $dt;
								break;
						}

						break;


					case 'mob': // both mobile fields
						$mob=(is_array($filter['value']))?$filter['value'][0]:$filter['value'];
						$where_clause[] = " ( mem.mobile like :whr".$field_counter."_mob OR mem.mobile2 like :whr".$field_counter."_mob )";
						switch($filter['type']){
							case 'CONTAINS':
								$str_params_to_bind[':whr'.$field_counter.'_mob']="%$mob%";
								break;
							case 'STARTS_WITH':
								$str_params_to_bind[':whr'.$field_counter.'_mob']="$mob%";
								break;
							case 'ENDS_WITH':
								$str_params_to_bind[':whr'.$field_counter.'_mob']="%$mob";
								break;
							case 'EQUAL':
							default:
								$str_params_to_bind[':whr'.$field_counter.'_mob']="$mob";
								break;
						}

						break;	

					case 'role':
						$role=(is_array($filter['value']))?$filter['value'][0]:$filter['value'];
						$where_clause[] = " r1.role_name like :whr".$field_counter."_role";
						switch($filter['type']){
							case 'CONTAINS':
								$str_params_to_bind[':whr'.$field_counter.'_role']="%$role%";
								break;
							case 'STARTS_WITH':
								$str_params_to_bind[':whr'.$field_counter.'_role']="$role%";
								break;
							case 'ENDS_WITH':
								$str_params_to_bind[':whr'.$field_counter.'_role']="%$role";
								break;
							case 'EQUAL':
							default:
								$str_params_to_bind[':whr'.$field_counter.'_role']="$role";
								break;
						}

						break;


					case 'dnd':
					case 'active':
						$status=(is_array($filter['value']))?$filter['value'][0]:$filter['value'];
						switch($filter['type']){
							case 'NOT_EQUAL':
								$where_clause[] = $fields_mapper[$filter['field']].' !=:whr'.$field_counter.'_active';
								$str_params_to_bind[':whr'.$field_counter.'_active']=$status;
								break;
							default:

								$where_clause[] = $fields_mapper[$filter['field']].'=:whr'.$field_counter.'_active';
								$str_params_to_bind[':whr'.$field_counter.'_active']=$status;
						}

						break;

					case 'active_dndno_or_id':
						$id_clause = '';
						if(is_array($filter['value'])){
							$place_holders=[];
							$k=0;
							foreach($filter['value'] as $userid){
								$k++;
								$place_holders[]=":whr".$field_counter."_userid_{$k}_";
								$int_params_to_bind[":whr".$field_counter."_userid_{$k}_"]=$userid;
								$id_clause = ' OR '.$fields_mapper['id'].' in('.implode(',',$place_holders).')';
							}
						}
						$where_clause[] = ' ( ('.$fields_mapper['active'].'="y" AND '.$fields_mapper['dnd'].'="n" ) '.$id_clause.' ) ';	
						break;

					// case 'multi_field_search':
					// 	$search_text = (is_array($filter['value']))?$filter['value'][0]:$filter['value'];
					// 	$where_clause[] = " (MATCH (mem.membership_no,mem.fname,mem.mname,mem.lname,mem.email,mem.mobile,mem.mobile2,mem.desig_in_assoc,mem.residence_city,mem.work_city,mem.work_type,mem.work_ind,mem.work_company,mem.blood_grp) AGAINST (:whr".$field_counter."_multifield IN NATURAL LANGUAGE MODE)) ";	
					// 	$str_params_to_bind[':whr'.$field_counter.'_multifield'] = $search_text;
					// 	break;


				}

			}


		}

		$select_string=$fields_mapper1['*'];
		$select_string_subquery=$fields_mapper['*'];

		if(array_key_exists('fieldstofetch', $options) && is_array($options['fieldstofetch'])){
			$fields_to_fetch_count=count($options['fieldstofetch']);

			if($fields_to_fetch_count>0){
				$selected_fields=array();

				if(in_array('recordcount', $options['fieldstofetch'])){
					$record_count=true;
				}else{

					if(!in_array('*',$options['fieldstofetch'])){
						if(!in_array('id',$options['fieldstofetch'])){ // This is required as the id is being used for table joining
							$options['fieldstofetch'][]='id';
							$fields_to_fetch_count+=1; // increment the count by 1 to include this column
						}

					}

				}

				for($i=0; $i<$fields_to_fetch_count; $i++){
					if(array_key_exists($options['fieldstofetch'][$i],$fields_mapper1)){
						$selected_fields[]=$fields_mapper1[$options['fieldstofetch'][$i]].(($options['fieldstofetch'][$i]!='*')?' as '.$options['fieldstofetch'][$i]:'');

					}

					if(array_key_exists($options['fieldstofetch'][$i],$fields_mapper)){
						$selected_fields_subquery[]=$fields_mapper[$options['fieldstofetch'][$i]].(($options['fieldstofetch'][$i]!='*')?' as '.$options['fieldstofetch'][$i]:'');

					}

				}

				if(count($selected_fields)>0){
					$select_string=implode(', ',$selected_fields);

				}

				if(count($selected_fields_subquery)>0){
					$select_string_subquery=implode(', ',$selected_fields_subquery);

				}


			}
		}

		$select_string_subquery=($record_count)?$select_string_subquery:'distinct '.$select_string_subquery;
		$group_by_clause='';
		if(array_key_exists('group_by', $options) && is_array($options['group_by'])){
			foreach ($options['group_by'] as $field) {
				if(preg_match("/^(mem|u|r|ur|r1|ur1|g|mg|g1|mg1|sec|msec|sec1|msec1)\./",$fields_mapper[$field]))
					$group_by_clause.=", ".$fields_mapper[$field];
				else
					$group_by_clause.=", $field";
			}

			$group_by_clause=trim($group_by_clause,",");
			if($group_by_clause!=''){
				$group_by_clause=' GROUP BY '.$group_by_clause;

			}
		}

		$order_by_clause = $order_by_clause_outer = ''; // $order_by_clause_outer is required to preserver the subquery's order

		if(array_key_exists('order_by', $options) && is_array($options['order_by'])){
			foreach ($options['order_by'] as $order) {
				if(preg_match("/^(mem|u|r|ur|r1|ur1|g|mg|g1|mg1|sec|msec|sec1|msec1)\./",$fields_mapper[$order['field']])){
					$order_by_clause.=", ".$fields_mapper[$order['field']];

					if(!$record_count){
						if(!preg_match("/,?\s*".str_replace('.', "\.", $fields_mapper[$order['field']])."/",$select_string_subquery))
							$select_string_subquery .= ", ".$fields_mapper[$order['field']]. ' as '.$order['field'];

						$order_by_clause_outer.=", ".$fields_mapper1[$order['field']];
					}

				}else if(array_key_exists($order['field'], $fields_mapper)){
					if(!preg_match("/\s*as\s*".$order['field']."/",$select_string_subquery))
						$select_string_subquery .= ", ".$fields_mapper[$order['field']].' as '.$order['field'];

					$order_by_clause.=", ".$order['field'];
					$order_by_clause_outer.=", ".$fields_mapper1[$order['field']];


				}else if(array_key_exists($order['field'], $fields_mapper1)){

					$order_by_clause_outer.=", ".$fields_mapper1[$order['field']];


				}

				if(array_key_exists('type', $order) && $order['type']=='DESC'){
					$order_by_clause.=' DESC';
					$order_by_clause_outer.=' DESC';
				}

			}

			$order_by_clause=trim($order_by_clause,",");
			$order_by_clause_outer=trim($order_by_clause_outer,",");
			if($order_by_clause!=''){
				$order_by_clause=' ORDER BY '.$order_by_clause;

			}

			if($order_by_clause_outer!=''){
				$order_by_clause_outer=' ORDER BY '.$order_by_clause_outer;

			}

			// user ID is a unique value across all the users so to maintain a unique order across queries with the same set of order by clauses we can include this field as the last field in the order by clause.
			if($order_by_clause!='' && !stristr($order_by_clause, 'mem.id')){

				$order_by_clause .= ', '.$fields_mapper['id'].' DESC ';
				$order_by_clause_outer .= ', '.$fields_mapper1['id']. ' DESC ';
			}


		}

		

		if(!$record_count && $order_by_clause==''){

			$order_by_clause=" ORDER BY mem.name ASC, mem.id ASC ";

			if(!preg_match("/\s+as\s+name/",$select_string_subquery)){
				$select_string_subquery .= ', '.$fields_mapper['name'].' as name';
				$select_string .= ', '.$fields_mapper1['name'].' as name';
			}
			if(!preg_match("/,?\s+mem\.id/",$select_string_subquery)){
				$select_string_subquery .= ', '.$fields_mapper['id'].' as id';
				$select_string .= ', '.$fields_mapper1['id'].' as id';
			}

			if($order_by_clause_outer == '')
				$order_by_clause_outer=" ORDER BY T1.name ASC, T1.id ASC ";

		}

		$limit_clause='';

		if(array_key_exists('page', $options) && filter_var($options['page'],FILTER_VALIDATE_INT) && $options['page']>0 && array_key_exists('recs_per_page', $options) && filter_var($options['recs_per_page'],FILTER_VALIDATE_INT) && $options['recs_per_page']>0){

			$limit_clause="LIMIT ".( ($options['page']-1) * $options['recs_per_page'] ).", $options[recs_per_page] ";

		}

		$where_clause_string = '';
		if(!empty($where_clause))
			$where_clause_string = ' WHERE '.implode(' AND ', $where_clause);

		$role_join = '';
		if(preg_match("/(r1|ur1)\./","$select_string_subquery $where_clause_string $group_by_clause $order_by_clause"))
			$role_join .= " JOIN `".CONST_TBL_PREFIX."user_roles` ur1 ON u.id = ur1.user_id JOIN  `".CONST_TBL_PREFIX."roles` r1 ON r1.role_id=ur1.role_id ";

		$grp_join = '';
		if(preg_match("/(g1|mg1)\./","$select_string_subquery $where_clause_string $group_by_clause $order_by_clause"))
			$grp_join .= " LEFT JOIN `".CONST_TBL_PREFIX."member_groups` mg1 ON mem.id = mg1.mem_id LEFT JOIN  `".CONST_TBL_PREFIX."groups` g1 ON g1.id=mg1.grp_id ";

		$sector_join = '';
		if(preg_match("/(sec1|msec1)\./","$select_string_subquery $where_clause_string $group_by_clause $order_by_clause"))
			$sector_join .= " LEFT JOIN `".CONST_TBL_PREFIX."member_sectors` msec1 ON mem.id = msec1.mem_id LEFT JOIN  `".CONST_TBL_PREFIX."sectors` sec1 ON sec1.id=msec1.sector_id ";

		

		$sql="SELECT $select_string_subquery from `".CONST_TBL_PREFIX."members` as mem LEFT JOIN `".CONST_TBL_PREFIX."users` as u ON mem.id=u.profile_id and u.profile_type='member' $role_join $grp_join $sector_join  $where_clause_string $group_by_clause $order_by_clause $limit_clause";

		if(empty($record_count)){
			$sql="SELECT $select_string from ($sql) as T1 ";
			if(preg_match("/(r|ur)\./",$select_string))
				$sql .= " LEFT JOIN `".CONST_TBL_PREFIX."user_roles` ur ON T1.user_acnt_id = ur.user_id JOIN  `".CONST_TBL_PREFIX."roles` r ON r.role_id=ur.role_id"; 

			if(preg_match("/(g|mg)\./",$select_string))
				$sql .= " LEFT JOIN `".CONST_TBL_PREFIX."member_groups` mg ON T1.id = mg.mem_id LEFT JOIN  `".CONST_TBL_PREFIX."groups` g ON g.id=mg.grp_id"; 

			if(preg_match("/(sec|msec)\./",$select_string))
				$sql .= " LEFT JOIN `".CONST_TBL_PREFIX."member_sectors` msec ON T1.id = msec.mem_id LEFT JOIN  `".CONST_TBL_PREFIX."sectors` sec ON sec.id=msec.sector_id"; 

			
			

			$sql .= $order_by_clause_outer;

		}

		$error_details_to_log = [];
		$error_details_to_log['function'] = __METHOD__;
		$error_details_to_log['sql'] = $sql;
		$error_details_to_log['str_params_to_bind'] = $str_params_to_bind;
		$error_details_to_log['int_params_to_bind'] = $int_params_to_bind;

		//echo $sql;

		try{
			$pdo_stmt_obj = PDOConn::query($sql, $str_params_to_bind, $int_params_to_bind);
		//	$pdo_stmt_obj->debugDumpParams();
			if(array_key_exists('resourceonly', $options) && $options['resourceonly'])
				return $pdo_stmt_obj;

			$idx = -1;
			$user_id = '';
			$data = $role_ids = $grp_ids = $sector_ids =[];

			while($row=$pdo_stmt_obj->fetch(\PDO::FETCH_ASSOC)){
				if(!$record_count){
					if($user_id!==$row['id']){
						$role_ids=$grp_ids=$sector_ids=[];
						++$idx;
						$data[$idx]=array_diff_key($row,['role'=>'', 'role_id'=>'']);

						if(array_key_exists('role', $row) || array_key_exists('role_id', $row)){
							$data[$idx]['assigned_roles'] = [];
							$data[$idx]['role_names'] = [];
						}
						if(array_key_exists('grp', $row) || array_key_exists('grp_id', $row)){
							$data[$idx]['assigned_grps'] = [];
							$data[$idx]['grp_names'] = [];
						}
						if(array_key_exists('sector', $row) || array_key_exists('sector_id', $row)){
							$data[$idx]['assigned_sectors'] = [];
							$data[$idx]['sector_names'] = [];
						}
						
						$user_id=$row['id'];
					}

					if(array_key_exists('assigned_roles', $data[$idx]) && !in_array($row['role_id'], $role_ids)  ){
						$data[$idx]['assigned_roles'][] = ['role'=>$row['role'],'role_id'=>$row['role_id']];
						$data[$idx]['role_names'][] = $row['role'];
						$role_ids[] = $row['role_id'];
					}
					if(array_key_exists('assigned_grps', $data[$idx]) && !empty($row['grp'])  && !in_array($row['grp_id'], $grp_ids)){
						$data[$idx]['assigned_grps'][] = ['grp'=>$row['grp'],'id'=>$row['grp_id']];
						$data[$idx]['grp_names'][] = $row['grp'];
						$grp_ids[] = $row['grp_id'];
					}
					if(array_key_exists('assigned_sectors', $data[$idx]) && !empty($row['sector'])  && !in_array($row['sector_id'], $sector_ids) ){
						$data[$idx]['assigned_sectors'][] = ['sector'=>$row['sector'],'id'=>$row['sector_id']];
						$data[$idx]['sector_names'][] = $row['sector'];
						$sector_ids[] = $row['sector_id'];
					}
					

				}else{
					$data[] = $row;
				}
			}
			return $data;

		}catch(\Exception $e){
			if(!is_a($e, '\PDOStatement'))
				ErrorHandler::logError($error_details_to_log,$e);
			else
				ErrorHandler::logError($error_details_to_log);
			return false;
		}

	}

	function saveDetails($data, $id =''){

	   if (empty($data['sector'])) {
    unset($data['sector']);
     }
		$str_data = $int_data = [];
		$table = '`'.CONST_TBL_PREFIX . 'members`';
		if(is_array($id) && !empty($id)){
			$type='update';
			$sql="UPDATE $table SET ";
			$place_holders = [];
			$id_count = count($id);
			for ($i=0; $i < $id_count; $i++) { 
				$key = ":id_{$i}_";
				$place_holders[] = $key;
				$int_data[$key] = $id[$i];
			}
			$whereclause=" WHERE `id` IN (".implode(",", $place_holders).")";
		}else if($id!=''){ // updating user details
			$type='update';
			$sql="UPDATE $table SET ";
			$int_data[':id'] = $id;
			$whereclause=" WHERE `id`=:id";

		}else{ // Inserting new user
			$type='insert';
			$sql="INSERT INTO $table SET ";

			$whereclause='';

		}

		$values=array();

		foreach($data as $field=>$value){
			$key = ":$field";
			if($value==='')
				$values[]="`$field`=NULL";
			else{
				$values[]="`$field`=$key";
				$str_data[$key] = $value;
			}
		}

		$sql.=implode(',',$values);
		$sql.=$whereclause;
		$error_details_to_log = [];
		$error_details_to_log['at'] = date('Y-m-d H:i:s');
		$error_details_to_log['function'] = __METHOD__;
		$error_details_to_log['type'] = $type;
		$error_details_to_log['data'] = $data;
		$error_details_to_log['id'] = $id;
		$error_details_to_log['sql'] = $sql;

		try{
		    $stmt_obj = PDOConn::query($sql, $str_data, $int_data);
			$affetcedrows= $stmt_obj->rowCount();
			if($type=='insert')
				return PDOConn::lastInsertId();
			return true;
		}catch(Exception $e){
			if(!is_a($e, '\PDOStatement'))
				ErrorHandler::logError($error_details_to_log,$e);
			else
				ErrorHandler::logError($error_details_to_log);
			return false;

		}

	}



	function sendNewRegistrationEmail($email_data, $recp){
		$html_msg = <<<EOF
<!DOCTYPE html> 
	<html>
		<head>
		</head> 
		<body>
			<p>Hi {$email_data['name']},</p>
			<p>Welcome to {$email_data['org_name']}! Your name has been added to the members' list. Please login to the directory here:</p>
			<p>
			Visit: {$email_data['login_url']}<br>
			Email: {$email_data['email']}<br>
			Password: {$email_data['password']}
			</p>
			<p>
				Happy Connecting!
			</p>
			<p>Regards,<br>{$email_data['from_name']}</p>
		</body>
	</html>	
EOF;
		$subject = CONST_MAIL_SUBJECT_PREFIX." Welcome to ".$email_data['org_name'];
		$extra_data = [];
		if(!empty(CONST_EMAIL_OVERRIDE))
			$extra_data['recp'] = explode(',',CONST_EMAIL_OVERRIDE);
		else{
			$extra_data['cc'] = $recp['cc']??[];
			$extra_data['bcc'] = $recp['bcc']??[];
			$extra_data['recp'] = $recp['to']??'';
		}
		$extra_data['from'] = CONST_MAIL_SENDERS_EMAIL;
		$extra_data['from_name'] = CONST_MAIL_SENDERS_NAME;

		$data = [
			'subject' => $subject,
			'html_message' => $html_msg,
		];

		if(!empty($extra_data['recp'])){
			$mail = new Mailer(true, ['use_default'=>CONST_USE_SERVERS_DEFAULT_SMTP]); // Will use server's default email settings
			$mail->resetOverrideEmails(); // becuase the overide email is being set in the recp var above
			return $mail->sendEmail($data, $extra_data);
		}

		return false;


	}

	public function uploadProflieImage($profile_id, $name, $tmp_name, $mode=''){ // for fronedn registrations the $mode should be "reg".
		if($name=='' || $tmp_name=='')
			return false;
			
		$now = time();	
		$ext = pathinfo($name, PATHINFO_EXTENSION);
		$tmp_file_name = CONST_PROFILE_IMG_PREFIX .($mode==='reg'?'reg-':'') . $profile_id.'-'.$now.'.'.strtolower($ext); 
		$dp_file_name = CONST_PROFILE_IMG_PREFIX . ($mode==='reg'?'reg-':'').$profile_id.'_'.uniqid().'.'.strtolower($ext); 
		if(!@move_uploaded_file($tmp_name, CONST_PROFILE_IMG_DIR_PATH . $tmp_file_name) && !@copy($tmp_name, CONST_PROFILE_IMG_DIR_PATH . $tmp_file_name)){
			return false;
		}
		$img_obj = new \eBizIndia\Img();
		// $img_obj->createThumbnail(CONST_DESIGN_IMAGE . $tmp_file_name, CONST_DESIGN_IMAGE . $disk_thumb_file_name, CONST_DESIGN_IMG_THUMB_WIDTH,'');
		if(!$img_obj->resizeImageWithADimensionFixed(CONST_PROFILE_IMG_DIR_PATH . $tmp_file_name, CONST_PROFILE_IMG_DIM['w'], null, CONST_PROFILE_IMG_DIR_PATH.$dp_file_name, 'WD')){
			unlink(CONST_PROFILE_IMG_DIR_PATH.$tmp_file_name);
			return false;
		}

		$result = [];
		$result['dp_file_name'] = $dp_file_name;
		$result['org_file_name'] = $name;
		unlink(CONST_PROFILE_IMG_DIR_PATH.$tmp_file_name);
		return $result;

	}

	public static function getBdayAnnvOndate($dt, $active = '', $dnd='', $ids_to_include=[], $list_type = 'both'){ // bday, annv, both, $active - y or n or blank string
		$where_clause = $str_data = $int_data = [];
		$dt_clause = ' = :dt';
		$dt_tm = strtotime($dt);
		$str_data[':dt'] = $dt;
		$y = date('Y', $dt_tm);
		
		$sql = "SELECT mem.id, mem.fname, mem.name, mem.batch_no, mem.mobile, mem.mobile2, mem.email, mem.dob, mem.annv, date_format(mem.dob, '$y-%m-%d') as dob_mnth_yr, date_format(mem.annv, '$y-%m-%d') as annv_mnth_yr, mem.active, mem.dnd from `".CONST_TBL_PREFIX . "members` mem WHERE "; 
		$active_dnd_clause = [];
		if(!empty($active)){
			$active_dnd_clause[] =  " mem.active=:active  ";
			$str_data[':active'] = $active;
		}
		if(!empty($dnd)){
			$active_dnd_clause[] =  " mem.dnd=:dnd  ";
			$str_data[':dnd'] = $dnd;
		}
		
		$id_clause = '';
		if(!empty($ids_to_include)){
			$place_holders=[];
			$k=0;
			foreach($ids_to_include as $mem_id){
				$k++;
				$place_holders[]=":memid_{$k}_";
				$int_data[":memid_{$k}_"]=$mem_id;
				$id_clause = ' mem.id in('.implode(',',$place_holders).')';
			}
		}
		if(!empty($active_dnd_clause)){
			$active_dnd_clause = [
				' ('.implode(' AND ', $active_dnd_clause).') '
			];
			if(!empty($id_clause))
				$active_dnd_clause[] = $id_clause;

			$where_clause[] = ' ( '.implode(' OR ', $active_dnd_clause).'  ) ';

		}

		if($list_type=='bday')
			$where_clause[] = ' mem.dob is not null and ( date_format(mem.dob, "'.$y.'-%m-%d") '.$dt_clause.' ) ';
		else if($list_type=='annv')
			$where_clause[] = ' mem.annv is not null and ( date_format(mem.annv, "'.$y.'-%m-%d") '.$dt_clause.' ) ';
		else if($list_type=='both')
			$where_clause[] = ' ( ( mem.dob is not null and ( date_format(mem.dob, "'.$y.'-%m-%d") '.$dt_clause.' ) ) OR ( mem.annv is not null and ( date_format(mem.annv, "'.$y.'-%m-%d") '.$dt_clause.' ) )  ) ';
		if(count($where_clause))
			$sql .= implode(' AND ', $where_clause);

		$sql .= ' ORDER BY name ';
		try{
			$data = [];
			$stmt_obj = PDOConn::query($sql, $str_data, $int_data);
			// $stmt_obj->debugDumpParams();
			while ($row = $stmt_obj->fetch(\PDO::FETCH_ASSOC)) {
				if($row['dob_mnth_yr']===$row['annv_mnth_yr'])
					$row['type'] = 'both';
				else if($row['dob_mnth_yr'] === $dt) 
					$row['type'] = 'bday';
				else if($row['annv_mnth_yr'] === $dt) 
					$row['type'] = 'annv';
				$data[] = $row;
			}
			return $data;
		}catch(\Exception $e){
			ErrorHandler::logError([], $e);
			return false;
		}
		
		
	}


	public static function getRegistrations($options=[]){
		$data = [];
		$fields_mapper = $fields_mapper1 = [];


		// $fields_mapper1['*'] = 'T1.*';
		// $fields_mapper1['id']='T1.id';
		// $fields_mapper1['title']='T1.title';
		// $fields_mapper1['fname']="T1.fname";
		// $fields_mapper1['mname']="T1.mname";
		// $fields_mapper1['lname']="T1.lname";
		// $fields_mapper1['name']="T1.name";
		// $fields_mapper1['email']="T1.email";
		// $fields_mapper1['mobile']="T1.mobile";
		// $fields_mapper1['mobile2']="T1.mobile2";
		// $fields_mapper1['gender']="T1.gender";
		// $fields_mapper1['blood_grp']="T1.blood_grp";
		// $fields_mapper1['dob']="T1.dob";
		// $fields_mapper1['annv']="T1.annv";
		// $fields_mapper1['marital_status']="T1.marital_status";
		// $fields_mapper1['desig_in_assoc']="T1.desig_in_assoc";
		// $fields_mapper1['mem_id']="T1.mem_id";
		// $fields_mapper1['membership_no']="T1.membership_no";
		// $fields_mapper1['membership_type']="T1.membership_type";
		// $fields_mapper1['batch_no']="T1.batch_no";
		// $fields_mapper1['profile_pic']="T1.profile_pic";
		// $fields_mapper1['active']="T1.active";
		// $fields_mapper1['dnd']="T1.dnd";
		// $fields_mapper1['status']="T1.status";
				

		$fields_mapper['*']="mem.id as id, COALESCE(mem.title,'') as title, COALESCE(mem.fname,'') as fname, COALESCE(mem.mname,'') as mname, COALESCE(mem.lname,'') as lname, mem.name as name, mem.email as email, mem.secondary_email as secondary_email, COALESCE(mem.mobile, '') as mobile, COALESCE(mem.mobile2, '') as mobile2, COALESCE(mem.edu_qual, '') as edu_qual, COALESCE(mem.linkedin_accnt, '') as linkedin_accnt, COALESCE(mem.x_accnt, '') as x_accnt, COALESCE(mem.fb_accnt, '') as fb_accnt, COALESCE(mem.website, '') as website, COALESCE(mem.gender, '') as gender, COALESCE(mem.blood_grp, '') as blood_grp, COALESCE(mem.dob, '') as dob, COALESCE(mem.annv, '') as annv, COALESCE(mem.marital_status, '') as marital_status, COALESCE(mem.residence_city,'') as residence_city, COALESCE(mem.residence_state,'') as residence_state, COALESCE(mem.residence_country,'') as residence_country, COALESCE(mem.residence_pin,'') as residence_pin, COALESCE(mem.residence_addrline1,'') as residence_addrline1, COALESCE(mem.residence_addrline2,'') as residence_addrline2, COALESCE(mem.residence_addrline3,'') as residence_addrline3, COALESCE(mem.residence_phone,'') as residence_phone, COALESCE(mem.residence_fax,'') as residence_fax, COALESCE(mem.work_type,'') as work_type, COALESCE(mem.work_ind,'') as work_ind, COALESCE(mem.work_company,'') as work_company, COALESCE(mem.designation,'') as designation, COALESCE(mem.work_city,'') as work_city, COALESCE(mem.work_state,'') as work_state, COALESCE(mem.work_country,'') as work_country, COALESCE(mem.work_pin,'') as work_pin, COALESCE(mem.work_addrline1,'') as work_addrline1, COALESCE(mem.work_addrline2,'') as work_addrline2, COALESCE(mem.work_addrline3,'') as work_addrline3, COALESCE(mem.work_phone,'') as work_phone, COALESCE(mem.work_phoneepabx,'') as work_phoneepabx, COALESCE(mem.work_fax,'') as work_fax, COALESCE(mem.work_secretary_name,'') as work_secretary_name, COALESCE(mem.work_secretary_mobile,'') as work_secretary_mobile, COALESCE(mem.work_secretary_email,'') as work_secretary_email,  COALESCE(mem.batch_no,'') as batch_no, COALESCE(mem.profile_pic,'') as profile_pic, mem.status as status, mem.mem_id as mem_id, COALESCE(mem.reg_on,'') as reg_on, COALESCE(mem.approved_on,'') as approved_on, COALESCE(mem.disapproved_on,'') as disapproved_on, COALESCE(mem.ref1_name,'') as ref1_name, COALESCE(mem.ref1_batch,'') as ref1_batch, COALESCE(mem.ref1_mobile,'') as ref1_mobile, COALESCE(mem.ref2_name,'') as ref2_name, COALESCE(mem.ref2_batch,'') as ref2_batch, COALESCE(mem.ref2_mobile,'') as ref2_mobile, mem.dnd as dnd, COALESCE(mem.hashtags, '') as hashtags, COALESCE(mem.membership_fee, '') as membership_fee, COALESCE(mem.membership_fee_gst_rate, '') as membership_fee_gst_rate, COALESCE(mem.payment_mode, '') as payment_mode, COALESCE(mem.payment_status, '') as payment_status, COALESCE(mem.payment_txn_ref, '') as payment_txn_ref, COALESCE(mem.payment_instrument_type, '') as payment_instrument_type, COALESCE(mem.payment_instrument, '') as payment_instrument, COALESCE(mem.paid_on, '') as paid_on, mem.status_remarks as status_remarks";



		$fields_mapper['recordcount']='count(distinct(mem.id))';
		$fields_mapper['id']="mem.id";
		$fields_mapper['title']='COALESCE(mem.title,"")';
		$fields_mapper['fname']='COALESCE(mem.fname,"")';
		$fields_mapper['mname']='COALESCE(mem.mname, "")';
		$fields_mapper['lname']='COALESCE(mem.lname, "")';
		$fields_mapper['name']='mem.name';
		$fields_mapper['email']='mem.email';
		$fields_mapper['secondary_email']='mem.secondary_email';
		$fields_mapper['mobile']='COALESCE(mem.mobile, "")';
		$fields_mapper['mobile2']='COALESCE(mem.mobile2, "")';
		$fields_mapper['edu_qual']='COALESCE(mem.edu_qual, "")';
		$fields_mapper['linkedin_accnt']='COALESCE(mem.linkedin_accnt, "")';
		$fields_mapper['x_accnt']='COALESCE(mem.x_accnt, "")';
		$fields_mapper['fb_accnt']='COALESCE(mem.fb_accnt, "")';
		$fields_mapper['website']='COALESCE(mem.website, "")';
		$fields_mapper['gender']='COALESCE(mem.gender, "")';
		$fields_mapper['blood_grp']='COALESCE(mem.blood_grp, "")';
		$fields_mapper['dob']='COALESCE(mem.dob, "")';
		$fields_mapper['annv']='COALESCE(mem.annv, "")';
		$fields_mapper['marital_status']='COALESCE(mem.marital_status, "")';
		$fields_mapper['residence_city']='COALESCE(mem.residence_city,"")';
		$fields_mapper['residence_state']='COALESCE(mem.residence_state,"")';
		$fields_mapper['residence_pin']='COALESCE(mem.residence_pin,"")';
		$fields_mapper['residence_country']='COALESCE(mem.residence_country,"")';
		$fields_mapper['residence_addrline1']='COALESCE(mem.residence_addrline1,"")';
		$fields_mapper['residence_addrline2']='COALESCE(mem.residence_addrline2,"")';
		$fields_mapper['residence_addrline3']='COALESCE(mem.residence_addrline3,"")';
		$fields_mapper['residence_phone']='COALESCE(mem.residence_phone,"")';
		$fields_mapper['residence_fax']='COALESCE(mem.residence_fax,"")';
		$fields_mapper['work_city']='COALESCE(mem.work_city,"")';
		$fields_mapper['work_state']='COALESCE(mem.work_state,"")';
		$fields_mapper['work_pin']='COALESCE(mem.work_pin,"")';
		$fields_mapper['work_country']='COALESCE(mem.work_country,"")';
		$fields_mapper['work_addrline1']='COALESCE(mem.work_addrline1,"")';
		$fields_mapper['work_addrline2']='COALESCE(mem.work_addrline2,"")';
		$fields_mapper['work_addrline3']='COALESCE(mem.work_addrline3,"")';
		$fields_mapper['work_phone']='COALESCE(mem.work_phone,"")';
		$fields_mapper['work_phoneepabx']='COALESCE(mem.work_phoneepabx,"")';
		$fields_mapper['work_fax']='COALESCE(mem.work_fax,"")';
		$fields_mapper['work_secretary_name']='COALESCE(mem.work_secretary_name,"")'; 
		$fields_mapper['work_secretary_mobile']='COALESCE(mem.work_secretary_mobile,"")'; 
		$fields_mapper['work_secretary_email']='COALESCE(mem.work_secretary_email,"")'; 
		$fields_mapper['work_type']='COALESCE(mem.work_type,"")';
		$fields_mapper['work_ind']='COALESCE(mem.work_ind,"")';
		$fields_mapper['work_company']='COALESCE(mem.work_company,"")';
		$fields_mapper['designation']='COALESCE(mem.designation,"")';
		$fields_mapper['batch_no']='COALESCE(mem.batch_no,"")';
		$fields_mapper['profile_pic']='COALESCE(mem.profile_pic,"")';
		$fields_mapper['dnd']='mem.dnd';
		$fields_mapper['hashtags']='COALESCE(mem.hashtags, "")';
		$fields_mapper['mem_id']='mem.mem_id'; // The corresponding record in the members table
		$fields_mapper['status']='mem.status';
		$fields_mapper['status_remarks']='mem.status_remarks';
		$fields_mapper['reg_on']='COALESCE(mem.reg_on,"")';
		$fields_mapper['approved_on']='COALESCE(mem.approved_on,"")';
		$fields_mapper['disapproved_on']='COALESCE(mem.disapproved_on,"")';
		$fields_mapper['membership_fee']='COALESCE(mem.membership_fee,"")';
		$fields_mapper['membership_fee_gst_rate']='COALESCE(mem.membership_fee_gst_rate,"")';
		$fields_mapper['payment_mode']='COALESCE(mem.payment_mode,"")';
		$fields_mapper['payment_status']='COALESCE(mem.payment_status,"")';
		$fields_mapper['payment_txn_ref']='COALESCE(mem.payment_txn_ref,"")';
		$fields_mapper['payment_instrument_type']='COALESCE(mem.payment_instrument_type,"")';
		$fields_mapper['payment_instrument']='COALESCE(mem.payment_instrument,"")';
		$fields_mapper['paid_on']='COALESCE(mem.paid_on,"")';
		
		
		$where_clause=[];

		$str_params_to_bind=[];
		$int_params_to_bind=[];

		if( array_key_exists('filters',$options) && is_array($options['filters']) ){
			$field_counter=0;
			foreach($options['filters'] as $filter){
				++$field_counter;
				switch ($filter['field']) {
					case 'batch_no':
					case 'mem_id':
					case 'id':
						switch($filter['type']){
							case 'IN':
								if(is_array($filter['value'])){
									$place_holders=[];
									$k=0;
									foreach($filter['value'] as $userid){
										$k++;
										$place_holders[]=":whr".$field_counter."_userid_{$k}_";
										$int_params_to_bind[":whr".$field_counter."_userid_{$k}_"]=$userid;
									}
									$where_clause[] = $fields_mapper[$filter['field']].' in('.implode(',',$place_holders).') ';

								}
								break;

							case 'NOT_IN':
								if(is_array($filter['value'])){
									$place_holders=[];
									$k=0;
									foreach($filter['value'] as $userid){
										$k++;
										$place_holders[]=":whr".$field_counter."_userid_{$k}_";
										$int_params_to_bind[":whr".$field_counter."_userid_{$k}_"]=$userid;
									}
									$where_clause[] = $fields_mapper[$filter['field']].' not in('.implode(',',$place_holders).') ';

								}
								break;

							case 'NOT_EQUAL':
								$userid=(is_array($filter['value']))?$filter['value'][0]:$filter['value'];
								$where_clause[] = $fields_mapper[$filter['field']].'!=:whr'.$field_counter.'_userid';
								$int_params_to_bind[':whr'.$field_counter.'_userid']=$userid;
								break;

							default:
								$userid=(is_array($filter['value']))?$filter['value'][0]:$filter['value'];
								$where_clause[] = $fields_mapper[$filter['field']].'=:whr'.$field_counter.'_userid';
								$int_params_to_bind[':whr'.$field_counter.'_userid']=$userid;
						}

						break;



					// case 'membership_type':
					// 	switch($filter['type']){
					// 		case 'IN':
					// 			$mt_others = false;
					// 			if(is_array($filter['value'])){
					// 				$place_holders=[];
					// 				$k=0;
					// 				foreach($filter['value'] as $memtype){
					// 					if($memtype == 'Others'){
					// 						$mt_others = true;
					// 						continue;
					// 					}

					// 					$k++;
					// 					$place_holders[]=":whr".$field_counter."_memtype_{$k}_";
					// 					$str_params_to_bind[":whr".$field_counter."_memtype_{$k}_"]=$memtype;
					// 				}
					// 				$wc=' mem.membership_type in('.implode(',',$place_holders).') ';
					// 				if($mt_others){
					// 					$where_clause[] = "( $wc OR mem.membership_type is null OR mem.membership_type='') ";
					// 				}else{
					// 					$where_clause[] = " $wc ";
					// 				}

					// 			}
					// 			break;

					// 		case 'EMPTY':
					// 			$where_clause[]=' (mem.membership_type is NULL OR mem.membership_type="") ';
					// 			break;

					// 		case 'NOT_EQUAL':
					// 			$memtype=(is_array($filter['value']))?$filter['value'][0]:$filter['value'];
					// 			$where_clause[]=' mem.membership_type!=:whr'.$field_counter.'_memtype';
					// 			$str_params_to_bind[':whr'.$field_counter.'_memtype']=$memtype;
					// 			break;

					// 		default:
					// 			$memtype=(is_array($filter['value']))?$filter['value'][0]:$filter['value'];
					// 			$where_clause[]=' mem.membership_type like :whr'.$field_counter.'_memtype';
					// 			$str_params_to_bind[':whr'.$field_counter.'_memtype']=$memtype;
					// 	}

					// 	break;

					case 'status':
					case 'blood_grp':
					case 'email':
					case 'secondary_email':
					case 'name':
						$nm=(is_array($filter['value']))?$filter['value'][0]:$filter['value'];
						$where_clause[] = $fields_mapper[$filter['field']]." like :whr".$field_counter."_nm";
						switch($filter['type']){
							case 'CONTAINS':
								$str_params_to_bind[':whr'.$field_counter.'_nm']="%$nm%";
								break;
							case 'STARTS_WITH':
								$str_params_to_bind[':whr'.$field_counter.'_nm']="$nm%";
								break;
							case 'ENDS_WITH':
								$str_params_to_bind[':whr'.$field_counter.'_nm']="%$nm";
								break;
							case 'EQUAL':
							default:
								$str_params_to_bind[':whr'.$field_counter.'_nm']="$nm";
								break;
						}

						break;


					case 'dnd':
					case 'multi_field_search':
						$search_text = (is_array($filter['value']))?$filter['value'][0]:$filter['value'];
						$where_clause[] = " (MATCH (mem.fname,mem.mname,mem.lname,mem.email,mem.secondary_email,mem.mobile,mem.mobile2,mem.residence_city,mem.work_city,mem.work_type,mem.work_ind,mem.work_company,mem.blood_grp,mem.status) AGAINST (:whr".$field_counter."_multifield IN NATURAL LANGUAGE MODE)) ";	
						$str_params_to_bind[':whr'.$field_counter.'_multifield'] = $search_text;
						break;


				}

			}


		}

		$select_string=$fields_mapper['*'];
		
		if(array_key_exists('fieldstofetch', $options) && is_array($options['fieldstofetch'])){
			$fields_to_fetch_count=count($options['fieldstofetch']);

			if($fields_to_fetch_count>0){
				$selected_fields=array();

				if(in_array('recordcount', $options['fieldstofetch'])){
					$record_count=true;
				}else{

					if(!in_array('*',$options['fieldstofetch'])){
						if(!in_array('id',$options['fieldstofetch'])){ // This is required as the id is being used for table joining
							$options['fieldstofetch'][]='id';
							$fields_to_fetch_count+=1; // increment the count by 1 to include this column
						}

					}

				}

				for($i=0; $i<$fields_to_fetch_count; $i++){
					if(array_key_exists($options['fieldstofetch'][$i],$fields_mapper)){
						$selected_fields[]=$fields_mapper[$options['fieldstofetch'][$i]].(($options['fieldstofetch'][$i]!='*')?' as '.$options['fieldstofetch'][$i]:'');

					}

				}

				if(count($selected_fields)>0){
					$select_string=implode(', ',$selected_fields);

				}

			}
		}

		$select_string=($record_count)?$select_string:'distinct '.$select_string;
		$group_by_clause='';
		if(array_key_exists('group_by', $options) && is_array($options['group_by'])){
			foreach ($options['group_by'] as $field) {
				if(preg_match("/^(mem)\./",$fields_mapper[$field]))
					$group_by_clause.=", ".$fields_mapper[$field];
				else
					$group_by_clause.=", $field";
			}

			$group_by_clause=trim($group_by_clause,",");
			if($group_by_clause!=''){
				$group_by_clause=' GROUP BY '.$group_by_clause;

			}
		}

		$order_by_clause = $order_by_clause_outer = ''; // $order_by_clause_outer is required to preserver the subquery's order

		if(array_key_exists('order_by', $options) && is_array($options['order_by'])){
			foreach ($options['order_by'] as $order) {
				if(preg_match("/^(mem)\./",$fields_mapper[$order['field']])){
					$order_by_clause.=", ".$fields_mapper[$order['field']];

					if(!$record_count){
						if(!preg_match("/,?\s*".str_replace('.', "\.", $fields_mapper[$order['field']])."/",$select_string))
							$select_string .= ", ".$fields_mapper[$order['field']]. ' as '.$order['field'];
					}

				}else if(array_key_exists($order['field'], $fields_mapper)){
					if(!preg_match("/\s*as\s*".$order['field']."/",$select_string))
						$select_string .= ", ".$fields_mapper[$order['field']].' as '.$order['field'];

					$order_by_clause.=", ".$order['field'];
				}

				if(array_key_exists('type', $order) && $order['type']=='DESC'){
					$order_by_clause.=' DESC';
				}

			}

			$order_by_clause=trim($order_by_clause,",");
			if($order_by_clause!=''){
				$order_by_clause=' ORDER BY '.$order_by_clause;

			}

			// user ID is a unique value across all the users so to maintain a unique order across queries with the same set of order by clauses we can include this field as the last field in the order by clause.
			if($order_by_clause!='' && !stristr($order_by_clause, 'mem.id')){

				$order_by_clause .= ', '.$fields_mapper['id'].' DESC ';
			}


		}

		

		if(!$record_count && $order_by_clause==''){

			$order_by_clause=" ORDER BY mem.reg_on DESC, mem.id DESC ";

			if(!preg_match("/\s+as\s+name/",$select_string)){
				$select_string .= ', '.$fields_mapper['name'].' as name';
			}
			if(!preg_match("/,?\s+mem\.id/",$select_string)){
				$select_string .= ', '.$fields_mapper['id'].' as id';
			}

		}

		$limit_clause='';

		if(array_key_exists('page', $options) && filter_var($options['page'],FILTER_VALIDATE_INT) && $options['page']>0 && array_key_exists('recs_per_page', $options) && filter_var($options['recs_per_page'],FILTER_VALIDATE_INT) && $options['recs_per_page']>0){

			$limit_clause="LIMIT ".( ($options['page']-1) * $options['recs_per_page'] ).", $options[recs_per_page] ";

		}

		$where_clause_string = '';
		if(!empty($where_clause))
			$where_clause_string = ' WHERE '.implode(' AND ', $where_clause);

		$sql="SELECT $select_string from `".CONST_TBL_PREFIX."member_regs` as mem $where_clause_string $group_by_clause $order_by_clause $limit_clause";

		if(!$record_count){

			$sql = "SELECT T1.*, mrsec.sector_id as sector_id, sec.sector as sector from ($sql) as T1 LEFT JOIN `".CONST_TBL_PREFIX."member_regs_sectors` as mrsec ON T1.id=mrsec.mem_id LEFT JOIN `".CONST_TBL_PREFIX."sectors` as sec ON mrsec.sector_id=sec.id ";

		}

		$error_details_to_log = [];
		$error_details_to_log['function'] = __METHOD__;
		$error_details_to_log['sql'] = $sql;
		$error_details_to_log['str_params_to_bind'] = $str_params_to_bind;
		$error_details_to_log['int_params_to_bind'] = $int_params_to_bind;

		try{
			$pdo_stmt_obj = PDOConn::query($sql, $str_params_to_bind, $int_params_to_bind);
			
			if(array_key_exists('resourceonly', $options) && $options['resourceonly'])
				return $pdo_stmt_obj;

			$idx = -1;
			$user_id = '';
			$data = [];

			while($row=$pdo_stmt_obj->fetch(\PDO::FETCH_ASSOC)){
				if($user_id!==$row['id']){
					++$idx;
					$data[$idx]=array_diff_key($row,['sector'=>'', 'sector_id'=>'']);

					if(array_key_exists('sector', $row) || array_key_exists('sector_id', $row)){
						$data[$idx]['assigned_sectors'] = [];
						$data[$idx]['sector_names'] = [];
					}
					
					$user_id=$row['id'];
				}

				if(array_key_exists('assigned_sectors', $data[$idx]) && !empty($row['sector'])){
					$data[$idx]['assigned_sectors'][] = ['sector'=>$row['sector'],'id'=>$row['sector_id']];
					$data[$idx]['sector_names'][] = $row['sector'];
				}
				
			}
			return $data;

		}catch(\Exception $e){
			if(!is_a($e, '\PDOStatement'))
				ErrorHandler::logError($error_details_to_log,$e);
			else
				ErrorHandler::logError($error_details_to_log);
			return false;
		}

	}


	function registerMember($data, $id=''){ 
		$str_data = $int_data = [];
		$table = '`'.CONST_TBL_PREFIX . 'member_regs`';
		if(is_array($id) && !empty($id)){
			$type='update';
			$sql="UPDATE $table SET ";
			$place_holders = [];
			$id_count = count($id);
			for ($i=0; $i < $id_count; $i++) { 
				$key = ":id_{$i}_";
				$place_holders[] = $key;
				$int_data[$key] = $id[$i];
			}
			$whereclause=" WHERE `id` IN (".implode(",", $place_holders).")";
		}else if($id!=''){ // updating user details
			$type='update';
			$sql="UPDATE $table SET ";
			$int_data[':id'] = $id;
			$whereclause=" WHERE `id`=:id";

		}else{ // Inserting new user
			$type='insert';
			$sql="INSERT INTO $table SET ";

			$whereclause='';
		}

		// $type='insert';
		// $sql= 'INSERT INTO `'.CONST_TBL_PREFIX . 'member_regs` SET ';
		$values=array();
		foreach($data as $field=>$value){
			$key = ":$field";
			if($value==='')
				$values[]="`$field`=NULL";
			else{
				$values[]="`$field`=$key";
				$str_data[$key] = $value;
			}
		}

		$sql.=implode(',',$values);
		$sql.=$whereclause;
		$error_details_to_log = [];
		$error_details_to_log['at'] = date('Y-m-d H:i:s');
		$error_details_to_log['function'] = __METHOD__;
		$error_details_to_log['type'] = $type;
		$error_details_to_log['data'] = $data;
		$error_details_to_log['int_data'] = $int_data;
		$error_details_to_log['str_data'] = $str_data;
		$error_details_to_log['id'] = $id;
		$error_details_to_log['sql'] = $sql;
		
		try{
			$stmt_obj = PDOConn::query($sql, $str_data, $int_data);
			$affetcedrows= $stmt_obj->rowCount();
			if($type=='insert')
				return PDOConn::lastInsertId();
			return true;
		}catch(Exception $e){
			if(!is_a($e, '\PDOStatement'))
				ErrorHandler::logError($error_details_to_log,$e);
			else
				ErrorHandler::logError($error_details_to_log);
			return false;

		}

	}

	function addToMembersDirectory(int $reg_id, array $data = []){
		if(empty($reg_id))
			return false;
		$str_data = $int_data = [];
		$sql = "INSERT INTO `".CONST_TBL_PREFIX . "members` (`batch_no`, `title`, `fname`, `mname`, `lname`, `email`, `secondary_email`, `mobile`, `mobile2`, `edu_qual`, `linkedin_accnt`, `x_accnt`, `fb_accnt`, `website`, `gender`, `blood_grp`, `dob`, `annv`, `residence_city`, `residence_state`, `residence_pin`, `residence_country`, `residence_addrline1`, `residence_addrline2`, `residence_addrline3`, `residence_phone`, `residence_fax`,`work_type`, `work_ind`,`work_company`, `designation`, `work_city`, `work_state`, `work_pin`, `work_country`, `work_addrline1`, `work_addrline2`, `work_addrline3`,`work_phone`,`work_phoneepabx`,`work_fax`,`work_secretary_name`,`work_secretary_mobile`,`work_secretary_email`, `dnd`, `remarks`, `membership_fee`, `membership_fee_gst_rate`, `amount_paid`, `payment_mode`, `payment_status`, `payment_txn_ref`, `payment_instrument_type`, `payment_instrument`, `paid_on`, `created_at`, `created_by`, `created_from`, `joining_dt`, `membership_no`) SELECT `batch_no`, `title`, `fname`, `mname`, `lname`, `email`, `mobile`, `mobile2`, `edu_qual`, `linkedin_accnt`, `x_accnt`, `fb_accnt`, `website`, `gender`, `blood_grp`, `dob`, `annv`, `residence_city`, `residence_state`, `residence_pin`, `residence_country`, `residence_addrline1`, `residence_addrline2`, `residence_addrline3`, `residence_phone`, `residence_fax`,`work_type`, `work_ind`,`work_company`, `designation`, `work_city`, `work_state`, `work_pin`, `work_country`, `work_addrline1`, `work_addrline2`, `work_addrline3`,`residence_phone`,`residence_fax`,`work_phone`,`work_phoneepabx`,`work_fax`, `work_secretary_name`,`work_secretary_mobile`,`work_secretary_email`, `dnd`, `status_remarks`, `membership_fee`, `membership_fee_gst_rate`, `amount_paid`, `payment_mode`, `payment_status`, `payment_txn_ref`, `payment_instrument_type`, `payment_instrument`, `paid_on`, :created_at, :created_by, :created_from, :joining_dt, :membership_no from `".CONST_TBL_PREFIX . "member_regs` WHERE id=:id";
		$int_data[':id'] = $reg_id;
		$str_data[':created_at'] = $data['created_at'];
		$str_data[':created_by'] = $data['created_by'];
		$str_data[':created_from'] = $data['created_from'];
		$str_data[':joining_dt'] = $data['joining_dt'];
		$str_data[':membership_no'] = $data['membership_no'];
		try{
			$stmt_obj = PDOConn::query($sql, $str_data, $int_data);
			return PDOConn::lastInsertId();
		}catch(Exception $e){
			if(!is_a($e, '\PDOStatement'))
				ErrorHandler::logError($error_details_to_log,$e);
			else
				ErrorHandler::logError($error_details_to_log);
			return false;
		}
	}

	function profileUpdatedNotification($email_data, $recp){
		$his = $email_data['gender']=='F'?'her':'his';
		$name = \eBizIndia\_esc($email_data['name'], true);
		$email = \eBizIndia\_esc($email_data['email'], true);
		$membership_no = \eBizIndia\_esc($email_data['membership_no']??'', true);
		$r = print_r($recp, true);
		$html_msg = <<<EOF
<!DOCTYPE html> 
	<html>
		<head>
		</head> 
		<body>
			<p>Hello,</p>
			<p>A member having the following details has updated {$his} profile.</p>
			<p>
			Name: {$name}<br>
			Membership No.: {$membership_no}<br>
			Email Id: {$email}<br>
			</p>
			<p>
				<a href="{$email_data['profile_url']}" >Click HERE</a> to view the member's updated profile.
			</p>
			<p>Regards,<br>{$email_data['from_name']}</p>
		</body>
	</html>	
EOF;
		$subject = CONST_MAIL_SUBJECT_PREFIX." Profile updated by ".$email_data['email'];
		$extra_data = [];
		if(!empty(CONST_EMAIL_OVERRIDE)){
			$extra_data['recp'] = explode(',',CONST_EMAIL_OVERRIDE);
		}else{
			$extra_data['cc'] = $recp['cc']??[];
			$extra_data['bcc'] = $recp['bcc']??[];
			$extra_data['recp'] = $recp['to']??'';
		}
		$extra_data['from'] = CONST_MAIL_SENDERS_EMAIL;
		$extra_data['from_name'] = CONST_MAIL_SENDERS_NAME;

		$data = [
			'subject' => $subject,
			'html_message' => $html_msg,
		];

		if(!empty($extra_data['recp'])){
			$mail = new Mailer(true, ['use_default'=>CONST_USE_SERVERS_DEFAULT_SMTP]); // Will use server's default email settings
			$mail->resetOverrideEmails(); // becuase the overide email is being set in the recp var above
			return $mail->sendEmail($data, $extra_data);
		}

		return false;


	}


	function sendNewRegistrationNotification($email_data, $recp){
		$html_msg = <<<EOF
<!DOCTYPE html> 
	<html>
		<head>
		</head> 
		<body>
			<p>Hello,</p>
			<p>A new registration has been submitted with the following details.</p>
			<p>
			Name: {$email_data['name']}<br>
			Batch: {$email_data['batch']}<br>
			Email Id: {$email_data['email']}<br>
			Mobile: {$email_data['mobile']}<br>
			</p>
			<p>
				<a href="{$email_data['login_url']}" >Click HERE</a> to login and approve the request.
			</p>
			<p>Regards,<br>{$email_data['from_name']}</p>
		</body>
	</html>	
EOF;
		$subject = CONST_MAIL_SUBJECT_PREFIX." New registration by ".$email_data['email'];
		$extra_data = [];
		if(!empty(CONST_EMAIL_OVERRIDE)){
			$extra_data['recp'] = explode(',',CONST_EMAIL_OVERRIDE);
		}else{
			$extra_data['cc'] = $recp['cc']??[];
			$extra_data['bcc'] = $recp['bcc']??[];
			$extra_data['recp'] = $recp['to']??'';
		}
		$extra_data['from'] = CONST_MAIL_SENDERS_EMAIL;
		$extra_data['from_name'] = CONST_MAIL_SENDERS_NAME;

		$data = [
			'subject' => $subject,
			'html_message' => $html_msg,
		];

		if(!empty($extra_data['recp'])){
			$mail = new Mailer(true, ['use_default'=>CONST_USE_SERVERS_DEFAULT_SMTP]); // Will use server's default email settings
			$mail->resetOverrideEmails(); // becuase the overide email is being set in the recp var above
			return $mail->sendEmail($data, $extra_data);
		}

		return false;


	}


	function sendRegistrationApprovalEmail($email_data, $recp){
		$html_msg = <<<EOF
<!DOCTYPE html> 
	<html>
		<head>
		</head> 
		<body>
			<p>Hi {$email_data['name']},</p>
			<p>Welcome to {$email_data['org_name']}! Your membership has been approved and your name has been added to the members' list. Please login to the Members' Only portal here:</p>
			<p>
			Visit: <a href="{$email_data['login_url']}" >{$email_data['login_url']}</a><br>
			Email: {$email_data['email']}<br>
			Password: {$email_data['password']}<br>
			</p>
			<p>
				Soon, we will add you to the WhatsApp group of our Alumni.<br>
				Happy Connecting!
			</p>
			<p>Regards,<br>{$email_data['from_name']}</p>
		</body>
	</html>	
EOF;
		$subject = CONST_MAIL_SUBJECT_PREFIX." Your registration has been approved.";
		$extra_data = [];
		if(!empty(CONST_EMAIL_OVERRIDE)){
			$extra_data['recp'] = explode(',',CONST_EMAIL_OVERRIDE);
		}else{
			$extra_data['cc'] = $recp['cc']??[];
			$extra_data['bcc'] = $recp['bcc']??[];
			$extra_data['recp'] = $recp['to']??[];
		}
		$extra_data['from'] = CONST_MAIL_SENDERS_EMAIL;
		$extra_data['from_name'] = CONST_MAIL_SENDERS_NAME;

		$data = [
			'subject' => $subject,
			'html_message' => $html_msg,
		];

		if(!empty($extra_data['recp'])){
			$mail = new Mailer(true, ['use_default'=>CONST_USE_SERVERS_DEFAULT_SMTP]); // Will use server's default email settings
			$mail->resetOverrideEmails(); // becuase the overide email is being set in the recp var above
			return $mail->sendEmail($data, $extra_data);
		}

		return false;


	}

	function sendRegistrationDisapprovalEmail($email_data, $recp){
		$html_msg = <<<EOF
<!DOCTYPE html> 
	<html>
		<head>
		</head> 
		<body>
			<p>Hello {$email_data['name']},</p>
			<p>Sorry, your membership request was not accepted. You may contact the authorities if required.</p>
			<p>Regards,<br>{$email_data['from_name']}</p>
		</body>
	</html>	
EOF;
		$subject = CONST_MAIL_SUBJECT_PREFIX." Your membership request was not accepted.";
		$extra_data = [];
		if(!empty(CONST_EMAIL_OVERRIDE)){
			$extra_data['recp'] = explode(',',CONST_EMAIL_OVERRIDE);
		}else{
			$extra_data['cc'] = $recp['cc']??[];
			$extra_data['bcc'] = $recp['bcc']??[];
			$extra_data['recp'] = $recp['to']??'';
		}
		$extra_data['from'] = CONST_MAIL_SENDERS_EMAIL;
		$extra_data['from_name'] = CONST_MAIL_SENDERS_NAME;

		$data = [
			'subject' => $subject,
			'html_message' => $html_msg,
		];

		if(!empty($extra_data['recp'])){
			$mail = new Mailer(true, ['use_default'=>CONST_USE_SERVERS_DEFAULT_SMTP]); // Will use server's default email settings
			$mail->resetOverrideEmails(); // becuase the overide email is being set in the recp var above
			return $mail->sendEmail($data, $extra_data);
		}

		return false;


	}


	function generateMembershipNo(){
		$prefix = 'MYMS';
		$suffix = '';
		$num = 1;
		$num_len = 4;
		$sql = "SELECT MAX(CAST(REGEXP_REPLACE(`membership_no`, '[^0-9]+','') as UNSIGNED))+1 as next_mno from `".CONST_TBL_PREFIX . "members` ";
		try{
			$pdo_stmt_obj = PDOConn::query($sql);
			$row = $pdo_stmt_obj->fetch(\PDO::FETCH_ASSOC);
			if(!empty($row))
				$num = $row['next_mno'];
			return $prefix.str_pad($num, $num_len, '0', STR_PAD_LEFT).$suffix;	

		}catch(\Exception $e){
			if(!is_a($e, '\PDOStatement'))
				ErrorHandler::logError([], $e);
			return false;
		}
	}	


	function alertMemberRegistrationError($email_data, $recp){

		$html_msg = <<<EOF
<!DOCTYPE html> 
<html>
	<head></head> 
	<body>
		<p>Hello,</p>
		<p>{$email_data['msg']}</p>
		<p>
		Registration Date: {$email_data['reg_on']}<br>
		Name: {$email_data['name']}<br>
		Batch: {$email_data['batch_no']}<br>
		Mobile: {$email_data['mobile']}<br>
		Email: {$email_data['email']}<br>
		
		Total Amount: &#8377;{$email_data['amount_payable']}<br><br>
		Instamojo Payment Status: {$email_data['pmtg_payment_status']}<br>
		Instamojo Payment Req. ID: {$email_data['pmtg_payment_req_id']}<br>
		Instamojo Payment ID: {$email_data['pmtg_payment_id']}<br><br>
		Bank Ref: {$email_data['pmt_bank_reference_number']}<br><br>

		</p>
		
		<p>Regards,<br>{$email_data['from_name']}</p>
	</body>
</html>	
EOF;


		$subject = CONST_MAIL_SUBJECT_PREFIX." Member registration error (".trim($email_data['fname'].' '.$email_data['lname'])." - ".$email_data['batch_no'].")";
		$extra_data = [];
		$extra_data['from'] = CONST_MAIL_SENDERS_EMAIL;
		$extra_data['from_name'] = CONST_MAIL_SENDERS_NAME;
		$extra_data['cc'] = $recp['cc']??[];
		$extra_data['bcc'] = $recp['bcc']??[];

		if(!empty(CONST_EMAIL_OVERRIDE)){
			$extra_data['recp'] = explode(',',CONST_EMAIL_OVERRIDE);
			$extra_data['cc'] = $extra_data['bcc'] = [];
		}else{
			$extra_data['recp'] = $recp['to']??'';
		}

		$data = [
			'subject' => $subject,
			'html_message' => $html_msg,
		];

		if(!empty($extra_data['recp'])){
			$mail = new Mailer(true, ['use_default'=>CONST_USE_SERVERS_DEFAULT_SMTP]); // Will use server's default email settings
			$mail->resetOverrideEmails(); // becuase the overide email is being set in the recp var above
			return $mail->sendEmail($data, $extra_data);
		}

		return false;

	}


	function sendContactRequestEmail($email_data, $recp){
		$org_name = CONST_ORG_DISP_NAME;
		$org_name_escaped = _esc(CONST_ORG_DISP_NAME, true);
		$msg_escaped = nl2br(_esc($email_data['msg'], true));
		$req_for_escaped = _esc($email_data['req_for'], true);
		$req_from_escaped = _esc($email_data['req_from'], true);
		$memno_unescaped = $memno_escaped = '';
		if(!empty($email_data['membership_no'])){
			$memno_unescaped = '('.$email_data['membership_no'].')';
			$memno_escaped = '('._esc($email_data['membership_no'], true).')';
		}
		$html_msg = <<<EOF
<!DOCTYPE html> 
	<html>
		<head>
		</head> 
		<body>
			<p>Hi {$req_for_escaped},</p>
			<p>You have a contact request from a member of the {$org_name_escaped}. Here are the details.</p>
			<p>
			Member: {$req_from_escaped} {$memno_escaped}<br><br>
			Email: <a href= "mailto:{$email_data['email']}" >{$email_data['email']}</a><br><br>
			Mobile: <a href="tel:{$email_data['mobile']}" >{$email_data['mobile']}</a><br><br>
			Link To Profile: <a href="{$email_data['link_to_profile']}" >{$email_data['link_to_profile']}</a><br><br>
			Message: {$msg_escaped}
			</p>
			<p>
				You may like to get back to the member using the above details.
			</p>
			<p>Regards,<br>{$email_data['from_name']}</p>
		</body>
	</html>	
EOF;
		$text_msg = <<<EOF
Hi {$email_data['req_for']},\n\n
You have a contact request from a member of the {$org_name}. Here are the details.\n\n
Member: {$email_data['req_from']} {$memno_unescaped}\n\n
Email: {$email_data['email']}\n\n
Mobile: {$email_data['mobile']}\n\n
Link To Profile: {$email_data['link_to_profile']}\n\n
Message: {$email_data['msg']}\n\n
You may like to get back to the member using the above details.\n\n
Regards,\n{$email_data['from_name']}
EOF;
		

		$subject = CONST_MAIL_SUBJECT_PREFIX." Contact request from ".$email_data['req_from'];
		$extra_data = [];
		if(!empty(CONST_EMAIL_OVERRIDE))
			$extra_data['recp'] = explode(',',CONST_EMAIL_OVERRIDE);
		else{
			$extra_data['cc'] = $recp['cc']??[];
			$extra_data['bcc'] = $recp['bcc']??[];
			$extra_data['recp'] = $recp['to']??'';
		}
		$extra_data['from'] = CONST_MAIL_SENDERS_EMAIL;
		$extra_data['from_name'] = CONST_MAIL_SENDERS_NAME;

		$data = [
			'subject' => $subject,
			'html_message' => $html_msg,
			'text_message' => $text_msg,
		];

		if(!empty($extra_data['recp'])){
			$mail = new Mailer(true, ['use_default'=>CONST_USE_SERVERS_DEFAULT_SMTP]); // Will use server's default email settings
			$mail->resetOverrideEmails(); // becuase the overide email is being set in the recp var above
			return $mail->sendEmail($data, $extra_data);
		}

		return false;


	}


}

// $obj = new Member(2);
// echo '<pre>';
// print_r($obj->getProfile());


// echo 'end<br>';