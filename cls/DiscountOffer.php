<?php
namespace eBizIndia;
class DiscountOffer{
	private $d_o_id;
	private $d_o_details;
	public function __construct(?int $d_o_id=null){
		$this->d_o_id = $d_o_id;
	}

	public function getDetails(){
		if(empty($this->d_o_id))
			return false;
		$options = [
			'filters' => [	[ 'field' => 'id', 'type' => 'EQUAL', 'value' => $this->d_o_id ]]
		];
		return  self::getList($options);
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
		
		if(!in_array('category_id', $restricted_fields) && $data['category_id'] == ''){
			$result['error_code']=2;
			$result['message'][]="A category is required for the epic offer.";
			$result['error_fields'][]="#add_form_field_categoryid";
		}else if(!in_array('title', $restricted_fields) && $data['title'] == ''){
			$result['error_code']=2;
			$result['message'][]="A title is required.";
			$result['error_fields'][]="#add_form_field_title";
		}else if(!in_array('description', $restricted_fields) && $data['description'] == ''){
			$result['error_code']=2;
			$result['message'][]="Description is required.";
			$result['error_fields'][]="#add_form_field_description";
		}else{
			// if(!in_array('dsk_img', $restricted_fields)){
			// 	if($other_data['dsk_img']['error']!=4 && $other_data['dsk_img']['error']!=0){
			// 		$result['error_code']=2;
			// 		$result['message'] = 'Process failed as the Discount Offer image for desktop screens could not be uploaded.'.$file_upload_errors[$other_data['dsk_img']['error']];
			// 		$result['error_fields'][] = '#add_form_field_dsk_img';
					
			// 	}else if($other_data['dsk_img']['error']==0){
			// 		$file_ext = strtolower(pathinfo($other_data['dsk_img']['name'], PATHINFO_EXTENSION));
			// 		if(empty($file_ext) || !in_array($file_ext, $other_data['field_meta']['discount_offer']['file_types'])){
			// 			$result['error_code']=2;
			// 			$result['message']="The selected file is not among one of the allowed file types.";
			// 			$result['error_fields'][] = '#add_form_field_dsk_img';
						
			// 		}else if(!in_array($other_data['dsk_img']['type'], CONST_MIME_TYPES[$file_ext]??[] )){
			// 			$result['error_code']=2;
			// 			$result['message']="The selected desktop Discount Offer image is not a valid file type.";
			// 			$result['error_fields'][] = '#add_form_field_dsk_img';
			// 		}
			// 	}
			// }

			if($result['error_code'] == 0){
				if(!in_array('mob_img', $restricted_fields)){
					/*if($mode=='add' && $other_data['mob_img']['error']==4){
						$result['error_code']=2;
						$result['message'] = 'Discount Offer image for small screens is required.';
						$result['error_fields'][] = '#add_form_field_mob_img';	
					}else*/if($other_data['mob_img']['error']!=4 && $other_data['mob_img']['error']!=0){
						$result['error_code']=2;
						$result['message'] = 'Process failed as the epic Offer image for small screens could not be uploaded.'.$file_upload_errors[$other_data['mob_img']['error']];
						$result['error_fields'][] = '#add_form_field_mob_img';
					}else if($other_data['mob_img']['error']==0){
						$file_ext = strtolower(pathinfo($other_data['mob_img']['name'], PATHINFO_EXTENSION));
						if(empty($file_ext) || !in_array($file_ext, $other_data['field_meta']['discount_offer']['file_types'])){
							$result['error_code']=2;
							$result['message']="The selected file is not among one of the allowed file types.";
							$result['error_fields'][] = '#add_form_field_mob_img';
						}else if(!in_array($other_data['mob_img']['type'], CONST_MIME_TYPES[$file_ext]??[] )){
							$result['error_code']=2;
							$result['message']="The selected small Discount Offer image is not a valid file type.";
							$result['error_fields'][] = '#add_form_field_mob_img';
						}
					}
				}
			}

			if($result['error_code'] == 0){
				if(!in_array('mou', $restricted_fields)){
					if($other_data['mou']['error']!=4 && $other_data['mou']['error']!=0){
						$result['error_code']=2;
						$result['message'] = 'Process failed as the MOU document could not be uploaded.'.$file_upload_errors[$other_data['mou']['error']];
						$result['error_fields'][] = '#add_form_field_mou';
					}else if($other_data['mou']['error']==0){
						$file_ext = strtolower(pathinfo($other_data['mou']['name'], PATHINFO_EXTENSION));
						if(empty($file_ext) || !in_array($file_ext, ['pdf'])){
							$result['error_code']=2;
							$result['message']="The selected file is not a PDF.";
							$result['error_fields'][] = '#add_form_field_mou';
						}else if(!in_array($other_data['mou']['type'], CONST_MIME_TYPES[$file_ext]??[] )){
							$result['error_code']=2;
							$result['message']="The selected MOU document is not a valid PDF file.";
							$result['error_fields'][] = '#add_form_field_mou';
						}
					}
				}
			}

			if($result['error_code'] == 0){
				if($data['valid_upto']==''){
					$result['error_code']=2;
					$result['message'][]="Valid upto date is required.";
					$result['error_fields'][]="#add_form_field_valid_upto_picker";
				}else if(!isDateValid($data['valid_upto'])){
					$result['error_code']=2;
					$result['message'][]="Valid upto date is invalid.";
					$result['error_fields'][]="#add_form_field_valid_upto_picker";
				}
			}
		}		
		return $result;
	}

