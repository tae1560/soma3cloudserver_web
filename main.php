<?PHP echo '<' . '?xml version="1.0" encoding="utf-8"?' . '>';?>

<?PHP
	include_once 'util.php';
	if (!validate_session())
		redirectToURL("index.php");

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
        <script type="text/javascript" src="scripts/jquery-1.8.1.min.js"></script>
        <script type="text/javascript">
			var temp = false;
			$(document).ready(function() {// 페이지가 로딩이 완료되면(이미지제외)

				$('#fileupload :file').change(function() {
					var file = this.files[0];
					name = file.name;
					size = file.size;
					type = file.type;
					//your validation
				});

				$('#fileupload :button').click(function() {
					console.log($('#fileupload :file'));
					
					var formData = new FormData($('#fileupload')[0]);
					$.ajax({
						url : 'api/file/upload/index.php?response_object=json', //server script to process data
						type : 'POST',
						xhr : function() {// custom xhr
							myXhr = $.ajaxSettings.xhr();
							if(myXhr.upload) {// check if upload property exists
								myXhr.upload.addEventListener('progress', function(e) {
									if(e.lengthComputable) {
										$('progress').attr({
											value : e.loaded,
											max : e.total
										});
									}
								}, false);
								// for handling the progress of the upload
							}
							return myXhr;
						},
						//Ajax events
						//beforeSend : beforeSendHandler,
						success : function(data) {
							console.log(data);
							location.reload();
						},
						//error : errorHandler,
						// Form data
						data : formData,
						//Options to tell JQuery not to process data or worry about content-type
						cache : false,
						contentType : false,
						processData : false
					});
				});
			});

        </script>
        <script type="text/javascript" charset="utf-8">
			function redirectToLogout() {
				window.location = "logout.php";
			}

			function downloadFile(path) {
				// post_to_url(path, params, method)

				post_to_url("api/file/download/", {
					'id' : session_id,
					'token' : session_token,
					'filepath' : path
				});
			}
        </script>
        <script src="scripts/utils.js"></script>
    </head>
    <body>
        <div class="wrapper">
            <div class="center title">
                <?PHP echo $_SESSION['id'] . "님 로그인을 환영합니다.";?>
            </div>
            <div id="foldertree">
                /
            </div>
            <div class="center">
                <table>
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
            <form id="fileupload" enctype="multipart/form-data" action="api/file/upload/" method="POST">
                <!-- input의 name은 $_FILES 배열의 name을 결정합니다 -->
                <div class="center">
                    이 파일을 전송합니다:
                    <input type="file" name="files[]" multiple>
                    <input type="button" name="send" value="파일 전송" />
                    <input type="hidden" name="id" value="<?PHP echo $_SESSION['id'];?>" />
                    <input type="hidden" name="token" value="<?PHP echo $_SESSION['token'];?>" />
                    <progress></progress>
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