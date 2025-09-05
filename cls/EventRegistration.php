<?php
namespace eBizIndia;
class EventRegistration{
	private $event_reg_id;
	private $mem_id;
	public function __construct(?int $event_reg_id=null, ?int $mem_id=null){
		$this->event_reg_id = $event_reg_id;
		$this->mem_id = $mem_id;
	}

	public function getDetails(){
		if(empty($this->event_reg_id))
			return false;
		$options = [];
		$options['filters'] = [
			[ 'field' => 'id', 'type' => 'EQUAL', 'value' => $this->event_reg_id ]
		];
		if(!empty($this->mem_id))
			$options['filters'][] = [ 'field' => 'member_id', 'type' => 'EQUAL', 'value' => $this->mem_id ];
		return  self::getList($options);
	}

	public function validate($data, $mode='add', $other_data=[]){
		$result['error_code'] = 0;
		
		if($data['event_id']==''){
			$result['error_code']=2;
			$result['message'][]="Please select an event to register for.";
			$result['error_fields'][]="#event_selector";
		}else if($data['no_of_tickets']=='' || $data['no_of_tickets']<=0){
			$result['error_code']=2;
			$result['message'][]="Please enter the number of persons.";
			$result['error_fields'][]="#add_form_field_nooftickets";
		}else{
			$ev_obj = new \eBizIndia\Event($data['event_id']);
			$event_details = $ev_obj->getDetails();	
			$result['event_details'] = $event_details; 

			if(empty($event_details[0])){
				$result['error_code']=2;
				$result['message'][]="The selected event was not found.";
				$result['error_fields'][]="#event_selector";
			}else if($event_details[0]['active']=='n' || $event_details[0]['reg_active']=='n'){
				$result['error_code']=2;
				$result['message'][]="The selected event is not available for registration at this time.";
				$result['error_fields'][]="#event_selector";
			}else if($data['no_of_tickets'] > ($event_details[0]['max_tkt_per_person']-$other_data['tkts_already_booked']) ){
				$tkts_allowed = $event_details[0]['max_tkt_per_person']-$other_data['tkts_already_booked'];
				if($tkts_allowed>0)
					$result['message'][]="The number of persons should be ".$tkts_allowed." or less.";
				else	
					$result['message'][]="You have already registered for the maximum number of allowed persons.";
				$result['error_fields'][]="#add_form_field_nooftickets";
			}else{
				$today = new \DateTime();
				$ev_start = new \DateTime($event_details[0]['start_dt'].' 00:00:00');
				$ev_end = new \DateTime($event_details[0]['end_dt'].' 23:59:59');
				$ev_reg_start = new \DateTime($event_details[0]['reg_start_dt'].' 00:00:00');
				$ev_reg_end = new \DateTime($event_details[0]['reg_end_dt'].' 23:59:59');

				if($today>$ev_end){
					$result['error_code']=2;
					$result['message'][]="The event is already over.";
					$result['error_fields'][]="#event_selector";
				}else if($today<$ev_reg_start){
					$result['error_code']=2;
					$result['message'][]="The regitrations for this event has not started yet.";
					$result['error_fields'][]="#event_selector";
				}else if($today>$ev_reg_end){
					$result['error_code']=2;
					$result['message'][]="The regitration for this event has been closed.";
					$result['error_fields'][]="#event_selector";
				}
			}

		}

		return $result;
	}


	public static function isEarlyBirdOfferApplicable($ev_details){
		if(empty($ev_details))
			return false;
		$offer = ['offer'=>'', 'offer_tkt_price'=>''];
		$early_bird_applicable = true;
		if($ev_details['early_bird']=='y'){
			if(!empty($ev_details['early_bird_end_dt'])){
				$today = strtotime(date('Y-m-d'));
				$early_bird_end_dt_tm = strtotime($ev_details['early_bird_end_dt']);
				if($early_bird_end_dt_tm<$today){
					$early_bird_applicable = false;
				}
			}

			if($early_bird_applicable===true){
				if(!empty($ev_details['early_bird_max_cnt'])){
					// get the confirmed counts
					$options = [];
					$options['fieldstofetch']= ['ev_id', 'bookings', 'tot_tickets'];
					$options['filters'] = [
						['field'=>'ev_id', 'type'=>'EQUAL', 'value'=>$ev_details['id']],
						['field'=>'reg_status', 'type'=>'EQUAL', 'value'=>'Confirmed'],
					];
					$confirmed_tkts = self::getEventWiseSummaryList($options);
					
					if($confirmed_tkts===false){
						return false;
					}
					
					if($ev_details['early_bird_max_cnt']<=$confirmed_tkts[0]['tot_tickets']){
						// Still a few early bird tickets are available
						$early_bird_applicable = false;
					}
					
				}else if($ev_details['early_bird_max_cnt']===0){
					// Count of 0 has explicitly been set, maybe to close the EB offer, so the EB offer is not applicable anymore even if an EB end date is available and is within the allowed range.
					$early_bird_applicable = false;
				}
			}
			


			/**/

			if($early_bird_applicable)
				$offer = ['offer'=>'EB', 'offer_tkt_price'=>$ev_details['early_bird_tkt_price']];

		}

		return $offer;

	}



