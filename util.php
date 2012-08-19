<?php

// args : request address and post arguments
// return : response text
function getResponstWithPost($address, $args) {
	$postdata = http_build_query($args);
	$opts = array('http' => array('method' => 'POST', 'header' => 'Content-type: application/x-www-form-urlencoded', 'content' => $postdata));
	$context = stream_context_create($opts);
	$response = file_get_contents("http://" . $_SERVER['HTTP_HOST'] . $address, false, $context);
	return $response;
}

function messageWithAlert($message) {
	echo "<script type=\"text/javascript\" charset=\"utf-8\">";
	echo "alert(\"$message\");";
	echo "</script>";
}

function redirectToURL($url, $delay=0) {
	echo "<script type=\"text/javascript\" charset=\"utf-8\">";
	echo "window.location = \"$url\";";
	echo "</script>";
}
    
?>