<?php
namespace eBizIndia;
use \Exception;
class User{

	protected $loggedinmember;
	protected $loginlocation;
	protected $menulist;
	protected $db_conn;
	private $last_mysql_error_code;
	private $last_sqlstate_code;

	public function __Construct($db_conn=null){
		$this->db_conn=$db_conn;
		$this->loggedinmember=array(); // details of the logged in member
		$this->menulist=array(); // menulist for the logged in user
		$this->last_mysql_error_code = $this->last_sqlstate_code='';

	}

	public function __get($name){

		if(in_array($name, ['last_mysql_error_code', 'last_sqlstate_code']))
			return $this->{$name};

	}


	function setUserStatus($userID,$status){
		$this->last_mysql_error_code = $this->last_sqlstate_code='';
		$sql="UPDATE `" . CONST_TBL_PREFIX . "users` set `status`='$status' WHERE `id`=$userID ";

		$error_details_to_log = [];
		$error_details_to_log['at'] = date('Y-m-d H:i:s');
		$error_details_to_log['method'] = "setUserStatus";
		$error_details_to_log['sql'] = $sql;
		$error_details_to_log['userID'] = $userID;
		$error_details_to_log['status'] = $status;

		try{
			$res=$this->db_conn->exec($sql);

			if($res===false){
				return false;

			}
		}catch(Exception $e){
			$error_details_to_log['exception_msg'] = $e->getMessage();
			$error_details_to_log['mysql_error'] = $this->db_conn->errorInfo();
			$error_details_to_log['affected rows'] = 'boolean false';
			\eBizIndia\logErrorInFile(time(),$_SERVER['REQUEST_URI'], json_encode($error_details_to_log));
			$this->last_mysql_error_code = $error_details_to_log['mysql_error'][1];
			$this->last_mysqlstate_code = $error_details_to_log['mysql_error'][0];
			return false;

		}

		return true;

	}

	function setDbConnection($db_conn){

		$this->db_conn=$db_conn;

	}

	function unsetDbConnection(){
		$this->db_conn=null;

	}

	function deleteUser($userid){
		$userid=(int)$userid;

		$this->last_mysql_error_code = $this->last_sqlstate_code='';


		$sql="DELETE from `" . CONST_TBL_PREFIX . "users` WHERE `id`=$userid ";
		$error_details_to_log = [];
		$error_details_to_log['at'] = date('Y-m-d H:i:s');
		$error_details_to_log['method'] = "deleteUser";
		$error_details_to_log['sql'] = $sql;
		$error_details_to_log['userID'] = $userID;


		try{
			$affectedrows=$this->db_conn->exec($sql); 					//
			$sql="DELETE from `" . CONST_TBL_PREFIX . "sessions` WHERE `user_id`=$userid ";
			$this->db_conn->exec($sql);
			if($affectedrows==0){
				$error_details_to_log['mysql_error'] = $this->db_conn->errorInfo();
				$error_details_to_log['affected rows'] = 0;
				\eBizIndia\logErrorInFile(time(),$_SERVER['REQUEST_URI'], json_encode($error_details_to_log));
				$this->last_mysql_error_code = $error_details_to_log['mysql_error'][1];
				$this->last_mysqlstate_code = $error_details_to_log['mysql_error'][0];
				return null;
			}
			return true;
		}catch(Exception $e){
			$error_details_to_log['exception_msg'] = $e->getMessage();
			$error_details_to_log['mysql_error'] = $this->db_conn->errorInfo();
			$error_details_to_log['affected rows'] = 'boolean false';
			\eBizIndia\logErrorInFile(time(),$_SERVER['REQUEST_URI'], json_encode($error_details_to_log));
			$this->last_mysql_error_code = $error_details_to_log['mysql_error'][1];
			$this->last_mysqlstate_code = $error_details_to_log['mysql_error'][0];
			// return false;
			throw $e;


		}

	}

	function checkEmailDuplicy($email,$id) // apparently not being used anywhere
	{
		$sql="SELECT `email` from `" . CONST_TBL_PREFIX . "users` where `email` = '".$email."' and `id` != '".$id."'";

		$res=$this->db_conn->query($sql);
		$useremail=array();
		if(isset($res))	{
			$row[]=$res->fetch(\PDO::FETCH_ASSOC);
		}
			return	$row;
	}


	function revokeUserRoles($userid, $roleids = []){
		$userid = (int)$userid;
		if($userid<=0 || (!empty($roleids) && !is_array($roleids)) )
			return false;
		$int_data = [];
		$sql = "DELETE from `" . CONST_TBL_PREFIX . "user_roles` WHERE user_id=$userid";

		if(!empty($roleids)){
			$place_holders = [];
			foreach ($roleids as $key => $rid) {
				$key = ":id_{$key}_";
				$place_holders[] = $key;
				$int_data[$key] = $id;
			}
			$sql .= " AND role_id in(".implode(',',$place_holders).") " ;

		}


		$error_details_to_log = [];
		$error_details_to_log['at'] = date('Y-m-d H:i:s');
		$error_details_to_log['method'] = __METHOD__;
		$error_details_to_log['sql'] = $sql;
		$error_details_to_log['userid'] = $userid;
		$error_details_to_log['roleids'] = $roleids;

		try{
			$res = PDOConn::query($sql, [], $int_data);
			if($res===false)
				return false;
			return true;
		}catch(Exception $e){ // w1
			$error_details_to_log['exception_msg'] = $e->getMessage();
			$error_details_to_log['mysql_error'] = $this->db_conn->errorInfo();
			$error_details_to_log['affected rows'] = 'boolean false';
			if(!is_a($e, '\PDOException'))
				ErrorHandler::logError($error_details_to_log,$e);
			else
				ErrorHandler::logError($error_details_to_log);
			return false;

		}
	}