	public static function getEventWiseSummaryList($options=[]){
		$data = [];
		$fields_mapper = [];

		$fields_mapper['*']="ev.id as ev_id, ev.name as ev_name, ev.start_dt as ev_start_dt, ev.end_dt as ev_end_dt, ev.reg_start_dt as ev_reg_start_dt, ev.reg_end_dt as ev_reg_end_dt, ev.active as ev_active, ev.time_text as ev_time_text, ev.reg_active as ev_reg_active, COUNT(evreg.id) as bookings, SUM(evreg.no_of_tickets) as tot_tickets, SUM(IFNULL(attn.persons_entered,0)) as attended, SUM(evreg.total_amount) as tot_amount ";

		$fields_mapper['recordcount']='count(1)';
		$fields_mapper['ev_id']="ev.id";
		$fields_mapper['ev_name']="ev.name";
		$fields_mapper['ev_active']="ev.active";
		$fields_mapper['ev_start_dt']="ev.start_dt";
		$fields_mapper['ev_end_dt']="ev.start_dt";
		$fields_mapper['ev_time_text']="ev.time_text";
		$fields_mapper['ev_reg_start_dt']="ev.reg_start_dt";
		$fields_mapper['ev_reg_end_dt']="ev.reg_end_dt";
		$fields_mapper['ev_reg_active']="ev.reg_active";
		$fields_mapper['tot_tickets']="SUM(evreg.no_of_tickets)";
		$fields_mapper['attended']="SUM(IFNULL(attn.persons_entered,0))";
		$fields_mapper['tot_amount']="SUM(evreg.total_amount)";
		$fields_mapper['bookings']="COUNT(evreg.id)";
		$fields_mapper['member_id']="evreg.member_id";
		$fields_mapper['reg_status']="evreg.reg_status";
						
		$where_clause=[];

		$str_params_to_bind=[];
		$int_params_to_bind=[];

		if( array_key_exists('filters',$options) && is_array($options['filters']) ){
			$field_counter=0;
			foreach($options['filters'] as $filter){
				++$field_counter;
				switch ($filter['field']) {
					case 'member_id':
					case 'ev_id':
						switch($filter['type']){
							case 'IN':
								if(is_array($filter['value'])){
									$place_holders=[];
									$k=0;
									foreach($filter['value'] as $id){
										$k++;
										$place_holders[]=":whr".$field_counter."_id_{$k}_";
										$int_params_to_bind[":whr".$field_counter."_id_{$k}_"]=$id;
									}
									$where_clause[] = $fields_mapper[$filter['field']].' in('.implode(',',$place_holders).') ';

								}
								break;

							case 'NOT_IN':
								if(is_array($filter['value'])){
									$place_holders=[];
									$k=0;
									foreach($filter['value'] as $id){
										$k++;
										$place_holders[]=":whr".$field_counter."_id_{$k}_";
										$int_params_to_bind[":whr".$field_counter."_id_{$k}_"]=$id;
									}
									$where_clause[] = $fields_mapper[$filter['field']].' not in('.implode(',',$place_holders).') ';

								}
								break;

							case 'NOT_EQUAL':
								$id=(is_array($filter['value']))?$filter['value'][0]:$filter['value'];
								$where_clause[] = $fields_mapper[$filter['field']].'!=:whr'.$field_counter.'_id';
								$int_params_to_bind[':whr'.$field_counter.'_id']=$id;
								break;

							default:
								$id=(is_array($filter['value']))?$filter['value'][0]:$filter['value'];
								$where_clause[] = $fields_mapper[$filter['field']].'=:whr'.$field_counter.'_id';
								$int_params_to_bind[':whr'.$field_counter.'_id']=$id;
						}

						break;

					case 'reg_status':
					case 'ev_name':
						$name=(is_array($filter['value']))?$filter['value'][0]:$filter['value'];
						$where_clause[] = $fields_mapper[$filter['field']]." like :whr".$field_counter."_name";
						switch($filter['type']){
							case 'CONTAINS':
								$str_params_to_bind[':whr'.$field_counter.'_name']="%$name%";
								break;
							case 'STARTS_WITH':
								$str_params_to_bind[':whr'.$field_counter.'_name']="$name%";
								break;
							case 'ENDS_WITH':
								$str_params_to_bind[':whr'.$field_counter.'_name']="%$name";
								break;
							case 'EQUAL':
							default:
								$str_params_to_bind[':whr'.$field_counter.'_name']="$name";
								break;
						}

						break;		

					case 'registered_on':
						switch($filter['type']){
							case 'BETWEEN':
								$dt1 = ((string)$filter['value'][0]).' 00:00:00';
								$dt2 = ((string)$filter['value'][1]).' 23:59:59';
								$where_clause[] = ' ( '.$fields_mapper['registered_on'].' BETWEEN :whr'.$field_counter.'_dt1 AND :whr'.$field_counter.'_dt2 ) ';
								$str_params_to_bind[':whr'.$field_counter.'_dt1']=$dt1;
								$str_params_to_bind[':whr'.$field_counter.'_dt2']=$dt2;
								break;

						}

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
		
		$order_by_clause = ''; 

		if(array_key_exists('order_by', $options) && is_array($options['order_by'])){
			foreach ($options['order_by'] as $order) {
				if(preg_match("/^(ev|evreg)\./",$fields_mapper[$order['field']])){
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
			if($order_by_clause!='' && !stristr($order_by_clause, 'mg.id')){

				$order_by_clause .= ', '.$fields_mapper['ev_id'].' DESC ';
			}


		}else if($options['order_by']==='random'){
			$order_by_clause .= ' ORDER BY RAND() ';
		}

		

		if(!$record_count && $order_by_clause==''){

			$order_by_clause=" ORDER BY ev.start_dt DESC, ev.name ASC";

			if(!preg_match("/\s+as\s+ev_start_dt/",$select_string)){
				$select_string .= ', '.$fields_mapper['ev_start_dt'].' as ev_start_dt';
			}
			if(!preg_match("/\s+as\s+ev_name/",$select_string)){
				$select_string .= ', '.$fields_mapper['ev_name'].' as ev_name';
			}
		}

		$limit_clause='';

		if(array_key_exists('page', $options) && filter_var($options['page'],FILTER_VALIDATE_INT) && $options['page']>0 && array_key_exists('recs_per_page', $options) && filter_var($options['recs_per_page'],FILTER_VALIDATE_INT) && $options['recs_per_page']>0){

			$limit_clause="LIMIT ".( ($options['page']-1) * $options['recs_per_page'] ).", $options[recs_per_page] ";

		}

		$where_clause_string = '';
		if(!empty($where_clause))
			$where_clause_string = ' WHERE '.implode(' AND ', $where_clause); 

		$cuml_fig_join = '';
		if(($options['cuml_fig_confirmed_only']??false)===true)
			$cuml_fig_join = ' AND evreg.reg_status="Confirmed"';

		$attended_join = '';
		if(preg_match("/attn\./", "$select_string $where_clause_string $order_by_clause"))
			$attended_join = ' LEFT JOIN  `'.CONST_TBL_PREFIX.'event_attendees_entry` attn ON evreg.id=attn.ev_reg_id ';

		$sql="SELECT $select_string from `".CONST_TBL_PREFIX."events` ev LEFT JOIN `".CONST_TBL_PREFIX."event_registrations` as evreg ON ev.id=evreg.event_id $cuml_fig_join $attended_join $where_clause_string GROUP BY ev.id $order_by_clause $limit_clause"; 

		$error_details_to_log = [];
		$error_details_to_log['function'] = __METHOD__;
		$error_details_to_log['sql'] = $sql;
		$error_details_to_log['str_params_to_bind'] = $str_params_to_bind;
		$error_details_to_log['int_params_to_bind'] = $int_params_to_bind;

		try{
			$pdo_stmt_obj = PDOConn::query($sql, $str_params_to_bind, $int_params_to_bind);
			if(array_key_exists('resourceonly', $options) && $options['resourceonly'])
				return $pdo_stmt_obj;

			$data = $pdo_stmt_obj->fetchAll(\PDO::FETCH_ASSOC);

			return $data;

		}catch(\Exception $e){
			if(!is_a($e, '\PDOStatement'))
				ErrorHandler::logError($error_details_to_log,$e);
			else
				ErrorHandler::logError($error_details_to_log);
			return false;
		}

	}



	public static function getList($options=[]){
		$data = [];
		$fields_mapper = [];

		$fields_mapper['*']="evreg.*, ev.name as ev_name, ev.description as ev_description, ev.venue as ev_venue, ev.dsk_img as ev_dsk_img, ev.mob_img as ev_mob_img, ev.max_tkt_per_person as ev_max_tkt_per_person, ev.active as ev_active, ev.start_dt as ev_start_dt, ev.end_dt as ev_end_dt, ev.time_text as ev_time_text, ev.reg_start_dt as ev_reg_start_dt, ev.reg_end_dt as ev_reg_end_dt, ev.reg_active as ev_reg_active, mem.name as mem_name, mem.email as mem_email, mem.batch_no as mem_batch_no , mem.profile_pic as mem_profile_pic , mem.membership_no as mem_membership_no, mem.mobile as mem_mobile, COALESCE(mem.work_company,'') as mem_work_company";

		$fields_mapper['recordcount']='count(1)';
		$fields_mapper['id']="evreg.id";
		$fields_mapper['booking_id']="evreg.booking_id";
		$fields_mapper['qr_code']="evreg.qr_code";
		$fields_mapper['event_id']="evreg.event_id";
		$fields_mapper['member_id']="evreg.member_id";
		$fields_mapper['no_of_tickets']="evreg.no_of_tickets";
		$fields_mapper['offer']="evreg.offer";
		$fields_mapper['price_per_tkt']="evreg.price_per_tkt";
		$fields_mapper['gst_perc']="evreg.gst_perc";
		$fields_mapper['conv_fee']="evreg.conv_fee";
		$fields_mapper['total_amount']="evreg.total_amount";
		$fields_mapper['payment_mode']="evreg.payment_mode";
		$fields_mapper['amount_paid']="evreg.amount_paid";
		$fields_mapper['paid_on']="evreg.paid_on";
		$fields_mapper['registered_on']="evreg.registered_on";
		$fields_mapper['reg_status']="evreg.reg_status";
		$fields_mapper['payment_status']="evreg.payment_status";
		$fields_mapper['payment_status_updated_on']="COALESCE(evreg.payment_status_updated_on,'')";
		$fields_mapper['ev_name']="ev.name";
		$fields_mapper['ev_description']="ev.description";
		$fields_mapper['ev_venue']="ev.venue";
		$fields_mapper['ev_dsk_img']="ev.dsk_img";
		$fields_mapper['ev_mob_img']="ev.mob_img";
		$fields_mapper['ev_max_tkt_per_person']="ev.max_tkt_per_person";
		$fields_mapper['ev_active']="ev.active";
		$fields_mapper['ev_start_dt']="ev.start_dt";
		$fields_mapper['ev_end_dt']="ev.start_dt";
		$fields_mapper['ev_time_text']="ev.time_text";
		$fields_mapper['ev_reg_start_dt']="ev.reg_start_dt";
		$fields_mapper['ev_reg_end_dt']="ev.reg_end_dt";
		$fields_mapper['ev_reg_active']="ev.reg_active";
		$fields_mapper['mem_name']="mem.name";
		$fields_mapper['mem_email']="mem.email";
		$fields_mapper['mem_batch_no']="mem.batch_no";
		$fields_mapper['mem_profile_pic']="mem.profile_pic";
		$fields_mapper['mem_membership_no']="mem.membership_no";
		$fields_mapper['mem_mobile']="mem.mobile";
		$fields_mapper['mem_work_company']="COALESCE(mem.work_company,'')";
		$fields_mapper['attended']="SUM(attn.persons_entered) OVER(PARTITION BY evreg.event_id, attn.ev_reg_id)";
		
				
		$where_clause=[];

		$str_params_to_bind=[];
		$int_params_to_bind=[];

		if( array_key_exists('filters',$options) && is_array($options['filters']) ){
			$field_counter=0;
			foreach($options['filters'] as $filter){
				++$field_counter;
				switch ($filter['field']) {
					case 'attended':
						// DO nothing. A case of this field has been created just to clarify that no where clause is allowed on this field.
						break;
					case 'id':
					case 'event_id':
					case 'member_id':
						switch($filter['type']){
							case 'IN':
								if(is_array($filter['value'])){
									$place_holders=[];
									$k=0;
									foreach($filter['value'] as $id){
										$k++;
										$place_holders[]=":whr".$field_counter."_id_{$k}_";
										$int_params_to_bind[":whr".$field_counter."_id_{$k}_"]=$id;
									}
									$where_clause[] = $fields_mapper[$filter['field']].' in('.implode(',',$place_holders).') ';

								}
								break;

							case 'NOT_IN':
								if(is_array($filter['value'])){
									$place_holders=[];
									$k=0;
									foreach($filter['value'] as $id){
										$k++;
										$place_holders[]=":whr".$field_counter."_id_{$k}_";
										$int_params_to_bind[":whr".$field_counter."_id_{$k}_"]=$id;
									}
									$where_clause[] = $fields_mapper[$filter['field']].' not in('.implode(',',$place_holders).') ';

								}
								break;

							case 'NOT_EQUAL':
								$id=(is_array($filter['value']))?$filter['value'][0]:$filter['value'];
								$where_clause[] = $fields_mapper[$filter['field']].'!=:whr'.$field_counter.'_id';
								$int_params_to_bind[':whr'.$field_counter.'_id']=$id;
								break;

							default:
								$id=(is_array($filter['value']))?$filter['value'][0]:$filter['value'];
								$where_clause[] = $fields_mapper[$filter['field']].'=:whr'.$field_counter.'_id';
								$int_params_to_bind[':whr'.$field_counter.'_id']=$id;
						}

						break;

					case 'qr_code':
					case 'mem_name':
					case 'mem_membership_no':
					case 'ev_name':
					case 'ev_description':
					case 'ev_venue':
					case 'booking_id':
						$name=(is_array($filter['value']))?$filter['value'][0]:$filter['value'];
						$where_clause[] = $fields_mapper[$filter['field']]." like :whr".$field_counter."_name";
						switch($filter['type']){
							case 'CONTAINS':
								$str_params_to_bind[':whr'.$field_counter.'_name']="%$name%";
								break;
							case 'STARTS_WITH':
								$str_params_to_bind[':whr'.$field_counter.'_name']="$name%";
								break;
							case 'ENDS_WITH':
								$str_params_to_bind[':whr'.$field_counter.'_name']="%$name";
								break;
							case 'EQUAL':
							default:
								$str_params_to_bind[':whr'.$field_counter.'_name']="$name";
								break;
						}

						break;		

					case 'ev_falls_in_period':
						$dt1 = $filter['value'][0];
						$dt2 = $filter['value'][1];

						if($dt1!='' && $dt2==''){
							$where_clause[] = ' '.$fields_mapper['ev_end_dt']. ' >= :whr'.$field_counter.'_dt1 ';	
							$str_params_to_bind[':whr'.$field_counter.'_dt1']=$dt1;
						}else if($dt1=='' && $dt2!=''){
							$where_clause[] = ' '.$fields_mapper['ev_start_dt']. ' <= :whr'.$field_counter.'_dt2 ';	
							$str_params_to_bind[':whr'.$field_counter.'_dt2']=$dt2;
						}else if($dt1!='' && $dt2!=''){
							$where_clause[] = ' ('.$fields_mapper['ev_end_dt']. ' >= :whr'.$field_counter.'_dt1 AND '.$fields_mapper['ev_start_dt']. ' <= :whr'.$field_counter.'_dt2  )';	
							$str_params_to_bind[':whr'.$field_counter.'_dt1']=$dt1;
							$str_params_to_bind[':whr'.$field_counter.'_dt2']=$dt2;
						}

						// $where_clause[] = ' ( (:whr'.$field_counter.'_dt1 between '.$fields_mapper['ev_start_dt'].' and '.$fields_mapper['ev_end_dt']. ' ) OR (:whr'.$field_counter.'_dt2 between '.$fields_mapper['ev_start_dt'].' and '.$fields_mapper['ev_end_dt']. ' ) OR ('.$fields_mapper['ev_start_dt'].' BETWEEN :whr'.$field_counter.'_dt1 AND :whr'.$field_counter.'_dt2 )  OR ('.$fields_mapper['ev_end_dt'].' BETWEEN :whr'.$field_counter.'_dt1 AND :whr'.$field_counter.'_dt2 )   ) ';
						// $str_params_to_bind[':whr'.$field_counter.'_dt1']=$dt1;
						// $str_params_to_bind[':whr'.$field_counter.'_dt2']=$dt2;
						break;
					case 'registered_on':
						switch($filter['type']){
							case 'BETWEEN':
								$dt1 = ((string)$filter['value'][0]).' 00:00:00';
								$dt2 = ((string)$filter['value'][1]).' 23:59:59';
								$where_clause[] = ' ( '.$fields_mapper['registered_on'].' BETWEEN :whr'.$field_counter.'_dt1 AND :whr'.$field_counter.'_dt2 ) ';
								$str_params_to_bind[':whr'.$field_counter.'_dt1']=$dt1;
								$str_params_to_bind[':whr'.$field_counter.'_dt2']=$dt2;
								break;

						}

						break;
					case 'reg_status':
					
						$status=(is_array($filter['value']))?$filter['value'][0]:$filter['value'];
						switch($filter['type']){
							case 'NOT_EQUAL':
								$where_clause[] = $fields_mapper[$filter['field']].' !=:whr'.$field_counter.'_regstatus';
								$str_params_to_bind[':whr'.$field_counter.'_regstatus']=$status;
								break;
							default:
								$where_clause[] = $fields_mapper[$filter['field']].'=:whr'.$field_counter.'_regstatus';
								$str_params_to_bind[':whr'.$field_counter.'_regstatus']=$status;
						}

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
				if($field=='attended')
					continue; // group by is not allowed on this field
				if(preg_match("/^(ev|evreg|mem)\./",$fields_mapper[$field]))
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
				if($order['field']=='attended')
					continue; // Order by is not allowed on this field

				if(preg_match("/^(ev|evreg|mem)\./",$fields_mapper[$order['field']])){
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
			if($order_by_clause!='' && !stristr($order_by_clause, 'mg.id')){

				$order_by_clause .= ', '.$fields_mapper['id'].' DESC ';
			}


		}else if($options['order_by']==='random'){
			$order_by_clause .= ' ORDER BY RAND() ';
		}

		

		if(!$record_count && $order_by_clause==''){

			$order_by_clause=" ORDER BY evreg.registered_on DESC, evreg.id DESC";

			if(!preg_match("/\s+as\s+registered_on/",$select_string)){
				$select_string .= ', '.$fields_mapper['registered_on'].' as registered_on';
			}
			if(!preg_match("/\s+as\s+id/",$select_string)){
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

		$event_join = '';
		if(preg_match("/(ev)\./","$select_string $where_clause_string $group_by_clause $order_by_clause"))
			$event_join .= " JOIN ".CONST_TBL_PREFIX."events ev ON evreg.event_id = ev.id ";

		$member_join = '';
		if(preg_match("/(mem)\./","$select_string $where_clause_string $group_by_clause $order_by_clause"))
			$member_join .= " JOIN ".CONST_TBL_PREFIX."members mem ON evreg.member_id = mem.id ";

		$attended_join = '';
		if(preg_match("/(attn)\./","$select_string"))
			$attended_join = ' LEFT JOIN `'.CONST_TBL_PREFIX.'event_attendees_entry` attn ON evreg.id=attn.ev_reg_id  ';


		$sql="SELECT $select_string from `".CONST_TBL_PREFIX."event_registrations` as evreg $event_join $member_join $attended_join $where_clause_string $group_by_clause $order_by_clause $limit_clause"; 

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
				
				$data[] = $row;
				
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


	public static function getAttendedData($ev_reg_id, $cumulative=true){
		if(empty($ev_reg_id))
			return false;

		try{
			if($cumulative)
				$sql = "SELECT er.id as ev_reg_id, er.booking_id, SUM(IFNULL(persons_entered,0)) as attended from `".CONST_TBL_PREFIX . "event_attendees_entry` a RIGHT JOIN `".CONST_TBL_PREFIX . "event_registrations` er ON a.ev_reg_id=er.id and er.id=:ev_reg_id  WHERE er.id=:ev_reg_id GROUP BY  a.ev_reg_id, er.booking_id";
			else
				$sql = "SELECT a.ev_reg_id, er.booking_id, a.persons_entered as attended, a.persons_allowed, a.entry_on, er.event_id, er.member_id  from `".CONST_TBL_PREFIX . "event_attendees_entry` a JOIN `".CONST_TBL_PREFIX . "event_registrations` er ON a.ev_reg_id=er.id and a.ev_reg_id=:ev_reg_id G";

			$int_params = [
				':ev_reg_id' => $ev_reg_id,
			];
			$pdo_stmt_obj = PDOConn::query($sql, [], $int_params);
			$data = $pdo_stmt_obj->fetchAll(\PDO::FETCH_ASSOC);
			return $data;

		}catch(\Exception $e){
			ErrorHandler::logError([],$e);
			return false;
		}
	}

	public function createAttendedEntry($data){
		if($this->event_reg_id=='' || empty($data))
			return false;
		$data['ev_reg_id'] = [$this->event_reg_id, 'int'];
		$values = $str_data = $int_data = [];
		$sql="INSERT INTO `".CONST_TBL_PREFIX . "event_attendees_entry` SET ";

		foreach($data as $field=>$value){ // Each value is an array of two items - the field value and the field type
			$key = ":$field";
			if($value[0]==='')
				$values[]="`$field`=NULL";
			else{
				$values[]="`$field`=$key";
				$bind_type = $value[1].'_data';
				$$bind_type[$key] = $value[0];
			}
		}

		$sql.=implode(',',$values);
		
		$error_details_to_log = [];
		$error_details_to_log['at'] = date('Y-m-d H:i:s');
		$error_details_to_log['function'] = __METHOD__;
		$error_details_to_log['data'] = $data;
		$error_details_to_log['sql'] = $sql;

		try{
			$stmt_obj = PDOConn::query($sql, $str_data, $int_data);
			return true;
		}catch(\Exception $e){
			if(!is_a($e, '\PDOStatement'))
				ErrorHandler::logError($error_details_to_log,$e);
			else
				ErrorHandler::logError($error_details_to_log);
			return false;

		}

	}



	public function saveDetails($data, $id =''){
		$str_data = $int_data = [];
		$table = '`'.CONST_TBL_PREFIX . 'event_registrations`';
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
		}else if($id!=''){ 
			$type='update';
			$sql="UPDATE $table SET ";
			$int_data[':id'] = $id;
			$whereclause=" WHERE `id`=:id";

		}else{ // Inserting new ad
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
		}catch(\Exception $e){
			if(!is_a($e, '\PDOStatement'))
				ErrorHandler::logError($error_details_to_log,$e);
			else
				ErrorHandler::logError($error_details_to_log);
			return false;

		}

	}

	public function updateDetails($data){
		if($this->event_reg_id=='')
			return false;
		return $this->saveDetails($data, $this->event_reg_id);
	}


	public function uploadImage($rec_id, $name, $tmp_name, $type){ 
		if($name=='' || $tmp_name=='')
			return false;
			
		$now = time();	
		$ext = pathinfo($name, PATHINFO_EXTENSION);
		$gen_file_name = CONST_EVENT_IMG_PREFIX . $rec_id.'-'.$type.'-'.uniqid().'.'.strtolower($ext); 
		if(!@move_uploaded_file($tmp_name, CONST_EVENT_IMG_DIR_PATH . $gen_file_name) && !@copy($tmp_name, CONST_EVENT_IMG_DIR_PATH . $gen_file_name)){
			return false;
		}
		
		$result = [];
		$result['gen_file_name'] = $gen_file_name;
		return $result;

	}


	public function calculateBookingAmount($no_of_tickets, $price_per_ticket, $gst_perc, $conv_fee){
		$no_of_tickets = (int)$no_of_tickets;
		$price_per_ticket = (float)$price_per_ticket; // The price set in the events master. Includes GST
		$gst_perc = (float)$gst_perc;
		$conv_fee = (int)$conv_fee;

		// need to the following steps in order to keep the calculation same as javascript
		$price_with_gst = $no_of_tickets * $price_per_ticket;
		$price_without_gst = $price_with_gst * (100/100+$gst_perc);
		$gst = round($price_with_gst - $price_without_gst,2);
		$price_without_gst_rounded = round($price_without_gst, 2);

		return floor($price_without_gst_rounded + $gst + ($no_of_tickets*$conv_fee));
	}


	public function generateBookingId(string $booking_dt_dmy){
		if(empty($booking_dt_dmy))
			return false;

		try{
			$prefix = 'YI/';
			$sl = '00001';
			$fy = (string) getFinancialYearForADate($booking_dt_dmy);
			$fy = substr($fy, 2,2).'-'.substr($fy, 7,2);
			$srch_str = $prefix.$fy.'/';
			$sql = 'SELECT booking_id from `'.CONST_TBL_PREFIX . 'event_registrations` WHERE `booking_id` like :bkid order by id desc limit 1';
			$str_data = [
				':bkid' => "$srch_str%"
			];
			$stmt_obj = PDOConn::query($sql, $str_data);
			$row = $stmt_obj->fetch(\PDO::FETCH_ASSOC);
			if(!empty($row) && !empty($row['booking_id']) ){
				$bk_id_parts = explode('/', $row['booking_id']);
				$sl = (int) $bk_id_parts[2];
				$sl += 1;
				$sl = str_pad((string)$sl, 5, '0', STR_PAD_LEFT);
			}
			return $prefix.$fy.'/'.$sl;

		}catch(\Exception $e){
			if(!is_a($e, '\PDOStatement'))
				ErrorHandler::logError($error_details_to_log,$e);
			return false;
		}

	}

	public static function generateUniqueQRCode($len = 5, $case_sensitive = false){ // $case_sensitive false means case insensitive i.e. only uppercase chars will be returned
		try{
			$max_tries = 4; 
			$try_after = 50000; // micro sec
			while($max_tries>0){
				$max_tries--;
				$qr_code = \eBizIndia\generateRandomString($len, $case_sensitive); 
				$sql = 'SELECT EXISTS(SELECT 1 from `'.CONST_TBL_PREFIX . 'event_registrations` WHERE `qr_code` like :qr_code) as ex';
				$str_data = [
					':qr_code' => $qr_code
				];
				$stmt_obj = PDOConn::query($sql, $str_data);
				$row = $stmt_obj->fetch(\PDO::FETCH_ASSOC);
				if(!empty($row)){
					if($row['ex']!=1){ // found a non existing QR code
						return $qr_code;
					}
				}
				if($max_tries<=0) // max tries exhausted
					throw new Exception('Error generating a unique QR code for event registration.');
				usleep($try_after); // wait before retrying
			}
		}catch(\Exception $e){
			if(!is_a($e, '\PDOStatement'))
				ErrorHandler::logError($error_details_to_log,$e);
			return false;
		}

	}

	function sendTicketBookingConfirmationEmail($email_data, $recp){
// 		$html_msg = <<<EOF
// <!DOCTYPE html> 
// 	<html>
// 		<head>
// 		</head> 
// 		<body>
// 			<p>Hello {$email_data['mem_name']},</p>
// 			<p>Your booking for the event <strong>{$email_data['ev_name']}</strong> is confirmed. Here are the details.</p>
// 			<p>
// 			Booking ID: {$email_data['booking_id']}<br>
// 			Booking Date: {$email_data['booking_date']}<br>
// 			No. of Tickets: {$email_data['no_of_tickets']}<br>
// 			Amount Paid: &#8377;{$email_data['amount_paid']}<br><br>
// 			Event Date(s): {$email_data['ev_dt_period']}<br>
// 			Event Time: {$email_data['ev_time_text']}<br>
// 			</p>
// 			<p>
// 				<a href="{$email_data['booking_details_page']}" >Click HERE</a> to login and view the booking and event details.
// 			</p>
// 			<p>Regards,<br>{$email_data['from_name']}</p>
// 		</body>
// 	</html>	
// EOF;

		$amt_paid = $email_data['amount_paid']<=0?'Free':'&#8377;'.$email_data['amount_paid'];

		$img_uri = CONST_APP_ASSETS_ROOT_URI.'/images/email/';
		$html_msg = <<<EOF
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; ">
</head>
<body>

<table style="width:600px;margin:auto;max-width:600px" width="600" cellspacing="0" cellpadding="0" border="0">
	<tbody>
		<tr>
			<td>
				<table style="font-family:Arial,Helvetica,sans-serif;padding:0;color:#000;width:600px;line-height:22px;background-color:#fff;font-size:14px;text-align:left;box-sizing:content-box;border-collapse:collapse;border-style:solid;border-width:1px;border-color:#dedede" cellspacing="0" cellpadding="0" border="0">
					<tbody>
						<tr>
							<td style="padding:16px;background-color:#72799a" valign="top">
								<table style="width:auto" cellspacing="0" cellpadding="0" border="0" align="left">
									<tbody>
										<tr>
											<td colspan="2" style="text-align:left;padding:8px; color:#ffffff;font-weight:bold;font-size:18px"> {$email_data['ev_name']}												
											</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
						<tr>
							<td valign="top">
								<table style="text-align:left;color:#000000;background-color:#ffffff" width="100%" cellspacing="0" cellpadding="0" border="0">
									<tbody>
										<tr>
											<td style="padding-top:12px;padding-bottom:24px;padding-left:24px;padding-right:24px">
												<table style="width:100%" cellspacing="0" cellpadding="0" border="0">
													<tbody>
														<tr>
															<td style="width: 40px;line-height:0px;padding-top:4px;padding-bottom:4px;padding-left:0px;padding-right:0px;">
																<a moz-do-not-send="true"> <img src="{$img_uri}success-icon.png" alt="image" style="width:26px;max-width:100%" moz-do-not-send="true" width=""> </a>
															</td>
															<td style="padding-top:0px;padding-bottom:0px;padding-left:0px;padding-right:0px">
																<h2><strong>Registration successful</strong></h2>
															</td>
														</tr>
													</tbody>
												</table>
												<table style="width:100%" cellspacing="0" cellpadding="0" border="0">
													<tbody>
														<tr>
															<td style="outline:none; border-color:transparent; text-align:center;" align="center" >	
																<img src="CID:tktqr" alt='QR Code' style="margin:0 auto 20px;" />
															</td>
														</tr>
													</tbody>
												</table>
												<table style="width:100%" cellspacing="0" cellpadding="0" border="0">
													<tbody>
														<tr>
															<td style="padding-top:0px;padding-bottom:0px;padding-left:0px;padding-right:0px; padding-bottom:20px;">
																<p>Hello {$email_data['mem_name']},</p>
																Here's your confirmed order for the <strong>{$email_data['ev_name']} </strong>event.																
															</td>
														</tr>
													</tbody>
												</table>
												<table style="width:100%" cellspacing="0" cellpadding="0" border="0">
													<tbody>
														<tr>
															<td style="padding-top:0px; padding-bottom:0px; padding-left:0px; padding-right:0px;width:30px;padding-bottom:15px; vertical-align: top;">
																<img src="{$img_uri}date_time-icon.png" alt="image" style="width:20px;max-width:100%" moz-do-not-send="true" width="">	
															</td>
															<td style="padding-top:0px; padding-bottom:0px; padding-left:0px; padding-right:0px;padding-bottom:15px; vertical-align: top;">
																{$email_data['ev_dt_period']}<br>
																{$email_data['ev_time_text']}
															</td>
														<tr>
														<tr>
															<td style="padding-top:0px; padding-bottom:30px; padding-left:0px; padding-right:0px;width:30px;vertical-align: top;">
																<img src="{$img_uri}location-icon.png" alt="image" style="width:20px;max-width:100%" moz-do-not-send="true" width="">
															</td>
															<td style="padding-top:0px; padding-bottom:30px; padding-left:0px; padding-right:0px;vertical-align: top;">
																{$email_data['ev_venue']}
															</td>
														<tr>
													<tbody>
												</table>
												<table style="width:100%" cellspacing="0" cellpadding="0" border="0">
													<tbody>
														<tr>
															<td style="font-size:16px;font-weight:bold;line-height:24px; padding-bottom:20px;  padding-top:30px;">
																Registration details																
															</td>
														</tr>
													<tbody>
												</table>
												<table style="font-size:13px;line-height:24px;border-collapse:collapse;border:1px solid #d0d9df;width:100%" cellspacing="0" cellpadding="8">
													<tbody>
														<tr>
															<td style="width:50%;font-size:14px;padding:6px 12px;border-top:1px solid #d0d9df; vertical-align:top; text-align:left;border-bottom:1px solid #d0d9df"><strong>Registration ID: </strong>
															</td>
															<td style="width:50%;font-size:14px;padding:6px 12px;border-top:1px solid #d0d9df; vertical-align:top; text-align:left;border-bottom:1px solid #d0d9df">{$email_data['booking_id']}
															</td>
															
														</tr>
														<tr>
															<td style="width:25%;font-size:14px;padding:6px 12px;border-top:1px solid #d0d9df;vertical-align:top;text-align:left;border-bottom:1px solid #d0d9df"><strong>Registered On: </strong>
															</td>
															<td style="width:25%;font-size:14px;padding:6px 12px;border-top:1px solid #d0d9df;vertical-align:top;text-align:left;border-bottom:1px solid #d0d9df">{$email_data['booking_date']}
															</td>
														</tr>
														<tr>
															<td style="width:25%;font-size:14px;padding:6px 12px;border-top:1px solid #d0d9df;vertical-align:top;text-align:left;border-bottom:1px solid #d0d9df"><strong>No. of Persons: </strong>
															</td>
															<td style="width:25%;font-size:14px;padding:6px 12px;border-top:1px solid #d0d9df;vertical-align:top;text-align:left;border-bottom:1px solid #d0d9df">{$email_data['no_of_tickets']}
															</td>
														</tr>
														
														<tr>
															<td style="width:25%;font-size:14px;padding:6px 12px;border-top:1px solid #d0d9df;vertical-align:top;text-align:left;border-bottom:1px solid #d0d9df"><strong>Amount Paid: </strong>
															</td>
															<td style="width:25%;font-size:14px;padding:6px 12px;border-top:1px solid #d0d9df;vertical-align:top;text-align:left;border-bottom:1px solid #d0d9df">{$amt_paid}
															</td>
														</tr>

														<tr>
															<td style="width:25%;font-size:14px;padding:6px 12px;border-top:1px solid #d0d9df;vertical-align:top;text-align:left;border-bottom:1px solid #d0d9df"><strong>Offer Applied: </strong>
															</td>
															<td style="width:25%;font-size:14px;padding:6px 12px;border-top:1px solid #d0d9df;vertical-align:top;text-align:left;border-bottom:1px solid #d0d9df">{$email_data['offer']}
															</td>
														</tr>
													</tbody>
												</table>												
												<table style="line-height:15px;border-collapse:collapse;width:100%" cellspacing="0" cellpadding="8">
													<tbody>
														<tr>
															<td style="width:50%;font-size:14px;padding:6px 12px; vertical-align:top; text-align:left;">&nbsp;</td>
														</tr>														
													</tbody>
												</table>
												<table style="width:100%" cellspacing="0" cellpadding="0" border="0">
													<tbody>
														<tr>
															<td align="center">	
																<a href="{$email_data['booking_details_page']}" style="display:inline-block;word-break:break-word;text-align:center;padding-top:12px;padding-bottom:12px;padding-left:16px;padding-right:16px; outline:none; font-size:16px;border-radius:8px; border-style:solid; border-style:solid;box-sizing:border-box;border-width:1px;background-color:#6622c9;border-color:transparent;color:#ffffff;text-decoration:none;margin:0 auto;" target="_blank" moz-do-not-send="true"> Login & View the Registration & Event Details </a> 
															</td>
														</tr>

														
													</tbody>
												</table>
												<table style="line-height:15px;border-collapse:collapse;width:100%" cellspacing="0" cellpadding="8">
													<tbody>
														<tr>
															<td style="width:50%;font-size:14px;padding:6px 12px;  vertical-align:top; text-align:left;">&nbsp;</td>
														</tr>														
													</tbody>
												</table>
												<table style="font-size:13px;line-height:15px;border-collapse:collapse;width:100%" cellspacing="0" cellpadding="8">
													<tbody>
														<tr>															
															<td style="width:50%;font-size:14px;padding:6px 0px;vertical-align:top; text-align:left;">Regards,
															</td>
															
														</tr>
														<tr>															
															<td style="width:25%;font-size:14px;padding:6px 0px;vertical-align:top;text-align:left;">{$email_data['from_name']}
															</td>
														</tr>														
													</tbody>
												</table>
												
												

											</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>

</body>
</html>

EOF;


		$subject = CONST_MAIL_SUBJECT_PREFIX." Event registration confirmed (ID: ".$email_data['booking_id'].")";
		$extra_data = [];
		$extra_data['from'] = CONST_MAIL_SENDERS_EMAIL;
		$extra_data['from_name'] = CONST_MAIL_SENDERS_NAME;
		$extra_data['cc'] = $recp['cc']??[];

		if(!empty(CONST_EMAIL_OVERRIDE))
			$extra_data['recp'] = explode(',',CONST_EMAIL_OVERRIDE);
		else{
			$extra_data['recp'] = $recp['to']??'';
		}

		$data = [
			'subject' => $subject,
			'html_message' => $html_msg,
			
		];

		if(!empty($email_data['qr_code']['file_name_path'])){
			$data['inlineimages'] = [
				[
				'image_filenamepath' => $email_data['qr_code']['file_name_path'],
				'image_identifier' => 'tktqr',
				'image_type' => $email_data['qr_code']['type'],
				]
			];
		}

		if(!empty($extra_data['recp'])){
			$mail = new Mailer(true, ['use_default'=>CONST_USE_SERVERS_DEFAULT_SMTP]); // Will use server's default email settings
			$mail->resetOverrideEmails(); // becuase the overide email is being set in the recp var above
			return $mail->sendEmail($data, $extra_data);
		}

		return false;


	}


	function alertEventRegistrationError($email_data, $recp){

		$html_msg = <<<EOF
<!DOCTYPE html> 
<html>
	<head></head> 
	<body>
		<p>Hello,</p>
		<p>{$email_data['msg']}</p>
		<p>
		Registration ID: {$email_data['booking_id']}<br>
		Registration Date: {$email_data['booking_date']}<br>
		Instamojo Payment Status: {$email_data['pmtg_payment_status']}<br>
		Instamojo Payment Req. ID: {$email_data['pmtg_payment_req_id']}<br>
		Instamojo Payment ID: {$email_data['pmtg_payment_id']}<br><br>
		Event: {$email_data['ev_name']}<br>
		No. of Persons: {$email_data['no_of_tickets']}<br>
		Total Amount: &#8377;{$email_data['amount_payable']}<br><br>
		Member Name: {$email_data['mem_name']}<br>

		Registration Details Page: <a href="{$email_data['booking_details_page']}" >{$email_data['booking_details_page']}</a>
		</p>
		
		<p>Regards,<br>{$email_data['from_name']}</p>
	</body>
</html>	
EOF;


		$subject = CONST_MAIL_SUBJECT_PREFIX." Event registration payment details update error (ID: ".$email_data['booking_id'].")";
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

}