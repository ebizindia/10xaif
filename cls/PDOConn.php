<?php
namespace eBizIndia;
class PDOConn{
	const VALID_DB_TYPES = ['mysql','mssql'];
	const DB_CREDS = CONST_DB_CREDS;
	private static $conn; // array of DB connection objects for one or more DB servers - mysql, mssql
	private static $last_db_error;
	private static $db_to_use;
	private static $execute_res;

	public static function getExecuteRes(){
		return self::$execute_res;
	}

	public static function switchDB(string $db_to_use = 'mysql'): void{
		
		self::$db_to_use = in_array($db_to_use, self::VALID_DB_TYPES)?$db_to_use:'mysql';
	}

	public static function getInstance(): \PDO {
		if(empty(self::$db_to_use))
			self::switchDB();
		if(empty(self::$conn[self::$db_to_use])){
			if(!self::connectToDB()){ // connect to the MySql DB
				header("HTTP/1.1 500 Internal Server Error",true, 500); die;
			}	
		}
		return self::$conn[self::$db_to_use];
	}

	public static function connectToDB(){ // db types - MySql, MSSql
		try{
			self::$last_db_error = [];
			if(self::$db_to_use=='mssql'){
				$dsn = "odbc:DRIVER=".self::DB_CREDS['mssql']['driver']."; Server=".self::DB_CREDS['mssql']['host']."; Port=".self::DB_CREDS['mssql']['port']."; Database=".self::DB_CREDS['mssql']['db'];
				self::$conn[self::$db_to_use] = new \PDO($dsn, self::DB_CREDS['mssql']['user'], self::DB_CREDS['mssql']['pswd'], [\PDO::ATTR_PERSISTENT => true,\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
				self::$conn[self::$db_to_use]->exec('SET NOCOUNT ON');
			} else { // MySql
				if(empty(self::$db_to_use))
					self::switchDB();
				$dsn = "mysql:host=".self::DB_CREDS['mysql']['host'].";dbname=".self::DB_CREDS['mysql']['db'];
				self::$conn[self::$db_to_use] = new \PDO($dsn,self::DB_CREDS['mysql']['user'],self::DB_CREDS['mysql']['pswd'], [\PDO::ATTR_PERSISTENT => true,\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
				//$conn->exec('SET time_zone=+00:00');
				self::$conn[self::$db_to_use]->exec('SET NAMES \'utf8mb4\'');
				self::$conn[self::$db_to_use]->exec('SET sql_mode=(SELECT REPLACE(@@sql_mode,\'ONLY_FULL_GROUP_BY\',\'\'))');
			}
			return true;
		}catch(\Exception $e){
			if(is_a($e, '\PDOException'))
				self::setLastError(null, '', [], [], $e);
			ErrorHandler::logError([], $e, true, 'DB_CONN_ERR');
			return false;
		}
	}

	public static function prepareQuery(string $sql, array $str_data=[], array $int_data=[]): \PDOStatement {
		if(empty(self::$conn[self::$db_to_use]))
			self::getInstance();
		try{
			self::$last_db_error = [];
			$pdo_st = self::$conn[self::$db_to_use]->prepare($sql);
			if(empty($pdo_st))
				throw new Exception("Error Processing Request", 1);
			if(!empty($str_data)){
				foreach($str_data as $param=>&$val){
					$pdo_st->bindParam($param,$val,\PDO::PARAM_STR);
				}
			}

			if(!empty($int_data)){
				foreach($int_data as $param=>&$val){
					$pdo_st->bindParam($param,$val,\PDO::PARAM_INT);
				}
			}
			return $pdo_st;
		}catch(\Exception $e){
			if(is_a($e, '\PDOException'))
				self::setLastError($pdo_st, $sql, $str_data, $int_data, $e);
			ErrorHandler::logError([], $e);
			throw $e;
		}
	}


	public static function query(string $sql, array $str_data=[], array $int_data=[]): \PDOStatement {
		try{
			self::$execute_res = null;
			$pdo_st = self::prepareQuery($sql, $str_data, $int_data);
			self::$execute_res = $pdo_st->execute();
			return $pdo_st;
		}catch(\Exception $e){
			if(is_a($e, '\PDOException')){
				self::setLastError($pdo_st, $sql, $str_data, $int_data, $e);
				ErrorHandler::logError([], $e);
				throw new \Exception('PDOException:: '.$e->getMessage(), $e->errorInfo[1]);
			}
			throw $e;
		}

	}

	public static function getLastError(){

		return self::$last_db_error;
	}

	public static function setLastError($pdo_st=null, string $sql='', array $str_data = [], array $int_data = [], $e=null): array{
		self::$last_db_error = [];
		if(!empty($pdo_st) && (!empty($pdo_st->errorInfo()[2]) || $pdo_st->errorInfo()[0]!='00000' ) ){
			self::$last_db_error = $pdo_st->errorInfo();
			self::$last_db_error[] = $sql;
			self::$last_db_error[] = $str_data;
			self::$last_db_error[] = $int_data;
		}else if(!empty(self::$conn[self::$db_to_use]) && (!empty(self::$conn[self::$db_to_use]->errorInfo()[2])  || self::$conn[self::$db_to_use]->errorInfo()[0]!='00000' )  ){
			self::$last_db_error = self::$conn[self::$db_to_use]->errorInfo();
			self::$last_db_error[] = $sql;
			self::$last_db_error[] = $str_data;
			self::$last_db_error[] = $int_data;
		}

		if(!empty($e)){
			if(!empty(self::$last_db_error) && self::$last_db_error[0]!='00000' && empty(self::$last_db_error[2])){
				self::$last_db_error[2] = $e->getMessage();
			}else if(empty(self::$last_db_error)){
				$code = $e->getCode();
				if(!empty($code) && $code!='00000'){
					self::$last_db_error[0] = $e->getCode();
					self::$last_db_error[1] = '';
					self::$last_db_error[2] = $e->getMessage();
					self::$last_db_error[3] = $sql;
					self::$last_db_error[4] = $str_data;
					self::$last_db_error[5] = $int_data;
				}
			}
		}
		return self::$last_db_error;
	}
	public static function lastInsertId()
	{
		if(empty(self::$conn[self::$db_to_use]))
			self::getInstance();
		return self::$conn[self::$db_to_use]->lastInsertId();
	}


	public static function resetAutoIncrement($table){
		try{
			self::query('ALTER TABLE '.$table.' auto_increment=1');
			return true;
		}catch(\Exception $e){
			return false;
		}
	}

}
