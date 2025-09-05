<?php
namespace eBizIndia;
class Meeting{
	//private $event_id;
	private $rec_id;
	//public function __construct(?int $event_id=null){
	public function __construct(?int $rec_id=null){
		//$this->event_id = $event_id;
		$this->rec_id = $rec_id;
	}

	public function getDetails(){
		if(empty($this->rec_id))
			return false;
		$options = [];
		$options['filters'] = [
			[ 'field' => 'id', 'type' => 'EQUAL', 'value' => $this->rec_id ]
		];
		return  self::getList($options);
	}

	public function validate($data, $mode='add', $other_data=[]){
		$result['error_code'] = 0;
		$result['message'] = [];
		$result['error_fields'] = [];
		$restricted_fields = $other_data['edit_restricted_fields']??[];
		
		// Validate required fields
		if(!in_array('meet_date', $restricted_fields) && empty(trim($data['meet_date'] ?? ''))){
			$result['error_code']=2;
			$result['message'][]="Meeting start date is required.";
			$result['error_fields'][]="#meet_date";
		}else if(!empty(trim($data['meet_date'] ?? ''))){
			if(!isDateValid($data['meet_date'])){
				$result['error_code']=2;
				$result['message'][]="Meeting start date is invalid.";
				$result['error_fields'][]="#meet_date";
			}
		}

		if(!in_array('meet_date_to', $restricted_fields) && empty(trim($data['meet_date_to'] ?? ''))){
			$result['error_code']=2;
			$result['message'][]="Meeting end date is required.";
			$result['error_fields'][]="#meet_date_to";
		}else if(!empty(trim($data['meet_date_to'] ?? ''))){
			if(!isDateValid($data['meet_date_to'])){
				$result['error_code']=2;
				$result['message'][]="Meeting end date is invalid.";
				$result['error_fields'][]="#meet_date_to";
			}
		}

		// Validate meeting title is required
		if(!in_array('meet_title', $restricted_fields) && empty(trim($data['meet_title'] ?? ''))){
			$result['error_code']=2;
			$result['message'][]="Meeting title is required.";
			$result['error_fields'][]="#meet_title";
		}

		// Time field is optional - no validation required
		// Venue field is optional - no validation required
		
		return $result;
	}

	
	public static function getList($options = []) {
    $data = [];
    $fields_mapper = [];

    $fields_mapper['*'] = "m.*";
    $fields_mapper['recordcount'] = 'count(1)';
    $fields_mapper['id'] = "m.id";
    $fields_mapper['meet_date'] = "m.meet_date";
    $fields_mapper['meet_date_to'] = "m.meet_date_to";
    $fields_mapper['meet_time'] = "m.meet_time";
    $fields_mapper['meet_title'] = "m.meet_title";
    $fields_mapper['venue'] = "m.venue";
    $fields_mapper['active'] = "m.active";

    $where_clause = [];
    $str_params_to_bind = [];
    $int_params_to_bind = [];

    if (array_key_exists('filters', $options) && is_array($options['filters'])) {
        $field_counter = 0;
        foreach ($options['filters'] as $filter) {
            ++$field_counter;
            switch ($filter['field']) {
                case 'id':
                    switch ($filter['type']) {
                        case 'IN':
                            if (is_array($filter['value'])) {
                                $place_holders = [];
                                $k = 0;
                                foreach ($filter['value'] as $id) {
                                    $k++;
                                    $place_holders[] = ":whr" . $field_counter . "_id_{$k}_";
                                    $int_params_to_bind[":whr" . $field_counter . "_id_{$k}_"] = $id;
                                }
                                $where_clause[] = $fields_mapper[$filter['field']] . ' IN (' . implode(',', $place_holders) . ') ';
                            }
                            break;

                        case 'NOT_IN':
                            if (is_array($filter['value'])) {
                                $place_holders = [];
                                $k = 0;
                                foreach ($filter['value'] as $id) {
                                    $k++;
                                    $place_holders[] = ":whr" . $field_counter . "_id_{$k}_";
                                    $int_params_to_bind[":whr" . $field_counter . "_id_{$k}_"] = $id;
                                }
                                $where_clause[] = $fields_mapper[$filter['field']] . ' NOT IN (' . implode(',', $place_holders) . ') ';
                            }
                            break;

                        case 'NOT_EQUAL':
                            $id = (is_array($filter['value'])) ? $filter['value'][0] : $filter['value'];
                            $where_clause[] = $fields_mapper[$filter['field']] . ' != :whr' . $field_counter . '_id';
                            $int_params_to_bind[':whr' . $field_counter . '_id'] = $id;
                            break;

                        default:
                            $id = (is_array($filter['value'])) ? $filter['value'][0] : $filter['value'];
                            $where_clause[] = $fields_mapper[$filter['field']] . ' = :whr' . $field_counter . '_id';
                            $int_params_to_bind[':whr' . $field_counter . '_id'] = $id;
                    }
                    break;

                case 'venue':
                case 'meet_title':
                case 'minutes':
                    $value = (is_array($filter['value'])) ? $filter['value'][0] : $filter['value'];
                    $where_clause[] = $fields_mapper[$filter['field']] . " LIKE :whr" . $field_counter . "_value";
                    switch ($filter['type']) {
                        case 'CONTAINS':
                            $str_params_to_bind[':whr' . $field_counter . '_value'] = "%$value%";
                            break;
                        case 'STARTS_WITH':
                            $str_params_to_bind[':whr' . $field_counter . '_value'] = "$value%";
                            break;
                        case 'ENDS_WITH':
                            $str_params_to_bind[':whr' . $field_counter . '_value'] = "%$value";
                            break;
                        case 'EQUAL':
                        default:
                            $str_params_to_bind[':whr' . $field_counter . '_value'] = "$value";
                            break;
                    }
                    break;

                case 'meet_date':
                    $dt = (is_array($filter['value'])) ? $filter['value'][0] : $filter['value'];
                    $where_clause[] = $fields_mapper[$filter['field']] . ' = :whr' . $field_counter . '_dt';
                    $str_params_to_bind[':whr' . $field_counter . '_dt'] = $dt;
                    break;

                case 'meet_date_to':
                    $dt = (is_array($filter['value'])) ? $filter['value'][0] : $filter['value'];
                    $where_clause[] = $fields_mapper[$filter['field']] . ' = :whr' . $field_counter . '_dt';
                    $str_params_to_bind[':whr' . $field_counter . '_dt'] = $dt;
                    break;

                case 'active':
                    $status = (is_array($filter['value'])) ? $filter['value'][0] : $filter['value'];
                    switch ($filter['type']) {
                        case 'NOT_EQUAL':
                            $where_clause[] = $fields_mapper[$filter['field']] . ' != :whr' . $field_counter . '_active';
                            $str_params_to_bind[':whr' . $field_counter . '_active'] = $status;
                            break;
                        default:
                            $where_clause[] = $fields_mapper[$filter['field']] . ' = :whr' . $field_counter . '_active';
                            $str_params_to_bind[':whr' . $field_counter . '_active'] = $status;
                    }
                    break;

                default:
                    $where_clause[] = $fields_mapper[$filter['field']] . ' ' . $filter['type'] . ' :whr' . $field_counter;
                    $str_params_to_bind[':whr' . $field_counter] = $filter['value'];
                    break;
            }
        }
    }

    $select_string = $fields_mapper['*'];

    if (array_key_exists('fieldstofetch', $options) && is_array($options['fieldstofetch'])) {
        $fields_to_fetch = [];
        foreach ($options['fieldstofetch'] as $field) {
            if (array_key_exists($field, $fields_mapper)) {
                $fields_to_fetch[] = $fields_mapper[$field] . (($field != '*') ? ' AS ' . $field : '');
            }
        }
        if (!empty($fields_to_fetch)) {
            $select_string = implode(', ', $fields_to_fetch);
        }
    }

    $where_clause_string = '';
    if (!empty($where_clause)) {
        $where_clause_string = ' WHERE ' . implode(' AND ', $where_clause);
    }

    $order_by_clause = '';
    if (array_key_exists('order_by', $options) && is_array($options['order_by'])) {
        $order_by = [];
        foreach ($options['order_by'] as $order) {
            $order_by[] = $fields_mapper[$order['field']] . (isset($order['type']) && strtoupper($order['type']) === 'DESC' ? ' DESC' : ' ASC');
        }
        if (!empty($order_by)) {
            $order_by_clause = ' ORDER BY ' . implode(', ', $order_by);
        }
    }

    $limit_clause = '';
    if (
        array_key_exists('page', $options) && filter_var($options['page'], FILTER_VALIDATE_INT) && $options['page'] > 0 &&
        array_key_exists('recs_per_page', $options) && filter_var($options['recs_per_page'], FILTER_VALIDATE_INT) && $options['recs_per_page'] > 0
    ) {
        $limit_clause = " LIMIT " . (($options['page'] - 1) * $options['recs_per_page']) . ", " . $options['recs_per_page'];
    }

    $sql = "SELECT $select_string FROM `meetings` AS m $where_clause_string $order_by_clause $limit_clause";

    //echo $sql;


    try {
        $pdo_stmt_obj = PDOConn::query($sql, $str_params_to_bind, $int_params_to_bind);

        if (array_key_exists('resourceonly', $options) && $options['resourceonly']) {
            return $pdo_stmt_obj;
        }

        while ($row = $pdo_stmt_obj->fetch(\PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        return $data;

    } catch (\Exception $e) {
        ErrorHandler::logError(['function' => __METHOD__, 'sql' => $sql, 'params' => ['str' => $str_params_to_bind, 'int' => $int_params_to_bind]], $e);
        return false;
    }
}

	public function getSessionDetails(){
		if(empty($this->rec_id))
			return false;
		$options = [];
		$options['filters'] = [
			[ 'field' => 'meeting_id', 'type' => '=', 'value' => $this->rec_id ]
		];
		return  self::getSessionList($options);

	}	

	

	public static function getSessionList($options = []) {
	$data = [];
    $fields_mapper = [];

    $fields_mapper['*'] = "ma.*";
    $fields_mapper['recordcount'] = 'count(1)';
    $fields_mapper['id'] = "ma.id";
    $fields_mapper['meeting_id'] = "ma.meeting_id";
    $fields_mapper['session_meet_date'] = "ma.session_meet_date";
    $fields_mapper['time_from'] = "ma.time_from";
    $fields_mapper['time_to'] = "ma.time_to";
    $fields_mapper['topic'] = "ma.topic";
   
    $where_clause = [];
    $str_params_to_bind = [];
    $int_params_to_bind = [];

    $select_string = $fields_mapper['*'];

    if (array_key_exists('filters', $options) && is_array($options['filters'])) {
        $field_counter = 0;
        foreach ($options['filters'] as $filter) {
            ++$field_counter;
            switch ($filter['field']) {
                case 'meeting_id':
                    switch ($filter['type']) {
                        case 'IN':
                            if (is_array($filter['value'])) {
                                $place_holders = [];
                                $k = 0;
                                foreach ($filter['value'] as $id) {
                                    $k++;
                                    $place_holders[] = ":whr" . $field_counter . "_id_{$k}_";
                                    $int_params_to_bind[":whr" . $field_counter . "_id_{$k}_"] = $id;
                                }
                                $where_clause[] = $fields_mapper[$filter['field']] . ' IN (' . implode(',', $place_holders) . ') ';
                            }
                            break;
                        default:
                    $where_clause[] = $fields_mapper[$filter['field']] . ' ' . $filter['type'] . ' :whr' . $field_counter;
                    $str_params_to_bind[':whr' . $field_counter] = $filter['value'];
                    break;
            }
        }
    }

    $where_clause_string = '';
    if (!empty($where_clause)) {
        $where_clause_string = ' WHERE ' . implode(' AND ', $where_clause);
    }
    
	$sql = "SELECT $select_string FROM `meeting_agenda` AS ma $where_clause_string $order_by_clause $limit_clause";

   //echo $sql;
   //die();

//$pdo_stmt_obj=null;
    try {
        $pdo_stmt_obj = PDOConn::query($sql, $str_params_to_bind, $int_params_to_bind);
//$pdo_stmt_obj->debugDumpParams();exit;
        if (array_key_exists('resourceonly', $options) && $options['resourceonly']) {
            return $pdo_stmt_obj;
        }

        while ($row = $pdo_stmt_obj->fetch(\PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        return $data;

    } catch (\Exception $e) {
    	
        ErrorHandler::logError(['function' => __METHOD__, 'sql' => $sql, 'params' => ['str' => $str_params_to_bind, 'int' => $int_params_to_bind]], $e);
        return false;
    }
	}

	}

	public function saveDetails($data, $id =''){
		$str_data = $int_data = [];
		$table = '`'.CONST_TBL_PREFIX . 'meetings`';
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
	
public function saveAgendaDetails($agendas, $meeting_id) {
    $table = '`' . CONST_TBL_PREFIX . 'meeting_agenda`';
    $insert_data = [];
    $update_data = [];
    $submitted_ids = [];
    $error_details_to_log = [];

    $error_details_to_log['at'] = date('Y-m-d H:i:s');
    $error_details_to_log['function'] = __METHOD__;
    $error_details_to_log['meeting_id'] = $meeting_id;

    try {
        // 1. Prepare submitted agendas
        foreach ($agendas as $agenda) {
            $agenda_id = isset($agenda['id']) ? (int)$agenda['id'] : 0;
            $session_meet_date = $agenda['session_meet_date'] ?? null;
            $time_from = $agenda['time_from'] ?? null;
            $time_to = $agenda['time_to'] ?? null;
            $topic = $agenda['topic'] ?? null;

            if ($agenda_id > 0) {
                // Update existing record
                $update_data[] = [
                    ':id' => $agenda_id,
                    ':meeting_id' => $meeting_id,
                    ':session_meet_date' => $session_meet_date,
                    ':time_from' => $time_from,
                    ':time_to' => $time_to,
                    ':topic' => $topic
                ];
                $submitted_ids[] = $agenda_id;
            } else {
                // Insert new record
                $insert_data[] = [
                    'meeting_id' => $meeting_id,
                    'session_meet_date' => $session_meet_date,
                    'time_from' => $time_from,
                    'time_to' => $time_to,
                    'topic' => $topic
                ];
            }
        }

        // 2. Fetch all existing agenda IDs for this meeting
        $existing_ids_stmt = PDOConn::query("SELECT id FROM $table WHERE meeting_id = :meeting_id", [':meeting_id' => $meeting_id]);
        $existing_ids = array_column($existing_ids_stmt->fetchAll(\PDO::FETCH_ASSOC), 'id');

        // 3. Identify records to delete
        $ids_to_delete = array_diff($existing_ids, $submitted_ids);
        
       
        if (!empty($ids_to_delete)) {
            $binds = [];
            $placeholders = [];
            foreach ($ids_to_delete as $index => $id) {
                $key = ":id_" . $index;
                $placeholders[] = $key;
                $binds[$key] = $id;
            }
            $in_placeholder = implode(',', $placeholders);
            $delete_sql = "DELETE FROM $table WHERE id IN ($in_placeholder)";
            PDOConn::query($delete_sql, $binds);
        }

        // 4. Execute Updates
        foreach ($update_data as $update_item) {
            PDOConn::query(
                "UPDATE $table 
                 SET `session_meet_date` = :session_meet_date, `time_from` = :time_from, `time_to` = :time_to, `topic` = :topic 
                 WHERE `id` = :id AND `meeting_id` = :meeting_id",
                 $update_item
            );
        }

        // 5. Execute Inserts
        if (!empty($insert_data)) {
            $sql = "INSERT INTO $table (`meeting_id`, `session_meet_date`, `time_from`, `time_to`, `topic`) VALUES ";
            $values = [];
            $binds = [];
            foreach ($insert_data as $index => $insert_item) {
                $values[] = "(:meeting_id_{$index}, :session_meet_date_{$index}, :time_from_{$index}, :time_to_{$index}, :topic_{$index})";
                $binds[":meeting_id_{$index}"] = $insert_item['meeting_id'];
                $binds[":session_meet_date_{$index}"] = $insert_item['session_meet_date'];
                $binds[":time_from_{$index}"] = $insert_item['time_from'];
                $binds[":time_to_{$index}"] = $insert_item['time_to'];
                $binds[":topic_{$index}"] = $insert_item['topic'];
            }
            $sql .= implode(", ", $values);
            PDOConn::query($sql, $binds);
        }

        return true;
    } catch (\Exception $e) {
        $error_details_to_log['agendas'] = $agendas;
        ErrorHandler::logError($error_details_to_log, $e);
        return false;
    }
}


	public function saveAgendaDetails0($agendas, $meeting_id) {
		    $table = '`' . CONST_TBL_PREFIX . 'meeting_agenda`';
		    $insert_data = [];
		    $update_data = [];
		    $error_details_to_log = [];
		    
		    $error_details_to_log['at'] = date('Y-m-d H:i:s');
		    $error_details_to_log['function'] = __METHOD__;
		    $error_details_to_log['meeting_id'] = $meeting_id;

		    try {
		        foreach ($agendas as $agenda) {
		            $agenda_id = isset($agenda['id']) ? (int)$agenda['id'] : 0;
		            $session_meet_date = $agenda['session_meet_date'] ?? null;
		            $time_from = $agenda['time_from'] ?? null;
		            $time_to = $agenda['time_to'] ?? null;
		            $topic = $agenda['topic'] ?? null;

		            if ($agenda_id > 0) {
		                // Update existing record
		                $sql = "UPDATE $table 
		                        SET `session_meet_date`=:$session_meet_date, `time_from` = :time_from, `time_to` = :time_to, `topic` = :topic 
		                        WHERE `id` = :id AND `meeting_id` = :meeting_id";
		                $update_data[] = [
		                    ':id' => $agenda_id,
		                    ':meeting_id' => $meeting_id,
		                    ':session_meet_date' => $session_meet_date,
		                    ':time_from' => $time_from,
		                    ':time_to' => $time_to,
		                    ':topic' => $topic
		                ];
		            } else {
		                // Insert new record
		                $insert_data[] = [
		                    'meeting_id' => $meeting_id,
		                    'session_meet_date' => $session_meet_date,
		                    'time_from' => $time_from,
		                    'time_to' => $time_to,
		                    'topic' => $topic
		                ];
		            }
		        }

		        // Execute Updates
		        foreach ($update_data as $update_item) {
		            $stmt = PDOConn::query("UPDATE $table 
		                                    SET `session_meet_date`=:session_meet_date,`time_from` = :time_from, `time_to` = :time_to, `topic` = :topic 
		                                    WHERE `id` = :id AND `meeting_id` = :meeting_id", 
		                                    $update_item);
		        }

		        // Execute Inserts
		        if (!empty($insert_data)) {
		            $sql = "INSERT INTO $table (`meeting_id`, `session_meet_date`, `time_from`, `time_to`, `topic`) VALUES ";
		            $values = [];
		            $binds = [];
		            foreach ($insert_data as $index => $insert_item) {
		                $values[] = "(:meeting_id_{$index}, :session_meet_date_{$index}, :time_from_{$index}, :time_to_{$index}, :topic_{$index})";
		                $binds[":meeting_id_{$index}"] = $insert_item['meeting_id'];
		                $binds[":session_meet_date_{$index}"] = $insert_item['session_meet_date'];
		                $binds[":time_from_{$index}"] = $insert_item['time_from'];
		                $binds[":time_to_{$index}"] = $insert_item['time_to'];
		                $binds[":topic_{$index}"] = $insert_item['topic'];
		            }
		            $sql .= implode(", ", $values);
		            PDOConn::query($sql, $binds);
		        }

		        return true;
		    } catch (\Exception $e) {
		        $error_details_to_log['agendas'] = $agendas;
		        ErrorHandler::logError($error_details_to_log, $e);
		        return false;
		    }
	}




	public function updateDetails($data){
		if($this->rec_id=='')
			return false;

		//print_r($data);
		//die();
		return $this->saveDetails($data, $this->rec_id);
	}


	


	public static function getAdsActiveOnDate($dt, $order_by = 'random'){
		$options=[];
		$options['filters'] = [

			['field' => 'event_on_date', 'type'=>'', 'value'=>$dt ],
			['field' => 'active', 'type'=>'EQUAL', 'value'=>'y' ],

		];
		$options['fieldstofetch'] = [
			'id', 'target_link', 'dsk_img', 'mob_img'
		];
		$options['order_by'] = 'random';
		return self::getList($options);
	}

	function deleteMeeting($meeting_id){
		$table = '`' . CONST_TBL_PREFIX . 'meetings`';
		$meeting_id=(int)$meeting_id;

		$this->last_mysql_error_code = $this->last_sqlstate_code='';


		$sql="DELETE from $table WHERE `id`=$meeting_id ";
		$error_details_to_log = [];
		$error_details_to_log['at'] = date('Y-m-d H:i:s');
		$error_details_to_log['method'] = "deleteMeeting";
		$error_details_to_log['sql'] = $sql;
		


		try{
			
			 $stmt = PDOConn::query($sql);

						
			
			return true;
		} catch (\Exception $e) {
		        $error_details_to_log['meeting'] = $meeting_id;
		        ErrorHandler::logError($error_details_to_log, $e);
		        return false;
		    }

	}



}