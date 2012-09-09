<?PHP

include_once "util.php";

// return : true if login status
function isLogin() {
	if (getId() == null || getToken() == null) {
		if(getId()) unsetId();
		if(getToken()) unsetToken();
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