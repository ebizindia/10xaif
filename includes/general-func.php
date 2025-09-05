<?php
namespace eBizIndia;
function download_send_headers($filename, $encoding='binary') {
    // disable caching
    $now = gmdate("D, d M Y H:i:s");
    header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
    header("Last-Modified: {$now} GMT");

    // force download
	header("Content-type:text/csv");
	// disposition / encoding on response body
    header("Content-Disposition: attachment;filename={$filename}");
    header("Content-Transfer-Encoding: {$encoding}");
}
function truncate($str, $width, $shortener_text="...", $replace_text="\n") {
    return current(explode($replace_text, wordwrap($str, $width, $shortener_text.$replace_text)));
}

function validateMobileNumber($mobile_number){
	return true;
}

function getDayNameByNumber($n){
	$days = ['Sunday','Monday','Tuesday', 'Wednesday','Thursday','Friday','Saturday'];
	return $days[$n];
}

function fullWordOrdinal($number) {
    $ord1 = array(1 => "first", 2 => "second", 3 => "third", 5 => "fifth", 8 => "eight", 9 => "ninth", 11 => "eleventh", 12 => "twelfth", 13 => "thirteenth", 14 => "fourteenth", 15 => "fifteenth", 16 => "sixteenth", 17 => "seventeenth", 18 => "eighteenth", 19 => "nineteenth");
    $num1 = array("zero", "one", "two", "three", "four", "five", "six", "seven", "eight", "nine", "ten", "eleven", "twelve", "thirteen", "fourteen", "fifteen", "sixteen", "seventeen", "eightteen", "nineteen");
    $num10 = array("zero", "ten", "twenty", "thirty", "fourty", "fifty", "sixty", "seventy", "eighty", "ninety");
    $places = array(2 => "hundred", "thousand", 6 => "million", 9 => "billion", 12 => "trillion", 15 => "quadrillion", 18 => "quintillion", 21 => "sextillion", 24 => "septillion", 27 => "octillion");

    $number = array_reverse(str_split($number));

    if ($number[0] == 0) {
        if ($number[1] >= 2)
            $out = str_replace("y", "ieth", $num10[$number[1]]);
        else
            $out = $num10[$number[1]]."th";
    } else if ($number[1] == 1) {
        $out = $ord1[$number[1] . $number[0]];
    } else {
        if (array_key_exists($number[0], $ord1))
            $out = $ord1[$number[0]];
        else
            $out = $num1[$number[0]]."th";
    }

    if($number[0] == 0 || $number[1] == 1){
        $i = 2;
    } else {
        $i = 1;
    }

    while ($i < count($number)) {
        if ($i == 1) {
            $out = $num10[$number[$i]] . " " . $out;
            $i++;
        } else if ($i == 2) {
            $out = $num1[$number[$i]] . " hundred " . $out;
            $i++;
        } else {
            if (isset($number[$i + 2])) {
                $tmp = $num1[$number[$i + 2]] . " hundred ";
                $tmpnum = $number[$i + 1].$number[$i];
                if ($tmpnum < 20)
                    $tmp .= $num1[$tmpnum] . " " . $places[$i] . " ";
                else
                    $tmp .= $num10[$number[$i + 1]] . " " . $num1[$number[$i]] . " " . $places[$i] . " ";

                $out = $tmp . $out;
                $i+=3;
            } else if (isset($number[$i + 1])) {
                $tmpnum = $number[$i + 1].$number[$i];
                if ($tmpnum < 20)
                    $out = $num1[$tmpnum] . " " . $places[$i] . " " . $out;
                else
                    $out = $num10[$number[$i + 1]] . " " . $num1[$number[$i]] . " " . $places[$i] . " " . $out;
                $i+=2;
            } else {
                $out = $num1[$number[$i]] . " " . $places[$i] . " " . $out;
                $i++;
            }
        }
    }
    return $out;
}

function stripslashes_deep($value)
{
       $value = is_array($value) ?
                   array_map('\eBizIndia\stripslashes_deep', $value) :
                   stripslashes($value);

       return $value;
}

function striptags_deep($value)
{
       $value = is_array($value) ?
                   array_map('\eBizIndia\striptags_deep', $value) :
                   strip_tags($value);

       return $value;
}

function trim_deep($value)
{
       $value = is_array($value) ?
                   array_map('\eBizIndia\trim_deep', $value) :
                   trim($value);

       return $value;
}

function truncateText($text, $limit){
	if(strlen($text)<=$limit)
		return $text;

	return substr($text, 0, strrpos($text, ' ', $limit));

}

function relativeDateTerm($tm){
	$today = date('Y-m-d');
	$yesterday = date('Y-m-d', time()-86400);
	$tomorrow = date('Y-m-d', time() +86400);

	$dt = date('Y-m-d', $tm);
	if($dt == $today)
		return 'Today';
	else if($dt == $yesterday)
		return 'Yesterday';
	else if($dt == $tomorrow)
		return 'Tomorrow';
	return false;
}
// Date format conversion
function dmY_to_Ymd($dt)
{ $dt=trim($dt);
  $year=substr($dt, 6,4);
  $month=substr($dt, 3,2);
  $day=substr($dt, 0,2);
  $dt=date("Y-m-d",mktime(00,00,00,$month,$day,$year));
  return $dt;
}

