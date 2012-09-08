<?PHP
session_start();

include_once ("util.php");

$id = getId();

$token = getToken();

$args = array('id' => $id, 'token' => $token);

$response = getResponstWithPost("/api/user/logout/index.php?response_object=json", $args);
$data = json_decode($response);

// case별로 처리하기
switch ($data->result_code) {
	case 200 :
	// Success
		session_start();

		messageWithAlert("Logout Success");
		unsetId();
		unsetToken();

		redirectToURL("index.php");

		break;

	default :
		//messageWithAlert("Failed");
		echo "비정상종료";
		unsetId();
		unsetToken();
		redirectToURL("index.php", 3);
		break;
}

//redirectToURL("index.php",3);
?>