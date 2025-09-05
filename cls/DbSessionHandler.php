<?php
namespace eBizIndia;
use \Exception;
class DbSessionHandler {
    public $lifetime;
    //public $memcache;
    public $init_session_data;
	public $session_table;
    public $db_conn;

    function __construct($db_conn,$session_table) {

        //print_r($db_conn);

		// $this->writeDebugInfo("In constructor: ",true);

        # Thanks, inf3rno
        //register_shutdown_function("session_write_close");
        $this->db_conn=$db_conn;

        //var_dump($this->db_conn);

		$this->session_table=$session_table;
        //$this->memcache = new Memcache;
        //$this->lifetime = intval(ini_get("session.gc_maxlifetime"));
        $this->lifetime = 5400;
        $this->init_session_data = null;
        //$this->memcache->connect("127.0.0.1",11211);

        return true;
    }

	private function writeDebugInfo($datastr, $override_sessionid_check=false, $session_id=''){
		//echo $datastr,'<br>';

        if($override_sessionid_check || $session_id=='tf6jnte8se8jteol5g496b95k6'){
			$fp=fopen(CONST_APP_FULL_PHYSICAL_PATH."/session-debug-log.txt",'a');
			fwrite($fp,"**** {$datastr} \n\n");
			fclose($fp);
		}
	}


    function open($save_path,$session_name){
        //var_dump($this->db_conn);
        $session_id = session_id();
		//echo $session_id,'<br>';
		//$this->writeDebugInfo("In open: \$session_id: {$session_id}", false, $session_id);

		if ($session_id !== "") {
            $this->init_session_data = $this->read($session_id);
			//$this->writeDebugInfo("Data read: {$this->init_session_data}", false, $session_id);
        }

        return true;
    }

    function close() {
		$session_id = session_id();
		//$this->writeDebugInfo("In close: \$session_id: {$session_id}", false);

		$this->lifetime = null;
        //$this->memcache = null;
        $this->init_session_data = null;

        return true;
    }

    function read($session_id) {
       //$this->writeDebugInfo("In read: \$session_id: {$session_id}", false, $session_id);

	   // $data = $this->memcache->get($sessionID);
      //  if ($data === false) {
            # Couldn't find it in MC, ask the DB for it

            //$session_id_escaped = mysql_real_escape_string($session_id);

            $session_id_escaped = $this->db_conn->quote($session_id);

            $sql="SELECT `sessionData` FROM `".$this->session_table."` WHERE `sessionID`=$session_id_escaped and `sessionExpirationTS`>=" . (time());

            $r = $this->db_conn->query($sql);
			$data = '';
            if (is_object($r) && ($r->columnCount() > 0)) {
                $data = (string)$r->fetchColumn(0);

            }

             //$this->writeDebugInfo("\$result: $data | Query: $sql |Errorinfo: ".print_r($this->db_conn->errorInfo(),true), false, $session_id);

            //if (is_resource($r) && ($this->db_conn->exec($r) !== 0)) {
              //  $data = mysql_result($r,0,"sessionData");

            //}





            # Refresh MC key: [Thanks Cal :-)]
            //$this->memcache->set($sessionID,$data,false,$this->lifeTime);
      //  }

        # The default miss for MC is (bool) false, so return it

        return $data;
    }