function convertdatetimetodmy($date){
	$datearr = explode(" ",$date);
	$finaldate = dmY_to_Ymd($datearr[0]). " " . $datearr[1];
	return $finaldate;
}


function getFinancialYearForADate($date, $finyr_start_day_month='0104'){ // $date should be in d-m-Y format
	$datearr=explode('-',$date);
	$date_day=$datearr[0];
	$date_month=$datearr[1];
	$date_year=$datearr[2];

	$finyr_startmonth=substr($finyr_start_day_month,2,2);

	if((int)$date_month>=(int)$finyr_startmonth){
		$financial_year=$date_year.'-'.($date_year+1);
	}else{
		$financial_year=($date_year-1).'-'.$date_year;
	}
	return $financial_year;
}

function generatePasswordGenKey($len=32){
	$pswdgenkey='';
	for($i=0; $i<$len; $i++){
		$pswdgenkey.=chr(mt_rand(1,127));

	}
	return substr($pswdgenkey,0,$len);

}

function generatePassword($len=8){
	$chars = ['0','1','2','3','4','5','6','7','8','9','A','a','B','b','C','c','D','d','E','e','F','f','G','g','H','h','I','i','J','j','K','k','L','l','M','m','N','n','O','o','P','p','Q','q','R','r','S','s','T','t','U','u','V','v','W','w','X','x','Y','y','Z','z','@','#','$']; //,'!','^','_','=','+','[',']',';','|','<','>'];
	return implode(array_intersect_key($chars, array_fill_keys(array_rand($chars, $len), '')));
}

function jsDateFormat($df=''){
	if($df=='')
		$df = 'd-m-Y';
	switch(strtolower($df)){
		case 'd-m-Y': return 'dd-mm-yyyy';break;
		case 'm-d-Y': return 'mm-dd-yyyy';break;
		case 'd-M-Y': return 'mm-dd-yyyy';break;
		case 'M-d-Y': return 'mm-dd-yyyy';break;
	}
}

function fetchURL($url, $curl_options){
	$ch=curl_init();
	$options=array(
					CURLOPT_URL=>$url,
					CURLOPT_FOLLOWLOCATION=>true,
					CURLOPT_MAXREDIRS=>1,
					CURLOPT_RETURNTRANSFER=>true,
					);
	$options  = array_replace($options, $curl_options);
	curl_setopt_array($ch,$options);
	$output=curl_exec($ch);
	curl_close($ch);

	if($output===false)
		echo curl_error();
	if($options[CURLOPT_RETURNTRANSFER])
		return $output;
	return;
}


function getPaginationData($tot_records,$recsperpage,$requestedpageno=1,$noofpagelinks=9){
	$paginationdata=array();
	$paginationdata['recs_per_page']=$recsperpage;
	$paginationdata['tot_pages']=ceil($tot_records/$paginationdata['recs_per_page']);
	$paginationdata['first_page']=1;
	$paginationdata['last_page']=$paginationdata['tot_pages'];

	if($requestedpageno>$paginationdata['tot_pages'])
		$paginationdata['curr_page']=$paginationdata['tot_pages'];
	else
		$paginationdata['curr_page']=$requestedpageno;

	if($paginationdata['curr_page']=='' || $paginationdata['curr_page']<0){
		$paginationdata['curr_page']=1;
	}elseif($paginationdata['curr_page']>$paginationdata['tot_pages']){
		$paginationdata['curr_page']=$paginationdata['tot_pages'];
	}
	$paginationdata['startindex']=($paginationdata['curr_page']-1)*$recsperpage;

	$paginationdata['move_window_upto']=((int)($noofpagelinks/2))+1;
	if($paginationdata['curr_page']<$paginationdata['last_page']){
		$paginationdata['next_page']=$paginationdata['curr_page']+1;
	}else{
		$paginationdata['next_page']='';//$paginationdata['curr_page'];
	}

	if($paginationdata['curr_page']>$paginationdata['first_page']){
		$paginationdata['prev_page']=$paginationdata['curr_page']-1;
	}else{
		$paginationdata['prev_page']='';//$paginationdata['curr_page'];
	}

	$change_disp_slot_after = $noofpagelinks-1;
	$change_disp_slot_after = $change_disp_slot_after<=0?1:$change_disp_slot_after;
	$curr_page_slot = ceil($paginationdata['curr_page']/$change_disp_slot_after);
	$paginationdata['start_disp_pageno'] = ($curr_page_slot-1) * $change_disp_slot_after + 1;
	$curr_slot_last_page = $paginationdata['start_disp_pageno'] + $noofpagelinks - 1;
	$paginationdata['end_disp_pageno'] = $paginationdata['tot_pages']>$curr_slot_last_page?$curr_slot_last_page:$paginationdata['tot_pages'];

	/*$half=(int)($noofpagelinks/2);
	$min=1;
	$max=$noofpagelinks;
	if($paginationdata['tot_pages']<=$noofpagelinks){
	}else if($paginationdata['curr_page'] <= $half){
		//$min=1;
		//$max=$noofpagelinks;
	}else if($paginationdata['curr_page'] > $paginationdata['tot_pages'] - $half){
		$min=$paginationdata['tot_pages'] - $noofpagelinks +1;
		$max=$paginationdata['tot_pages'];
	}else{
		$min = $paginationdata['curr_page'] - $half;
		$max=$paginationdata['curr_page']+$half;
	}
	$paginationdata['start_disp_pageno']=$min;
	$paginationdata['end_disp_pageno']=$max;*/

	/*if($paginationdata['curr_page']<=$paginationdata['move_window_upto']){
		$paginationdata['start_disp_pageno']=1;
		$paginationdata['end_disp_pageno']=($paginationdata['tot_pages']>=$noofpagelinks)? $noofpagelinks:$paginationdata['tot_pages'];
	}elseif($paginationdata['curr_page']>$paginationdata['move_window_upto']){
		$paginationdata['start_disp_pageno']=$paginationdata['curr_page']-($paginationdata['move_window_upto']-1);
		$paginationdata['end_disp_pageno']=$paginationdata['curr_page']+($paginationdata['move_window_upto']-1);
		$paginationdata['end_disp_pageno']=($paginationdata['end_disp_pageno']>$paginationdata['tot_pages'])? $paginationdata['tot_pages']:$paginationdata['end_disp_pageno'];
	}*/

	return $paginationdata;

}


