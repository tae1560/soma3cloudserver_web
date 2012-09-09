<?PHP
include_once "../../configure.php";
include_once $configure['dbconn_path'];

function get_dir_size($path) // KB                           
{                       
    $result=explode("\t",exec("du -k -s ".$path),2);
    return ($result[1]==$path ? $result[0] : "error"); 
}

function getCurrentSpace($id) {
	global $configure;
	
	$dirpath = $configure['storage_dir'] . $id . "/";
	return get_dir_size($dirpath);
}

function getUserSpace($id) {
	global $configure;
	global $dbconn;

	$link = mysql_connect($dbconn['address'], $dbconn['id'], $dbconn['password']) or die("Could not connect<br>");
	$select = mysql_select_db($configure['user_database_name']);
	if (!$select) {
		echo "데이타베이스 선택시 오류가 발생하였습니다.";
		exit ;
	}

	// get user information from database
	$query = "SELECT * FROM " . $configure['user_information_table_name'] . " WHERE id='$id';";
	$result = mysql_query($query);
	if (!$result) {
		echo "질의 수행시 오류가 발생하였습니다.";
		exit ;
	}

	$rows = mysql_num_rows($result);
	
	if ($rows != 1) {
		return null;
	}
	
	$row = mysql_fetch_array($result);

	mysql_close($link);

	return $row['space']; // KB
}

function getFreeSpace ($id) {
	$user_space = getUserSpace($id);
	$current_space = getCurrentSpace($id);
	return $user_space - $current_space;  
}
// 남은용량 찾기 
?>