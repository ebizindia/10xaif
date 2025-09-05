<?php
namespace eBizIndia;
class Img{

	function __Construct(){
		ini_set('memory_limit',-1);

	}

	function cropImage($crop_w,$crop_h,$srcimage,&$srcimgrscr=null,&$trgtimgrsrc=null,$targetfilenamewithpath=null,$crop_start_x=0,$crop_start_y=0){
		if($srcimgrscr==null){
			list($src_w, $src_h) = getimagesize($srcimage);
			$crop_w=($crop_w>$src_w)?$src_w:$crop_w;
			$crop_h=($crop_h>$src_h)?$src_h:$crop_h;
			$src = imagecreatefromstring(file_get_contents($srcimage));
		}else{
			$src=&$srcimgrscr;
			$imgWd=imagesx($src);
			$imgHt=imagesy($src);
			$crop_w=($crop_w>$imgWd)?$imgWd:$crop_w;
			$crop_h=($crop_h>$imgHt)?$imgHt:$crop_h;

		}

		if($trgtimgrsrc==null)
			$canvas = imagecreatetruecolor($crop_w, $crop_h);
		else
			$canvas = &$trgtimgrsrc;

		imagecopy($canvas, $src, 0, 0, $crop_start_x, $crop_start_y, $crop_w, $crop_h);


		if($targetfilenamewithpath!=null && $targetfilenamewithpath!=''){
			$targetextension=substr($targetfilenamewithpath, strrpos($targetfilenamewithpath,'.')+1);
			switch(strtolower($targetextension)){
				case 'png':	$func='imagepng';
							break;
				case 'gif':	$func='imagegif';
							break;
				case 'jpg':
				case 'jpeg':	$func='imagejpeg';
								break;
				default: $func='imagepng';
			}
			if(!$func($canvas,$targetfilenamewithpath))
				return false;
		}elseif($trgtimgrsrc==null){
			header('Content-Type: image/png');
			if(!imagepng($canvas)){
				ob_end_clean();
				return false;
			}
		}
		return true;
	}



	function resizeImageAsSquare($source,$targetsize,$target='',$quality = 100){

		$temp=pathinfo($source);
		$srcextension=$temp['extension'];

		if($target!=''){
			$temp=pathinfo($target);
			$trgtextension=$temp['extension'];
		}else{
			$trgtextension=$srcextension;
		}


		switch(strtolower($srcextension))
		{
			case 'jpg':
			case 'jpeg':
			case 'pjpeg':
				if(!$src_img=imagecreatefromjpeg($source))
					return false;
				break;
			case 'png':
				if(!$src_img=imagecreatefrompng($source))
					return false;
				break;
			case 'gif':
				if(!$src_img=imagecreatefromgif($source))
					return false;
				break;
			default:
				if(!$src_img=imagecreatefromstring(file_get_contents($source)))
					return false;

		}

		$imgWd=imagesx($src_img);
		$imgHt=imagesy($src_img);
		$croprequired=false;
		$crop_start_y=0;
		$crop_start_x=0;
		if($imgWd<$imgHt){
			$targetWidth=$targetsize;
			$targetHeight=(int)(($imgHt/$imgWd)*$targetWidth);
			if($targetHeight>$targetsize){
				$crop_start_y=(int)(($targetHeight-$targetsize)/2);
				$croprequired=true;
			}
		}elseif($imgHt<$imgWd){
			$targetHeight=$targetsize;
			$targetWidth=(int)(($imgWd/$imgHt)*$targetHeight);
			if($targetWidth>$targetsize){
				$crop_start_x=(int)(($targetWidth-$targetsize)/2);
				$croprequired=true;
			}
		}else{
			$targetHeight=$targetWidth=$targetsize;

		}

		if(!$dst_img=imagecreatetruecolor($targetWidth,$targetHeight)){
			imagedestroy($src_img);
			return false;
		}
		if(!imagecopyresampled($dst_img,$src_img,0,0,0,0,$targetWidth,$targetHeight,$imgWd,$imgHt)){
			imagedestroy($dst_img);
			imagedestroy($src_img);
			return false;
		}
		if($croprequired){
			if(!$dst_cropped_img=imagecreatetruecolor($targetsize,$targetsize)){
				imagedestroy($dst_img);
				imagedestroy($src_img);
				return false;
			}
			if(!$this->cropImage($targetsize,$targetsize,'',$dst_img,$dst_cropped_img,null,$crop_start_x,$crop_start_y)){
				imagedestroy($dst_img);
				imagedestroy($src_img);
				imagedestroy($dst_cropped_img);
				return false;
			}

			$dst_img=&$dst_cropped_img;

		}


		switch(strtolower($trgtextension))
		{
			case 'jpg':
			case 'jpeg':
			case 'pjpeg':

				if($target!=''){
					return imagejpeg($dst_img,$target,$quality);
				}else{
					header('Content-Type: image/jpeg');
					if(!imagejpeg($dst_img)){
						header_remove("Content-Type");
						return false;
					}
				}

				break;
			case 'png':
				if($target!=''){
					return imagepng($dst_img,$target);
				}else{
					header('Content-Type: image/png');
					if(!imagepng($dst_img)){
						header_remove("Content-Type");
						return false;
					}
				}

				break;
			case 'gif':
				if($target!=''){
					return imagegif($dst_img,$target);
				}else{
					header('Content-Type: image/gif');
					if(!imagegif($dst_img)){
						header_remove("Content-Type");
						return false;
					}
				}

				break;
		}
		imagedestroy($dst_img);
		imagedestroy($src_img);
		if($dst_cropped_img){
			imagedestroy($dst_cropped_img);
		}
		return true;

	}


