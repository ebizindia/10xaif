<?php
namespace eBizIndia;
class Payment{
	private $pmt_for;
	private $pmt_for_id;
	private $pmt_req;
	private $instaobj;

	function __construct($instamojo_vars, $pmt_for='', $pmt_for_id=null){
		$this->pmt_for = $pmt_for;
		$this->pmt_for_id = $pmt_for_id;
		$this->pmt_req = [];
		// if(!empty($pmt_req_id)){
		// 	$this->initPaymentReqDetails($pmt_req_id);
		// }
		$this->instaobj = \Instamojo\Instamojo::init('app',[
		    "client_id" =>  $instamojo_vars['client_id'],  // 'test_DN07H7xZDCHR7yNzI55cDKgosDpgVBEeSir',
		    "client_secret" =>  $instamojo_vars['client_secret'], // 'test_wU6gXPWfjHmdUya2WesPgwbI1cKFqmm0wLepijr9H0gAWJsCRSnqPeljJaRP3JR4ApSJuiIBorC8xYnjk63Z8zbVGUF27JU6XnuAccekLGEhkOhT6Ls19nvMQHv'
		   
		],$instamojo_vars['sandbox']); // true for using the sandbox
	}

	function __get($name){
		if(in_array($name, ['pmt_for', 'pmt_for_id', 'pmt_req']))
			return $this->{$name};
	}

	function getPaymentReqDetails(string $pmt_req_id='', array $paid_for=[] , array $fields_to_fetch=[]){
		if($pmt_req_id=='' && (empty($paid_for['paid_for']) || $paid_for['paid_for_id']<=0) ){
			if(empty($this->pmt_for) || $this->pmt_for_id<=0)
				return false;
			$paid_for['paid_for'] = $this->pmt_for;
			$paid_for['paid_for_id'] = $this->pmt_for_id;
		}
		if(empty($fields_to_fetch))
			$sql = "SELECT `pmt_gateway`, `pmt_req_created_on`, `pmt_for`, `pmt_for_id`, `pmt_req_amount`, `pmt_req_purpose`, `pmt_req_status` from `online_payments` ";
		else{
			// validate the field characters in the field names before using in the query
			$tmp = array_filter($fields_to_fetch, function($fld){
				return preg_match("/[^A-Za-z0-9_]+/", $fld);
			});
			if(!empty($tmp))
				return false;

			$sql = "SELECT `".implode('`, `', $fields_to_fetch)."` from `online_payments` ";
		}	


		$int_data = $str_data = [];
		if($pmt_req_id!=''){
			$sql .= " WHERE `pmt_request_id`=:pmt_req_id ";
			$str_data = [
				':pmt_req_id' => $pmt_req_id,
			];
		}else if($paid_for['paid_for']!='' && $paid_for['paid_for_id']>0){
			$sql .= " WHERE `pmt_for`=:paid_for and pmt_for_id=:paid_for_id";
			$str_data = [
				':paid_for' => $paid_for['paid_for'],
			];
			$int_data = [
				':paid_for_id' => $paid_for['paid_for_id'],
			];
			
		}
		
		$error_details_to_log = [];
		$error_details_to_log['function'] = __METHOD__;
		$error_details_to_log['sql'] = $sql;
		$error_details_to_log['str_data'] = $str_data;
		$error_details_to_log['int_data'] = $int_data;
		
		try{
			$pdo_stmt_obj = PDOConn::query($sql, $str_data, $int_data);
			$res = $pdo_stmt_obj->fetch(\PDO::FETCH_ASSOC);
			return empty($res)?[]:$res;
		}catch(\Exception $e){
			if(!is_a($e, '\PDOStatement'))
				ErrorHandler::logError($error_details_to_log,$e);
			else
				ErrorHandler::logError($error_details_to_log);
			return false;
		}
	}

	function generatePaymentReq($data){
		try{
			$this->pmt_req =  $this->instaobj->createPaymentRequest($data);
			return $this->pmt_req;
		}catch(\Exception $e){
			$this->pmt_req = [];
			ErrorHandler::logError([__METHOD__,'Data for PR: '.print_r($data, true)]);
			throw $e;
		}
	}

