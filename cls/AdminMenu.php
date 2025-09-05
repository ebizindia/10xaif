<?php
namespace eBizIndia;
use \Exception;
class AdminMenu{

	private $usertypes; 
	private $last_mysql_error_code;
	private $last_sqlstate_code;

	function __Construct(){
		$this->usertypes=[];//explode(',',CONST_USER_TYPES);
		$this->last_mysql_error_code = $this->last_sqlstate_code='';
		
	}

	public function __get($name){

		if(in_array($name, ['last_mysql_error_code', 'last_sqlstate_code']))
			return $this->{$name};

	}
	
	function grantRevokePrivilegesOfAUser($for_user,$action,$menu_perm_ids){
		$this->last_mysql_error_code = $this->last_sqlstate_code='';
		$for_user=(int)$for_user;
		$action=(int)$action;
		if($action!=1)
			$action=0;

		if($for_user<=0){
			return 2;

		}	

		if(!is_array($menu_perm_ids) || count($menu_perm_ids)==0)

		{

			return 3;

		}

		$error_details_to_log = [];
		$error_details_to_log['at'] = date('Y-m-d H:i:s');
		$error_details_to_log['method'] = __METHOD__;
		$error_details_to_log['for_user'] = $for_user;
		$error_details_to_log['action'] = $action;
		$error_details_to_log['menu_perm_ids'] = $menu_perm_ids;
		
		try{	

			$sql = "SELECT menu_perm_id from `".CONST_TBL_PREFIX."user_based_menu_perms` WHERE user_id=$for_user and menu_perm_id in(".implode(',',$menu_perm_ids).") and grant_revoke_status!=$action";

			$error_details_to_log['sql1'] = $sql;

			$res = $this->db_conn->query($sql);
			$data = [];
			// if(!$res)
			// 	return 1;

			while($row = $res->fetch(\PDO::FETCH_ASSOC)){
				$data[] = $row['menu_perm_id'];
			}
			

			if(count($data)>0){

				$sql="delete from `".CONST_TBL_PREFIX."user_based_menu_perms` WHERE user_id=$for_user and menu_perm_id in(".implode(',',$data).")";

				$error_details_to_log['sql2'] = $sql;

				$affected_rows=$this->db_conn->exec($sql);

				if(!$affected_rows){
					
					return 1;

				}
			}

			$menu_perm_id_to_insert = array_diff($menu_perm_ids,$data);
			if(count($menu_perm_id_to_insert)>0){
				$sql = "INSERT IGNORE INTO  `".CONST_TBL_PREFIX."user_based_menu_perms`(user_id, menu_perm_id, grant_revoke_status) values";
				$values = [];
				foreach($menu_perm_id_to_insert as $perm_id){	
					
					$values[] = "($for_user,$perm_id,$action)";

				}

				$sql .= implode(',',$values);

				$error_details_to_log['sql3'] = $sql;

				$res=$this->db_conn->exec($sql);

/*				if(!$res){
					
					return 1;

				}*/

			}

		}catch(Exception $e){

			$error_details_to_log['exception_msg'] = $e->getMessage();
			$error_details_to_log['mysql_error'][0] = $this->db_conn->errorInfo();
			if(is_object($res))
				$error_details_to_log['mysql_error'][1] = $res->errorInfo();
			$error_details_to_log['result'] = 'boolean false';
			\eBizIndia\logErrorInFile(time(),$_SERVER['REQUEST_URI'], json_encode($error_details_to_log));

			if(!empty($error_details_to_log['mysql_error'][1])  && $error_details_to_log['mysql_error'][1][1]!=''){
				$this->last_mysql_error_code = $error_details_to_log['mysql_error'][1][1];
				$this->last_mysqlstate_code = $error_details_to_log['mysql_error'][1][0];
			}else{

				$this->last_mysql_error_code = $error_details_to_log['mysql_error'][0][1];
				$this->last_mysqlstate_code = $error_details_to_log['mysql_error'][0][0];
			}

			return 1;	
		}
		return 0;	

	}



	function grantRevokePrivilegesOfARole($for_role,$action,$menu_perm_ids){
		$this->last_mysql_error_code = $this->last_sqlstate_code='';
		$for_role=(int)$for_role;
		$action=(int)$action;
		if($action!=1)
			$action=0;

		if($for_role<=0){
			return 2;

		}	

		if(!is_array($menu_perm_ids) || count($menu_perm_ids)==0)

		{

			return 3;

		}

		
		$error_details_to_log = [];
		$error_details_to_log['at'] = date('Y-m-d H:i:s');
		$error_details_to_log['method'] = __METHOD__;
		$error_details_to_log['for_role'] = $for_role;
		$error_details_to_log['action'] = $action;
		$error_details_to_log['menu_perm_ids'] = $menu_perm_ids;

		try{	
			
			if($action == 1){

				if(count($menu_perm_ids)>0){
					$sql = "INSERT IGNORE INTO  `".CONST_TBL_PREFIX."role_based_menu_perms`(role_id, menu_perm_id) values";
					$values = [];
					foreach($menu_perm_ids as $perm_id){	
						
						$values[] = "($for_role,$perm_id)";

					}

					$sql .= implode(',',$values);

					$error_details_to_log['sql'] = $sql;

					$res=$this->db_conn->exec($sql);

					if(!$res){
						
						return 1;

					}

				}

			}else{

				$sql="delete from `".CONST_TBL_PREFIX."role_based_menu_perms` WHERE role_id=$for_role and menu_perm_id in(".implode(',',$menu_perm_ids).")";

				$error_details_to_log['sql'] = $sql;
				$affected_rows=$this->db_conn->exec($sql);

				if(!$affected_rows){
					
					return 1;

				}


			}

			
		}catch(Exception $e){
			$error_details_to_log['exception_msg'] = $e->getMessage();
			$error_details_to_log['mysql_error'] = $this->db_conn->errorInfo();
			$error_details_to_log['result'] = 'boolean false';
			\eBizIndia\logErrorInFile(time(),$_SERVER['REQUEST_URI'], json_encode($error_details_to_log));
			$this->last_mysql_error_code = $error_details_to_log['mysql_error'][1];
			$this->last_mysqlstate_code = $error_details_to_log['mysql_error'][0];
			
			return 1;	
		}

		return 0;	

	}



