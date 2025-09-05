<?php
require_once 'inc.php';//print_r($_GET);
switch(filter_has_var(INPUT_GET,'mode')?strtolower($_GET['mode']):''){
	case 'togglemenumode':
		$result=array('error_code'=>0,'message'=>'');
		// $filterparams=array(
		// 						array('filteron'=>'email','filtertext'=>$loggedindata[0]['email'])
		// 						);
			
		// $userdatas=$usercls->getAdminUsers('RECORDS',$filterparams);

		$options=[];
		$options['filters']=[];
		$options['filters'][]=array('field'=>'email','type'=>'EQUAL','value'=>$loggedindata[0]['email']);
		$userdatas=$usercls->getList($options);

		$recordids=array();
		if($userdatas!==false ){

			foreach($userdatas as $userdata){

				$recordids[]=$userdata['id'];

			}
		}
		$data=array();
		$data['lastUpdatedOn']=date('Y-m-d H:i:s');
		$data['lastUpdatedBy']=$loggedindata[0]['id'];
		$data['lastUpdatedFrom']=$_SERVER['REMOTE_ADDR'];
		
		$old_menu_mode=($loggedindata[0]['user_settings_array']['menu_mode']!='')?$loggedindata[0]['user_settings_array']['menu_mode']:'BASIC';

		$loggedindata[0]['user_settings_array']['menu_mode']=($old_menu_mode=='BASIC')?'ADVANCE':'BASIC';

		$data['settings']=json_encode($loggedindata[0]['user_settings_array']);
		
		$res=$usercls->saveUserDetails($data,$recordids);

		if($res===false){
			$result['error_code']=1; // DB error
			$result['mysql_error_code']=$conn->errorInfo()[1];
			$result['message']="Changes could not be saved due to server error.";
			
		}elseif($res===null){
			$result['error_code']=1; // DB error
			$result['mysql_error_code']='';
			$result['message']="Changes could not be saved due to server error.";
		}else{
			
			// If the user is editing his own account
			$options=array();
			if($old_menu_mode!=$loggedindata[0]['user_settings_array']['menu_mode']){ // $loggedindata[0]['user_settings_array'] has already been updated with the new menu mode value above
				$result['other_data']['menu_mode_changed'] = $options['menu_mode_changed'] = 1;

			}

			$max_tries=3;
			$try_interval=1; // in sec
			for($try=0; $try<$max_tries; $try++){
				$userdata=$usercls->refreshLoggedInUserData($options);
				if($userdata!==false)
					break;
				sleep($try_interval); // wait for the specified interval before retrying	
			}
			
			
		}

		die(json_encode($result));
		break;

	case 'page-guide-closed':
		
		exit;
		break;
	
}
if(filter_has_var(INPUT_POST,'act')){
	switch($_POST['act']){
		case 'isSessionActive':
			$resp = ['insession'=>false];
			$password = $loggedindata[0]['id'].'_'.$_loaded_comp->company_details['id'];
			if(password_verify($password, $_POST['sesscode'])){ 
				$resp['insession'] = true;
			}
			echo json_encode($resp);
			exit;
		case 'getCompanies':
			$options = [];
			$options['filters'] = [
				['field'=>'active', 'type'=>'EQUAL', 'value'=>'y']
			];
			$options['order_by'] = [
				['field'=>'period_end', 'type'=>'DESC'],
				['field'=>'name', 'type'=>'ASC'],
			];

			$options['fieldstofetch'] = ['id', 'name', 'period_start_dmy', 'period_end_dmy'];
			$recs = \eBizIndia\Company::getList($options);
			echo json_encode([$recs, $_loaded_comp->company_details['id']??0]);
			exit;
		case 'loadCompany':
			$result = ['error_code'=> 0, 'msg' =>''];
			$rec_id = (int)($_POST['rec_id']??0);
			if($rec_id<=0){
				$result['error_code'] = 1;
				$result['msg'] = 'Please select a company to load.';
			}else{
				$options = [];
				$options['filters'] = [
					['field'=>'id', 'type'=>'EQUAL', 'value'=>$rec_id]
				];
				$options['fieldstofetch'] = ['id', 'name', 'active'];
				$dtls = \eBizIndia\Company::getList($options);	
				if(empty($dtls)){
					$result['error_code'] = 2;
					$result['msg'] = 'Company not found.';
				}else if($dtls[0]['active']=='n'){
					$result['error_code'] = 3;
					$result['msg'] = 'You cannot load an inactive company.';
				}else{
					$_SESSION['loaded_comp_id'] = $rec_id; // needs to be activated for the loaded company to be available across the modules in the session
					$result['error_code'] = 0;
					$result['msg'] = 'The selected company was loaded successfully.';
				}
			}
			echo json_encode($result);
			exit;	
		case 'unloadCompany':
			$result = ['error_code'=> 0, 'msg' =>''];
			if(($_loaded_comp->company_details['id']??0)<=0){
				$result['error_code'] = 1;
				$result['msg'] = 'No company has been loaded yet.';
			}else{
				unset($_SESSION['loaded_comp_id']);
				$result['error_code'] = 0;
				$result['msg'] = 'The loaded company has been successfully unloaded.';
			}

			echo json_encode($result);
			exit;

		case 'getCompaniesForImport':
			$r = $_loaded_comp->hasTransactions();
			if($r){
				echo json_encode(['ec'=>1]);
				exit;
			}else if($r===null){
				echo json_encode(['ec'=>2]);
				exit;
			}

			$options = [];
			$options['filters'] = [
				['field'=>'period_start', 'type'=>'LESS_THAN_EQUAL', 'value'=> $_loaded_comp->company_details['period_start'] ]
			];
			$options['order_by'] = [
				['field'=>'period_end', 'type'=>'DESC'],
				['field'=>'name', 'type'=>'ASC'],
			];

			$options['fieldstofetch'] = ['id', 'name', 'period_start_dmy', 'period_end_dmy'];
			$recs = \eBizIndia\Company::getList($options);
			echo json_encode([$recs, $_loaded_comp->company_details['id']??0]);
			exit;
		case 'importMaster':
			$result = ['error_code'=> 0, 'msg' =>''];
			if(($_loaded_comp->company_details['id']??0)<=0){
				$result['error_code'] = 1;
				$result['msg'] = 'No company has been loaded yet.';
			}else if(!$usercls->verifyPassword($_POST['p'])){
				$result['error_code'] = 2;
				$result['msg'] = 'Invalid password.';
			}else{
				$from_comp = new \eBizIndia\Company($_POST['importfromcomp']);
				if(($from_comp->company_details['id']??0)<=0){
					$result['error_code'] = 3;
					$result['msg'] = 'The source company was not found.';	
				}else{
					if(!\eBizIndia\Company::importMaster($from_comp, $usercls, $_POST)){
						$result['error_code'] = 4;
						$result['msg'] = 'Import failed.';		
					}
				}

			}

			echo json_encode($result);
			exit;
			
	}
}

/*
{act: "importMaster", importfromcomp: "28",…}
act
: 
"importMaster"
active_only
: 
{emp: "y", achd: "y"}
import
: 
{emp: "y", geninc: "y", inccat: "y", mt: "y", stg: "y", achd: "y"}
import_action
: 
{emp: "MERGE", geninc: "REPLACE", inccat: "MERGE", mt: "MERGE", stg: "MERGE", achd: "REPLACE"}
importfromcomp
: 
"28"


*/


?>