function jsonEncode($array){
	if(!is_array($array)){
		//return '"'.str_replace(array(chr(10),chr(13),chr(8),chr(9),"'",'"'),array("\\n","\\r","\\b","\\t","\'",'\"'),addslashes($array)).'"';
		return '"'.str_replace(array(chr(10),chr(13),chr(8),chr(9),'"'),array("\\n","\\r","\\b","\\t",'\"'),$array).'"';


	}
	$keys=array_keys($array);

	$associative=false;
	for($i=0;$i<count($keys);$i++){
		if(is_string($keys[$i])){
			$associative=true;
			break;
		}
	}

	$temp=array();
	if($associative){
		$jsonstr.='{';
		foreach($array as $key=>$value){
			$temp[]='"'.addslashes($key).'":'.jsonEncode($value);
		}
		$jsonstr.=implode(',',$temp).'}';

	}else{

		$jsonstr.='[';
		for($k=0;$k<count($array);$k++){
			$temp[]=jsonEncode($array[$k]);
		}
		$jsonstr.=implode(',',$temp).']';

	}
	return $jsonstr;
}


function force_file_download($dir,$file,$display_filename,$content_type=true)
{
	if ((isset($file))&&(file_exists($dir.$file))) {
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		if($content_type==true)
		header("Content-Type: ".getMimeType($file));
		header("Content-Disposition: attachment; filename=\"$display_filename\"");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: " . filesize($dir.$file));


		$file = @fopen($dir.$file,"rb");
		if ($file) {
			while(!feof($file)) {
				print(fread($file, 1024*8));
				flush();
				if (connection_status()!=0) {
				@fclose($file);
				die();
				}
			}
			@fclose($file);
		}


	} else {
	   echo "No file selected";
	} //end if

}//end function


function getMimeType($filename){
	$allowed_ext = array (

						  // archives
						  'zip' => 'application/zip',

						  // documents
						  'pdf' => 'application/pdf',
						  'doc' => 'application/msword',
						  'xls' => 'application/vnd.ms-excel',
						  'ppt' => 'application/vnd.ms-powerpoint',

						  // executables
						  'exe' => 'application/octet-stream',

						  // images
						  'gif' => 'image/gif',
						  'png' => 'image/png',
						  'jpg' => 'image/jpeg',
						  'jpeg' => 'image/jpeg',

						  // audio
						  'mp3' => 'audio/mpeg',
						  'wav' => 'audio/x-wav',

						  // video
						  'mpeg' => 'video/mpeg',
						  'mpg' => 'video/mpeg',
						  'mpe' => 'video/mpeg',
						  'mov' => 'video/quicktime',
						  'avi' => 'video/x-msvideo'
						);
	// file extension
	$fext = strtolower(substr(strrchr($filename,"."),1));
	// get mime type
	if ($allowed_ext[$fext] == '') {
	  $mtype = '';
	  // mime type is not set, get from server settings
	  if (function_exists('mime_content_type')) {
		$mtype = mime_content_type($file_path);
	  }
	  else if (function_exists('finfo_file')) {
		$finfo = finfo_open(FILEINFO_MIME); // return mime type
		$mtype = finfo_file($finfo, $file_path);
		finfo_close($finfo);
	  }
	  if ($mtype == '') {
		$mtype = "application/force-download";
	  }
	}
	else {
	  // get mime type defined by admin
	  $mtype = $allowed_ext[$fext];
	}
	return $mtype;
}