	function grantRevokeUsersPrivileges($for_menu,$action,$user_ids,$all_users_having_default_access_to_this_menu){
		$this->last_mysql_error_code = $this->last_sqlstate_code='';
		$for_menu=(int)$for_menu;
		$action=(int)$action;
		if($action!=1)
			$action=0;

		if($for_menu<=0){
			return 2;

		}		

		if(!is_array($user_ids) || count($user_ids)==0)

		{

			return 3;

		}

		$error_details_to_log = [];
		$error_details_to_log['at'] = date('Y-m-d H:i:s');
		$error_details_to_log['method'] = __METHOD__;
		$error_details_to_log['for_menu'] = $for_menu;
		$error_details_to_log['action'] = $action;
		$error_details_to_log['user_ids'] = $user_ids;
		$error_details_to_log['all_users_having_default_access_to_this_menu'] = $all_users_having_default_access_to_this_menu;
		
		if(!is_array($all_users_having_default_access_to_this_menu)){
			
			return 4;
		}else{
			$users_having_default_access=array_intersect($user_ids,$all_users_having_default_access_to_this_menu);
			$users_without_default_access=array_diff($user_ids,$users_having_default_access);

			
			if($action==1){
				$add_exception_with_curr_action_for=$users_without_default_access;
				$remove_exception_for=$users_having_default_access;
			}else{
				$add_exception_with_curr_action_for=$users_having_default_access;
				$remove_exception_for=$users_without_default_access;

			}

			if(count($remove_exception_for)>0){
				$sql="delete from `".CONST_TBL_PREFIX."menu_assignment_exceptions` WHERE `userId` in(".implode(',',$remove_exception_for).") and `menuId`=$for_menu";

				$error_details_to_log['sql1'] = $sql;

				try{
					$affected_rows=$this->db_conn->exec($sql);

					if(!$affected_rows){
						
						return 1;

					}
				}catch(Exception $e){
					$error_details_to_log['exception_msg'] = $e->getMessage();
					$error_details_to_log['mysql_error'] = $this->db_conn->errorInfo();
					$error_details_to_log['result'] = 'boolean false';
					\eBizIndia\logErrorInFile(time(),$_SERVER['REQUEST_URI'], json_encode($error_details_to_log));
					$this->last_mysql_error_code = $error_details_to_log['mysql_error'][1];
					$this->last_mysqlstate_code = $error_details_to_log['mysql_error'][0];
					
					return 1;	
				}
			}

			$cnt=count($add_exception_with_curr_action_for);
			if($cnt>0){
				
				$sql="INSERT into `".CONST_TBL_PREFIX."menu_assignment_exceptions`(`userId`,`menuId`,`status`) values";
				$values=array();
				for($i=0; $i<$cnt; $i++){
					$values[]="({$add_exception_with_curr_action_for[$i]},$for_menu,'$action')";
				}
				if(count($values)>0){
					$sql.=implode(',',$values);

					$sql.=" ON DUPLICATE KEY UPDATE `status`=VALUES(`status`)";

					$error_details_to_log['sql2'] = $sql;

					try{
						$res=$this->db_conn->exec($sql);

						// if($res===false){
							
						// 	return 5;

						// }
					}catch(Exception $e){
						$error_details_to_log['exception_msg'] = $e->getMessage();
						$error_details_to_log['mysql_error'] = $this->db_conn->errorInfo();
						$error_details_to_log['result'] = 'boolean false';
						\eBizIndia\logErrorInFile(time(),$_SERVER['REQUEST_URI'], json_encode($error_details_to_log));
						$this->last_mysql_error_code = $error_details_to_log['mysql_error'][1];
						$this->last_mysqlstate_code = $error_details_to_log['mysql_error'][0];
						
						return 5;	
					}


				}

			}
			//$this->db_conn->commit();


			

		}	
		return 0;

	}


	function getUsersHavingAccessByDefaultOrByUsertype($menuid){
		$this->last_mysql_error_code = $this->last_sqlstate_code='';
		$menuid = (int)$menuid;
		$sql="SELECT u.id FROM `".CONST_TBL_PREFIX."users` u,  `".CONST_TBL_PREFIX."menus` m  WHERE m.id=$menuid and m.availableByDefault='1'
UNION
 
 select u.id from `".CONST_TBL_PREFIX."users` u WHERE usertype='ADMIN' 

UNION

select u.id FROM `".CONST_TBL_PREFIX."users` u JOIN `".CONST_TBL_PREFIX."menu_assignments` ma ON u.usertype=ma.usertype and ma.menuId=$menuid ";

		$error_details_to_log = [];
		$error_details_to_log['at'] = date('Y-m-d H:i:s');
		$error_details_to_log['method'] = __METHOD__;
		$error_details_to_log['menuid'] = $menuid;
		$error_details_to_log['sql'] = $sql;
	
		try{
			$res=$this->db_conn->query($sql);

			
			$userids=array();
			while($row=$res->fetch(\PDO::FETCH_ASSOC)){
				$userids[]=$row['id'];
			}

			return $userids;
		}catch(Exception $e){ 
			
			$error_details_to_log['exception_msg'] = $e->getMessage();
			$error_details_to_log['mysql_error'][0] = $this->db_conn->errorInfo();
			if(is_object($res))
				$error_details_to_log['mysql_error'][1] = $res->errorInfo();
			$error_details_to_log['result'] = 'boolean false';
			\eBizIndia\logErrorInFile(time(),$_SERVER['REQUEST_URI'], json_encode($error_details_to_log));

			if(!empty($error_details_to_log['mysql_error'][1])  && $error_details_to_log['mysql_error'][1][1]!=''){
				$this->last_mysql_error_code = $error_details_to_log['mysql_error'][1][1];
				$this->last_mysqlstate_code = $error_details_to_log['mysql_error'][1][0];
			}else{

				$this->last_mysql_error_code = $error_details_to_log['mysql_error'][0][1];
				$this->last_mysqlstate_code = $error_details_to_log['mysql_error'][0][0];
			}
			return false;
		}
	}


	function getMenusToWhichTheUserHasAccesByDefaultOrByUsertype($user_id){
		$this->last_mysql_error_code = $this->last_sqlstate_code='';
		$user_id=(int)$user_id;
		$sql="select m.id as menuId from ".CONST_TBL_PREFIX."menus m,  ".CONST_TBL_PREFIX."users u WHERE u.id=$user_id and m.availableByDefault='1' 

			UNION

			select ma.menuId from `".CONST_TBL_PREFIX."menu_assignments` ma JOIN (select usertype from ".CONST_TBL_PREFIX."users where id=$user_id) as u ON u.usertype=ma.usertype

		";

		$error_details_to_log = [];
		$error_details_to_log['at'] = date('Y-m-d H:i:s');
		$error_details_to_log['method'] = __METHOD__;
		$error_details_to_log['user_id'] = $user_id;
		$error_details_to_log['sql'] = $sql;
	

		try{
			$res=$this->db_conn->query($sql);

			// if(!is_object($res)){
				
			// 	return false;
			// }

			$menuids=array();
			while($row=$res->fetch(\PDO::FETCH_ASSOC)){
				$menuids[]=$row['menuId'];
			}

			return $menuids;
		}catch(Exception $e){ 
			
			$error_details_to_log['exception_msg'] = $e->getMessage();
			$error_details_to_log['mysql_error'][0] = $this->db_conn->errorInfo();
			if(is_object($res))
				$error_details_to_log['mysql_error'][1] = $res->errorInfo();
			$error_details_to_log['result'] = 'boolean false';
			\eBizIndia\logErrorInFile(time(),$_SERVER['REQUEST_URI'], json_encode($error_details_to_log));

			if(!empty($error_details_to_log['mysql_error'][1])  && $error_details_to_log['mysql_error'][1][1]!=''){
				$this->last_mysql_error_code = $error_details_to_log['mysql_error'][1][1];
				$this->last_mysqlstate_code = $error_details_to_log['mysql_error'][1][0];
			}else{

				$this->last_mysql_error_code = $error_details_to_log['mysql_error'][0][1];
				$this->last_mysqlstate_code = $error_details_to_log['mysql_error'][0][0];
			}
			return false;
		}

	}



	function savePrivilegeExceptions($menu_id, $perm_type, $user_ids)

