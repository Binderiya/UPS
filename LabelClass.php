<?php

// Shipping Label definition
class Label {
	public $ship_tool
  	public $outputFileName = "labels/";

	public function save($response){
		$ext=$ship_tool->EPLlabel=='PNG' ? "gif" : "txt";
		$labelName=$ship_tool->trackingnumber.".".$ext;
		$fp = fopen($outputFileName.$labelName, 'wb');   	        	        
		fwrite($fp, base64_decode($response->ShipmentResults->PackageResults->ShippingLabel->GraphicImage));
		fclose($fp);
	  return $labelName;      
	}
	
	public static function set_epl(){
		$labelimageformat['Code'] = 'EPL';
    	$labelstocksize['Height']=6;
    	$labelstocksize['Width']=4;
    	$labelspecification['LabelStockSize']=$labelstocksize;
    	$labelspecification['LabelImageFormat'] = $labelimageformat;
    	return $labelspecification;
	}
	
	public static function set_png(){
		$labelimageformat['Code'] = 'GIF';
    	$labelimageformat['Description'] = 'GIF';
   		$labelspecification['LabelImageFormat'] = $labelimageformat;
   	}
}