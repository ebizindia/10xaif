<?php
namespace eBizIndia;
class Helper{
	
	private function __construct(){}

	public static function getMasterList($type){
		switch(strtolower($type)){
			case 'designations':
				$fld = 'desig_in_assoc'; break;
			case 'mem_types':
				$fld = 'membership_type'; break;
			case 'work_type':
				$fld = 'work_type'; break;
			case 'work_ind':
				$fld = 'work_ind'; break;
			case 'work_company':
				$fld = 'work_company'; break;
			case 'cities':
				$fld = ['residence_city', 'work_city']; break;
			case 'states':
				$fld = ['residence_state', 'work_state']; break;
			case 'countries':
				$fld = ['residence_country', 'work_country']; break;
				
			default: return [];
		}
		
		try{
			$data = [];
			if(!is_array($fld)){

				$sql = "SELECT distinct `$fld` from `".CONST_TBL_PREFIX."members` as mem Order By $fld ASC";
				
			}else{
				$sql = [];
				foreach ($fld as $val) {
					$sql []= "SELECT distinct `$val` as mast_val from `".CONST_TBL_PREFIX."members` as mem ";	
				}
				$sql ="SELECT distinct mast_grp.mast_val from (".implode(' UNION ', $sql).") as mast_grp order by mast_grp.mast_val ASC";
				$fld = 'mast_val';  
				
			}
			$pdo_stmt_obj = PDOConn::query($sql);
			while($row=$pdo_stmt_obj->fetch(\PDO::FETCH_ASSOC)){
				if(!empty($row[$fld]))
					$data[] = $row[$fld];
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

	public static function resetAutoID($table){
		try{
			PDOConn::query('ALTER table `'.$table.'` auto_increment=1');
		}catch(\Exception $e){

		}
	}

}