	function assignRolesToUsers($userid, $roleids){
		$userid = (int)$userid;
		if($userid<=0 || !is_array($roleids) || empty($roleids))
			return false;

		$roleids = array_map('intval', $roleids);

		$sql = "INSERT IGNORE INTO `" . CONST_TBL_PREFIX . "user_roles` (`user_id`,`role_id`) values";

		$values = $int_data = [];
		$index = 0;
		foreach($roleids as $rid){
			if($rid>0){
				$key1 = ":userid_{$index}_";
				$key2 = ":rid_{$index}_";
				$int_data[$key1] = $userid;
				$int_data[$key2] = $rid;
				$values[] = "($key1, $key2)";
			}
		}

		if(empty($values))
			return false;

		$sql .= implode(',',$values);


		$error_details_to_log = [];
		$error_details_to_log['at'] = date('Y-m-d H:i:s');
		$error_details_to_log['method'] = __METHOD__;
		$error_details_to_log['sql'] = $sql;
		$error_details_to_log['userid'] = $userid;
		$error_details_to_log['roleids'] = $roleids;

		try{
			$stmt_obj = PDOConn::query($sql, [], $int_data);
			$affetcedrows = $stmt_obj->rowCount();
			return true;
		}catch(Exception $e){ // w1
			if(!is_a($e, '\PDOException'))
				ErrorHandler::logError($error_details_to_log,$e);
			else
				ErrorHandler::logError($error_details_to_log);
			return false;
		}

	}



	function saveRole($role, $roleid=''){
		$this->last_mysql_error_code = $this->last_sqlstate_code='';
		$type='insert';
		if($roleid!=''){
			$type='update';
			$roleid = (int)$roleid;
			if($roleid<=0)
				return false;
			$sql="UPDATE `" . CONST_TBL_PREFIX . "roles` SET ";

			$whereclause=" WHERE `role_id`=$roleid";

		}else{ // Inserting new role

			$sql="INSERT INTO `" . CONST_TBL_PREFIX . "roles` SET ";

			$whereclause='';

		}

		$values=array();



		foreach($role as $field=>$value){

			if($value==='')

				$values[]="`$field`=NULL";

			else

				$values[]="`$field`=".$this->db_conn->quote($value);

		}

		$sql.=implode(',',$values);
		$sql.=$whereclause;


		$error_details_to_log = [];
		$error_details_to_log['at'] = date('Y-m-d H:i:s');
		$error_details_to_log['method'] = __METHOD__;
		$error_details_to_log['type'] = $type;
		$error_details_to_log['sql'] = $sql;
		$error_details_to_log['role'] = $role;
		$error_details_to_log['roleid'] = $roleid;



		try{
			$affetcedrows=$this->db_conn->exec($sql);

			if($affetcedrows===false){ // will be required if the PDO exception does not work
				$error_details_to_log['mysql_error'] = $this->db_conn->errorInfo();
				$error_details_to_log['affected rows'] = 'boolean false';
				\eBizIndia\logErrorInFile(time(),$_SERVER['REQUEST_URI'], json_encode($error_details_to_log));
				return false;

			}elseif($affetcedrows==0){
				$error_details_to_log['mysql_error'] = $this->db_conn->errorInfo();
				$error_details_to_log['affected rows'] = 0;
				\eBizIndia\logErrorInFile(time(),$_SERVER['REQUEST_URI'], json_encode($error_details_to_log));
				return null;

			}else{

				if($type=='insert')
					return $this->db_conn->lastInsertId();
				return true;

			}

		}catch(Exception $e){ // w1
			$error_details_to_log['exception_msg'] = $e->getMessage();
			$error_details_to_log['mysql_error'] = $this->db_conn->errorInfo();
			$error_details_to_log['affected rows'] = 'boolean false';
			\eBizIndia\logErrorInFile(time(),$_SERVER['REQUEST_URI'], json_encode($error_details_to_log));
			$this->last_mysql_error_code = $error_details_to_log['mysql_error'][1];
			$this->last_mysqlstate_code = $error_details_to_log['mysql_error'][0];
			return false;

		}

	}