	{
		$this->last_mysql_error_code = $this->last_sqlstate_code='';

		if(!is_array($user_ids) || count($user_ids)==0)

		{

			// die('1');

			return false;

		}

		$opp_perm_type=1-$perm_type;

		$error_details_to_log = [];
		$error_details_to_log['at'] = date('Y-m-d H:i:s');
		$error_details_to_log['method'] = __METHOD__;
		$error_details_to_log['menu_id'] = $menu_id;
		$error_details_to_log['perm_type'] = $perm_type;
		$error_details_to_log['user_ids'] = $user_ids;
		

		$sql="DELETE FROM `" . CONST_TBL_PREFIX ."menu_assignment_exceptions` WHERE menuId='$menu_id' and `status`='$opp_perm_type' and userId in(".implode(',',$user_ids).")";

		$error_details_to_log['sql1'] = $sql;		

		try{
			$this->db_conn->exec($sql);
			if($this->db_conn->rowCount()==-1){

				// die($sql.' |2');

				return false;

			}	
		}catch(Exception $e){
			$error_details_to_log['exception_msg'] = $e->getMessage();
			$error_details_to_log['mysql_error'] = $this->db_conn->errorInfo();
			$error_details_to_log['result'] = 'boolean false';
			\eBizIndia\logErrorInFile(time(),$_SERVER['REQUEST_URI'], json_encode($error_details_to_log));
			$this->last_mysql_error_code = $error_details_to_log['mysql_error'][1];
			$this->last_mysqlstate_code = $error_details_to_log['mysql_error'][0];
			
			return false;
		}

		$clause=($perm_type==1)?'NOT':'';

		$adminclause=($perm_type==1)?" AND usertype!='ADMIN' ":'';

		

		$sql="SELECT id from " . CONST_TBL_PREFIX ."users 

			  WHERE usertype $clause IN(

											SELECT usertype FROM `".CONST_TBL_PREFIX."menu_assignments` WHERE menuId='$menu_id'

										   ) 

				$adminclause 

				AND id in(".implode(',',$user_ids).")";

		
		$error_details_to_log['sql2'] = $sql;		

		try{
			$res=$this->db_conn->query($sql);

			// if(!$res){

			// 	// die($sql.' |3');

			// 	return false;

			// }else{

				$user_ids=array();
				
				//while($row=mysql_fetch_assoc($res)){
				while($row=$res->fetch(\PDO::FETCH_ASSOC)){
					
					$user_ids[]=$row['id'];
					
				}
				
				
			// }

			

			if(count($user_ids)>0){

				$sql="INSERT IGNORE INTO `" . CONST_TBL_PREFIX ."menu_assignment_exceptions`(`menuId`, `userId`, `status`) VALUES ";

				$ids=array();

				foreach($user_ids as $id)

				{

					$ids[]="('".$menu_id."','".$id."','".$perm_type."')";

				}

				$sql.=implode(",",$ids);

				//print "<br>".$qs;

				$error_details_to_log['sql3'] = $sql;	

				$res=$this->db_conn->exec($sql);

				//if(mysql_affected_rows()==-1)
				// if($res===false)

				// {

				// 	// die($sql.'  |4');

				// 	return false;

				// }

			}

			return true;


		}catch(Exception $e){ 
			
			$error_details_to_log['exception_msg'] = $e->getMessage();
			$error_details_to_log['mysql_error'][0] = $this->db_conn->errorInfo();
			if(is_object($res))
				$error_details_to_log['mysql_error'][1] = $res->errorInfo();
			$error_details_to_log['result'] = 'boolean false';
			\eBizIndia\logErrorInFile(time(),$_SERVER['REQUEST_URI'], json_encode($error_details_to_log));

			if(!empty($error_details_to_log['mysql_error'][1])  && $error_details_to_log['mysql_error'][1][1]!=''){
				$this->last_mysql_error_code = $error_details_to_log['mysql_error'][1][1];
				$this->last_mysqlstate_code = $error_details_to_log['mysql_error'][1][0];
			}else{

				$this->last_mysql_error_code = $error_details_to_log['mysql_error'][0][1];
				$this->last_mysqlstate_code = $error_details_to_log['mysql_error'][0][0];
			}
			return false;
		}

	}

	

	

	function getPrivilegeExceptionsList($menu_id, $type)

	{
		// $type=0 users not having permission, 1= users having permission for this menu

		$this->last_mysql_error_code = $this->last_sqlstate_code='';
		

		if($type==0){

			$clause="NOT";

			$adminclause=" AND u.usertype!='ADMIN' ";

		}	

		$error_details_to_log = [];
		$error_details_to_log['at'] = date('Y-m-d H:i:s');
		$error_details_to_log['method'] = __METHOD__;
		$error_details_to_log['menu_id'] = $menu_id;
		$error_details_to_log['type'] = $type;
		

		$sql="SELECT u.id, u.title, concat(u.fname,' ',IFNULL(u.lname,'')) as userFullName, u.usertype  

			FROM " . CONST_TBL_PREFIX ."users u

					WHERE (u.id IN (

												SELECT userId

												FROM " . CONST_TBL_PREFIX ."menu_assignment_exceptions 

												WHERE menuId='$menu_id' AND `status`='$type'

											)

						OR u.usertype $clause IN  (

												SELECT usertype FROM ".CONST_TBL_PREFIX."menu_assignments WHERE menuId='$menu_id'

											)	

						) $adminclause	AND u.id not in( SELECT userId FROM " . CONST_TBL_PREFIX ."menu_assignment_exceptions  WHERE menuId='$menu_id' AND `status`='".(1-$type)."')

					ORDER BY userFullName";

		$error_details_to_log['sql'] = $sql;

		try{
			$res=$this->db_conn->query($sql);;

			// if(!$res){
				
			// 	return false;
			// }	

			$menuprivilegeexceptions=array();				
			
			//while($row=mysql_fetch_assoc($res)){
			while($row=$this->db_conn->fetchAll($res)){
				$menuprivilegeexceptions[]=$row;	
				
			}
			
			return $menuprivilegeexceptions;	

		}catch(Exception $e){ 
			
			$error_details_to_log['exception_msg'] = $e->getMessage();
			$error_details_to_log['mysql_error'][0] = $this->db_conn->errorInfo();
			if(is_object($res))
				$error_details_to_log['mysql_error'][1] = $res->errorInfo();
			$error_details_to_log['result'] = 'boolean false';
			\eBizIndia\logErrorInFile(time(),$_SERVER['REQUEST_URI'], json_encode($error_details_to_log));

			if(!empty($error_details_to_log['mysql_error'][1])  && $error_details_to_log['mysql_error'][1][1]!=''){
				$this->last_mysql_error_code = $error_details_to_log['mysql_error'][1][1];
				$this->last_mysqlstate_code = $error_details_to_log['mysql_error'][1][0];
			}else{

				$this->last_mysql_error_code = $error_details_to_log['mysql_error'][0][1];
				$this->last_mysqlstate_code = $error_details_to_log['mysql_error'][0][0];
			}
			return false;
		}

	}

	

