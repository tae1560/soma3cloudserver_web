<?php

include_once "util.php";

$args = array('id' => $_POST['id'], 'hashedPassword' => md5($_POST['password']) );
$response = getResponstWithPost("/api/user/join/index.php", $args);
$data = json_decode($response);
echo $response;

// case별로 처리하기
switch ($data->result_code) {
	case 409: // Conflict occured
		messageWithAlert("같은 아이디가 존재합니다.");
		redirectToURL("Join.php");
		break;
	
	default:
		messageWithAlert("Failed");
		redirectToURL("Join.php",3);
		break;
}
?>