<?php
namespace eBizIndia;
class DiscountOfferCat{
	private $d_o_c_id;
	private $d_o_c_details;
	public function __construct(?int $d_o_c_id=null){
		$this->d_o_c_id = $d_o_c_id;
	}

	public function getDetails(){
		if(empty($this->d_o_c_id))
			return false;
		$options = [
			'filters' => [	[ 'field' => 'id', 'type' => 'EQUAL', 'value' => $this->d_o_c_id ]]
		];
		return  self::getList($options);
	}

	
	public static function getList($options=[]){
		$data = [];
		$fields_mapper = [];

		$fields_mapper['*']="doffcat.*";

		$fields_mapper['record_count']='count(1)';
		$fields_mapper['offer_count']='count(doff.id)';
		$fields_mapper['id']="doffcat.id";
		$fields_mapper['name']="doffcat.name";
		$fields_mapper['img']="doffcat.img";
		$fields_mapper['active']="doffcat.active";
		$fields_mapper['doff_active']="doff.active";
		$fields_mapper['doff_title']="doff.title";
		$fields_mapper['doff_description']="doff.description";
				
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

					case 'doff_title':
					case 'doff_description':
					case 'name':
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

					case 'doff_active':
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
		
		if(array_key_exists('fields_to_fetch', $options) && is_array($options['fields_to_fetch'])){
			$fields_to_fetch_count=count($options['fields_to_fetch']);

			if($fields_to_fetch_count>0){
				$selected_fields=[];
				if(in_array('record_count', $options['fields_to_fetch'])){
					$record_count=true;
				}else{
					if(!in_array('*',$options['fields_to_fetch'])){
						if(!in_array('id',$options['fields_to_fetch'])){ // This is required as the id is being used for table joining
							$options['fields_to_fetch'][]='id';
							$fields_to_fetch_count+=1; // increment the count by 1 to include this column
						}
					}
				}
				for($i=0; $i<$fields_to_fetch_count; $i++){
					if(array_key_exists($options['fields_to_fetch'][$i],$fields_mapper)){
						$selected_fields[]=$fields_mapper[$options['fields_to_fetch'][$i]].(($options['fields_to_fetch'][$i]!='*')?' as '.$options['fields_to_fetch'][$i]:'');
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
				if(preg_match("/^(doff|doffcat)\./",$fields_mapper[$field]))
					$group_by_clause.=", ".$fields_mapper[$field];
				else
					$group_by_clause.=", $field";
			}

			$group_by_clause=trim($group_by_clause,",");
			if($group_by_clause!=''){
				$group_by_clause=' GROUP BY '.$group_by_clause;
			}
		}

		$order_by_clause = $order_by_clause_outer = '';

		if(array_key_exists('order_by', $options) && is_array($options['order_by'])){
			foreach ($options['order_by'] as $order) {
				if(preg_match("/^(doff|doffcat)\./",$fields_mapper[$order['field']])){
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
			if($order_by_clause!='')
				$order_by_clause=' ORDER BY '.$order_by_clause;

			if($order_by_clause!='' && !stristr($order_by_clause, 'doffcat.id'))
				$order_by_clause .= ', '.$fields_mapper['id'].' DESC ';

		}else if($options['order_by']==='random'){
			$order_by_clause .= ' ORDER BY RAND() ';
		}

		

		if(!$record_count && $order_by_clause==''){

			$order_by_clause=" ORDER BY doffcat.name ASC";

			// if(!preg_match("/\s+as\s+valid_upto/",$select_string)){
			// 	$select_string .= ', '.$fields_mapper['valid_upto'].' as valid_upto';
			// }
		}

		$limit_clause='';
		if(array_key_exists('page', $options) && filter_var($options['page'],FILTER_VALIDATE_INT) && $options['page']>0 && array_key_exists('recs_per_page', $options) && filter_var($options['recs_per_page'],FILTER_VALIDATE_INT) && $options['recs_per_page']>0){
			$limit_clause="LIMIT ".( ($options['page']-1) * $options['recs_per_page'] ).", ".$options['recs_per_page'];
		}

		$where_clause_string = '';
		if(!empty($where_clause))
			$where_clause_string = ' WHERE '.implode(' AND ', $where_clause);

		$doff_join = '';
		if(preg_match("/doff\./", " $select_string $where_clause_string $group_by_clause $order_by_clause "))
			$doff_join = ' LEFT JOIN `'.CONST_TBL_PREFIX.'discount_offers` doff ON doffcat.id=doff.category_id ';

		$sql="SELECT $select_string from `".CONST_TBL_PREFIX."discount_offer_categories` as doffcat $doff_join $where_clause_string $group_by_clause $order_by_clause $limit_clause";
		//if($_SESSION['dev_mode']==1)
			//echo $sql;
		$error_details_to_log = [];
		$error_details_to_log['function'] = __METHOD__;
		$error_details_to_log['sql'] = $sql;
		$error_details_to_log['str_params_to_bind'] = $str_params_to_bind;
		$error_details_to_log['int_params_to_bind'] = $int_params_to_bind;

		try{
			$pdo_stmt_obj = PDOConn::query($sql, $str_params_to_bind, $int_params_to_bind);
		
			if(array_key_exists('resource_only', $options) && $options['resource_only'])
				return $pdo_stmt_obj;

			$data = [];
			while($row=$pdo_stmt_obj->fetch(\PDO::FETCH_ASSOC))	
				$data[] = $row;
			$pdo_stmt_obj->closeCursor();
			return $data;

		}catch(\Exception $e){
			if(!is_a($e, '\PDOStatement'))
				ErrorHandler::logError($error_details_to_log,$e);
			else
				ErrorHandler::logError($error_details_to_log);
			return false;
		}
	}
	
}