	function getMenuListWithUserGroupPrivileges($roles,$menucategorystatus='', $menustatus='', $usergroupstatus=''){

		$this->last_mysql_error_code = $this->last_sqlstate_code='';

		$error_details_to_log = [];
		$error_details_to_log['at'] = date('Y-m-d H:i:s');
		$error_details_to_log['method'] = __METHOD__;
		$error_details_to_log['roles'] = $roles;
		$error_details_to_log['menucategorystatus'] = $menucategorystatus;
		$error_details_to_log['menustatus'] = $menustatus;
		$error_details_to_log['usergroupstatus'] = $usergroupstatus;

		$sql1="SELECT m.id as menuid, m.name as menuname, m.url as menuurl, m.categoryId as menucategoryid, m.viewOrder as menuorderingroup,m.status as menustatus ,mc.name as manucategoryname ,mc.menu_cat_icon as menu_cat_icon, mc.viewOrder as menucategoryorder, mc.status as menucatstatus,m.availableByDefault from " . CONST_TBL_PREFIX ."menus m, " . CONST_TBL_PREFIX ."menucategories mc WHERE m.categoryId=mc.id ";	

		if($menustatus!='')

			$sql1.=" AND m.status='$menustatus'";

		if($menucategorystatus!='')

			$sql1.=" AND mc.status='$menucategorystatus'";

		
		$sql="SELECT menu.menuid, menu.menuname, menu.menuurl, menu.manucategoryname, menu.menu_cat_icon, menu.menucategoryid, menu.menucategoryorder,menu.menucatstatus, menu.menustatus,menu.menuorderingroup,menu.availableByDefault, ma.usertype as usertype from ($sql1) as menu LEFT OUTER JOIN " . CONST_TBL_PREFIX ."menu_assignments as ma ON menu.menuid=ma.menuId ORDER BY menu.menucategoryorder, menu.manucategoryname, menu.menuorderingroup, menu.menuname";


		
		$error_details_to_log['sql'] = $sql;

		try{
			$res=$this->db_conn->query($sql);

			// if(!$res){
			// 	//mysql_error();
			// 	$this->db_conn->errorInfo();
			// 	return false;
			// }	

			$menuprivileges=array();

			$index=-1;

			$menuindex=-1;

			$menucatid='';

			$menuid='';

			$usertypes=array_fill_keys($this->usertypes,0);

			$temp=array();

			

			//while($row=mysql_fetch_assoc($res)){
			while($row=$this->db_conn->fetchAll($res)){

				if($menucatid!=$row['menucategoryid']){

					$index+=1;

					$menuindex=-1;

					$menuprivileges[$index]['menucategoryid']=$row['menucategoryid'];

					$menuprivileges[$index]['manucategoryname']=$row['manucategoryname'];
					
					$menuprivileges[$index]['menu_cat_icon']=$row['menu_cat_icon'];
					
					$menuprivileges[$index]['menucatstatus']=$row['menucatstatus'];

					$menuprivileges[$index]['menus']=array();

					$menucatid=$row['menucategoryid'];

				}

				if($menuid!=$row['menuid']){

					$menuindex+=1;

					$menuprivileges[$index]['menus'][$menuindex]['menuid']=$row['menuid'];

					$menuprivileges[$index]['menus'][$menuindex]['menuname']=$row['menuname'];

					$menuprivileges[$index]['menus'][$menuindex]['menuurl']=$row['menuurl'];
					
					$menuprivileges[$index]['menus'][$menuindex]['availableByDefault']=$row['availableByDefault'];

					$menuprivileges[$index]['menus'][$menuindex]['menustatus']=$row['menustatus'];
					
					$menuprivileges[$index]['menus'][$menuindex]['usertypes']=$usertypes;

					$menuprivileges[$index]['menus'][$menuindex]['usertypes']['ADMIN']=1;
					
					$menuid=$row['menuid'];

				}

				
				
				if(array_key_exists($row['usertype'],$usertypes)){

					$menuprivileges[$index]['menus'][$menuindex]['usertypes'][$row['usertype']]=1;

				}

							

				

			}

			return $menuprivileges;

		}catch(Exception $e){ 
			
			$error_details_to_log['exception_msg'] = $e->getMessage();
			$error_details_to_log['mysql_error'][0] = $this->db_conn->errorInfo();
			if(is_object($res))
				$error_details_to_log['mysql_error'][1] = $res->errorInfo();
			$error_details_to_log['result'] = 'boolean false';
			\eBizIndia\logErrorInFile(time(),$_SERVER['REQUEST_URI'], json_encode($error_details_to_log));

			if(!empty($error_details_to_log['mysql_error'][1])  && $error_details_to_log['mysql_error'][1][1]!=''){
				$this->last_mysql_error_code = $error_details_to_log['mysql_error'][1][1];
				$this->last_mysqlstate_code = $error_details_to_log['mysql_error'][1][0];
			}else{

				$this->last_mysql_error_code = $error_details_to_log['mysql_error'][0][1];
				$this->last_mysqlstate_code = $error_details_to_log['mysql_error'][0][0];
			}
			return false;
		}

	

	}

	

	

	function getMenuList($menucategorystatus='', $menustatus='', $options=array()){
		$this->last_mysql_error_code = $this->last_sqlstate_code='';

		$error_details_to_log = [];
		$error_details_to_log['at'] = date('Y-m-d H:i:s');
		$error_details_to_log['method'] = __METHOD__;
		$error_details_to_log['menucategorystatus'] = $menucategorystatus;
		$error_details_to_log['menustatus'] = $menustatus;
		$error_details_to_log['options'] = $options;


		$fields_mapper['*']="m.id as menuid, m.name as menuname, m.url as menuurl, m.categoryId as menucategoryid, m.menu_icon, mc.name as manucategoryname, mc.menu_cat_icon as menu_cat_icon, mc.viewOrder as menucategoryorder, mc.showInDisplay as showCatInDisp, m.viewOrder as menuorderingroup, m.showInDisplay as showMenuInDisplay, m.availableByDefault as availableByDefault, m.not_available_to_admin as not_available_to_admin, map.menu_perm_id as menu_perm_id, map.perm as perm, map.perm_name as perm_name, map.display_order as perm_display_order";
		$fields_mapper['menuid']='m.id';
		$fields_mapper['menuname']='m.name';
		$fields_mapper['menuurl']='m.url';
		$fields_mapper['menucategoryid']='m.categoryId';
		$fields_mapper['menu_icon']='m.menu_icon';
		$fields_mapper['manucategoryname']='mc.name';
		$fields_mapper['menu_cat_icon']='mc.menu_cat_icon';
		$fields_mapper['menucategoryorder']='mc.viewOrder';
		$fields_mapper['menuorderingroup']='m.viewOrder';
		$fields_mapper['menucategorystatus']='mc.status';
		$fields_mapper['menustatus']='m.status';
		$fields_mapper['showMenuInDisplay']='m.showInDisplay';
		$fields_mapper['menuslug']='m.slug';
		$fields_mapper['availableByDefault']='m.availableByDefault';
		$fields_mapper['not_available_to_admin']='m.not_available_to_admin';
		$fields_mapper['menu_perm_id']='map.menu_perm_id';
		$fields_mapper['perm']='map.perm';
		$fields_mapper['perm_name']='map.perm';
		$fields_mapper['perm_display_order']='map.display_order';


		$select_string=	$fields_mapper['*'];
		if(is_array($options['fields_to_fetch']) && ($fields_to_fetch_count=count($options['fields_to_fetch']))>0){
			$selected_fields=array('m.id as menuid','m.categoryId as menucategoryid');
			
			for($i=0; $i<$fields_to_fetch_count; $i++){
				if(array_key_exists($options['fields_to_fetch'][$i],$fields_mapper)){
					$selected_fields[]=$fields_mapper[$options['fields_to_fetch'][$i]].' as '.$options['fields_to_fetch'][$i];
				}
			}
			
			$selected_fields=array_unique($selected_fields);

			if(count($selected_fields)>0){
				$select_string=implode(', ',$selected_fields);
			}
		}


		$sql="SELECT $select_string from " . CONST_TBL_PREFIX ."menu_allowed_perms map RIGHT JOIN " . CONST_TBL_PREFIX ."menus m ON m.id=map.menu_id JOIN " . CONST_TBL_PREFIX ."menucategories mc ON m.categoryId=mc.id WHERE 1 ";	

		$str_data = [];
		if($menustatus!=''){
			$str_data[':menu_status'] = $menustatus;
			$sql.=" AND m.status=:menu_status";
		}

		if($menucategorystatus!=''){
			$str_data[':menu_cat_status'] = $menucategorystatus;
			$sql.=" AND mc.status=:menu_cat_status";
		}

		if($options['visible_menus_only'])
			$sql.=" AND m.showInDisplay='1'";
		
		$sql.=" ORDER BY mc.viewOrder, mc.id, m.viewOrder, map.display_order ASC";

		$error_details_to_log['sql'] = $sql;
		
		try{
			$stmt_obj = PDOConn::query($sql, $str_data);

			if($options['resource'])
				return $stmt_obj;	

			$menuprivileges=array();

			$index=-1;

			$menuindex=-1;

			$menucatid='';

			$menuid='';

			while($row=$stmt_obj->fetch(\PDO::FETCH_ASSOC)){

				if($menucatid!=$row['menucategoryid']){

					$index+=1;

					$menuindex=-1;

					$menuprivileges[$index] = array_intersect_key($row, ['menucategoryid'=>'', 'manucategoryname'=>'', 'menu_cat_icon'=>'', 'menucategoryorder'=>'', 'showCatInDisp'=>'']);

					$menuprivileges[$index]['menus']=array();

					$menucatid=$row['menucategoryid'];
				}

				if($menuid!=$row['menuid']){
					$menuindex+=1;
					$menuprivileges[$index]['menus'][$menuindex]=array_diff_key($row,array('menucategoryid'=>'','manucategoryname'=>'','menu_cat_icon'=>'', 'menucategoryorder'=>'', 'showCatInDisp'=>'', 'menu_perm_id'=>'', 'perm'=>'', 'perm_name'=>'', 'perm_display_order'=>''));
					
					$menuprivileges[$index]['menus'][$menuindex]['perms'] = [];

					$menuid=$row['menuid'];
				}

				if($row['menu_perm_id']!='')
					$menuprivileges[$index]['menus'][$menuindex]['perms'][] = array_intersect_key($row, ['menu_perm_id'=>'', 'perm'=>'', 'perm_name'=>'', 'perm_display_order'=>'']);	
			}

			return $menuprivileges;	

		}catch(Exception $e){ 
			if(!is_a($e, '\PDOStatement'))
				ErrorHandler::logError($error_details_to_log,$e);	
			else
				ErrorHandler::logError($error_details_to_log);	
			return false;
		}

	}

	

	

