<?php

include_once "util.php";

// args
$id = $_POST['id'];
$password = $_POST['password'];

$args = array('id' => $id, 'hashedPassword' => md5($password) );
$response = getResponstWithPost("/api/user/get_token/index.php?response_object=json", $args);
$data = json_decode($response);
//echo $response;

// case별로 처리하기
switch ($data->result_code) {
	case 200: // Success
		session_start();
	
		echo "Login Success";
		setId($id);
		setToken($data->token);
		
		redirectToURL("index.php");
		  
		break;
	
	default:
		messageWithAlert("Failed to login : ".$data->result_code." ".$data->result);
		redirectToURL("login.php",3);
		break;
}
?>