    function write($session_id,$data) {
		# This is called upon script termination or when session_write_close() is called, which ever is first.
        //$result = $this->memcache->set($sessionID,$data,false,$this->lifeTime);

		//$this->writeDebugInfo("In write function: \$session_id: {$session_id} \n\n\$data: $data\n\n init_session_data: {$this->init_session_data}", false, $session_id);

		/* if($session_id=='m9bnh8fhos8ein0u14ooehmtu2'){
			$fp=fopen(CONST_APP_FULL_PHYSICAL_PATH."/session-debug-log.txt",'a');
			fwrite($fp,"**** In write function: \n\n\$data: $data\n\n init_session_data: {$this->init_session_data}\n\n");
			fclose($fp);
		}else{
			$fp=fopen(CONST_APP_FULL_PHYSICAL_PATH."/session-debug-log.txt",'a');
			fwrite($fp,"**** In write function: Another session ID\n\n");
			fclose($fp);
		} */

		$session_id_escaped = $this->db_conn->quote($session_id);
        $session_expiration_ts = ($this->lifetime + time());

		if ($this->init_session_data !== $data) {

            //$session_data = mysql_real_escape_string($data);

            $sql="INSERT INTO `".$this->session_table."` (`sessionID`,`sessionExpirationTS`,`sessionData`) VALUES($session_id_escaped,$session_expiration_ts,".$this->db_conn->quote($data).") ON DUPLICATE KEY UPDATE `sessionExpirationTS`=VALUES(`sessionExpirationTS`), `sessionData`=VALUES(`sessionData`)";

            $r =$this->db_conn->exec($sql);
            $result = ($r===false)?false:true;
        }else{
			$result=true;
            $sql="UPDATE `".$this->session_table."` set `sessionExpirationTS`=$session_expiration_ts WHERE `sessionID`=$session_id_escaped";
			$r =$this->db_conn->exec($sql);
            if($r===false)
				$result=false;

		}

        $rr=($result===false)?'FALSE':'TRUE';


       // $this->writeDebugInfo("\$result: $rr | $r | Query: $sql |Errorinfo: ".print_r($this->db_conn->errorInfo(),true), false, $session_id);


        //return $result;
        return $result===false;
    }

    function destroy($session_id) {
		//$this->writeDebugInfo("In destroy: \$session_id: {$session_id}", false, $session_id);
		# Called when a user logs out...
        //$this->memcache->delete($sessionID);
        //$session_id = mysql_real_escape_string($session_id);

        $session_id = $this->db_conn->quote($session_id);
        $r =$this->db_conn->exec("DELETE FROM `".$this->session_table."` WHERE `sessionID`=$session_id");

        return true;
    }

    function gc($max_lifetime) {
		# We need this atomic so it can clear MC keys as well...
        $r =$this->db_conn->exec("DELETE  FROM `".$this->session_table."` WHERE `sessionExpirationTS`<" . (time() - $max_lifetime));
        // if (is_resource($r) && (($rows = mysql_num_rows($r)) !== 0)) {
            // for ($i=0;$i<$rows;$i++) {
                // $this->destroy(mysql_result($r,$i,"sessionID"));
            // }
        // }

        return true;
    }

    /*function updateSession($filters, $data){
        $where = array();
        foreach($filters as $key=>$value){
            switch($key){
                case 'session_id':
                    $where[] = ' AND sessionID=\''.mysql_real_escape_string($value).'\'';
                    break;
               case 'user_id':
                    $where[] = ' AND user_id=\''.mysql_real_escape_string($value).'\'';
                    break;
            }
        }
        $where = implode('', $where);
        $fields = array();
        foreach($data as $key=>$value){
            if($value=='')
                $fields[] = '`'.$key.'`=NULL';
            else
                $fields[] = '`'.$key.'`=\''.mysql_real_escape_string($value).'\'';
        }
        $fields = implode(',', $fields);
        if($fields!=''){
            $sql = 'UPDATE '.$this->session_table.' '. $fields.' '. $where;
            $rs = mysql_query($sql);
            if(!$rs)
                return false;
            return true;
        }
        return false;
    }

    function deleteSession($filters){
        $where = array();
        foreach($filters as $key=>$value){
            switch($key){
                case 'session_id':
                    $where[] = ' AND sessionID=\''.mysql_real_escape_string($value).'\'';
                    break;
               case 'user_id':
                    $where[] = ' AND user_id=\''.mysql_real_escape_string($value).'\'';
                    break;
            }
        }
        $where = implode('', $where);
        $sql = 'DELETE FROM '.$this->session_table.' '. $where;
        $rs = mysql_query($sql);
        if(!$rs)
            return false;
        return true;

    }*/
}


?>
