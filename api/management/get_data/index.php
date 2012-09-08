<?PHP
// configuration
$configure['state_database_name'] = "csstate";
$configure['lvm_state_table_name'] = "lvm_state";
$configure['storage_state_table_name'] = "storage_state";

$configure['statistics_database_name'] = "csstatistics";
$configure['lvm_statistics_table_name'] = "lvm_states";
$configure['storage_statistics_table_name'] = "storage_states";

// connect to db
include_once "../../dbconn.php";

$link = mysql_connect($dbconn['address'], $dbconn['id'], $dbconn['password']) or die("Could not connect<br/>");

$category = $_GET['category'];
if ($category == "lvm_state") {
	$select = mysql_select_db($configure['state_database_name']);
	$query = "SELECT * FROM " . $configure['lvm_state_table_name'] . ";";
} else if ($category == "storage_state") {
	$select = mysql_select_db($configure['state_database_name']);
	$query = "SELECT * FROM " . $configure['storage_state_table_name'] . ";";
} else if ($category == "lvm_statistics") {
	$select = mysql_select_db($configure['statistics_database_name']);
	$query = "SELECT * FROM " . $configure['lvm_statistics_table_name'] . " LIMIT 0, 60;";
} else if ($category == "storage_statistics") {
	$select = mysql_select_db($configure['statistics_database_name']);
	$query = "SELECT * FROM " . $configure['storage_statistics_table_name'] . "LIMIT 0, 60;";
} else {
	echo "Wrong category";
	exit;
}

if (!$select) {
	echo "데이타베이스 선택시 오류가 발생하였습니다." . mysql_error();
	exit ;
}

$result = mysql_query($query);
if (!$result) {
	echo "질의 수행시 오류가 발생하였습니다.";
	exit ;
}

$number_of_rows = mysql_num_rows($result);
for ($i = 0; $i < $number_of_rows; $i++) {
	//$row = mysql_fetch_array($result);
	$rows[] = mysql_fetch_array($result);
}

$json_data = json_encode($rows);

// disconnect from db
mysql_close($link);

echo $json_data;
?>