	function saveMenuPrivilegesForUserTypes($data){
		$this->last_mysql_error_code = $this->last_sqlstate_code='';
		if(!$this->deleteAllMenuPrivilegesForUserTypes())

			return false;

		$error_details_to_log = [];
		$error_details_to_log['at'] = date('Y-m-d H:i:s');
		$error_details_to_log['method'] = __METHOD__;
		$error_details_to_log['data'] = $data;

		$recstoinsert=count($data);
		if($recstoinsert>0){
			$sql="INSERT INTO `" . CONST_TBL_PREFIX ."menu_assignments`(`menuId`,`usertype`) values";

			$values=array();

			for($i=0;$i<$recstoinsert;$i++){
				$data[$i]['menuId']=(int)$data[$i]['menuId'];	
				$values[]="(".$data[$i]['menuId'].",".$this->db_conn->quote($data[$i]['usertype']).")";
			}

			$sql.=implode(',',$values);
			
			$error_details_to_log['sql'] = $sql;

			try{
				$res=$this->db_conn->exec($sql);

				
				if($this->db_conn->rowCount()<=0){

					return false;

				}
			}catch(Exception $e){ 
			
				$error_details_to_log['exception_msg'] = $e->getMessage();
				$error_details_to_log['mysql_error'] = $this->db_conn->errorInfo();
				$error_details_to_log['result'] = 'boolean false';
				\eBizIndia\logErrorInFile(time(),$_SERVER['REQUEST_URI'], json_encode($error_details_to_log));
				$this->last_mysql_error_code = $error_details_to_log['mysql_error'][1];
				$this->last_mysqlstate_code = $error_details_to_log['mysql_error'][0];
				return false;
			}
		}
		return true;


	}

	

	private function deleteAllMenuPrivilegesForUserTypes(){
		$this->last_mysql_error_code = $this->last_sqlstate_code='';
		$sql="Truncate table `" . CONST_TBL_PREFIX ."menu_assignments`";

		$error_details_to_log = [];
		$error_details_to_log['at'] = date('Y-m-d H:i:s');
		$error_details_to_log['method'] = __METHOD__;
		$error_details_to_log['sql'] = $sql;

		try{
			$res=$this->db_conn->exec($sql);

			if(!$res){

				return false;

			}	

			return true;	
		}catch(Exception $e){ 
			
			$error_details_to_log['exception_msg'] = $e->getMessage();
			$error_details_to_log['mysql_error'] = $this->db_conn->errorInfo();
			$error_details_to_log['result'] = 'boolean false';
			\eBizIndia\logErrorInFile(time(),$_SERVER['REQUEST_URI'], json_encode($error_details_to_log));
			$this->last_mysql_error_code = $error_details_to_log['mysql_error'][1];
			$this->last_mysqlstate_code = $error_details_to_log['mysql_error'][0];
			return false;
		}	

	

	}

	


