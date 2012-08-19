<?php

include_once "util.php";

// args
$id = $_POST['id'];
$password = $_POST['password'];

$args = array('id' => $id, 'hashedPassword' => md5($password) );
$response = getResponstWithPost("/api/user/get_token/index.php", $args);
$data = json_decode($response);
//echo $response;

// case별로 처리하기
switch ($data->result_code) {
	case 200: // Success
		session_start();
	
		echo "Login Success";
		$_SESSION['id'] = $id;
		$_SESSION['token'] = $data->token;
		
		redirectToURL("index.php");
		  
		break;
	
	default:
		messageWithAlert("Failed");
		redirectToURL("login.php",3);
		break;
}
?>