	public static function getList($options=[]){
		$data = [];
		$fields_mapper = [];

		$fields_mapper['*']="doff.*, doffcat.name as category_name";

		$fields_mapper['record_count']='count(1)';
		$fields_mapper['id']="doff.id";
		$fields_mapper['category_id']="doff.category_id";
		$fields_mapper['category_name']="doffcat.name";
		$fields_mapper['title']="doff.title";
		$fields_mapper['description']="doff.description";
		$fields_mapper['address']="doff.address";
		$fields_mapper['offer_url']="doff.offer_url";
		$fields_mapper['company']="doff.company";
		$fields_mapper['dsk_img']="doff.dsk_img";
		$fields_mapper['mob_img']="doff.mob_img";
		$fields_mapper['mou']="doff.mou";
		$fields_mapper['mou_org_file_name']="doff.mou_org_file_name";
		$fields_mapper['display_start_date']="doff.display_start_date";
		$fields_mapper['display_end_date']="doff.display_end_date";
		$fields_mapper['valid_upto']="doff.valid_upto";
		$fields_mapper['active']="doff.active";
		$fields_mapper['category_active']="doffcat.active";
				
		$where_clause=[];

		$str_params_to_bind=[];
		$int_params_to_bind=[];

		if( array_key_exists('filters',$options) && is_array($options['filters']) ){
			$field_counter=0;
			foreach($options['filters'] as $filter){
				++$field_counter;
				switch ($filter['field']) {
					case 'category_id':
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

					case 'title':
					case 'description':
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

					case 'validity_period':
						if($filter['value'][0] && $filter['value'][1]){
							$where_clause[] = $fields_mapper['valid_upto'].' BETWEEN :whr'.$field_counter.'_dt1 AND :whr'.$field_counter.'_dt2 ';
							$str_params_to_bind[':whr'.$field_counter.'_dt1']=$filter['value'][0];
							$str_params_to_bind[':whr'.$field_counter.'_dt2']=$filter['value'][1];
						}else if($filter['value'][0]){
							$where_clause[] = $fields_mapper['valid_upto'].' >= :whr'.$field_counter.'_dt1 ';
							$str_params_to_bind[':whr'.$field_counter.'_dt1']=$filter['value'][0];
						}else if($filter['value'][1]){
							$where_clause[] = $fields_mapper['valid_upto'].' <= :whr'.$field_counter.'_dt1 ';
							$str_params_to_bind[':whr'.$field_counter.'_dt1']=$filter['value'][1];
						}
						break;
					case 'display_period':
						/*$dt1 = $filter['value'][0];
						$dt2 = $filter['value'][1];
						$where_clause[] = ' ( (:whr'.$field_counter.'_dt1 between '.$fields_mapper['display_start_date'].' and '.$fields_mapper['display_end_date']. ' ) OR (:whr'.$field_counter.'_dt2 between '.$fields_mapper['display_start_date'].' and '.$fields_mapper['display_end_date']. ' ) OR ('.$fields_mapper['display_start_date'].' BETWEEN :whr'.$field_counter.'_dt1 AND :whr'.$field_counter.'_dt2 )  OR ('.$fields_mapper['display_end_date'].' BETWEEN :whr'.$field_counter.'_dt1 AND :whr'.$field_counter.'_dt2 )   ) ';
						$str_params_to_bind[':whr'.$field_counter.'_dt1']=$dt1;
						$str_params_to_bind[':whr'.$field_counter.'_dt2']=$dt2;*/
						if($filter['value'][0] && $filter['value'][1]){
							$where_clause[] = ' ( (:whr'.$field_counter.'_dt1 between '.$fields_mapper['display_start_date'].' and '.$fields_mapper['display_end_date']. ' ) OR (:whr'.$field_counter.'_dt2 between '.$fields_mapper['display_start_date'].' and '.$fields_mapper['display_end_date']. ' ) OR ('.$fields_mapper['display_start_date'].' BETWEEN :whr'.$field_counter.'_dt1 AND :whr'.$field_counter.'_dt2 )  OR ('.$fields_mapper['display_end_date'].' BETWEEN :whr'.$field_counter.'_dt1 AND :whr'.$field_counter.'_dt2 )   ) ';
							$str_params_to_bind[':whr'.$field_counter.'_dt1']=$filter['value'][0];
							$str_params_to_bind[':whr'.$field_counter.'_dt2']=$filter['value'][1];
						}else if($filter['value'][0]){
							$where_clause[] = ' ( ('.$fields_mapper['display_start_date'].' >= :whr'.$field_counter.'_dt1 OR '.$fields_mapper['display_end_date'].' >= :whr'.$field_counter.'_dt1 ) ) ';
							$str_params_to_bind[':whr'.$field_counter.'_dt1']=$filter['value'][0];
						}else if($filter['value'][1]){
							$where_clause[] = ' ( ('.$fields_mapper['display_start_date'].' <= :whr'.$field_counter.'_dt1 OR '.$fields_mapper['display_end_date'].' <= :whr'.$field_counter.'_dt1 ) ) ';
							$str_params_to_bind[':whr'.$field_counter.'_dt1']=$filter['value'][1];
						}
						break;
					case 'valid_upto':
						switch($filter['type']){
							case 'BETWEEN':
								$where_clause[] = $filter['field']." BETWEEN :whr".$field_counter."_vus AND  :whr".$field_counter."_vue";	
								$str_params_to_bind[':whr'.$field_counter.'_vus'] = $filter['value'][0];
								$str_params_to_bind[':whr'.$field_counter.'_vue'] = $filter['value'][1];
								break;
							case 'AFTER':
								$where_clause[] = $filter['field']." > :whr".$field_counter."_vu";	
								$str_params_to_bind[':whr'.$field_counter.'_vu'] = $filter['value'][0];
								break;
							case 'ON_OR_AFTER':
								$where_clause[] = $filter['field']." >= :whr".$field_counter."_vu";	
								$str_params_to_bind[':whr'.$field_counter.'_vu'] = $filter['value'][0];
								break;
							case 'BEFORE':
								$where_clause[] = $filter['field']." < :whr".$field_counter."_vu";	
								$str_params_to_bind[':whr'.$field_counter.'_vu'] = $filter['value'][0];
								break;
							case 'ON_OR_BEFORE':
								$where_clause[] = $filter['field']." <= :whr".$field_counter."_vu";	
								$str_params_to_bind[':whr'.$field_counter.'_vu'] = $filter['value'][0];
								break;
							case 'EQUAL':
							default:
								$where_clause[] = $filter['field']." = :whr".$field_counter."_jdt";	
								$str_params_to_bind[':whr'.$field_counter.'_vu'] = $filter['value'][0];
								break;
						}

						break;
					case 'category_active':
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

			if($order_by_clause!='' && !stristr($order_by_clause, 'doff.id'))
				$order_by_clause .= ', '.$fields_mapper['id'].' DESC ';

		}else if($options['order_by']==='random'){
			$order_by_clause .= ' ORDER BY RAND() ';
		}

		

		if(!$record_count && $order_by_clause==''){

			$order_by_clause=" ORDER BY doff.valid_upto DESC";

			if(!preg_match("/\s+as\s+valid_upto/",$select_string)){
				$select_string .= ', '.$fields_mapper['valid_upto'].' as valid_upto';
			}
		}

		$limit_clause='';
		if(array_key_exists('page', $options) && filter_var($options['page'],FILTER_VALIDATE_INT) && $options['page']>0 && array_key_exists('recs_per_page', $options) && filter_var($options['recs_per_page'],FILTER_VALIDATE_INT) && $options['recs_per_page']>0){
			$limit_clause="LIMIT ".( ($options['page']-1) * $options['recs_per_page'] ).", ".$options['recs_per_page'];
		}

		$where_clause_string = '';
		if(!empty($where_clause))
			$where_clause_string = ' WHERE '.implode(' AND ', $where_clause);

		$doffcat_join = '';
		if(preg_match("/doffcat\./", " $select_string $where_clause_string $group_by_clause $order_by_clause "))
			$doffcat_join = ' JOIN `'.CONST_TBL_PREFIX.'discount_offer_categories` doffcat ON doff.category_id=doffcat.id ';

		$sql="SELECT $select_string from `".CONST_TBL_PREFIX."discount_offers` as doff $doffcat_join $where_clause_string $group_by_clause $order_by_clause $limit_clause";
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

	public function save($data, $id =''){
		$str_data = $int_data = [];
		$table = '`'.CONST_TBL_PREFIX . 'discount_offers`';
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

		}else{ 
			$type='insert';
			$sql="INSERT INTO $table SET ";

			$whereclause='';

		}
		$values=[];
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

		try{
			$stmt_obj = PDOConn::query($sql, $str_data, $int_data);
			$affetcedrows= $stmt_obj->rowCount();
			if($type=='insert')
				return PDOConn::lastInsertId();
			return true;
		}catch(Exception $e){
			$error_details_to_log = [
				'at' => date('Y-m-d H:i:s'),
				'function' => __METHOD__,
				'type' => $type,
				'data' => $data,
				'id' => $id,
				'sql' => $sql
			];
			if(!is_a($e, '\PDOStatement'))
				ErrorHandler::logError($error_details_to_log,$e);
			else
				ErrorHandler::logError($error_details_to_log);
			return false;
		}
	}

	public function update($data){
		if($this->d_o_id=='')
			return false;
		return $this->save($data, $this->d_o_id);
	}

	public function uploadOfferImage($rec_id, $name, $tmp_name, $type){ 
		if($name=='' || $tmp_name=='')
			return false;
			
		$now = time();	
		$ext = pathinfo($name, PATHINFO_EXTENSION);
		$gen_file_name = CONST_DISCOUNT_OFFER_IMG_PREFIX . $rec_id.'-'.$type.'-'.uniqid().'.'.strtolower($ext); 
		if(!@move_uploaded_file($tmp_name, CONST_DISCOUNT_OFFER_IMG_DIR_PATH . $gen_file_name) && !@copy($tmp_name, CONST_DISCOUNT_OFFER_IMG_DIR_PATH . $gen_file_name)){
			return false;
		}		
		return ['gen_file_name' => $gen_file_name];
	}


	public function uploadOfferMOU($rec_id, $name, $tmp_name){ 
		if($name=='' || $tmp_name=='')
			return false;
			
		$now = time();	
		$ext = pathinfo($name, PATHINFO_EXTENSION);
		$gen_file_name = CONST_DISCOUNT_OFFER_MOU_PREFIX . $rec_id.'-'.uniqid().'.'.strtolower($ext); 
		if(!@move_uploaded_file($tmp_name, CONST_DISCOUNT_OFFER_MOU_DIR_PATH . $gen_file_name) && !@copy($tmp_name, CONST_DISCOUNT_OFFER_MOU_DIR_PATH . $gen_file_name)){
			return false;
		}		
		return ['gen_file_name' => $gen_file_name];
	}

}