	function getRoleWiseMenuAssignments($roleid, $options=[]){
		$this->last_mysql_error_code = $this->last_sqlstate_code='';

		$error_details_to_log = [];
		$error_details_to_log['at'] = date('Y-m-d H:i:s');
		$error_details_to_log['method'] = __METHOD__;
		$error_details_to_log['roleid'] = $roleid;
		$error_details_to_log['options'] = $options;
		

		if($roleid == 1){

			$sql= "SELECT m.id as menuid, m.categoryId as menucat_id, m.name, mc.name as menucat_name, mc.menu_cat_icon as menu_cat_icon, mc.status as menucat_status, mc.slug as menucat_slug, mc.status as menucat_status, mc.showInDisplay as show_in_display, mc.viewOrder as menucat_vieworder, m.availableByDefault, m.slug menuslug, m.url, m.page, m.description, m.menu_icon, m.status, m.targetWindow, m.viewOrder as menuvieworder, m.disableAccess, m.disablingMsg, m.showInDisplay as showMenuInDisplay, map.*  FROM ".CONST_TBL_PREFIX."menus m JOIN ".CONST_TBL_PREFIX."menucategories mc ON m.categoryId=mc.id LEFT JOIN ".CONST_TBL_PREFIX."menu_allowed_perms map ON m.id=map.menu_id WHERE m.not_available_to_admin='0' ORDER BY mc.viewOrder, m.viewOrder, map.perm ";

		}else{
			
			$sql= "SELECT m.id as menuid, m.categoryId as menucat_id, m.name, mc.name as menucat_name, mc.menu_cat_icon as menu_cat_icon, mc.status as menucat_status, mc.slug as menucat_slug, mc.status as menucat_status, mc.showInDisplay as show_in_display, mc.viewOrder as menucat_vieworder, m.availableByDefault, m.slug menuslug, m.url, m.page, m.description, m.menu_icon, m.status, m.targetWindow, m.viewOrder as menuvieworder, m.disableAccess, m.disablingMsg, m.showInDisplay as showMenuInDisplay, map.*  FROM ".CONST_TBL_PREFIX."menus m JOIN ".CONST_TBL_PREFIX."menucategories mc ON m.categoryId=mc.id LEFT JOIN ".CONST_TBL_PREFIX."menu_allowed_perms map ON m.id=map.menu_id LEFT JOIN  ".CONST_TBL_PREFIX."role_based_menu_perms rbmp ON map.menu_perm_id=rbmp.menu_perm_id and rbmp.role_id=:roleid WHERE (m.availableByDefault='1' OR  rbmp.menu_perm_id is not null) ORDER BY mc.viewOrder, m.viewOrder, map.perm ";
		}	

		$error_details_to_log['sql'] = $sql;

		try{
			
			$pdo_stmt = $this->db_conn->prepare($sql);
			$pdo_stmt->bindParam(':roleid',$roleid,\PDO::PARAM_INT);
			$pdo_stmt->execute();

			if($options['recource_only'])
				return $pdo_stmt;

			$i=-1;
			$catid = '';
			$menulist = [];
			while($row = $pdo_stmt->fetch(\PDO::FETCH_ASSOC)){
				if($catid!=$row['menucat_id']){

				  $i++;

				  $menulist[$i] = ['menucat_id'=>$row['menucat_id'], 'menucat_name'=>$row['menucat_name'], 'menu_cat_icon'=>$row['menu_cat_icon'], 'menucat_slug'=>$row['menucat_slug'], 'show_in_display'=>$row['show_in_display'], 'menus'=> [] ];

				  $catid=$row['menucat_id'];
				  $menu_id = '';
				  $k = -1;

				}	

				if($menu_id!=$row['menuid']){
					$k++;
					$menulist[$i]['menus'][$k]=['menuid'=>$row['menuid'],'description'=>$row['description'],'menuname'=>$row['name'], 'availableByDefault'=>$row['availableByDefault'], 'menuslug'=>$row['menuslug'],'menuurl'=>$row['url'], 'menupage'=>$row['page'], 'menu_icon'=>$row['menu_icon'],'target_window'=>$row['target_window'],'disableAccess'=>$row['disableAccess'],'disablingMsg'=>$row['disablingMsg'],'showMenuInDisplay'=>$row['showMenuInDisplay'], 'perms'=>[] ];

					$menu_id=$row['menuid'];
				}

				if($row['perm']!='')
					$menulist[$i]['menus'][$k]['perms'][] = $row['perm'];

			}				
			
			return $menulist;

		}catch(Exception $e){
			
			$error_details_to_log['exception_msg'] = $e->getMessage();
			$error_details_to_log['mysql_error'][0] = $this->db_conn->errorInfo();
			if(is_object($pdo_stmt))
				$error_details_to_log['mysql_error'][1] = $pdo_stmt->errorInfo();
			$error_details_to_log['result'] = 'boolean false';
			\eBizIndia\logErrorInFile(time(),$_SERVER['REQUEST_URI'], json_encode($error_details_to_log));

			if(!empty($error_details_to_log['mysql_error'][1])  && $error_details_to_log['mysql_error'][1][1]!=''){
				$this->last_mysql_error_code = $error_details_to_log['mysql_error'][1][1];
				$this->last_mysqlstate_code = $error_details_to_log['mysql_error'][1][0];
			}else{

				$this->last_mysql_error_code = $error_details_to_log['mysql_error'][0][1];
				$this->last_mysqlstate_code = $error_details_to_log['mysql_error'][0][0];
			}
			return false;

		}

	}