function getFileMimeType($file_path){
	if($size = getimagesize($file_path)){
		return $size['mime'];
	}else{
		$file_extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
		$mime_type = '';
		switch($file_extension){
			case 'pdf': $mime_type = 'pdf'; break;
			case 'csv':
			case 'xls':
			case 'xlsx':
				$mime_type = 'xls'; break;
			case 'txt':
			case 'rtf':
			case 'doc':
			case 'docx':
				$mime_type = 'doc'; break;
		}
		return $mime_type;
	}
}

function getReadableFileSizeString($file_size_in_bytes) {
	if($file_size_in_bytes <= 0)
		return '0 B';
	$idx = -1;
	$byte_units = ['KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
	do {
		$file_size_in_bytes = $file_size_in_bytes / 1024;
		++$idx;
	} while($file_size_in_bytes > 1024);
	return round(max($file_size_in_bytes, 0.1), 1, PHP_ROUND_HALF_UP) . ' ' . $byte_units[$idx];
}

function sendAMail($to,$subject,$htmlmessage='',$textmessage='',$from='',$fromname='',$html=true,$type='phpmail',$smtpdetails=array(),$cc=array(),$bcc=array(),$inlineimages=array(),$attachments=array()){
	
	try {

		$mail = new \PHPMailer\PHPMailer\PHPMailer(true); //the true param means it will throw exceptions on errors, which we need to catch
		//$type='hardcodedsmtp';
		switch($type){
			case 'smtp':	$mail->IsSMTP();
							$mail->SMTPAuth=true;
							if($smtpdetails['secure'] == true)
								$mail->SMTPSecure = true;
							else{
								$mail->SMTPSecure=false;
								$mail->SMTPAutoTLS = false;
							}
							$mail->Host=$smtpdetails['host'];
							$mail->Port=$smtpdetails['port'];
							$mail->Username=$smtpdetails['username'];
							$mail->Password=$smtpdetails['password'];
							break;

			case 'hardcodedsmtp':	$mail->IsSMTP();
							$mail->SMTPAuth=true;
							//if(isset($smtpdetails['secure']) && $smtpdetails['secure']=='ssl')
							$mail->SMTPSecure=true;
							$mail->Host='smtp.gmail.com';
							$mail->Port=465;
							$mail->Username='actestmail@gmail.com';
							$mail->Password='acmail2013';
							break;

			case 'phpmail':
			default:		$mail->IsMail();  // tell the class to use Phpmail
							break;

		}

		$mail->From=$from;

		if($fromname!='')

			$mail->FromName=$fromname;

		for($i=0;$i<count($to);$i++)

			$mail->AddAddress($to[$i]);



		for($i=0;$i<count($cc);$i++)

			$mail->AddCC($cc[$i]);



		for($i=0;$i<count($bcc);$i++)

			$mail->AddBCC($bcc[$i]);

		$mail->Subject = $subject;

		if($html == true){

			$mail->Body = $htmlmessage;

			$mail->IsHTML(true);

			$mail->AltBody = $textmessage; // optional - MsgHTML will create an alternate automatically

		}else{

			$mail->Body = $textmessage;

			$mail->isHTML(false);

		}

		for($i=0;$i<count($attachments);$i++)
		{

			$mail->AddAttachment($attachments[$i]['attachment_filenamepath'],$attachments[$i]['attachment_name'],$attachments[$i]['encoding'],$attachments[$i]['contenttype']);      // attachment

		}

		for($i=0;$i<count($inlineimages);$i++)

			$mail->AddEmbeddedImage($inlineimages[$i]['image_filenamepath'],$inlineimages[$i]['image_identifier'],$inlineimages[$i]['image_filenamepath'],'base64','image/jpeg');      // attachment



		$mail->Send();



	} catch (\Exception $e) {
		
		ErrorHandler::logError('',$e);

		return false;

	}

	return true;

}

function beforeSiteShutdown(){ // Will execute just before the script completes.

	global $site_cls;
	$site_cls->unsetDbConnection();
	$_SESSION['site_obj']=serialize($site_cls); // storing the member object in session
}

function beforeShutdown(){ // Will execute just before the script completes.

	global $usercls;
	$usercls->unsetDbConnection();
	$_SESSION['userobj']=serialize($usercls); // storing the member object in session
}


function applyGlobalDateFormatInTpl($datetimestr){
	return date(CONST_DATE_DISPLAY_FORMAT,strtotime($datetimestr));

}

function applyGlobalDateTimeFormatInTpl($datetimestr){
	return date(CONST_DATE_TIME_DISPLAY_FORMAT,strtotime($datetimestr));

}


function uploadFiles($absoluteuploaddirpath,$fileinputname,&$objref,$allowedfiletypes=array(),$disallowedfiletypes=array(),$filegenarationfunction=''){
	echo $filegenarationfunction(1); exit;

	$result=array();
	for($i=0;$i<count($_FILES[$fileinputname]['name']);$i++)
	{
		$result[$i]=array('originalName'=>$_FILES[$fileinputname]['name'][$i],'generatedName'=>'','errorno'=>$_FILES[$fileinputname]['error'][$i], 'othererror'=>'');

		if($_FILES[$fileinputname]['error'][$i]==0)
		{
			$name=$_FILES[$fileinputname]['name'][$i];
			$file_ext = $_FILES[$fileinputname]['extension'][$i];
			$temp=pathinfo($_FILES[$fileinputname]['name'][$i]);
			$extension=$temp['extension'];

			if((!empty($allowedfiletypes) && !in_array($extension,$allowedfiletypes)) || (!empty($disallowedfiletypes) && in_array($extension,$disallowedfiletypes)))
			{
				$result[$i]['othererror']='UNKNOWN_TYPE';

			}
			else
			{


				$targetfilename=$taskObj->generateFileName($taskid).'.'.$extension;
				$result[$i]['generatedName']=$targetfilename;
				$dest=$absoluteuploaddirpath.$targetfilename;

				if(!move_uploaded_file($_FILES[$fileinputname]['tmp_name'][$i],$dest)){
					$result[$i]['othererror']='MOVE_FAILED';
				}
			}

		}
	}

	return $result;


}

/*function formatDate($date, $curr_format, $new_format, $month_names){
	$months_flipped=array_flip($month_names);
	switch($curr_format){
		case 'd-m-Y':
			$date_parts = explode('-', $date);
			$ymd = $date_parts[2]+'-'+$date_parts[1]+'-'+$date_parts[0];
			break;
		case 'm-d-Y':
			$date_parts = explode('-', $date);
			$ymd = $date_parts[2]+'-'+$date_parts[0]+'-'+$date_parts[1];
			break;
		case 'M-d-Y':
			$date_parts = explode('-', $date);
			$ymd = $date_parts[2]+'-'+str_pad($months_flipped[$temp_date_parts[1]],2,'0',STR_PAD_LEFT)+'-'+$date_parts[1];
			break;
		case 'dd-M-Y':
			$date_parts = explode('-', $date);
			$ymd = $date_parts[2]+'-'+str_pad($months_flipped[$temp_date_parts[1]],2,'0',STR_PAD_LEFT)+'-'+$date_parts[0];
			break;
	}
	$date = strtotime($ymd);
	$new_format = $new_format = '' ? 'Y-M-d';
	return date($new_format, $date);
}*/

function getYmdFromDate($date, $month_names, $format){
	$months_flipped=array_flip($month_names);

	$temp_date_parts=explode('-',$date);
	switch($format){
		case 'd-m-Y':
			$date_Ymd = $temp_date_parts[2].'-'.$temp_date_parts[1].'-'.$temp_date_parts[0];
			break;
		case 'm-d-Y':
			$date_Ymd = $temp_date_parts[2].'-'.$temp_date_parts[0].'-'.$temp_date_parts[1];
			break;
		case 'd-M-Y':
			$date_Ymd = $temp_date_parts[2].'-'.str_pad($months_flipped[$temp_date_parts[1]],2,'0',STR_PAD_LEFT).'-'.$temp_date_parts[0];
			break;
		case 'M-d-Y':
			$date_Ymd=$temp_date_parts[2].'-'.str_pad($months_flipped[$temp_date_parts[0]],2,'0',STR_PAD_LEFT).'-'.$temp_date_parts[1];
			break;
	}
	return $date_Ymd;
}

function getTimeZonesList(){
	$timezones=array();
	$timezones[]=array((-12.0*60*60), "-12:00", "Eniwetok, Kwajalein");
	$timezones[]=array((-11.0*60*60), "-11:00", "Midway Island, Samoa");
	$timezones[]=array((-10.0*60*60), "-10:00", "Hawaii");
	$timezones[]=array((-9.0*60*60), "-9:00", "Alaska");
	$timezones[]=array((-8.0*60*60), "-8:00", "Pacific Time (US &amp; Canada)");
	$timezones[]=array((-7.0*60*60), "-7:00", "Mountain Time (US &amp; Canada)");
	$timezones[]=array((-6.0*60*60), "-6:00", "Central Time (US &amp; Canada), Mexico City");
	$timezones[]=array((-5.0*60*60), "-5:00", "Eastern Time (US &amp; Canada), Bogota, Lima");
	$timezones[]=array((-4.0*60*60), "-4:00", "Atlantic Time (Canada), Caracas, La Paz");
	$timezones[]=array((-3.5*60*60), "-3:30", "Newfoundland");
	$timezones[]=array((-3.0*60*60), "-3:00", "Brazil, Buenos Aires, Georgetown");
	$timezones[]=array((-2.0*60*60), "-2:00", "Mid-Atlantic");
	$timezones[]=array((-1.0*60*60), "-1:00", "Azores, Cape Verde Islands");
	$timezones[]=array((0.0*60*60), "00:00", "Western Europe Time, London, Lisbon, Casablanca");
	$timezones[]=array((1.0*60*60), "+1:00", "Brussels, Copenhagen, Madrid, Paris");
	$timezones[]=array((2.0*60*60), "+2:00", "Kaliningrad, South Africa");
	$timezones[]=array((3.0*60*60), "+3:00", "Baghdad, Riyadh, Moscow, St. Petersburg");
	$timezones[]=array((3.5*60*60), "+3:30", "Tehran");
	$timezones[]=array((4.0*60*60), "+4:00", "Abu Dhabi, Muscat, Baku, Tbilisi");
	$timezones[]=array((4.5*60*60), "+4:30", "Kabul");
	$timezones[]=array((5.0*60*60), "+5:00", "Ekaterinburg, Islamabad, Karachi, Tashkent");
	$timezones[]=array((5.5*60*60), "+5:30", "Bombay, Calcutta, Madras, New Delhi");
	$timezones[]=array((5.75*60*60), "+5:45", "Kathmandu");
	$timezones[]=array((6.0*60*60), "+6:00", "Almaty, Dhaka, Colombo");
	$timezones[]=array((7.0*60*60), "+7:00", "Bangkok, Hanoi, Jakarta");
	$timezones[]=array((8.0*60*60), "+8:00", "Beijing, Perth, Singapore, Hong Kong");
	$timezones[]=array((9.0*60*60), "+9:00", "Tokyo, Seoul, Osaka, Sapporo, Yakutsk");
	$timezones[]=array((9.5*60*60), "+9:30", "Adelaide, Darwin");
	$timezones[]=array((10.0*60*60), "+10:00", "Eastern Australia, Guam, Vladivostok");
	$timezones[]=array((11.0*60*60), "+11:00", "Magadan, Solomon Islands, New Caledonia");
	$timezones[]=array((12.0*60*60), "+12:00", "Auckland, Wellington, Fiji, Kamchatka");

	return $timezones;

}


function get_tz_options()
{


	$all = timezone_identifiers_list();
	//return $all;
    $i = 0;
	$dateTimeObj=new DateTime();
	$city=array();
	$offset_for_sort=array();
    foreach($all AS $eachzone) {
      $zone = explode('/',$eachzone);

	  if($zone[0] == 'Africa' || $zone[0] == 'America' || $zone[0] == 'Antarctica' || $zone[0] == 'Arctic' || $zone[0] == 'Asia' || $zone[0]== 'Atlantic' || $zone[0]== 'Australia' || $zone[0] == 'Europe' || $zone[0]== 'Indian' || $zone[0] == 'Pacific') {

			$zonen[$i]['continent'] = isset($zone[0]) ? $zone[0] : '';
			$zonen[$i]['zone']=$eachzone;

			switch(strtolower($eachzone)){
				case 'asia/kolkata':
				case 'asia/calcutta':
							$zonen[$i]['city'] ='Calcutta, Kolkata, Mumbai, Chennai, Delhi, Bangalore, Ahmedabad';
							break;
				case 'asia/karachi':
							$zonen[$i]['city'] ='Islamabad, Karachi, Peshawar, Lahore';
							break;
				default: 	$zonen[$i]['city'] = isset($zone[1]) ? $zone[1] : '';
							$zonen[$i]['city'] .= isset($zone[2]) ? '/'.$zone[2] : '';


			}

			$city[]=$zonen[$i]['city'];

			$offsetinsec=timezone_offset_get(new DateTimeZone($eachzone),$dateTimeObj);

			$offset_for_sort[]=$offsetinsec;

			$offsetinmin=(int)($offsetinsec/60);
			$offsetinhr=(int)($offsetinmin/60);
			$offesetinmin=$offsetinmin%60;

			$zonen[$i]['zone_utc_offset']=(($offsetinsec>=0)?'+':'-').str_pad(abs($offsetinhr),2,'0',STR_PAD_LEFT).':'.str_pad($offesetinmin,2,'0',STR_PAD_LEFT);

			$i++;
	  }

    }

    array_multisort($offset_for_sort,SORT_ASC,SORT_NUMERIC,
    				$city,SORT_ASC,SORT_STRING,
    				$zonen);

   /* echo '<br><pre>';
    print_r($offset_for_sort);
    print_r($city);
    print_r($zonen);
    echo '</pre><br>';

    asort($zonen);
    */
	return array_merge($zonen);

	/*
    $structure = '';
    $continent='';
	foreach($zonen AS $zone) {
		if($zone['continent']!=$continent){
			$timezones[strtolower($zone['continent'])]['continent']=$continent;
			$timezones[strtolower($zone['continent'])]['zones']=array();
			$continent=$zone['continent'];

		}



	}
    foreach($zonen AS $zone) {
      extract($zone);
      if($continent == 'Africa' || $continent == 'America' || $continent == 'Antarctica' || $continent == 'Arctic' || $continent == 'Asia' || $continent == 'Atlantic' || $continent == 'Australia' || $continent == 'Europe' || $continent == 'Indian' || $continent == 'Pacific') {
        if(!isset($selectcontinent)) {
          $structure .= '<optgroup label="'.$continent.'">'; // continent
		  $timezones[strtolower($continent)]['continent']=$continent;
        } elseif($selectcontinent != $continent) {
          $structure .= '</optgroup><optgroup label="'.$continent.'">'; // continent
		  $timezones[strtolower($continent)]['zones']=array();
        }

        if(isset($city) != ''){
          if (!empty($subcity) != ''){
            $city = $city . '/'. $subcity;
          }
          $structure .= "<option ".((($continent.'/'.$city)==$selectedzone)?'selected="selected "':'')." value=\"".($continent.'/'.$city)."\">".str_replace('_',' ',$city)."</option>"; //Timezone
        } else {
          if (!empty($subcity) != ''){
            $city = $city . '/'. $subcity;
          }
          $structure .= "<option ".(($continent==$selectedzone)?'selected="selected "':'')." value=\"".$continent."\">".$continent."</option>"; //Timezone
        }

        $selectcontinent = $continent;
      }
    }
    $structure .= '</optgroup>';
    return $structure;
  }
  echo timezonechoice($selectedzone);
  echo '</select>';
  echo '<span class="notes"> '.$desc.' </span></div>';
  */
}


function getCountries($db_conn,$status=-1){ // $status can be -1, 0 or 1
	$countries=array();
	$whereclause='';

	if($status===1 || $status===0)
		$whereclause=" AND `active`=$status";

	$sql="SELECT country_name from `".CONST_TBL_PREFIX."country` c WHERE 1 $whereclause";
	$res=$db_conn->query($sql);
	if($res===false)
		return false;
	while($row=$res->fetch(PDO::FETCH_ASSOC)){
		$countries[]=$row['country_name'];

	}

	return $countries;
}


function convertBetweenTimeZones($date_time_str, $tz_from, $tz_to, $format='Y-m-d H:i:s'){
	$date=new DateTime($date_time_str, new DateTimeZone($tz_from));
	$date->setTimeZone(new DateTimeZone($tz_to));
	return $date->format($format);
}


function sendSessionTimeOutNotification($showAlert='1'){
		if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){ // its an AJAX request
			//echo json_encode(array('SESS_EXPIRED'=>'1'));
			header("HTTP/1.0 401 Unauthorized Access", true, 401);
			die();
		}else{
			$jsdir=CONST_JAVASCRIPT_DIR;
			$custom_jsdir=CONST_THEMES_CUSTOM_JAVASCRIPT_PATH;
			$res_ver=RESOURCE_VERSION;

			echo <<<EOF

			<script type="text/javascript">
				window.jQuery || document.write("<script src='{$jsdir}jquery-3.6.0.min.js'>"+"<"+"/script>");
			</script>

			<script type='text/javascript' src="{$custom_jsdir}common-functions.{$res_ver}.js" ></script>

			<script type='text/javascript' >

			if(window.self!=window.top){
				window.top.common_js_funcs.notifyUserOfSessionExpiry();
			}else{
				common_js_funcs.notifyUserOfSessionExpiry();
			}
			</script>
EOF;
		}

}



