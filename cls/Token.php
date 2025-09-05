<?php
namespace eBizIndia;
class Token{
	const life_time = 600; // in sec - 10 minutes	

	public static function generate(){
		$token = md5(random_bytes(64));
		$_SESSION['token'] = [];
		$_SESSION['token'][$token] = time()+self::life_time; // valid for next 10 minutes
		return $token;
	}

	public static function get(){
		return key($_SESSION['token']??[]);
	}

	public static function verifyFromHeader(){
		$token = filter_input(INPUT_SERVER, 'HTTP_TOKEN');
		return self::verify($token);
	}

	public static function verifyFromPayload(){
		$token = filter_input(INPUT_POST, 'csrf');
		return self::verify($token);
	}

	private static function verify($token){
		if(empty($token))
			return false;
		if(isset($_SESSION['token'][$token])){
			$token_val = $_SESSION['token'][$token];
			unset($_SESSION['token'][$token]);
			if($token_val>time())
				return true;
		}
		return false;	
	}

}