	function getUserRoles($userid='', $active_only = true, $recource_only = false, $role_for = ''){
		$this->last_mysql_error_code = $this->last_sqlstate_code='';
		
		$error_details_to_log = [];
		$error_details_to_log['at'] = date('Y-m-d H:i:s');
		$error_details_to_log['method'] = __METHOD__;
		$error_details_to_log['userid'] = $userid;
		$error_details_to_log['active_only'] = $active_only;
		$error_details_to_log['recource_only'] = $recource_only;
		$error_details_to_log['role_for'] = $role_for;

		$sql = 'SELECT distinct r.role_id, r.role_name, r.role_status, r.role_type, r.role_for from '.CONST_TBL_PREFIX.'roles r LEFT JOIN '.CONST_TBL_PREFIX.'user_roles ur ON r.role_id=ur.role_id WHERE 1 ';

		$whereclause = $int_data = [];

		$userid = (int)$userid;
		$role_for = (int)$role_for;

		if($userid>0){
			$int_data[':userid'] = $userid;
			$whereclause[] = ' AND ur.user_id=:userid';			
		}

		if($active_only)
			$whereclause[] = ' AND r.role_status = 1 ';

		if($role_for>0){
			$int_data[':role_for'] = $role_for;
			$whereclause[] = ' AND r.role_for =:role_for ';
		}

		$sql .= implode(' ',$whereclause);
		$sql .= " ORDER by r.role_name ";

		$error_details_to_log['sql'] = $sql;

		try{
			$pdo_stmt = PDOConn::query($sql, int_data:$int_data);
			
			if($recource_only)
				return $pdo_stmt;

			$data = [];

			while($row = $pdo_stmt->fetch(\PDO::FETCH_ASSOC)){
				$data[] = $row;
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
	
	function getUsersMenuList($userid, $options=array()){
		$this->last_mysql_error_code = $this->last_sqlstate_code='';
		$userid=(int)$userid;
		if($userid<=0)
			return [];

		$is_admin = false;
		$res = $this->getUserRoles($userid,true,true);
		if($res!==false){
			while($row = $res->fetch(\PDO::FETCH_ASSOC)){
				if(strtolower($row['role_name'])!='admin')
					$non_admin_roleids[] = $row['role_id'];
				else
					$is_admin = true;
			}
		}

		$error_details_to_log = [];
		$error_details_to_log['at'] = date('Y-m-d H:i:s');
		$error_details_to_log['method'] = __METHOD__;
		$error_details_to_log['userid'] = $userid;
		$error_details_to_log['options'] = $options;
		

		// If the mode selected is basic get the basic set of menus only as defined for the client or get the default system defined set if no client specific definition is found  
		$menuids_to_show=array();
		
		///////////////////////////////////////////////////////////////////

		$int_params_to_bind = [];

		if($is_admin){
			$sql = "SELECT m.id AS menuid, m.categoryId as menucat_id, m.name, mc.name AS menucat_name, mc.menu_cat_icon AS menu_cat_icon, mc.status as menucat_status, mc.slug as menucat_slug, mc.status as menucat_status, mc.showInDisplay as show_in_display, mc.viewOrder as menucat_vieworder, m.availableByDefault, m.slug, m.url, m.page, m.description, m.menu_icon, m.status, m.targetWindow, m.viewOrder AS menuvieworder, m.disableAccess, m.disablingMsg, m.showInDisplay AS showMenuInDisplay, 0 as included_in_basic_view, map . * FROM ".CONST_TBL_PREFIX."menus m JOIN ".CONST_TBL_PREFIX."menucategories mc ON m.categoryId = mc.id LEFT JOIN ".CONST_TBL_PREFIX."menu_allowed_perms map ON m.id = map.menu_id WHERE 1 AND m.status = '1' AND mc.status = '1' and m.not_available_to_admin='0' ORDER BY mc.viewOrder, m.viewOrder, map.perm";

		}else{
			$sql="SELECT m.id as menuid, m.categoryId as menucat_id, m.name, mc.name as menucat_name, mc.menu_cat_icon as menu_cat_icon, mc.status as menucat_status, mc.slug as menucat_slug, mc.status as menucat_status, mc.showInDisplay as show_in_display, mc.viewOrder as menucat_vieworder, m.availableByDefault, m.slug menuslug, m.url, m.page, m.description, m.menu_icon, m.status, m.targetWindow, m.viewOrder as menuvieworder, m.disableAccess, m.disablingMsg, m.showInDisplay as showMenuInDisplay, 0 as included_in_basic_view, map.*  FROM ".CONST_TBL_PREFIX."menus m JOIN ".CONST_TBL_PREFIX."menucategories mc ON m.categoryId=mc.id LEFT JOIN ".CONST_TBL_PREFIX."menu_allowed_perms map ON m.id=map.menu_id WHERE 1 and m.status='1' and mc.status='1' and (m.availableByDefault='1' OR   map.menu_perm_id IN (select T1.menu_perm_id from (SELECT distinct map.menu_perm_id FROM ".CONST_TBL_PREFIX."menu_allowed_perms map JOIN ".CONST_TBL_PREFIX."role_based_menu_perms rbmp on map.menu_perm_id = rbmp.menu_perm_id JOIN ".CONST_TBL_PREFIX."roles AS r ON rbmp.role_id = r.role_id JOIN ".CONST_TBL_PREFIX."user_roles AS ur ON r.role_id = ur.role_id and ur.user_id=:userid1  LEFT JOIN ".CONST_TBL_PREFIX."user_based_menu_perms as ubmp ON rbmp.menu_perm_id = ubmp.menu_perm_id AND ubmp.user_id =:userid2 AND ubmp.grant_revoke_status = '0' WHERE ubmp.grant_revoke_status is NULL  UNION  SELECT distinct ubmp.menu_perm_id from ".CONST_TBL_PREFIX."user_based_menu_perms as ubmp WHERE ubmp.user_id=:userid3 and  ubmp.grant_revoke_status = 1) T1)) ORDER BY mc.viewOrder, m.viewOrder, map.perm";

			$int_params_to_bind[':userid1'] = $int_params_to_bind[':userid2'] = $int_params_to_bind[':userid3'] = $userid;

		}
		$error_details_to_log['sql'] = $sql;
		try{
			
			$pdo_stmt_obj = PDOConn::query($sql, int_data:$int_params_to_bind);
			
			if($options['resourceonly'])
				return 	$pdo_stmt_obj;

			$i=-1;
			$catid = '';
			$menulist = [];
			while($row = $pdo_stmt_obj->fetch(\PDO::FETCH_ASSOC)){
				if($catid!=$row['menucat_id']){

				  $i++;

				  $menulist[$i] = ['menucat_id'=>$row['menucat_id'], 'menucat_name'=>$row['menucat_name'], 'menu_cat_icon'=>$row['menu_cat_icon'], 'menucat_slug'=>$row['menucat_slug'], 'show_in_display'=>$row['show_in_display'], 'menus'=> [], 'active_menu_count'=>0 ];

				  $catid=$row['menucat_id'];
				  $menu_id = '';
				  $k = -1;
				  
				}	

				if($menu_id!=$row['menuid']){
					$k++;
					$menulist[$i]['menus'][$k]=['menuid'=>$row['menuid'],'description'=>$row['description'],'menuname'=>$row['name'], 'availableByDefault'=>$row['availableByDefault'], 'menuslug'=>$row['menuslug'],'menuurl'=>$row['url'], 'menupage'=>$row['page'], 'menu_icon'=>$row['menu_icon'],'target_window'=>$row['targetWindow'],'disableAccess'=>$row['disableAccess'],'disablingMsg'=>$row['disablingMsg'],'showMenuInDisplay'=>$row['showMenuInDisplay'], 'included_in_basic_view'=>$row['included_in_basic_view'], 'perms'=>[] ];

					if(in_array($row['menuid'],$menuids_to_show)){
						$menulist[$i]['menus'][$k]['included_in_basic_view'] = 1;
					}

					if($row['showMenuInDisplay'] == '1')
						$menulist[$i]['active_menu_count'] += 1;

					$menu_id=$row['menuid'];
				}

				if($row['perm']!='')
					$menulist[$i]['menus'][$k]['perms'][] = $row['perm'];

			}
			
		}catch(Exception $e){var_dump($e);
			
			if(!is_a($e, '\PDOStatement'))
				ErrorHandler::logError($error_details_to_log,$e);	
			else
				ErrorHandler::logError($error_details_to_log);	
			
			return [];

		}
		
		return $menulist;
	}	


	function changeCategoryStatus($menucatid,$menucatstatus){
		$this->last_mysql_error_code = $this->last_sqlstate_code='';
		$error_details_to_log = [];
		$error_details_to_log['at'] = date('Y-m-d H:i:s');
		$error_details_to_log['method'] = __METHOD__;
		$error_details_to_log['menucatid'] = $menucatid;
		$error_details_to_log['menucatstatus'] = $menucatstatus;
		
		$menucatid=(int)$menucatid;
		$menucatstatus=(int)$menucatstatus;

		
		$sql="UPDATE `".CONST_TBL_PREFIX."menucategories` SET `status`='$menucatstatus' WHERE `id`=$menucatid";
		
		$error_details_to_log['sql'] = $sql;

		try{	
			$res=$this->db_conn->exec($sql);
			if(!$res)
				return false;
			return true;	
		}catch(Exception $e){
			$error_details_to_log['exception_msg'] = $e->getMessage();
			$error_details_to_log['mysql_error'] = $this->db_conn->errorInfo();
			$error_details_to_log['result'] = 'boolean false';
			\eBizIndia\logErrorInFile(time(),$_SERVER['REQUEST_URI'], json_encode($error_details_to_log));
			$this->last_mysql_error_code = $error_details_to_log['mysql_error'][1];
			$this->last_mysqlstate_code = $error_details_to_log['mysql_error'][0];
			
			return false;	
		}
			

	}

	function changeMenuStatus($menuid,$menustatus){
		$this->last_mysql_error_code = $this->last_sqlstate_code='';
		$error_details_to_log = [];
		$error_details_to_log['at'] = date('Y-m-d H:i:s');
		$error_details_to_log['method'] = __METHOD__;
		$error_details_to_log['menuid'] = $menuid;
		$error_details_to_log['menustatus'] = $menustatus;

		$menuid=(int)$menuid;
		$menustatus=(int)$menustatus;
		
		$sql="UPDATE `".CONST_TBL_PREFIX."menus` SET `status`='$menustatus' WHERE `id`=$menuid";
		$error_details_to_log['sql'] = $sql;

		try{
			$res=$this->db_conn->exec($sql);
			if(!$res)
				return false;
			return true;
		}catch(Exception $e){
			$error_details_to_log['exception_msg'] = $e->getMessage();
			$error_details_to_log['mysql_error'] = $this->db_conn->errorInfo();
			$error_details_to_log['result'] = 'boolean false';
			\eBizIndia\logErrorInFile(time(),$_SERVER['REQUEST_URI'], json_encode($error_details_to_log));
			$this->last_mysql_error_code = $error_details_to_log['mysql_error'][1];
			$this->last_mysqlstate_code = $error_details_to_log['mysql_error'][0];
			
			return false;	
		}

	}


	function getUsersHavingAccessTo($menuid, $menu_perm=[], $recource_only = false){
		$this->last_mysql_error_code = $this->last_sqlstate_code='';
		$userids=array();
		
		$error_details_to_log = [];
		$error_details_to_log['at'] = date('Y-m-d H:i:s');
		$error_details_to_log['method'] = __METHOD__;
		$error_details_to_log['menuid'] = $menuid;
		$error_details_to_log['menu_perm'] = $menu_perm;
		$error_details_to_log['recource_only'] = $recource_only;

		$menuid=(int)$menuid;

		// availableas: 1 - available by default, 2 - available by role inheritance, 4 - available by explicit assignment, 6 - 2 & 4 

		$menu_perm_clause = [];
		$menu_perm_params = [];

		if(!is_array($menu_perm) && $menu_perm!=''){

			$menu_perm_clause[':menu_perm1'] = 	' AND map.perm = :menu_perm1 ';
			$menu_perm_clause[':menu_perm2'] = 	' AND maprm.perm = :menu_perm2 ';
			$menu_perm_clause[':menu_perm3'] = 	' AND map.perm = :menu_perm3 ';
			
			$menu_perm_params[':menu_perm1'] =  $menu_perm_params[':menu_perm2'] = $menu_perm_params[':menu_perm3'] = trim($menu_perm);


		}else if(is_array($menu_perm) && !empty($menu_perm)){

			$menu_perm_clause[':menu_perm1'] = 	[];
			$menu_perm_clause[':menu_perm2'] = 	[];
			$menu_perm_clause[':menu_perm3'] =	[];
			$cnt = count($menu_perm);

			for($i=0; $i<$cnt;  $i++){

				$menu_perm_clause[':menu_perm1'][] = 	':menu_perm1_'.$i;
				$menu_perm_clause[':menu_perm2'][] = 	':menu_perm2_'.$i;
				$menu_perm_clause[':menu_perm3'][] =	':menu_perm3_'.$i;

				$menu_perm_params[':menu_perm1_'.$i] =  $menu_perm_params[':menu_perm2_'.$i] = $menu_perm_params[':menu_perm3_'.$i] = trim($menu_perm[$i]);
			}

			$menu_perm_clause[':menu_perm1'] = ' AND map.perm IN('.implode(',',$menu_perm_clause[':menu_perm1']).') ';
			$menu_perm_clause[':menu_perm2'] = ' AND maprm.perm IN('.implode(',',$menu_perm_clause[':menu_perm2']).') ';
			$menu_perm_clause[':menu_perm3'] = ' AND map.perm IN('.implode(',',$menu_perm_clause[':menu_perm3']).') ';

		}


		
		$sql = "select T1.id, T1.menu_id, T1.menu_perm_id, SUM(T1.availableas) as availableas FROM 
		(
			SELECT DISTINCT u.id AS id, m.id AS menu_id, 0 AS menu_perm_id, NULL AS grant_revoke_status, 1 as availableas FROM ".CONST_TBL_PREFIX."users u, ".CONST_TBL_PREFIX."menus m WHERE m.id =:menuid1 AND m.availableByDefault = '1'  
			UNION 
			
			
				SELECT DISTINCT ur.user_id AS id, map.menu_id, rbmp.menu_perm_id, ubmp.grant_revoke_status, 2 as availableas  FROM ".CONST_TBL_PREFIX."menu_allowed_perms map JOIN ".CONST_TBL_PREFIX."role_based_menu_perms rbmp ON map.menu_perm_id = rbmp.menu_perm_id AND map.menu_id =:menuid2 ";

			if($menu_perm!='')
				$sql .= $menu_perm_clause[':menu_perm1'];


	$sql .= " JOIN ".CONST_TBL_PREFIX."roles AS r ON rbmp.role_id = r.role_id JOIN ".CONST_TBL_PREFIX."user_roles AS ur ON r.role_id = ur.role_id LEFT JOIN ".CONST_TBL_PREFIX."user_based_menu_perms AS ubmp ON rbmp.menu_perm_id = ubmp.menu_perm_id AND ur.user_id = ubmp.user_id and ubmp.grant_revoke_status=0 WHERE ubmp.grant_revoke_status IS NULL 
				
				UNION

				SELECT DISTINCT ua.id as id, maprm.menu_id AS menu_id, maprm.menu_perm_id AS menu_perm_id, NULL AS grant_revoke_status, 2 as availableas FROM ".CONST_TBL_PREFIX."users ua JOIN ".CONST_TBL_PREFIX."user_roles url ON ua.id = url.user_id JOIN ".CONST_TBL_PREFIX."roles rl on url.role_id=rl.role_id and rl.role_id = 1, ".CONST_TBL_PREFIX."menu_allowed_perms maprm WHERE maprm.menu_id=:menuid4";

			if($menu_perm!='')
				$sql .= $menu_perm_clause[':menu_perm2'];	
			

			$sql .=" UNION 
			
			SELECT DISTINCT ubmp.user_id, map.menu_id, ubmp.menu_perm_id, ubmp.grant_revoke_status, 4 as availableas FROM ".CONST_TBL_PREFIX."user_based_menu_perms AS ubmp JOIN ".CONST_TBL_PREFIX."menu_allowed_perms map ON ubmp.menu_perm_id = map.menu_perm_id AND map.menu_id =:menuid3 ";

			if($menu_perm!='')
				$sql .= $menu_perm_clause[':menu_perm3'];

			$sql .= "  WHERE ubmp.grant_revoke_status =1 

		) T1 group by T1.id, T1.menu_id, T1.menu_perm_id";
		
		
		$error_details_to_log['sql'] = $sql;

		try{
			
			$pdo_stmt = $this->db_conn->prepare($sql);
			$pdo_stmt->bindParam(':menuid1',$menuid,\PDO::PARAM_INT);
			$pdo_stmt->bindParam(':menuid2',$menuid,\PDO::PARAM_INT);
			$pdo_stmt->bindParam(':menuid3',$menuid,\PDO::PARAM_INT);
			$pdo_stmt->bindParam(':menuid4',$menuid,\PDO::PARAM_INT);

			if($menu_perm!=''){
				foreach($menu_perm_params as $prm=>&$val)
					$pdo_stmt->bindParam($prm,$val);

			}

			$pdo_stmt->execute();

			if($recource_only)
				return $pdo_stmt;

			$data = [];

			while($row = $pdo_stmt->fetch(\PDO::FETCH_ASSOC)){

				$data[$row['id']][] = $row;
			}				
			
			return $data;

		}catch(Exception $e){
			$error_details_to_log['exception_msg'] = $e->getMessage();
			$error_details_to_log['mysql_error'][0] = $this->db_conn->errorInfo();
			if(is_object($pdo_stmt))
				$error_details_to_log['mysql_error'][1] = $pdo_stmt->errorInfo();
			$error_details_to_log['result'] = 'boolean false';
			\eBizIndia\logErrorInFile(time(),$_SERVER['REQUEST_URI'], json_encode($error_details_to_log));

			if(!empty($error_details_to_log['mysql_error'][1])  && $error_details_to_log['mysql_error'][1][1]!=''){
				$this->last_mysql_error_code = $error_details_to_log['mysql_error'][1][1];
				$this->last_mysqlstate_code = $error_details_to_log['mysql_error'][1][0];
			}else{

				$this->last_mysql_error_code = $error_details_to_log['mysql_error'][0][1];
				$this->last_mysqlstate_code = $error_details_to_log['mysql_error'][0][0];
			}
			return false;

		}

		

	}

	
}

?>