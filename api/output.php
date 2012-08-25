<?php
function array_to_xml($array, $xml = false){
	// convert array to xml
	
    if($xml === false){
        $xml = new SimpleXMLElement('<root/>');
    }
    foreach($array as $key => $value){
        if(is_array($value)){
            array2xml($value, $xml->addChild($key));
        }else{
            $xml->addChild($key, $value);
        }
    }
    return $xml->asXML();
}
function output_result($array) {
	// DEBUG : only json mode
	echo json_encode($array);
	return;
	
	
	if (strtolower($_GET['response_object']) == "json") {
		// output return value as json
		echo json_encode($array);
	
	} else {
		// output return value as xml
		
		header('Content-type: text/xml');
		print array_to_xml($array);		
	}
}
?>