	function recordPaymentRequest(){
		$pmt_req_created_on = date('Y-m-d H:i:s', strtotime($this->pmt_req['created_at'])); // from UTC to the apllication's timezone
		$data = [
			'pmt_gateway' => 'instamojo',
			'pmt_request_id' => $this->pmt_req['id'],
			'pmt_req_created_on' => $pmt_req_created_on,
			'pmt_for' => $this->pmt_for,
			'pmt_for_id' => $this->pmt_for_id,
			'pmt_req_amount' => $this->pmt_req['amount'],
			'pmt_req_purpose' => $this->pmt_req['purpose'],
			'pmt_req_status' => $this->pmt_req['status'],
			'last_updated_on' => date('Y-m-d H:i:s'),
		];
		$str_data = $int_data = [];
		$sql = "INSERT INTO online_payments set ";
		$place_holders = [];
		foreach ($data as $fld => $value) {
			$place_holders[] = "`$fld`=:$fld";
			if(empty($value))
				$value=null;
			if($fld=='pmt_for_id'){
				$int_data[":$fld"] = $value;
			}else{
				$str_data[":$fld"] = $value;
			}
		}
		$sql .= implode(', ',$place_holders);

		$error_details_to_log = [];
		$error_details_to_log['at'] = date('Y-m-d H:i:s');
		$error_details_to_log['function'] = __METHOD__;
		$error_details_to_log['data'] = $data;
		$error_details_to_log['sql'] = $sql;

		try{
			$stmt_obj = PDOConn::query($sql, $str_data, $int_data);
			return PDOConn::lastInsertId();
		}catch(\Exception $e){
			if(!is_a($e, '\PDOStatement'))
				ErrorHandler::logError($error_details_to_log,$e);
			else
				ErrorHandler::logError($error_details_to_log);
			return false;
		}


	}

	function updatePaymentInfo($pmt_req_id, $data){ // update the payment request row with the payment info
		$sql="UPDATE online_payments SET ";
		$str_data = [];
		$str_data[':pmt_request_id'] = $pmt_req_id;
		$whereclause=" WHERE `pmt_request_id`=:pmt_request_id and `pmt_req_status`='Pending'"; // only pending payment request should be allowed to be updated
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

		if(!empty($values)){
			$values[]="`last_updated_on`=:last_updated_on";
			$str_data[':last_updated_on'] = date('Y-m-d H:i:s');
		}

		$sql.=implode(',',$values);
		$sql.=$whereclause;

		$error_details_to_log = [];
		$error_details_to_log['at'] = date('Y-m-d H:i:s');
		$error_details_to_log['function'] = __METHOD__;
		$error_details_to_log['str_data'] = $str_data;
		$error_details_to_log['sql'] = $sql;

		try{
			$stmt_obj = PDOConn::query($sql, $str_data);
			$affetcedrows= $stmt_obj->rowCount();
			if($affetcedrows<=0){
				$error_details_to_log['affetcedrows'] = $affetcedrows;
				throw new Exception("Error Processing Request", 1);

			}
				//return false;
			return true;
		}catch(\Exception $e){
			if(!is_a($e, '\PDOStatement'))
				ErrorHandler::logError($error_details_to_log,$e);
			else
				ErrorHandler::logError($error_details_to_log);
			return false;

		}
	}

