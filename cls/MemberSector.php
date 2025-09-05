<?php
namespace eBizIndia;
class MemberSector{
	public static function getList($options=[]){
		$data = [];
		$fields_mapper = [];

		$fields_mapper['*']="msec.id as id, msec.sector as sector, msec.active as active";

		$fields_mapper['recordcount']='count(distinct(msec.id))';
		$fields_mapper['id']="msec.id";
		$fields_mapper['sector']="msec.sector";
		$fields_mapper['active']="msec.active";
				
		$where_clause=[];

		$str_params_to_bind=[];
		$int_params_to_bind=[];

		if( array_key_exists('filters',$options) && is_array($options['filters']) ){
			$field_counter=0;
			foreach($options['filters'] as $filter){
				++$field_counter;
				switch ($filter['field']) {
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



					case 'sector':
						switch($filter['type']){
							case 'IN':
								if(is_array($filter['value'])){
									$place_holders=[];
									$k=0;
									foreach($filter['value'] as $sector){
										$k++;
										$place_holders[]=":whr".$field_counter."_sector_{$k}_";
										$str_params_to_bind[":whr".$field_counter."_sector_{$k}_"] = $sector;
									}
									$where_clause[] = $fields_mapper[$filter['field']].' not in('.implode(',',$place_holders).') ';
								}
								break;

							case 'CONTAINS':
								$sector=(is_array($filter['value']))?$filter['value'][0]:$filter['value'];
								$where_clause[] = $fields_mapper[$filter['field']].' like :whr'.$field_counter.'_sector_';
								$str_params_to_bind[':whr'.$field_counter.'_sector_']='%'.$sector.'%';
								break;

							case 'NOT_EQUAL':
								$sector=(is_array($filter['value']))?$filter['value'][0]:$filter['value'];
								$where_clause[] = $fields_mapper[$filter['field']].'!=:whr'.$field_counter.'_sector_';
								$str_params_to_bind[':whr'.$field_counter.'_sector_']=$sector;
								break;

							default:
								$sector=(is_array($filter['value']))?$filter['value'][0]:$filter['value'];
								$where_clause[] = $fields_mapper[$filter['field']].' like :whr'.$field_counter.'_sector_';
								$str_params_to_bind[':whr'.$field_counter.'_sector_']=$sector;
						}

						break;

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
				if(preg_match("/^(msec)\./",$fields_mapper[$field]))
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
				if(preg_match("/^(msec)\./",$fields_mapper[$order['field']])){
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
			if($order_by_clause!='' && !stristr($order_by_clause, 'msec.id')){

				$order_by_clause .= ', '.$fields_mapper['id'].' DESC ';
			}


		}

		

		if(!$record_count && $order_by_clause==''){

			$order_by_clause=" ORDER BY msec.sector ASC";

			if(!preg_match("/\s+as\s+sector/",$select_string)){
				$select_string .= ', '.$fields_mapper['sector'].' as sector';
			}
		}

		$limit_clause='';

		if(array_key_exists('page', $options) && filter_var($options['page'],FILTER_VALIDATE_INT) && $options['page']>0 && array_key_exists('recs_per_page', $options) && filter_var($options['recs_per_page'],FILTER_VALIDATE_INT) && $options['recs_per_page']>0){

			$limit_clause="LIMIT ".( ($options['page']-1) * $options['recs_per_page'] ).", $options[recs_per_page] ";

		}

		$where_clause_string = '';
		if(!empty($where_clause))
			$where_clause_string = ' WHERE '.implode(' AND ', $where_clause);

		$sql="SELECT $select_string from `".CONST_TBL_PREFIX."sectors` as msec $where_clause_string $group_by_clause $order_by_clause $limit_clause";

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


	static function assignRevoke(int $mem_id, array $sector_ids=[]){ 
		$str_data = $int_data = [];
		if(empty($mem_id))
			return false;

		try{
			$int_data = [
				':mem_id'=>$mem_id
			];
			$stmt_obj = PDOConn::query('DELETE from `'.CONST_TBL_PREFIX . 'member_sectors` WHERE mem_id=:mem_id', [], $int_data);
			if(empty($stmt_obj)){
				return false;
			}
			if(!empty($sector_ids)){
				$insert_sql = 'INSERT INTO `'.CONST_TBL_PREFIX . 'member_sectors`(`mem_id`, `sector_id`) VALUES ';
				$values=array();
				foreach($sector_ids as $id){
					$key = ":sectorid_{$id}";
					$values[]="(:mem_id, $key)";
					$int_data[$key] = $id;
				}
				$insert_sql.=implode(',',$values);
				$stmt_obj = PDOConn::query($insert_sql, [], $int_data);
				if(empty($stmt_obj)){
					return false;
				}
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


	static function assignRevokeFromReg(int $mem_id, array $sector_ids=[]){ 
		$str_data = $int_data = [];
		if(empty($mem_id))
			return false;

		try{
			$int_data = [
				':mem_id'=>$mem_id
			];
			$stmt_obj = PDOConn::query('DELETE from `'.CONST_TBL_PREFIX . 'member_regs_sectors` WHERE mem_id=:mem_id', [], $int_data);
			if(empty($stmt_obj)){
				return false;
			}
			if(!empty($sector_ids)){
				$insert_sql = 'INSERT INTO `'.CONST_TBL_PREFIX . 'member_regs_sectors`(`mem_id`, `sector_id`) VALUES ';
				$values=array();
				foreach($sector_ids as $id){
					$key = ":sectorid_{$id}";
					$values[]="(:mem_id, $key)";
					$int_data[$key] = $id;
				}
				$insert_sql.=implode(',',$values);
				$stmt_obj = PDOConn::query($insert_sql, [], $int_data);
				if(empty($stmt_obj)){
					return false;
				}
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



	static function add(array $sectors){ 
		$str_data = [];
		if(empty($sectors))
			return false;
		$sector_ids = [];
		try{
			foreach($sectors as $sector){
				$insert_sql =  'INSERT INTO `'.CONST_TBL_PREFIX . 'sectors` set `sector` = :sector ON DUPLICATE KEY UPDATE `active`="y" ';
				$str_data[':sector'] = $sector;
				$stmt_obj = PDOConn::query($insert_sql, $str_data);
				if($stmt_obj===false)
					return false;
				$sector_ids[] = PDOConn::lastInsertId();
			}
			return $sector_ids;
		}catch(\Exception $e){
			if(!is_a($e, '\PDOStatement'))
				ErrorHandler::logError($error_details_to_log,$e);
			else
				ErrorHandler::logError($error_details_to_log);
			return false;

		}
	}


	static function update($data, int $id){ 
		$str_data = $int_data = [];
		if(empty($data) || empty($id))
			return false;
		try{
			$sql =  ' UPDATE `'.CONST_TBL_PREFIX . 'sectors` set '; 
			if(!empty($data['sector'])){
				$sql .=' `sector` = :sector '; 
				$str_data[':sector'] = $data['sector'];
			}
			if(!empty($data['active'])){
				$sql .=' `active` = :active '; 
				$str_data[':active'] = $data['active'];
			}
			$sql .=' WHERE `id`=:id ';

			$int_data[':id'] = $id;
			$stmt_obj = PDOConn::query($sql, $str_data, $int_data);
			if($stmt_obj===false)
				return false;
			return true;
		}catch(\Exception $e){
			if(!is_a($e, '\PDOStatement'))
				ErrorHandler::logError($error_details_to_log,$e);
			else
				ErrorHandler::logError($error_details_to_log);
			return false;
		}
	}

	static function delete(array $sector_ids){ 
		$str_data = [];
		if(empty($sector_ids))
			return false;
		try{
			$sql = 'DELETE FROM `'.CONST_TBL_PREFIX . 'sectors` WHERE id in';
			$placeholders = [];
			$idx=0;
			foreach($sector_ids as $id){
				$idx++;
				$key = ':id_'.$idx.'_';
				$placeholders[] = $key;
				$int_data[$key] = $id;
			}

			$sql .= '('.implode(',',$placeholders).')';
			$stmt_obj = PDOConn::query($sql, [], $int_data);
			if($stmt_obj===false)
				return false;
			return true;	
		}catch(\Exception $e){
			if(!is_a($e, '\PDOStatement'))
				ErrorHandler::logError($error_details_to_log,$e);
			else
				ErrorHandler::logError($error_details_to_log);
			return false;

		}
	}

}