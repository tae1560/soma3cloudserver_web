<?PHP

// return : true if login status
function isLogin() {
	if ($_SESSION['token'] == null) {
		return false;
	} 
	
	return true;
}

function loginPage() {
	include_once "login.php";
}


// args : reference of variable for return with json
function process() {
	loginPage();
	
	
}

function main() {
	$returnValue = process();
	//echo test;

	// output return value
	//output_result($returnValue);
}

// start main
main();
?>