	function updatePaymentStatus($pmt_for, $pmt_for_id, $pmt_details){
		if(empty($pmt_for) || empty($pmt_for_id) || empty($pmt_details) )
			return false;

		if($pmt_for=='event_reg'){

			$sql = "Update event_registrations set reg_status=:reg_status,payment_status=:payment_status, payment_mode=:payment_mode, paid_on=:paid_on, payment_status_updated_on=:pmt_status_updated_on ";
			$str_data = [
				':reg_status' => $pmt_details['payment_status']=='Paid'?'Confirmed':'Pending',
				':payment_status' => $pmt_details['payment_status'],
				':payment_mode' => $pmt_details['payment_mode'],
				':paid_on' => $pmt_details['paid_on'],
				':pmt_status_updated_on' => date('Y-m-d H:i:s'),
			];
			$int_data = [
				':pmt_for_id' => $pmt_for_id,
			];

			if(isset($pmt_details['amount_paid'])){
				$sql .= " , amount_paid=:amount_paid";
				$str_data[':amount_paid'] = $pmt_details['amount_paid'];
			}
			$sql .= " WHERE id=:pmt_for_id";
			

		}else if($pmt_for=='mem_reg'){
			$sql = "Update member_regs set payment_status=:payment_status, payment_mode=:payment_mode, paid_on=:paid_on ";
			$str_data = [
				':payment_status' => $pmt_details['payment_status'],
				':payment_mode' => $pmt_details['payment_mode'],
				':paid_on' => $pmt_details['paid_on'],
			];
			$int_data = [
				':pmt_for_id' => $pmt_for_id,
			];

			if(isset($pmt_details['amount_paid'])){
				$sql .= " , amount_paid=:amount_paid, payment_txn_ref=:payment_txn_ref, payment_instrument_type=:payment_instrument_type, payment_instrument=:payment_instrument";
				$str_data[':amount_paid'] = $pmt_details['amount_paid'];
				$str_data[':payment_txn_ref'] = $pmt_details['bank_reference_number'];
				$str_data[':payment_instrument_type'] = $pmt_details['pmt_instrument_type'];
				$str_data[':payment_instrument'] = $pmt_details['pmt_billing_instrument'];
			}
			$sql .= " WHERE id=:pmt_for_id";
		}
		else
			return false;

		$error_details_to_log = [];
		$error_details_to_log['at'] = date('Y-m-d H:i:s');
		$error_details_to_log['function'] = __METHOD__;
		$error_details_to_log['str_data'] = $str_data;
		$error_details_to_log['int_data'] = $int_data;
		$error_details_to_log['sql'] = $sql;

		try{
			$stmt_obj = PDOConn::query($sql, $str_data, $int_data);
			$affetcedrows= $stmt_obj->rowCount();
			if($affetcedrows<=0){
				$error_details_to_log['affetcedrows'] = $affetcedrows;
				throw new Exception("Error Processing Request", 1);
				// return false;
			}
			return true;
		}catch(\Exception $e){
			if(!is_a($e, '\PDOStatement'))
				ErrorHandler::logError($error_details_to_log,$e);
			else
				ErrorHandler::logError($error_details_to_log);
			return false;

		}
	}

