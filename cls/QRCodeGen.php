<?php
namespace eBizIndia;
require_once 'qr-code/vendor/autoload.php';
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Font\OpenSans;
use Endroid\QrCode\Writer\PngWriter;
Abstract class QRCodeGen{

	public static function createPng($text, $file_name_path, $size=300, $margin=30, $label='', $logo_path=''){
		try{
			$builder = new Builder(
			    writer: new PngWriter(),
			    writerOptions: [],
			    validateResult: false,
			    data: $text,
			    encoding: new Encoding('UTF-8'),
			    errorCorrectionLevel: ErrorCorrectionLevel::High,
			    size: $size,
			    margin: $margin,
			    roundBlockSizeMode: RoundBlockSizeMode::Margin,
			    logoPath: $logo_path,
			    logoResizeToWidth: 50,
			    logoPunchoutBackground: true,
			    labelText: $label,
			    labelFont: new OpenSans(12),
			    labelAlignment: LabelAlignment::Center,
			);
			$result = $builder->build();
			if(!empty($file_name_path))
				$result->saveToFile($file_name_path);
			else{
				ob_clean();
				header('Content-Type: ' . $result->getMimeType());
				echo $result->getString();
				exit;

			}
			return true;
		}catch(\Exception $e){
			return false;
		}
	}
}