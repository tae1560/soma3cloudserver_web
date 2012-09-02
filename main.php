<?PHP echo '<' . '?xml version="1.0" encoding="utf-8"?' . '>';?>
<?PHP
	include_once 'file_management.php';
?>
<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title>Cloud Storage</title>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <link type="text/css" rel="stylesheet" href="styles/common.css" />
        <link type="text/css" rel="stylesheet" href="styles/login.css" />
        <script type="text/javascript" charset="utf-8">
			function redirectToLogout() {
				window.location = "logout.php";
			}
			function downloadFile(path) {
				// post_to_url(path, params, method)
				var session_id = '<?PHP echo $_SESSION['id'];?>';
				var session_token = '<?PHP echo $_SESSION['token'];?>';
				post_to_url("api/file/download/", {'id':session_id, 'token':session_token, 'filepath':path});
			}
        </script>
        <script src="scripts/utils.js"></script>
    </head>
    <body>
    	
        <div class="wrapper">
            <div class="center title">
                <?PHP echo $_SESSION['id'] . "님 로그인을 환영합니다.";?>
            </div>
            <div style="width: 30%; margin: auto;">
                <table class="center">
                    <thead>
                        <th>이름</th>
                        <th>종류</th>
                        <th>파일크기(byte)</th>
                    </thead>
                    <?PHP
$list = get_list($_SESSION['id'], $_SESSION['token']);
foreach ($list as $one) {
                    ?>
                    <tr>
                        <td><a href="#" onclick="downloadFile('<?PHP echo $one -> name;?>')"><?PHP echo $one -> name;?></a></td> 
                        <td><?PHP echo $one -> type;?></td>
                        <td><?PHP echo $one -> size;?></td>
                    </tr>
                    <?PHP
						}
                    ?>
                </table>
            </div>
            <form enctype="multipart/form-data" action="api/file/upload/" method="POST">
                <!-- input의 name은 $_FILES 배열의 name을 결정합니다 -->
                <div class="center">
                    이 파일을 전송합니다:
                    <input type="file" name="files[]" multiple>
                    <input type="submit" value="파일 전송" />
                    <input type="hidden" name="id" value="<?PHP echo $_SESSION['id'];?>" />
                    <input type="hidden" name="token" value="<?PHP echo $_SESSION['token'];?>" />
                </div>
            </form>
            <form accept-charset="UTF-8" action="login_check.php" class="simple_form user" id="user_new" method="post">
                <div class="block center login small">
                    <div class="block_content">
                        <input class="half-width-button" id="user_logout" name="logout" type="button" value="Logout!" onclick="redirectToLogout();">
                    </div>
                </div>
            </form>
        </div>
    </body>
</html>