	function handlePaymentRespose($pmt_resp, $update_booking_record=true){
		$payment_details_for_update = [];
		if(empty($pmt_resp) || empty($pmt_resp['payment_id']) || empty($pmt_resp['payment_status'])  || empty($pmt_resp['payment_request_id']))
			return [1];
		$pmt_req_details = $this->getPaymentReqDetails($pmt_resp['payment_request_id']);
		if(empty($pmt_req_details))
			return [2];
		
		$pmt_for = $pmt_req_details['pmt_for'];
		$pmt_for_id = $pmt_req_details['pmt_for_id'];

		if($pmt_req_details['pmt_req_status']=='Completed')
			return [3, $pmt_for_id]; // This payment request is already marked as completed so it cannot be updated any further 


		// fetch payment request details and payment details via API calls
		try{
			$payment_request_details = $this->instaobj->getPaymentRequestDetails($pmt_resp['payment_request_id']);
		}catch(\Exception $e){
			ErrorHandler::logError([__METHOD__.': Fetching of the payment request details via API call failed.'], $e);
			$payment_request_details = [];
		}
		// ErrorHandler::logError(['$pmt_req_details'=>$pmt_req_details]);
		if(!empty($payment_request_details)){
			$data_to_update = [];
			$data_to_update['pmt_req_modified_on'] = date('Y-m-d H:i:s', strtotime($payment_request_details['modified_at'])); // get the time stamp in apps timezone from UTC
			$data_to_update['pmt_req_status'] = $payment_request_details['status'];
			$data_to_update['pmt_id'] = $pmt_resp['payment_id'];
			try{
				$payment_details = $this->instaobj->getPaymentDetails($pmt_resp['payment_id']);
				$payment_details_for_update = [
					'payment_status' => $pmt_resp['payment_status']=='Credit'?'Paid':'Failed',
					'payment_mode' => 'Online',
					'amount_paid' => $payment_details['amount'], //+$payment_details['fees']+ $payment_details['total_taxes'],
					'paid_on' => $pmt_for=='mem_reg'?date('Y-m-d H:i:s', strtotime($payment_details['completed_at'])):date('Y-m-d'),
					'bank_reference_number' => $payment_details['bank_reference_number'],
					'pmt_instrument_type' => $payment_details['instrument_type'],
					'pmt_billing_instrument' => $payment_details['billing_instrument'],
				];
			}catch(\Exception $e){
				$payment_details = [];
				$payment_details_for_update = [
					'payment_status' => $pmt_resp['payment_status']=='Credit'?'Paid':'Failed',
					'payment_mode' => 'Online',
					'paid_on' => $pmt_for=='mem_reg'?date('Y-m-d H:i:s'):date('Y-m-d'),
				];
				ErrorHandler::logError([__METHOD__.': Fetching of the payment details via API call failed.'], $e); 
			}
			// ErrorHandler::logError(['$payment_details'=>$payment_details, '$payment_details_for_update'=>$payment_details_for_update]);
			if(!empty($payment_details)){
				$data_to_update['pmt_currency'] = $payment_details['currency'];
				$data_to_update['pmt_amount'] = $payment_details['amount'];
				$data_to_update['pmt_fees'] = $payment_details['fees'];
				$data_to_update['pmt_total_taxes'] = $payment_details['total_taxes'];
				$data_to_update['pmt_instrument_type'] = $payment_details['instrument_type'];
				$data_to_update['pmt_billing_instrument'] = $payment_details['billing_instrument'];
				$data_to_update['pmt_bank_reference_number'] = $payment_details['bank_reference_number'];
				$data_to_update['pmt_failure_reason'] = empty($payment_details['failure']['reason'])?null:$payment_details['failure']['reason'];
				$data_to_update['pmt_failure_msg'] = empty($payment_details['failure']['message'])?null:$payment_details['failure']['message'];
				$data_to_update['pmt_completed_at'] = date('Y-m-d H:i:s', strtotime($payment_details['completed_at']));
			}
			
			// ErrorHandler::logError(['$data_to_update'=>$data_to_update]);
			
			if($update_booking_record && !$this->updatePaymentStatus($pmt_for, $pmt_for_id, $payment_details_for_update ))
				return [4, $pmt_for_id, $payment_details_for_update, $payment_details]; // payment details could not be updated in the booking record

			if(!$this->updatePaymentInfo($pmt_resp['payment_request_id'], $data_to_update))
				return [5, $pmt_for_id, $payment_details_for_update, $payment_details]; // The booking record was updated with the payment status but the payment record could not be updated
			if(!isset($data_to_update['pmt_currency']))
				return [6, $pmt_for_id, $payment_details_for_update, $payment_details]; // The booking record was updated with the payment status but the payment record could not be updated with the payment details as the API call for fetching the payment details failed.
			
		}else{
			$payment_details_for_update = [
				'payment_status' => $pmt_resp['payment_status']=='Credit'?'Paid':'Failed',
				'payment_mode' => 'Online',
				'paid_on' => date('Y-m-d'),
			];
			// ErrorHandler::logError(['$payment_details_for_update'=>$payment_details_for_update]);
			
			if($update_booking_record && !$this->updatePaymentStatus($pmt_for, $pmt_for_id, $payment_details_for_update ))
				return [8, $pmt_for_id]; // payment details could not be updated in the booking record

			return [7, $pmt_for_id, $payment_details_for_update, $payment_details]; // The booking record was updated with the payment status but the payment record could not be updated as the API call for fetching the payment request details failed.
		}

		return [0, $pmt_for_id, $payment_details_for_update, $payment_details]; 
	}

}