function logErrorInFile($tm, $module_identifier, $details_json){

	if(!CONST_LOG_ERRORS || CONST_ERROR_LOG=='' || !file_exists(CONST_ERROR_LOG) || !is_writable(CONST_ERROR_LOG))
		return false;


	$msg = 'Error Dt-Tm: '.date('d-m-Y H:i:s',$tm).', Module/URL: '.$module_identifier.', Details: '.$details_json;
	return error_log("\n$msg\n",3,CONST_ERROR_LOG);

}

function logoutSession(){
	if(isset($_COOKIE['loggedin_user']))
		setcookie('loggedin_user',false,time()-31536000,CONST_APP_PATH_FROM_ROOT.'/',$_SERVER['HTTP_HOST'],false,true);
	if(isset($_COOKIE['expired']))
		setcookie('expired',false,time()-31536000,CONST_APP_PATH_FROM_ROOT.'/',$_SERVER['HTTP_HOST'],false,true);

	$_SESSION=array();

	// If it's desired to kill the session, also delete the session cookie.

	// Note: This will destroy the session, and not just the session data!

	if (isset($_COOKIE[session_name()]))
		setcookie(session_name(), '', time()-42000,CONST_SESSION_COOKIE_PATH);
	session_destroy();
}

function getRemoteIP(): string{
	$ip_address = '';
	//whether ip is from share internet
	if (!empty($_SERVER['HTTP_CLIENT_IP'])){
	  $ip_address = $_SERVER['HTTP_CLIENT_IP'];
	}
	else if (!empty($_SERVER['HTTP_TRUE_CLIENT_IP'])){
	  $ip_address = $_SERVER['HTTP_TRUE_CLIENT_IP'];
	}
	// for Cloudflare served web-app
	else if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])){
	  $ip_address = $_SERVER['HTTP_CF_CONNECTING_IP'];
	}
	//whether ip is from proxy
	elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
	  $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	//whether ip is from remote address
	elseif (!empty($_SERVER['REMOTE_ADDR'])){
	  $ip_address = $_SERVER['REMOTE_ADDR'];
	}
	  
	return $ip_address;
}

