<?php
namespace eBizIndia;
class ErrorHandler{
	
	public static function logError($error_info=[], $e=null, bool $show_msg=false, string $msg_to_show = ''): void{
		
		if(defined('CONST_LOG_ERRORS') && CONST_LOG_ERRORS){
			$data = [];
			$data[] = 'Log Date & time: '.date('d-m-Y H:i:s');
			$data[] = 'IP: '.getRemoteIP();
			if(!empty($error_info)){
				if(is_array($error_info)){
					array_walk($error_info, function(&$val, $key){
						$val = $key.': '.print_r($val,true);
					});
					$data = array_merge($data,  $error_info);

				}else{
					$data[] = 'Error msg: '.$error_info;
				}
				unset($error_info);
			}

			if(is_object($e)){
				$data[] = 'Referer: '.($_SERVER['HTTP_REFERRER']??'');
				$data[] = 'User agent: '.($_SERVER['HTTP_USER_AGENT']??'');
				$data[] = 'Error msg: '.$e->getMessage();
				$data[] = 'Error code: '.$e->getCode();
				$data[] = 'URL: '.CONST_APP_ABSURL.($_SERVER['REQUEST_URI']??'');
				$data[] = 'File: '.$e->getFile(). ' | Line: '.$e->getLine();
				$data[] = 'Trace: '.$e->getTraceAsString();
			}

			self::writeToLogFile(implode(PHP_EOL,$data));		

		}

		if(!empty($show_msg))
			self::showMsgToUser($msg_to_show??'');
	}

	public static function showMsgToUser(string $msg=''): void{
		$msg = trim($msg);
		if(empty($msg))
			$msg='Sorry, some error occurred. The system failed to recover.\nPlease contact the IT team with the date, time and some basic details of the action taken.\nYou may refresh the page and try.';
		else{
			switch (strtoupper($msg)) {
				case 'DB_CONN_ERR':
					$msg = 'Database connection failed.\nPlease contact the IT team with the date, time and some basic details of the action taken.\nYou may refresh the page and try.';
					break;
				
				case 'UNKNOWN_ERR':
					$msg = 'Some unknown error occurred.\nPlease contact the IT team with the date, time and some basic details of the action taken.\nYou may refresh the page and try.';
					break;
			}
		}

		$msg = htmlentities($msg);
		if(($_SERVER['HTTP_X_REQUESTED_WITH']??'')=='XMLHttpRequest'){
			echo json_encode(array('ERROR_EXCEPTION_MSG'=>$msg));
			exit;
		}else{
			echo "<script type='text/javascript' >\n";
			echo 'if(top !== self){ alert("'.str_replace('"', '\\"', $msg).'"); }'.PHP_EOL;
			echo "</script>\n";
			die('<div style="margin: 0 auto; width: 100%; text-align:center; font-weight:bold; font-size: 18px; padding-top:20px; color:#ff3333; " >'.nl2br(str_replace('\n', '<br>', $msg)).'</div>');
		}
	}

	private static function writeToLogFile(string $data): void{
		\error_log($data.PHP_EOL.'*****************************'.PHP_EOL.PHP_EOL, 3, CONST_ERROR_LOG);
		// $max_tries = 3;
		// $duration_between_tries = 100000; // microsec
		// $fp = null;
		// while($max_tries--){
		// 	if(!$fp)
		// 		$fp = fopen(CONST_ERROR_LOG,'a+');
		// 	if(flock($fp, LOCK_EX)){	
		// 		fwrite($fp,$data.PHP_EOL.'*****************************'.PHP_EOL.PHP_EOL);
		// 		fflush($fp); // flush output before releasing the lock
	    // 		flock($fp, LOCK_UN); // release the exclusive lock
		// 		fclose($fp);	
		// 		break;
		// 	}

		// 	usleep($duration_between_tries);
		// 	$duration_between_tries += 20000; // increase sleep duration by 20 milisec
		// }
	}

}
