<?PHP echo '<' . '?xml version="1.0" encoding="utf-8"?' . '>';?>

<?PHP
	// configuration
	$configure['state_database_name'] = "csstate";
	$configure['lvm_state_table_name'] = "lvm_state";
	$configure['storage_state_table_name'] = "storage_state";
?>

<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title>the singer invitation - purchase</title>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <link type="text/css" rel="stylesheet" href="styles/common.css" />
        <link type="text/css" rel="stylesheet" href="styles/management.css" />
        <link rel="stylesheet" type="text/css" href="jqplot/src/jquery.jqplot.css" />
        <link rel="stylesheet" type="text/css" href="jqplot/examples.css" />
        <!-- BEGIN: load jquery -->
        <script language="javascript" type="text/javascript" src="jqplot/src/jquery.js"></script>
        <!-- END: load jquery -->
        <!-- BEGIN: load jqplot -->
        <script language="javascript" type="text/javascript" src="jqplot/src/jquery.jqplot.js"></script>
        <script language="javascript" type="text/javascript" src="jqplot/src/plugins/jqplot.logAxisRenderer.js"></script>
        <script language="javascript" type="text/javascript" src="jqplot/src/plugins/jqplot.dateAxisRenderer.js"></script>
        <script language="javascript" type="text/javascript" src="jqplot/src/plugins/jqplot.highlighter.js"></script>
        <script language="javascript" type="text/javascript" src="jqplot/src/plugins/jqplot.cursor.js"></script>
        <!-- END: load jqplot -->
        <style type="text/css" media="screen">
            .jqplot-axis {
                font-size: 0.85em;
            }
        </style>
        <script type="text/javascript" language="javascript">
			$(document).ready(function() {
				$.jqplot.config.enablePlugins = true;
				var goog = [["2009/6/22 2:12", 425.32], ["2009/6/22 9:11", 420.09], ["2009/6/22 9:13", 424.84], ["2009/6/22 9:14", 444.32], ["2009/6/22 10:15", 417.23], ["2009/6/22 12:15", 393.5]];
				plot = $.jqplot('chart', [goog], {
					title : 'LVM Statistics',
					series : [{
						label : 'LVM Statistics',
						neighborThreshold : -1
					}],
					axes : {
						xaxis : {
							renderer : $.jqplot.DateAxisRenderer,
							min : '2009/6/22 1:00',
							tickInterval : "1 hours",
							tickOptions : {
								formatString : "%H:%M"
							}
						},
						yaxis : {
							renderer : $.jqplot.LogAxisRenderer,
							tickOptions : {
								formatString : '$%.2f'
							}
						}
					},
					cursor : {
						zoom : false,
						showTooltip : false,
						clickReset : true
					}
				});
			});

        </script>
    </head>
    <body>
        <?PHP
		// connect to db
		include_once "api/dbconn.php";

		$link = mysql_connect($dbconn['address'], $dbconn['id'], $dbconn['password']) or die("Could not connect<br>");
		$select = mysql_select_db($configure['state_database_name']);
		if (!$select) {
			echo "데이타베이스 선택시 오류가 발생하였습니다." . mysql_error();
			exit ;
		}
        ?>

        <!-- State 출력 시작 -->
        <div id="state_div">
            <!-- LVM State 시작 -->
            <table class="state_table">
                <caption>
                    LVM State
                </caption>
                <thead>
                    <th> hostname </th>
                    <th> 현재 접속중인 Connection 수 </th>
                    <th> 최근 접속한 Connection 수 </th>
                </thead>
                <?PHP
// lvm state part
$query = "SELECT * FROM " . $configure['lvm_state_table_name'] . ";";
$result = mysql_query($query);
if (!$result) {
echo "질의 수행시 오류가 발생하였습니다.";
exit ;
}

$rows = mysql_num_rows($result);
for ($i=0; $i < $rows; $i++) {
$row = mysql_fetch_array($result);
                ?>

                <tr>
                    <td><?PHP echo $row[0];?></td>
                    <td><?PHP echo $row[1];?></td>
                    <td><?PHP echo $row[2];?></td>
                </tr>
                <?PHP
					} // end of for statement
                ?>
            </table>
            <!-- LVM State 끝 -->
            <!-- Storage State 시작 -->
            <table class="state_table">
                <caption>
                    Storage State
                </caption>
                <thead>
                    <th> hostname </th>
                    <th> 받은 패킷 </th>
                    <th> 보낸 패킷 </th>
                    <th> 하드 사용량(MB) </th>
                    <th> 유저 할당량(MB) </th>
                </thead>
                <?PHP
// lvm state part
$query = "SELECT * FROM " . $configure['storage_state_table_name'] . ";";
$result = mysql_query($query);
if (!$result) {
echo "질의 수행시 오류가 발생하였습니다.";
exit ;
}

$rows = mysql_num_rows($result);
for ($i=0; $i < $rows; $i++) {
$row = mysql_fetch_array($result);
                ?>

                <tr>
                    <td><?PHP echo $row[0];?></td>
                    <td><?PHP echo $row[1];?></td>
                    <td><?PHP echo $row[2];?></td>
                    <td><?PHP echo (int)($row[3]/1024/1024);?></td>
                    <td><?PHP echo (int)($row[4]/1024/1024);?></td>
                </tr>
                <?PHP
					} // end of for statement
                ?>
            </table>
            <!-- Storage State 끝 -->
        </div>
        <!-- State 출력 끝 -->
        <?PHP
			// disconnect from db
			mysql_close($link);
        ?>
        
        
        <!-- <div id="chart"></div> -->
    </body>
</html>