	function saveUserDetails($data, $id=''){
  
        $str_data = $int_data = [];
		$table = '`'.CONST_TBL_PREFIX . 'users`';
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
		}catch(Exception $e){var_dump($e);
			if(!is_a($e, '\PDOStatement'))
				ErrorHandler::logError($error_details_to_log,$e);
			else
				ErrorHandler::logError($error_details_to_log);
			return false;

		}

	}


	function getRoles($role_name='', $role_id='', $role_for=''){
		$str_data = [];
		$sql = "SELECT * from `" . CONST_TBL_PREFIX . "roles` r ";
		$where_clause = [];
		if(!empty($role_name)){
			$str_data[':role_name'] = $role_name;
			$where_clause[] = " role_name like :role_name ";	
		}
		if(!empty($role_id)){
			$str_data[':role_id'] = $role_id;
			$where_clause[] = " role_id like :role_id ";	
		}
		if(!empty($role_for)){
			$str_data[':role_for'] = $role_for;
			$where_clause[] = " role_for like :role_for ";	
		}

		if(!empty($where_clause))
			$sql .= ' WHERE '.implode(' AND ', $where_clause);

		$error_details_to_log = [];
		$error_details_to_log['at'] = date('Y-m-d H:i:s');
		$error_details_to_log['function'] = __METHOD__;
		$error_details_to_log['data'] = compact('role_name', 'role_id', 'role_for');
		$error_details_to_log['sql'] = $sql;

		try{
			$data = [];
			$stmt_obj = PDOConn::query($sql, $str_data);
			while($row=$stmt_obj->fetch(\PDO::FETCH_ASSOC)){
				$data[] = $row;
			}
			return $data;
		}catch(Exception $e){var_dump($e);
			if(!is_a($e, '\PDOStatement'))
				ErrorHandler::logError($error_details_to_log,$e);
			else
				ErrorHandler::logError($error_details_to_log);
			return false;
		}

	}


	function getUserLevelsList($active = ''){
		$this->last_mysql_error_code = $this->last_mysqlstate_code = '';
		$ulevels = [];

		$sql = "SELECT code, name, active from `" . CONST_TBL_PREFIX . "user_levels` ulvl ";

		if($active!=''){

			if($active!='Y')
				$active='N';
			$sql .= " WHERE active='$active' ";

		}

		$sql .= " ORDER BY code, name ";


		$error_details_to_log = [];
		$error_details_to_log['at'] = date('Y-m-d H:i:s');
		$error_details_to_log['function'] = __METHOD__;
		$error_details_to_log['active'] = $active;
		$error_details_to_log['sql'] = $sql;

		try{

			$res = $this->db_conn->query($sql);

			if($res===false){  // will be required if the PDO exception does not work
				$error_details_to_log['mysql_error'] = $this->db_conn->errorInfo();
				$error_details_to_log['res'] = 'boolean false';
				\eBizIndia\logErrorInFile(time(),$_SERVER['REQUEST_URI'], json_encode($error_details_to_log));
				return false;

			}elseif($res==0){
				$error_details_to_log['mysql_error'] = $this->db_conn->errorInfo();
				$error_details_to_log['res'] = 0;
				\eBizIndia\logErrorInFile(time(),$_SERVER['REQUEST_URI'], json_encode($error_details_to_log));
				return [];

			}else{

				while($row = $res->fetch(\PDO::FETCH_ASSOC)){

					$ulevels[$row['code']] = $row;

				}

			}

			return $ulevels;

		}catch(Exception $e){
			$error_details_to_log['exception_msg'] = $e->getMessage();
			$error_details_to_log['mysql_error'] = $this->db_conn->errorInfo();
			$error_details_to_log['affected rows'] = 'boolean false';
			\eBizIndia\logErrorInFile(time(),$_SERVER['REQUEST_URI'], json_encode($error_details_to_log));
			$this->last_mysql_error_code = $error_details_to_log['mysql_error'][1];
			$this->last_mysqlstate_code = $error_details_to_log['mysql_error'][0];
			return false;
		}

	}

	public function getUseridsUnderAUserByRepOfcHierarchy($for_userid, $start_level=1, $uptill_level = 1, $active_only=''){ // uptill_level=1 means immediately under


		$userids = $userids_tmp = [];

		if($uptill_level<=0)
			return $userids;

		$options = [];
		$options['filters'] = [];
		if($active_only!==''){
			$active_only = (int)$active_only;
			$options['filters'][] = ['field'=>'status', 'type'=>'EQUAL', 'value'=>($active_only===0)?'0':'1'];
		}

		$options['fieldstofetch'] = ['id'];
		$userids = $this->getList($options);
		if($userids === false){

			$error_details_to_log = [];
			$error_details_to_log['at'] = date('Y-m-d H:i:s');
			$error_details_to_log['function'] = __METHOD__;
			$error_details_to_log['options'] = $options;
			$error_details_to_log['mysql_error_no'] = $this->last_mysql_error_code;
			$error_details_to_log['mysql_state_code'] = $this->last_mysql_state_code;
			\eBizIndia\logErrorInFile(time(),$_SERVER['REQUEST_URI'], json_encode($error_details_to_log));
			throw new Exception("Error fetching rep ofc wise users under a user", 10002);
		}

		if(!empty($userids))
			$userids_tmp = $userids = array_column($userids, 'id');


		if($start_level>1)
			$userids = []; // don't add the queried userids to the array to be returned as the starting level has not been reached

		foreach($userids_tmp as $uid){

			$userids = array_merge($userids,$this->getUseridsUnderAUserByRepOfcHierarchy($uid, --$start_level, --$uptill_level, $active_only));

		}

		return $userids;

	}



	public function getList($options=[]){
		$this->last_mysql_error_code = $this->last_mysqlstate_code = '';
		$data=array();
		$fields_mapper = $fields_mapper1 = [];


		$fields_mapper1['*'] = 'T1.*, r.role_name as role, ur.role_id as role_id';
		$fields_mapper1['id']='T1.id';
		$fields_mapper1['username']='T1.username';
		$fields_mapper1['password']='T1.password';
		$fields_mapper1['profile_type']="T1.profile_type";
		$fields_mapper1['profile_id']='T1.profile_id';
		$fields_mapper1['status']='T1.status';
		$fields_mapper1['activated']="T1.activated";
		$fields_mapper1['pswdResetRequestedOn']="T1.pswdResetRequestedOn";
		$fields_mapper1['role']='r.role_name';
		$fields_mapper1['role_id']='r.role_id';


		$fields_mapper['*']="u.id as id, u.username as username, u.status as status, u.activated as activated, u.profile_type as profile_type, u.profile_id as profile_id, u.pswdResetRequestedOn as pswdResetRequestedOn";

		$fields_mapper['recordcount']='count(distinct(u.id))';
		$fields_mapper['id'] ='u.id';
		$fields_mapper['username'] ='u.username';
		$fields_mapper['password'] ='u.password';
		$fields_mapper['profile_type'] ='u.profile_type';
		$fields_mapper['profile_id'] ='u.profile_id';
		$fields_mapper['status'] ='u.status';
		$fields_mapper['activated'] ='u.activated';
		$fields_mapper['pswdResetRequestedOn'] ='u.pswdResetRequestedOn';
		

		$where_clause = [];

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
									$where_clause[] = ' ur1.role_id in('.implode(',',$place_holders).') ';

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
									$where_clause[] = ' ur1.role_id not in('.implode(',',$place_holders).') ';

								}
								break;

							case 'NOT_EQUAL':
								$roleid=(is_array($filter['value']))?$filter['value'][0]:$filter['value'];
								$where_clause[] = ' ur1.role_id!=:whr'.$field_counter.'_roleid';
								$int_params_to_bind[':whr'.$field_counter.'_roleid']=$roleid;
								break;

							default:
								$roleid=(is_array($filter['value']))?$filter['value'][0]:$filter['value'];
								$where_clause[] = ' ur1.role_id=:whr'.$field_counter.'_roleid';
								$int_params_to_bind[':whr'.$field_counter.'_roleid']=$roleid;
						}

						break;




					case 'profile_type':
						switch($filter['type']){
							case 'NOT_EQUAL':
								$ut=(is_array($filter['value']))?$filter['value'][0]:$filter['value'];
								$where_clause[] = $fields_mapper[$filter['field']].'!=:whr'.$field_counter.'_pt';
								$str_params_to_bind[':whr'.$field_counter.'_pt']=$pt;
								break;

							default:
								$pt=(is_array($filter['value']))?$filter['value'][0]:$filter['value'];
								$where_clause[] = $fields_mapper[$filter['field']].'=:whr'.$field_counter.'_pt';
								$str_params_to_bind[':whr'.$field_counter.'_pt']=$pt;
								break;
						}

						break;

					case 'username':
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


					case 'role':
						$role=(is_array($filter['value']))?$filter['value'][0]:$filter['value'];
						switch($filter['type']){
							case 'IN':
								if(is_array($filter['value'])){
									$place_holders=[];
									$k=0;
									foreach($filter['value'] as $role){
										$k++;
										$place_holders[]=":whr".$field_counter."_role_{$k}_";
										$str_params_to_bind[":whr".$field_counter."_role_{$k}_"]=$role;
									}
									$where_clause[] = ' r1.role_name in('.implode(',',$place_holders).') '; 
								}
								break;
							case 'CONTAINS':
								$str_params_to_bind[':whr'.$field_counter.'_role']="%$role%";
								$where_clause[] =" r1.role_name like :whr".$field_counter."_role";
								break;
							case 'STARTS_WITH':
								$str_params_to_bind[':whr'.$field_counter.'_role']="$role%";
								$where_clause[] =" r1.role_name like :whr".$field_counter."_role";
								break;
							case 'ENDS_WITH':
								$str_params_to_bind[':whr'.$field_counter.'_role']="%$role";
								$where_clause[] =" r1.role_name like :whr".$field_counter."_role";
								break;
							case 'EQUAL':
							default:
								$str_params_to_bind[':whr'.$field_counter.'_role']="$role";
								$where_clause[] =" r1.role_name like :whr".$field_counter."_role";
								break;
						}

						break;


					case 'status':
						$status=(is_array($filter['value']))?$filter['value'][0]:$filter['value'];
						switch($filter['type']){
							case 'NOT_EQUAL':
								$where_clause[] = $fields_mapper[$filter['field']].' !=:whr'.$field_counter.'_status';
								$str_params_to_bind[':whr'.$field_counter.'_status']=$status;
								break;
							default:

								$where_clause[] = $fields_mapper[$filter['field']].'=:whr'.$field_counter.'_status';
								$str_params_to_bind[':whr'.$field_counter.'_status']=$status;
						}

						break;


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
				if(preg_match("/^(u|r1|ur1|r|ur)\./",$fields_mapper[$field]))
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
				if(preg_match("/^(u|r1|ur1|r|ur)\./",$fields_mapper[$order['field']])){
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
			if($order_by_clause!='' && !stristr($order_by_clause, 'clm.id')){

				$order_by_clause .= ', '.$fields_mapper['id'].' DESC ';
				$order_by_clause_outer .= ', '.$fields_mapper1['id']. ' DESC ';
			}


		}

		if(!$record_count && $order_by_clause==''){

			$order_by_clause=" ORDER BY username DESC, id DESC ";

			if(!preg_match("/\s+as\s+username/",$select_string_subquery)){
				$select_string_subquery .= ', '.$fields_mapper['username'].' as username ';
				$select_string .= ', '.$fields_mapper1['username'].' as username ';
			}
			if(!preg_match("/,?\s+u\.id/",$select_string_subquery)){
				$select_string_subquery .= ', '.$fields_mapper['id'].' as id';
				$select_string .= ', '.$fields_mapper1['id'].' as id';
			}

			if($order_by_clause_outer == '')
				$order_by_clause_outer=" ORDER BY username DESC, id DESC ";

		}

		$limit_clause='';

		if(array_key_exists('page', $options) && filter_var($options['page'],FILTER_VALIDATE_INT) && $options['page']>0 && array_key_exists('recs_per_page', $options) && filter_var($options['recs_per_page'],FILTER_VALIDATE_INT) && $options['recs_per_page']>0){

			$limit_clause="LIMIT ".( ($options['page']-1) * $options['recs_per_page'] ).", $options[recs_per_page] ";

		}

		$where_clause_string = '';
		if(!empty($where_clause))
			$where_clause_string = ' WHERE '.implode(' AND ', $where_clause);

		$role_join = '';
		if(preg_match("/(r1|ur1)\./","$select_string_subquery $where_clause_string $group_by_clause $having_clause_for_group_by $order_by_clause"))
			$role_join .= " JOIN ".CONST_TBL_PREFIX."user_roles ur1 ON u.id = ur1.user_id JOIN  ".CONST_TBL_PREFIX."roles r1 ON r1.role_id=ur1.role_id ";

		$sql="SELECT $select_string_subquery from `".CONST_TBL_PREFIX."users` as u $role_join   $where_clause_string $group_by_clause $order_by_clause $limit_clause";

		if(empty($record_count)){
			$sql="SELECT $select_string from ($sql) as T1 ";
			if(preg_match("/(r|ur)\./",$select_string))
				$sql .= " JOIN ".CONST_TBL_PREFIX."user_roles ur ON T1.id = ur.user_id JOIN  ".CONST_TBL_PREFIX."roles r ON r.role_id=ur.role_id";

			$sql .= $order_by_clause_outer;

		}
		/*echo $sql;
		print_r($str_params_to_bind);
		print_r($int_params_to_bind);
		*/$error_details_to_log = [];
		$error_details_to_log['at'] = date('Y-m-d H:i:s');
		$error_details_to_log['function'] = __METHOD__;
		$error_details_to_log['sql'] = $sql;
		$error_details_to_log['str_params_to_bind'] = $str_params_to_bind;
		$error_details_to_log['int_params_to_bind'] = $int_params_to_bind;
		try{
			PDOConn::switchDB('mysql');
			$pdo_stmt_obj = PDOConn::query($sql, $str_params_to_bind, $int_params_to_bind);
			// $pdo_stmt_obj->debugDumpParams()

			if(array_key_exists('resourceonly', $options) && $options['resourceonly'])
				return $pdo_stmt_obj;

			$idx = -1;
			$user_id = '';
			$data = [];

			while($row=$pdo_stmt_obj->fetch(\PDO::FETCH_ASSOC)){
				if(!$record_count){
					if($user_id!=$row['id']){
						++$idx;
						$data[$idx]=array_diff_key($row,['role'=>'', 'role_id'=>'']);

						if(array_key_exists('role', $row) || array_key_exists('role_id', $row)){
							$data[$idx]['assigned_roles'] = [];
							$data[$idx]['role_names'] = [];
						}

						$user_id=$row['id'];
					}

					if(array_key_exists('assigned_roles', $data[$idx])){
						$data[$idx]['assigned_roles'][] = ['role'=>$row['role'],'role_id'=>$row['role_id']];
						$data[$idx]['role_names'][] = $row['role'];
					}

				}else{
					$data[] = $row;
				}
			}
			return $data;

		}catch(Exception $e){

			if(!is_a($e, '\PDOStatement'))
				ErrorHandler::logError($error_details_to_log,$e);
			else
				ErrorHandler::logError($error_details_to_log);
			return false;

		}

	}




	function getUsersMenuList(){
		$admin_menus=new AdminMenu();
		return $admin_menus->getUsersMenuList($this->loggedinmember['id'],$options);
	}

	function updateSessionRecord($data=array(), $filters=array()){
		$this->last_mysql_error_code = $this->last_sqlstate_code='';
		$where = array();
		foreach($filters as $field=>$value){
			$where[] = ' AND `'.$field.'` = \''.$value.'\'';
		}

		$fields = array();
		foreach($data as $field=>$value){
			//beware - this might also update the sessiondata field which might affect the users session
			$fields[] = '`'.$field.'` = \''.$value.'\'';
		}
		if(count($fields)==0)
			return false;

		$sql = 'UPDATE '.CONST_TBL_PREFIX.'sessions SET '.implode(',', $fields). ' WHERE 1 '.implode('', $where);
		//echo $sql; exit;

		$error_details_to_log = [];
		$error_details_to_log['at'] = date('Y-m-d H:i:s');
		$error_details_to_log['method'] = __METHOD__;
		$error_details_to_log['sql'] = $sql;
		$error_details_to_log['data'] = $data;
		$error_details_to_log['filters'] = $filters;

		try{

			$rs = $this->db_conn->exec($sql);//echo mysql_error();echo mysql_affected_rows();exit;
			return !!$rs;

		}catch(Exception $e){ // w1
			$error_details_to_log['exception_msg'] = $e->getMessage();
			$error_details_to_log['mysql_error'] = $this->db_conn->errorInfo();
			$error_details_to_log['affected rows'] = 'boolean false';
			\eBizIndia\logErrorInFile(time(),$_SERVER['REQUEST_URI'], json_encode($error_details_to_log));
			$this->last_mysql_error_code = $error_details_to_log['mysql_error'][1];
			$this->last_mysqlstate_code = $error_details_to_log['mysql_error'][0];
			return false;

		}

	}

	
	function login($username,$password,$timestmp,$remember=0,$loginlocation='', $access_restricted_to_roles = []){
		$response=false;
		$data['login_datetime']=date('Y-m-d H:i:s', $timestmp);
		$data['ip']=$_SERVER['REMOTE_ADDR'];
		$data['username']=$username;
		$data['user_id']='';
		$data['login_as'] = null;

		$options=[];
		$options['filters']=[];
		$options['filters'][]=array('field'=>'username','type'=>'EQUAL','value'=>$username);
		$options['fieldstofetch'] = array('id', 'password', 'profile_type', 'status', 'role');
		$userdetails=$this->getList($options);

		if($userdetails === false){
			$data['errorcode']=1; // DB Error
			$data['result']=0;
			$data['reason']='Server error';
			$response=false;

		}else{
			if(empty($userdetails)){
				$data['errorcode']=2; // Username not found
				$data['result']=0;
				$data['reason']='Invalid email ID or password';
				$response=false;

			}else{
				$userdetails = $userdetails[0];

				if(!empty($access_restricted_to_roles) && !in_array($userdetails['assigned_roles'][0]['role'], $access_restricted_to_roles)){
					$data['errorcode']=8; // 
					$data['result']=0;
					$data['reason']='Access is temporarily forbidden';
					$response=false;
				}else{
					$data['login_as'] = $userdetails['profile_type'];
					$data['user_id']=$userdetails['id']; // in order to track the user account the auto Id needs to be stored with the login stats

					if(!password_verify($password, $userdetails['password'])){ // password did not match
						$data['errorcode']=3; // Invalid password
						$data['result']=0;
						$data['reason']='Invalid email ID or password';
						$response=false;

					}elseif($userdetails['status']=='0'){
						$data['errorcode']=4; // Account suspended
						$data['result']=0;
						$data['reason']='Your account has been put on hold for administrative reasons';

						$response=false;

					}else{
						$profile_details = $this->getProfileForUser($userdetails['id'], $userdetails['profile_type']);

						if($profile_details[0]['active']!='y'){
							// connected profile apparently deactivated
							$data['errorcode']=7; // Account suspended  
							$data['result']=0;
							$data['reason']='Your account has been put on hold for administrative reasons';

							$response=false;	

						}else{
							// if($userdetails['pswdResetRequestedOn']!=''){
							// 	//make the 'password reset requested on' field null
							// 	$this->saveUserDetails(array('pswdResetRequestedOn'=>''),$userdetails['id']);
							// }


							$pswd_for_cookie = $userdetails['password'];
							unset($userdetails['password']); // make sure the password related fields are not stored in session
							$userdetails['profile_details'] = $profile_details[0];
							$this->loggedinmember = $userdetails;
							$this->loggedinmember['loggedin']=1;

							$this->loginlocation=$loginlocation;
							$this->menulist = $this->getUsersMenuList();

							$sql="UPDATE " . CONST_TBL_PREFIX . "users set `lastLogin`=:login_datetime, `lastLoginIP`=:ip where `id`=:id";
							$str_data = [
								':ip' => $data['ip'],
								':login_datetime' => $data['login_datetime'],
							];
							$int_data = [
								':id' => $this->loggedinmember['id']
							];
							try{
								$stmt_obj = PDOConn::query($sql, $str_data, $int_data);
							}catch(\Exception $e){
								if(is_a($e, '\PDOStatement'))
									if(class_exists('ErrorHandler'))
										ErrorHandler::logError([],$e);
							}
							if($remember==1){
								setcookie('loggedin_user',base64_encode($this->loggedinmember['username'].$pswd_for_cookie),time()+(30*24*60*60),CONST_APP_PATH_FROM_ROOT.'/',$_SERVER['HTTP_HOST'],false,true);
								setcookie('is_remember_me',1,time()+(30*24*60*60),CONST_APP_PATH_FROM_ROOT.'/',$_SERVER['HTTP_HOST'],false,true);
							}else{
								setcookie('is_remember_me',false,time()-31536000,CONST_APP_PATH_FROM_ROOT.'/',$_SERVER['HTTP_HOST'],false,true);
							}

							$response=true;
						}

						

					}

				}


			}
		}

		return array($response,$data);

	}


	function getProfileForUser($user_id, $profile_type){
		if($profile_type === 'member')
			$obj = new Member();
		// else
			// create object of the employee class
		return $obj->getProfile($user_id);
	}

	function loginWithCookie($loginlocation='', $access_restricted_to_roles = []){
		// return false; // a temporary override

		$response=false;
		$tm=time();
		if(trim($_COOKIE['loggedin_user'])=='')
			return false;
		$data1=base64_decode(trim($_COOKIE['loggedin_user']));
		$tmp=explode('$',$data1);
		$username = $tmp[0];
		$enc_pswd = strstr($data1, '$');

		$error_details_to_log = [];
		$error_details_to_log['at'] = date('Y-m-d H:i:s');
		$error_details_to_log['method'] = __METHOD__;
		$error_details_to_log['sql'] = $sql;
		$error_details_to_log['loginlocation'] = $loginlocation;
		$error_details_to_log['data1'] = $data1;
		$error_details_to_log['username'] = $username;
		$error_details_to_log['enc_pswd'] = $enc_pswd;

		$data=array();
		$data['login_datetime']=date('Y-m-d H:i:s',$tm);
		$data['ip'] = \eBizIndia\getRemoteIP();
		$data['username']=$username;

		$options=[];
		$options['filters']=[];
		$options['filters'][]=array('field'=>'username','type'=>'EQUAL','value'=>$username);
		$options['fieldstofetch'] = array('id', 'password', 'profile_type', 'status', 'role');
		$userdetails=$this->getList($options);

		if($userdetails === false){
			$data['errorcode']=1; // DB error
			$data['result']=0;
			$data['reason']='Server error';
			$response=false;
		}else{
			if(empty($userdetails)){
				$data['errorcode']=2; // Username not found
				$data['result']=0;
				$data['reason']='Invalid email ID';
				$response=false;
			} else {
				$userdetails = $userdetails[0];
				if(!empty($access_restricted_to_roles) && !in_array($userdetails['assigned_roles'][0]['role'], $access_restricted_to_roles)){
					$data['errorcode']=7; // 
					$data['result']=0;
					$data['reason']='Forbidden Role';
					$response=false;
				}else{

					$data['user_id'] = $userdetails['id']; // to track the user account the auto Id needs to be stored with the login stats
					$data['login_as'] = $userdetails['profile_type'];

					if($enc_pswd != $userdetails['password']){
						$data['errorcode']=3; // Invalid password
						$data['result']=0;
						$data['reason']='Invalid password';
						$response=false;
					}elseif($userdetails['status']==0){
						$data['errorcode']=4; // Account suspended
						$data['result']=0;
						$data['reason']='Your account has been suspended for administrative reasons.';
						$response=false;
					}else{
						
						$profile_details = $this->getProfileForUser($userdetails['id'], $userdetails['profile_type']);

						if(empty($profile_details)){
							$data['errorcode']=5; // DB Error
							$data['result']=0;
							$data['reason']='Server error';
							$response=false;	
						}elseif($profile_details[0]['active']!='y'){
							// connected profile apparently deactivated
							$data['errorcode']=6; // Account suspended  
							$data['result']=0;
							$data['reason']='Your account has been put on hold for administrative reasons';
							$response=false;	
						}else{
							$data['errorcode']=0; // Successful
							$data['result']=1;
							$data['reason']='';

							unset($userdetails['password']); // make sure the password related fields are not stored in session
							$userdetails['profile_details'] = $profile_details[0];
							$this->loggedinmember = $userdetails;
							$this->loggedinmember['loggedin']=1;
							$this->loginlocation=$loginlocation;
							$this->menulist = $this->getUsersMenuList();
							$response=true;
							$sql="UPDATE " . CONST_TBL_PREFIX . "users set `lastLogin`=:login_datetime, `lastLoginIP`=:ip where `id`=:id ";
							$str_data = [
								':ip' => $data['ip'],
								':login_datetime' => $data['login_datetime'],
							];
							$int_data = [
								':id' => $this->loggedinmember['id']
							];
							try{
								$stmt_obj = PDOConn::query($sql, $str_data, $int_data);
							}catch(\Exception $e){
								if(!is_a($e, '\PDOStatement'))
									if(class_exists('ErrorHandler'))
										ErrorHandler::logError([],$e);
							}

						}
					}
				}

			}
		}

		if($response===false){
			$error_details_to_log['result_data'] = $data;
			\eBizIndia\ErrorHandler::logError($error_details_to_log);
			if(isset($_COOKIE['loggedin_user']))
				setcookie('loggedin_user',$_COOKIE['loggedin_user'],time()-42000,CONST_APP_PATH_FROM_ROOT.'/',$_SERVER['HTTP_HOST'],false,true); // deleting cookie if exists
		}

		return array($response,$data);

	}

	function canAccessThisProgram($scriptname){
		for($i=0;$i<count($this->menulist);$i++){
			for($k=0;$k<count($this->menulist[$i]['menus']); $k++){
				if($this->menulist[$i]['menus'][$k]['showMenuInDisplay']!=1)
					continue;
				$temp=explode(',',$this->menulist[$i]['menus'][$k]['menuurl']);
				if(array_search($scriptname,$temp)!==false || $scriptname=='index.php')
					return $this->menulist[$i]['menus'][$k];
			}

		}

		return false; // actual statement
		// return true; // by pass for development
	}

	function logMenuAccess($log_data){
		if(count($log_data) == 0)
			return false;
		$sql = 'INSERT INTO '. CONST_TBL_PREFIX.'menu_access_log SET ';
		$idx=0;
		$temp = $str_data = [];
		foreach($log_data as $fld=>$val){
			$k = ":fld_{$idx}_";
			if($val===''){
				$temp[]="`$fld`=NULL";
			}else{
				$temp[]="`$fld`=$k";
				$str_data[$k] = $val;

			}
			$idx++;

		}
		$sql.=implode(',',$temp);


		$error_details_to_log = [];
		$error_details_to_log['at'] = date('Y-m-d H:i:s');
		$error_details_to_log['method'] = __METHOD__;
		$error_details_to_log['sql'] = $sql;
		$error_details_to_log['log_data'] = $log_data;

		try{
			$stmt_obj = PDOConn::query($sql, $str_data);
			return true;
		}catch(Exception $e){
			if(!is_a($e, '\PDOStatement'))
				ErrorHandler::logError($error_details_to_log,$e);
			else
				ErrorHandler::logError($error_details_to_log);
			return false;
		}
	}



	function refreshLoggedInUserData($options=array()){
		$opt=[];
		$opt['filters']=[];
		$opt['filters'][]=array('field'=>'id','type'=>'EQUAL','value'=>$this->loggedinmember['id']);
		$res=$this->getList($opt);
		if($res!=false){
			$profile_details = $this->getProfileForUser($this->loggedinmember['id'], $this->loggedinmember['profile_type']);
			if($profile_details!=false){
				$this->loggedinmember = array_merge($res[0],array('loggedin'=>1));
				$this->loggedinmember['profile_details'] = $profile_details[0];
				
				return $this->getLoggedinUserData();
			}
		}
		return false;
	}

	function getLoggedinUserData(){
		return array($this->loggedinmember,$this->menulist,$this->loginlocation);
	}


	function saveLoginData($data){

		$this->last_mysql_error_code = $this->last_sqlstate_code='';

		$query="INSERT INTO " .CONST_TBL_PREFIX."user_login_stats set ";
		$temp = $str_data = array();

		foreach($data as $key=>$val){
			$k = ":$key";
			if($val===''){
				$temp[]="`$key`=NULL";
			}else{
				$temp[]="`$key`=$k";
				$str_data[$k] = $val;

			}

		}
		$query.=implode(',',$temp);

		$error_details_to_log = [];
		$error_details_to_log['at'] = date('Y-m-d H:i:s');
		$error_details_to_log['method'] = __METHOD__;
		$error_details_to_log['sql'] = $query;
		$error_details_to_log['data'] = $data;

		try{
			$stmt_obj = PDOConn::query($query, $str_data);
			return true;
		}catch(Exception $e){
			if(is_a($e, '\PDOStatement'))
				if(class_exists('ErrorHandler'))
					ErrorHandler::logError([],$e);
			return false;
		}
	}

	function usernameExists($username, $user_ids_to_ignore=[], $ignoreactivationstatus=true){

		$str_data = [
			':username' => $username,
		];
		$int_data = $place_holders = [];
		$sql="SELECT * from " . CONST_TBL_PREFIX . "users where username=:username";

		if(!empty($user_ids_to_ignore)){
			foreach ($user_ids_to_ignore as $key => $id) {
				$key = ":id_{$key}_";
				$place_holders[] = $key;
				$int_data[$key] = $id;
			}
			$sql .= ' and `id` not in('.implode(',',$place_holders).') ';
		}

		if($ignoreactivationstatus==false){
			$sql.=" and status=1";
		}
		$error_details_to_log = [];
		$error_details_to_log['at'] = date('Y-m-d H:i:s');
		$error_details_to_log['method'] = __METHOD__;
		$error_details_to_log['sql'] = $sql;
		$error_details_to_log['email'] = $email;
		$error_details_to_log['user_ids_to_ignore'] = $user_ids_to_ignore;
		$error_details_to_log['ignoreactivationstatus'] = $ignoreactivationstatus;

		try{
			$stmt_obj = PDOConn::query($sql, $str_data, $int_data);
			$row = $stmt_obj->fetch(\PDO::FETCH_ASSOC);
			if(empty($row))
				return [];
			$stmt_obj->closeCursor();
			return $row;
		}catch(Exception $e){
			if(is_a($e, '\PDOStatement'))
				if(class_exists('ErrorHandler'))
					ErrorHandler::logError([],$e);
			return false;
		}

	}

	function verifyPassword(string $password): bool{
		$error_details_to_log = [];
		$error_details_to_log['at'] = date('Y-m-d H:i:s');
		$error_details_to_log['method'] = __METHOD__;
		
		if(empty($password)){
			$error_details_to_log['password_to_verify'] = 'Not available';
			ErrorHandler::logError($error_details_to_log);
			return false;
		}

		if(empty($this->loggedinmember['id'])){
			$error_details_to_log['logged_in_user_id'] = '';
			ErrorHandler::logError($error_details_to_log);
			return false;
		}

		$error_details_to_log['logged_in_user_id'] = $this->loggedinmember['id'];

		$options=[];
		$options['filters']=[];
		$options['filters'][]=array('field'=>'id','type'=>'EQUAL','value'=>$this->loggedinmember['id']);
		$options['fieldstofetch'] = array('id', 'password', 'status');
		$user_details = $this->getList($options);
		if(empty($user_details)){
			$error_details_to_log['query_options'] = $options;
			ErrorHandler::logError($error_details_to_log);
			return false;
		}else{
			if(!password_verify($password, $user_details[0]['password'])){ // password did not match
				return false;
			}
			return true;
		}

	}
}