	function resizeImage($source,$th,$tw,$target='',$quality = 100){
		$temp=pathinfo($source);
		$srcextension=$temp['extension'];

		if($target!=''){
			$temp=pathinfo($target);
			$trgtextension=$temp['extension'];
		}else{
			$trgtextension=$srcextension;
		}


		switch(strtolower($srcextension))
		{
			case 'jpg':
			case 'jpeg':
			case 'pjpeg':
				if(!$src_img=imagecreatefromjpeg($source))
					return false;
				break;
			case 'png':
				if(!$src_img=imagecreatefrompng($source))
					return false;
				break;
			case 'gif':
				if(!$src_img=imagecreatefromgif($source))
					return false;
				break;
			default:
				if(!$src_img=imagecreatefromstring(file_get_contents($source)))
					return false;

		}

		$imgWd=imagesx($src_img);
		$imgHt=imagesy($src_img);
		$croprequired=false;
		$crop_start_y=0;
		$crop_start_x=0;
		if($imgWd<=$imgHt && $imgWd>=$tw){
			$targetWidth=$tw;
			$targetHeight=(int)(($imgHt/$imgWd)*$targetWidth);
			if($targetHeight>$th){
				$crop_start_y=(int)(($targetHeight-$th)/2);
				$croprequired=true;
			}
		}elseif($imgHt<=$imgWd && $imgHt>=$th){
			$targetHeight=$th;
			$targetWidth=(int)(($imgWd/$imgHt)*$targetHeight);
			if($targetWidth>$tw){
				$crop_start_x=(int)(($targetWidth-$tw)/2);
				$croprequired=true;
			}
		}else{
			$targetHeight=$th;
			$targetWidth=$tw;

		}


		if(!$dst_img=imagecreatetruecolor($targetWidth,$targetHeight)){
			imagedestroy($src_img);
			return false;
		}
		if(!imagecopyresampled($dst_img,$src_img,0,0,0,0,$targetWidth,$targetHeight,$imgWd,$imgHt)){
			imagedestroy($dst_img);
			imagedestroy($src_img);
			return false;
		}
		if($croprequired){
			if(!$dst_cropped_img=imagecreatetruecolor($tw,$th)){
				imagedestroy($dst_img);
				imagedestroy($src_img);
				return false;
			}
			if(!$this->cropImage($tw,$th,'',$dst_img,$dst_cropped_img,null,$crop_start_x,$crop_start_y)){
				imagedestroy($dst_img);
				imagedestroy($src_img);
				imagedestroy($dst_cropped_img);
				return false;
			}

			$dst_img=&$dst_cropped_img;

		}

		switch(strtolower($trgtextension))
		{
			case 'jpg':
			case 'jpeg':
			case 'pjpeg':
				header('Content-Type: image/jpeg');
				if(!($target!=''?imagejpeg($dst_img,$target,$quality):imagejpeg($dst_img)))
					return false;
				break;
			case 'png':
				if($target!=''){
					if(!imagepng($dst_img,$target)){
						return false;
					}
				}else{
					header('Content-Type: image/png');
					if(!imagepng($dst_img)){
						return false;
					}
				}
				break;
			case 'gif':

					header('Content-Type: image/gif');
					if(!($target!=''?imagegif($dst_img,$target):imagegif($dst_img)))
						return false;
				break;
		}
		imagedestroy($dst_img);
		imagedestroy($src_img);
		if($dst_cropped_img){
			imagedestroy($dst_cropped_img);
		}
		return true;

	}



