<?PHP

// configurations
$configure['dbconn_path'] = '../../dbconn.php';
$configure['user_database_name'] = 'csuser';
$configure['user_information_table_name'] = 'users';
$configure['token_information_table_name'] = 'user_session';


$configure['token_timeout'] = 30; // 30 minutes
	

// result messages and codes
$configure['results']['invalid_argument']['message'] = "Invalid Argument";
$configure['results']['invalid_argument']['code'] = 500;

$configure['results']['success']['message'] = "Success";
$configure['results']['success']['code'] = 200;

$configure['results']['failed_authentication']['message'] = "Failed to authenticate";
$configure['results']['failed_authentication']['code'] = 401;

$configure['results']['conflict']['message'] = "Conflict occured";
$configure['results']['conflict']['code'] = 409;

$configure['results']['internal_error']['message'] = "Internal Server Error";
$configure['results']['internal_error']['code'] = 500;

?>