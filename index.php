<?PHP

session_start();

// return : true if login status
function isLogin() {
	if ($_SESSION['token'] == null) {
		return false;
	} 
	
	return true;
}

function mainPage() {
	include_once "main.php";
}

function loginPage() {
	include_once "login.php";
}


// args : reference of variable for return with json
function process() {
	if (isLogin()) {
		mainPage();
	} else {
		loginPage();		
	}
	
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