	function resizeImageWithADimensionFixed($source,$fxdmsz,$othdmsz=null,$target='',$dmtofx='HT',$cropeqally=false, $quality = 100){

		$temp=pathinfo($source);
		$srcextension=$temp['extension'];

		if($target!=''){
			$temp=pathinfo($target);
			$trgtextension=$temp['extension'];
		}else{
			$trgtextension=$srcextension;
		}


		switch(strtolower($srcextension))
		{
			case 'jpg':
			case 'jpeg':
			case 'pjpeg':
				if(!$src_img=imagecreatefromjpeg($source))
					return false;
				break;
			case 'png':
				if(!$src_img=imagecreatefrompng($source))
					return false;
				break;
			case 'gif':
				if(!$src_img=imagecreatefromgif($source))
					return false;
				break;
			default:
				if(!$src_img=imagecreatefromstring(file_get_contents($source)))
					return false;

		}

		$imgWd=imagesx($src_img);
		$imgHt=imagesy($src_img);

		$cropping=0;
		$crop_start_y=0;
		$crop_start_x=0;

		if($dmtofx=='WD'){
			// fix the width, calculate height
			$targetWidth=$tw=$fxdmsz;
			$th=$othdmsz;
			$targetHeight=(int)(($imgHt/$imgWd)*$targetWidth);
			if($othdmsz!=null && $targetHeight>$othdmsz){
				$cropping=1;
				$dmtocrop='HT';
				if($cropeqally)
					$crop_start_y=(int)(($targetHeight-$othdmsz)/2);

			}

		}else{
			// fix the height, calculate width
			$targetHeight=$th=$fxdmsz;
			$tw=$othdmsz;
			$targetWidth=(int)(($imgWd/$imgHt)*$targetHeight);
			if($othdmsz!=null && $targetWidth>$othdmsz){
				$cropping=1;
				$dmtocrop='WD';
				if($cropeqally)
					$crop_start_x=(int)(($targetWidth-$othdmsz)/2);
			}
		}



		if(!$dst_img=imagecreatetruecolor($targetWidth,$targetHeight)){
			imagedestroy($src_img);
			return false;
		}
		if(!imagecopyresampled($dst_img,$src_img,0,0,0,0,$targetWidth,$targetHeight,$imgWd,$imgHt)){
			imagedestroy($dst_img);
			imagedestroy($src_img);
			return false;
		}

		if($cropping==1){
			// cropping required
			if(!$dst_cropped_img=imagecreatetruecolor($tw,$th)){
				imagedestroy($dst_img);
				imagedestroy($src_img);
				return false;
			}
			if(!$this->cropImage($tw,$th,'',$dst_img,$dst_cropped_img,null,$crop_start_x,$crop_start_y)){
				imagedestroy($dst_img);
				imagedestroy($src_img);
				imagedestroy($dst_cropped_img);
				return false;
			}
			$dst_img=&$dst_cropped_img;

		}

		switch(strtolower($trgtextension))
		{
			case 'jpg':
			case 'jpeg':
			case 'pjpeg':
				header('Content-Type: image/jpeg');
				if(!($target!=''?imagejpeg($dst_img,$target,$quality):imagejpeg($dst_img)))
					return false;
				break;
			case 'png':
				header('Content-Type: image/png');
				if(!($target!=''?imagepng($dst_img,$target):imagepng($dst_img)))
					return false;
				break;
			case 'gif':
				header('Content-Type: image/gif');
				if(!($target!=''?imagegif($dst_img,$target):imagegif($dst_img)))
					return false;
				break;
		}
		imagedestroy($dst_img);
		imagedestroy($src_img);
		if($dst_cropped_img){
			imagedestroy($dst_cropped_img);
		}
		return true;

	}


	function createThumbnail($sourcefile,$thumbNail,$thumbWidth,$thumbHeight, $extension='',$outputToBrowser=false)
	{
		if($extension=='')
			$extension=substr($sourcefile, strrpos($sourcefile,'.')+1);

		$src_img=imagecreatefromstring(file_get_contents($sourcefile));
		$imgWd=imagesx($src_img);
		$imgHt=imagesy($src_img);

		if($thumbWidth=='')
			$thumbWidth=$imgWd;
		if($thumbHeight=='')
			$thumbHeight=$imgHt;

		$thumb=$this->getThumbSize($imgWd, $imgHt, $thumbWidth, $thumbHeight);

		$dst_img=imagecreatetruecolor($thumb['width'],$thumb['height']);
		imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb['width'],$thumb['height'],$imgWd,$imgHt);
		switch(strtolower($extension))
		{
			case 'jpg':
			case 'jpeg':
			case 'pjpeg':

				if($outputToBrowser===false)
					imagejpeg($dst_img,$thumbNail);
				else{
					header('Content-Type: image/jpeg');
					imagejpeg($dst_img);
				}

				break;
			case 'png':
				if($outputToBrowser===false)
					imagepng($dst_img,$thumbNail);
				else{
					header('Content-Type: image/png');
					imagepng($dst_img);
				}
				break;
			case 'gif':
				if($outputToBrowser===false)
					imagegif($dst_img,$thumbNail);
				else{
					header('Content-Type: image/gif');
					imagegif($dst_img);
				}
				break;
		}
		imagedestroy($dst_img);
		imagedestroy($src_img);
		return true;
	}


	function getThumbSize($imgWd, $imgHt, $thumbWd, $thumbHt)
	{
		$width=$imgWd;
		$height=$imgHt;
		if($imgWd>$thumbWd)
		{
			$width=$thumbWd;
			$height=$imgHt*$thumbWd/$imgWd;
			if($height>$thumbHt)
			{
				$width=$width*$thumbHt/$height;
				$height=$thumbHt;
			}
		}
		else if($imgHt>$thumbHt)
		{
			$height=$thumbHt;
			$width=$imgWd*$thumbHt/$imgHt;
			if($width>$thumbWd)
			{
				$height=$height*$thumbWd/$width;
				$width=$thumbWd;
			}
		}
		return array('height'=>$height, 'width'=>$width);
	}

}

?>