function _esc($text, $fetch = false){
	if($fetch)
		return htmlentities($text);	
	echo htmlentities($text);
}


function isDateValid($dt, $in_past = true, $in_future = true){ // yyyy-mm-dd or dd-mm-yyyy
	if(preg_match("/^(\d\d\d\d)-(\d\d)-(\d\d)$/", $dt, $matches)){
		$y = $matches[1];
		$m = $matches[2];
		$d = $matches[3];
	}else if(preg_match("/^(\d\d)-(\d\d)-(\d\d\d\d)$/", $dt, $matches)){
		$y = $matches[3];
		$m = $matches[2];
		$d = $matches[1];
	}

	if(!checkdate((int)$m, (int)$d, (int)$y))
		return false;
	$curr_dt = new \DateTime(date('Y-m-d'));
	$dt_obj = new \DateTime("$y-$m-$d");

	if(!$in_past && $dt_obj<$curr_dt || !$in_future && $dt_obj>$curr_dt)
		return false;
	return true;
}


function parseCSV($file, $file_mime_type = '', $col_headers = [], $first_data_row = -1, $col_nos_to_fetch = []){
	$result = ['ec'=>0, 'msg'=>'', 'data'=>[]];
	if($file=='' || !file_exists($file)){
		$result['ec'] = 1;	
		$result['msg'] = 'The given file was not found.';	
	}else{
		$mime = empty($file_mime_type)?mime_content_type($file):$file_mime_type;
		if(strcasecmp($mime,'text/csv')!==0 && strcasecmp($mime,'application/csv')!==0 && strcasecmp($mime,'application/vnd.ms-excel')!==0){
			$result['ec'] = 2;	
			$result['msg'] = 'Invalid file type.';			
		}else{
			$fp = fopen($file, 'r');
			if(!$fp){
				$result['ec'] = 3;	
				$result['msg'] = 'The file is not readable.';		
			}else{
				$column_headers_index = [];
				$header_row_index = -1; // the first row index is 0
				$index = -1;
				$colcount = !empty($col_headers)?count($col_headers):count($col_nos_to_fetch);
				if($colcount<=0){
					$result['ec'] = 4;	
					$result['msg'] = 'Column headers not specified.';					
				}else{
					if(!empty($col_nos_to_fetch)){
						sort($col_nos_to_fetch);
						foreach($col_nos_to_fetch as $col){
							$column_headers_index['col_'.$col] = $col;
						}
						$column_headers_index_flipped = array_flip($column_headers_index);
					}

					$col_headers = array_map('strtolower', $col_headers);
					$col_headers_flipped = array_flip($col_headers);
					while($row = fgetcsv($fp)){
						++$index;
						$row = array_map('trim', $row);
						if(empty($column_headers_index)){
							$row = array_map('strtolower', $row);
							if(count($row) >= $colcount){
								$res = array_intersect_key(array_flip($row), $col_headers_flipped);
								if(count($res)==$colcount){ // all the col headers are available in the row
									$header_row_index = $index; // header row found at index
									$column_headers_index = $res;
									$column_headers_index_flipped = array_flip($column_headers_index);
									if($first_data_row<0)
										$first_data_row = $index+1;
									else if($first_data_row <= $header_row_index){
										$result['ec'] = 5;	
										$result['msg'] = 'Specified data row index is invalid.';
										break; // break out of the loop
									}
								}
							}
						}else if($first_data_row<0){
							continue;
						}else if($index>=$first_data_row){
							if(!empty($row) && count($row) >= $colcount){
								$data_str = trim(implode('',$row));
								if(empty($data_str)){
									// blank row, continue to the next row
									continue;
								}

								$result['data'][] = array_combine(array_keys($column_headers_index), array_intersect_key($row, $column_headers_index_flipped ) );
							}else{
								$result['ec'] = 5;	
								$result['msg'] = 'Specified data row index is invalid.';
								$result['data'] = []; // empty the data collected till now
							}


						}
					}

				}

			}

		}

	}

	return $result;
	
}

function _p($var){
	echo '<pre>';
	_esc(print_r($var, true));
	echo '</pre>';
}

function getDaysInterval($date1, $date2) {
    $datetime1 = new \DateTime($date1);
    $datetime2 = new \DateTime($date2);

    $interval = $datetime1->diff($datetime2);

    return $interval->days;
}


function base64UrlEncode($data) {
    // url safe encoding
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}


function base64UrlDecode($data) {
    // decoding url safe encoded values
    $data = trim(strtr($data, '-_', '+/'));
    $data_len = strlen($data);
    $total_data_len = $data_len + ($data_len%4);
    return  base64_decode(str_pad($data, $total_data_len, '=', STR_PAD_RIGHT));
}

function generateRandomString($length = 5, $case_sensitive = true) {
	if($case_sensitive)
    	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    else	
    	$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    // Generate enough random bytes to cover the length
    $bytes = random_bytes($length);
    for ($i = 0; $i < $length; $i++) {
        // Map byte to character index using modulo
        $randomString .= $characters[ord($bytes[$i]) % $charactersLength];
    }
    return $randomString;
}

?>
