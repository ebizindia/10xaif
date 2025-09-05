<?php
namespace eBizIndia;
class AdBanner{
	private $ad_id;
	private $ad_details;
	public function __construct(?int $ad_id=null){
		$this->ad_id = $ad_id;
	}

	public function getDetails(){
		if(empty($this->ad_id))
			return false;
		$options = [];
		$options['filters'] = [
			[ 'field' => 'id', 'type' => 'EQUAL', 'value' => $this->ad_id ]
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
		
		if(!in_array('name', $restricted_fields) && $data['name'] == ''){
			$result['error_code']=2;
			$result['message'][]="A name is required.";
			$result['error_fields'][]="#add_form_field_name";
		}else if(!in_array('target link', $restricted_fields) && $data['target_link'] == ''){
			$result['error_code']=2;
			$result['message'][]="Ad's target URL is required.";
			$result['error_fields'][]="#add_form_field_targetlink";
		}else{
			if(!in_array('dsk_img', $restricted_fields)){
				if($mode=='add' && $other_data['dsk_img']['error']==4){
					$result['error_code']=2;
					$result['message'] = 'Ad banner image for desktop screens is required.';
					$result['error_fields'][] = '#add_form_field_dskimg';	
				}elseif($other_data['dsk_img']['error']!=4 && $other_data['dsk_img']['error']!=0){
					$result['error_code']=2;
					$result['message'] = 'Process failed as the Ad banner image for desktop screens could not be uploaded.'.$file_upload_errors[$other_data['dsk_img']['error']];
					$result['error_fields'][] = '#add_form_field_dskimg';
					
				}else if($other_data['dsk_img']['error']==0){
					$file_ext = strtolower(pathinfo($other_data['dsk_img']['name'], PATHINFO_EXTENSION));
					if(empty($file_ext) || !in_array($file_ext, $other_data['field_meta']['ad_banner']['file_types'])){
						$result['error_code']=2;
						$result['message']="The selected file is not among one of the allowed file types.";
						$result['error_fields'][] = '#add_form_field_dskimg';
						
					}else if(!in_array($other_data['dsk_img']['type'], CONST_MIME_TYPES[$file_ext]??[] )){
						$result['error_code']=2;
						$result['message']="The selected desktop Ad Banner image is not a valid file type.";
						$result['error_fields'][] = '#add_form_field_dskimg';
					}
				}
			}

			if($result['error_code'] == 0){
				if(!in_array('mob_img', $restricted_fields)){
					if($mode=='add' && $other_data['mob_img']['error']==4){
						$result['error_code']=2;
						$result['message'] = 'Ad banner image for small screens is required.';
						$result['error_fields'][] = '#add_form_field_mobimg';	
					}elseif($other_data['mob_img']['error']!=4 && $other_data['mob_img']['error']!=0){
						$result['error_code']=2;
						$result['message'] = 'Process failed as the Ad banner image for small screens could not be uploaded.'.$file_upload_errors[$other_data['mob_img']['error']];
						$result['error_fields'][] = '#add_form_field_mobimg';
						
					}else if($other_data['mob_img']['error']==0){
						$file_ext = strtolower(pathinfo($other_data['mob_img']['name'], PATHINFO_EXTENSION));
						if(empty($file_ext) || !in_array($file_ext, $other_data['field_meta']['ad_banner']['file_types'])){
							$result['error_code']=2;
							$result['message']="The selected file is not among one of the allowed file types.";
							$result['error_fields'][] = '#add_form_field_mobimg';
							
						}else if(!in_array($other_data['mob_img']['type'], CONST_MIME_TYPES[$file_ext]??[] )){
							$result['error_code']=2;
							$result['message']="The selected small Ad Banner image is not a valid file type.";
							$result['error_fields'][] = '#add_form_field_mobimg';
						}
					}
				}

			}

			if($result['error_code'] == 0){
				if($data['start_dt']==''){
					$result['error_code']=2;
					$result['message'][]="Start date is required.";
					$result['error_fields'][]="#add_form_field_startdt_picker";
				}else if(!isDateValid($data['start_dt'])){
					$result['error_code']=2;
					$result['message'][]="Start date is invalid.";
					$result['error_fields'][]="#add_form_field_startdt_picker";
				}else if($data['end_dt']==''){
					$result['error_code']=2;
					$result['message'][]="End date is required.";
					$result['error_fields'][]="#add_form_field_enddt_picker";
				}else if(!isDateValid($data['end_dt'])){
					$result['error_code']=2;
					$result['message'][]="End date is invalid.";
					$result['error_fields'][]="#add_form_field_enddt_picker";
				}
			}
		} 


		
		return $result;
	}

	public static function getList($options=[]){
		$data = [];
		$fields_mapper = [];

		$fields_mapper['*']="ad.*";

		$fields_mapper['recordcount']='count(1)';
		$fields_mapper['id']="ad.id";
		$fields_mapper['name']="ad.name";
		$fields_mapper['dsk_img']="ad.dsk_img";
		$fields_mapper['mob_img']="ad.mob_img";
		$fields_mapper['target_link']="ad.target_link";
		$fields_mapper['start_dt']="ad.start_dt";
		$fields_mapper['end_dt']="ad.end_dt";
		$fields_mapper['active']="ad.active";
				
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

					case 'name':
					case 'target_link':
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

					case 'falls_in_period':
						$dt1 = $filter['value'][0];
						$dt2 = $filter['value'][1];
						$where_clause[] = ' ( (:whr'.$field_counter.'_dt1 between '.$fields_mapper['start_dt'].' and '.$fields_mapper['end_dt']. ' ) OR (:whr'.$field_counter.'_dt2 between '.$fields_mapper['start_dt'].' and '.$fields_mapper['end_dt']. ' ) OR ('.$fields_mapper['start_dt'].' BETWEEN :whr'.$field_counter.'_dt1 AND :whr'.$field_counter.'_dt2 )  OR ('.$fields_mapper['end_dt'].' BETWEEN :whr'.$field_counter.'_dt1 AND :whr'.$field_counter.'_dt2 )   ) ';
						$str_params_to_bind[':whr'.$field_counter.'_dt1']=$dt1;
						$str_params_to_bind[':whr'.$field_counter.'_dt2']=$dt2;
						break;
					case 'ads_on_date':
						$dt = (is_array($filter['value']))?$filter['value'][0]:$filter['value'];
						$where_clause[] = ' ( :whr'.$field_counter.'_dt between '.$fields_mapper['start_dt'].' and '.$fields_mapper['end_dt']. ' )';
						$str_params_to_bind[':whr'.$field_counter.'_dt']=$dt;
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
				if(preg_match("/^(ad)\./",$fields_mapper[$field]))
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
				if(preg_match("/^(ad)\./",$fields_mapper[$order['field']])){
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

			$order_by_clause=" ORDER BY ad.start_dt DESC, ad.end_dt DESC";

			if(!preg_match("/\s+as\s+start_dt/",$select_string)){
				$select_string .= ', '.$fields_mapper['start_dt'].' as start_dt';
			}
			if(!preg_match("/\s+as\s+end_dt/",$select_string)){
				$select_string .= ', '.$fields_mapper['end_dt'].' as end_dt';
			}
		}

		$limit_clause='';

		if(array_key_exists('page', $options) && filter_var($options['page'],FILTER_VALIDATE_INT) && $options['page']>0 && array_key_exists('recs_per_page', $options) && filter_var($options['recs_per_page'],FILTER_VALIDATE_INT) && $options['recs_per_page']>0){

			$limit_clause="LIMIT ".( ($options['page']-1) * $options['recs_per_page'] ).", $options[recs_per_page] ";

		}

		$where_clause_string = '';
		if(!empty($where_clause))
			$where_clause_string = ' WHERE '.implode(' AND ', $where_clause);

		$sql="SELECT $select_string from `".CONST_TBL_PREFIX."ad_banners` as ad $where_clause_string $group_by_clause $order_by_clause $limit_clause";

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


	public function saveDetails($data, $id =''){
		$str_data = $int_data = [];
		$table = '`'.CONST_TBL_PREFIX . 'ad_banners`';
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
		}catch(Exception $e){
			if(!is_a($e, '\PDOStatement'))
				ErrorHandler::logError($error_details_to_log,$e);
			else
				ErrorHandler::logError($error_details_to_log);
			return false;

		}

	}

	public function updateDetails($data){
		if($this->ad_id=='')
			return false;
		return $this->saveDetails($data, $this->ad_id);
	}


	public function uploadBannerImage($rec_id, $name, $tmp_name, $type){ 
		if($name=='' || $tmp_name=='')
			return false; // 
			
		$now = time();	
		$ext = pathinfo($name, PATHINFO_EXTENSION);
		$gen_file_name = CONST_AD_BANNER_IMG_PREFIX . $rec_id.'-'.$type.'-'.uniqid().'.'.strtolower($ext); 
		if(!@move_uploaded_file($tmp_name, CONST_AD_BANNER_IMG_DIR_PATH . $gen_file_name) && !@copy($tmp_name, CONST_AD_BANNER_IMG_DIR_PATH . $gen_file_name)){
			return false;
		}

		$pic_size = getimagesize(CONST_AD_BANNER_IMG_DIR_PATH.$gen_file_name);
		if(!empty($pic_size)){
			if($pic_size[1]>CONST_AD_BANNER_DIM[$type]['mh']){ // the actual height of the uploaded image exceeds the max allowed height for the $type
				$img_obj = new \eBizIndia\Img();
				if(!$img_obj->resizeImageWithADimensionFixed(CONST_AD_BANNER_IMG_DIR_PATH . $gen_file_name, CONST_AD_BANNER_DIM[$type]['mh'], null, CONST_AD_BANNER_IMG_DIR_PATH.$gen_file_name)){
					unlink(CONST_AD_BANNER_IMG_DIR_PATH.$gen_file_name);
					return false;
				}
			}
		}

		
		$result = [];
		$result['gen_file_name'] = $gen_file_name;
		return $result;

	}


	public static function getAdsActiveOnDate($dt, $order_by = 'random'){
		$options=[];
		$options['filters'] = [

			['field' => 'ads_on_date', 'type'=>'', 'value'=>$dt ],
			['field' => 'active', 'type'=>'EQUAL', 'value'=>'y' ],

		];
		$options['fieldstofetch'] = [
			'id', 'target_link', 'dsk_img', 'mob_img'
		];
		$options['order_by'] = 'random';
		return self::getList($options);
	}



}