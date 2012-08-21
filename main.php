<?PHP echo '<' . '?xml version="1.0" encoding="utf-8"?' . '>'; ?>

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
        	var session_id =<?PHP echo "\"" . $_SESSION['id'] . "\""; ?>;
			var session_token = <?PHP echo "\"" . $_SESSION['token'] . "\""; ?>;
	var temp;
	var current_path;

	$(document).ready(function() {// 페이지가 로딩이 완료되면(이미지제외)

		addClickListenerToFolder($('#root_folder li'));
		$('#root_folder li').click();
		
		$('progress').hide();

		$('#fileupload :file').change(function() {
			var file = this.files[0];
			name = file.name;
			size = file.size;
			type = file.type;
			//your validation
		});

		// file upload part
		$('#fileupload input[name=send]').click(function() {
			//console.log($('#fileupload :file'));

			var formData = new FormData($('#fileupload')[0]);
			//var formData = new FormData();
			//formData.append("files", $("input[name='files[]']")[0].files);
			formData.append("folderpath",current_path+"/");
			formData.append("id", session_id);
			formData.append("token",session_token);
			console.log(formData);
			console.log(formData.toString());
			$.ajax({
				url : 'api/file/upload/index.php?response_object=json', //server script to process data
				type : 'POST',
				xhr : function() {// custom xhr
					myXhr = $.ajaxSettings.xhr();
					if (myXhr.upload) {// check if upload property exists
						myXhr.upload.addEventListener('progress', function(e) {
							if (e.lengthComputable) {
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
				beforeSend : function(e) {
					$('progress').show();
				},
				success : function(data) {
					console.log(data);
					$('progress').hide();
					//location.reload();
					loadFileTableWithPath(current_path);
				},
				error : function(data){
					console.log(data);
					$('progress').hide();
					//location.reload();
					loadFileTableWithPath(current_path);
				},
				// Form data
				data : formData,
				// data : {
					// formData : formData,
					// id : session_id,
					// token : session_token,
					// folderpath: current_path + "/"
				// },
				//Options to tell JQuery not to process data or worry about content-type
				cache : false,
				contentType : false,
				processData : false
			});
		});
		
		// folder_maker
		$('#folder_maker input[name=make_folder]').click(function(){
			var data = $('#folder_maker input[name=foldername]').attr("value");
			//console.log($('#folder_maker input[name=foldername]'));
			//console.log(data);
			if (data == null || data == "") {
				alert("폴더명을 입력해 주세요");
				$('#folder_maker input[name=foldername]').focus();
			}
			else make_dir(current_path + "/" + data);
		});

		
	});

	// ul태그를 받고 그 ul태그에 맞는 패스를 구하고 ul태그에 합침
	function getULWithArray(array, path) {
		var ul = $("<ul></ul>").clone().
		attr("path", path);

		for (var i = 0; i < array.length; i++) {

			if (array[i]['type'] == "dir") {
				var newList = $("<li></li>").appendTo(ul).
				html(array[i]['name']).
				attr("path", path + "/" + array[i]['name']);
				addClickListenerToFolder(newList);
			};
		};

		return ul;
	}

	function expandFolder(obj) {
		// obj : folder
		var iter = obj;
		
		var path = obj.attr("path");
		if (!path) obj.attr("path", "/");
		path = obj.attr("path");

		// get folder list
		$.ajax({
			url : 'api/file/get_list/index.php?response_object=json', //server script to process data
			type : 'POST',
			success : function(data) {
				var json_data = json_parse(data);
				var list = json_data['list'];
				// console.log("ajax : " + path);
				// console.log("data : " + data);
				// console.log("list : " + list);
				
				if(list) {
					var ul = getULWithArray(list, path);
					var temp = $('#root_folder li[path="'+path+'"]');
					//console.log(temp);
					//ul.appendTo(temp.parent());
					temp.after(ul);
				}
				
			},
			error : function(data) {
				console.log("error");
			},
			data : {
				id : session_id,
				token : session_token,
				folderpath: path
			}
			//Options to tell JQuery not to process data or worry about content-type
		});
		
	}
	
	function folderClickListener(obj) {
		
		var path = obj.attr("path");
		//console.log(path);
		 
		if (path) {
			if ($("UL[path='"+path+"']")[0]) {
				$("UL[path='"+path+"']").remove();
			} else {
				expandFolder(obj);
			}
		} else {
			expandFolder(obj);
		}
		
		loadFileTableWithPath(obj.attr("path"));
	}
	
	function addClickListenerToFolder(obj) {
		obj.click(function(e) {
			folderClickListener($(this));
		}).
		addClass("clickable");
	} 

	function loadFileTableWithPath(path) {
		current_path = path;

		$.ajax({
			url : 'api/file/get_list/index.php?response_object=json', //server script to process data
			type : 'POST',
			//Ajax events
			//beforeSend
			success : function(data) {
				var json_data = json_parse(data);
				var list = json_data['list'];

				$("#folder_content").empty();

				// table header
				var table = $("<table></table>").clone();
				var thead = $("<thead></thead>").appendTo(table);
				$("<th>이름</th>").appendTo(thead);
				$("<th>종류</th>").appendTo(thead);
				$("<th>파일크기(byte)</th>").appendTo(thead);
				$("<th>파일삭제</th>").appendTo(thead);
				
				// .. record
				var subTr = $("<tr></tr>").appendTo(table);
				$("<td>..</td>").appendTo(subTr).click(function() {
					//var path = $(this).parent().attr("path");
					//console.log($(this));
					//if (path) alert(path);
					//dirname(current_ath);
					loadFileTableWithPath(dirname(current_path));
				}).addClass("clickable");
				$("<td></td>").appendTo(subTr);
				$("<td></td>").appendTo(subTr);
				$("<td></td>").appendTo(subTr);
				
				// dir part
				if (list) {
					for (var i = 0; i < list.length; i++) {
						if (list[i]['type'] == "dir") {
							var tr = $("<tr></tr>").appendTo(table);
							$("<td>" + list[i]['name'] + "</td>").appendTo(tr).click(function() {
								loadFileTableWithPath(current_path + "/" + $(this).attr("filename"));
							}).addClass("clickable").attr("filename", list[i]['name']);
							$("<td>" + list[i]['type'] + "</td>").appendTo(tr);
							$("<td>" + list[i]['size'] + "</td>").appendTo(tr);
							$("<td>삭제</td>").appendTo(tr).click(function() {
								deleteFile(current_path + "/" + $(this).attr("filename"));
							}).addClass("clickable").attr("filename", list[i]['name']);
						}
					};					
				}
				
				// file part
				if (list) {
					for (var i = 0; i < list.length; i++) {
						
						if (list[i]['type'] !== "dir") {
							var tr = $("<tr></tr>").appendTo(table);
							$("<td>" + list[i]['name'] + "</td>").appendTo(tr).click(function() {
								downloadFile(current_path + $(this).attr("filename"));
							}).addClass("clickable").attr("filename", list[i]['name']);
							$("<td>" + list[i]['type'] + "</td>").appendTo(tr);
							$("<td>" + list[i]['size'] + "</td>").appendTo(tr);
							$("<td>삭제</td>").appendTo(tr).click(function() {
								//console.log(current_path + "/" + $(this).attr("filename"));
								deleteFile(current_path + "/" + $(this).attr("filename"));
							}).addClass("clickable").attr("filename", list[i]['name']);
						}
					};					
				}

				table.appendTo($("#folder_content"));
			},
			//error : errorHandler,
			// Form data
			data : {
				id : session_id,
				token : session_token,
				folderpath : path
			}
			//Options to tell JQuery not to process data or worry about content-type
		});
	}
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

			function deleteFile(path) {
				$.ajax({
					url : 'api/file/delete/index.php?response_object=json', //server script to process data
					type : 'POST',
					success : function(data) {
						//console.log(data);
						//location.reload();
						loadFileTableWithPath(current_path);
					},
					data : {
						id : session_id,
						token : session_token,
						filepath : path
					}
				});
			}

			function make_dir(path) {
				$.ajax({
					url : 'api/file/make_dir/index.php?response_object=json', //server script to process data
					type : 'POST',
					success : function(data) {
						//location.reload();
						loadFileTableWithPath(current_path);
					},
					data : {
						id : session_id,
						token : session_token,
						folderpath : path
					}
				});
			}
        </script>
        <script src="scripts/utils.js"></script>
    </head>
    <body>
        <div class="wrapper">
            <div class="center title">
                <?PHP echo $_SESSION['id'] . "님 로그인을 환영합니다."; ?>
            </div>
            <div id="foldertree">
                <ul id="root_folder">
                    <li>
                        /
                    </li>
                </ul>
            </div>
            <div id="folder_content" class="center">
			
            </div>
            <div id="folder_maker">
				새폴더 만들기 : 
				<input type="text" name="foldername" />
				<input type="button" name="make_folder" value="폴더 만들기" />
			</div>
            <form id="fileupload" enctype="multipart/form-data" action="api/file/upload/" method="POST">
                <!-- input의 name은 $_FILES 배열의 name을 결정합니다 -->
                <div class="center">
                    이 파일을 전송합니다:
                    <input type="file" name="files[]" multiple>
                    <input type="button" name="send" value="파일 전송" />
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