<?PHP echo '<' . '?xml version="1.0" encoding="utf-8"?' . '>';?>
<?PHP
// configuration
$configure['state_database_name'] = "csstate";
$configure['lvm_state_table_name'] = "lvm_state";
$configure['storage_state_table_name'] = "storage_state";

$configure['statistics_database_name'] = "csstatistics";
$configure['lvm_statistics_table_name'] = "lvm_states";
$configure['storage_statistics_table_name'] = "storage_states";

include_once "util.php";
?>
<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title>Cloud Storage</title>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <link type="text/css" rel="stylesheet" href="styles/common.css" />
        <link type="text/css" rel="stylesheet" href="styles/management.css" />
        <link rel="stylesheet" type="text/css" href="jqplot/src/jquery.jqplot.css" />
        <!-- <link rel="stylesheet" type="text/css" href="jqplot/examples.css" /> -->
        <!-- BEGIN: load jquery -->
        <script language="javascript" type="text/javascript" src="jqplot/src/jquery.js"></script>
        <script language="javascript" type="text/javascript" src="scripts/jquery.dateFormat-1.0.js"></script>
        <!-- END: load jquery -->
        <!-- BEGIN: load jqplot -->
        <script language="javascript" type="text/javascript" src="jqplot/src/jquery.jqplot.js"></script>
        <script language="javascript" type="text/javascript" src="jqplot/src/plugins/jqplot.logAxisRenderer.js"></script>
        <script language="javascript" type="text/javascript" src="jqplot/src/plugins/jqplot.dateAxisRenderer.js"></script>
        <script language="javascript" type="text/javascript" src="jqplot/src/plugins/jqplot.pieRenderer.js"></script>
        <script language="javascript" type="text/javascript" src="jqplot/src/plugins/jqplot.highlighter.js"></script>
        <script language="javascript" type="text/javascript" src="jqplot/src/plugins/jqplot.canvasTextRenderer.js"></script>
        <script language="javascript" type="text/javascript" src="jqplot/src/plugins/jqplot.canvasAxisTickRenderer.js"></script>
        <script language="javascript" type="text/javascript" src="jqplot/src/plugins/jqplot.categoryAxisRenderer.js"></script>
        <script language="javascript" type="text/javascript" src="jqplot/src/plugins/jqplot.barRenderer.js"></script>
        <script language="javascript" type="text/javascript" src="jqplot/src/plugins/jqplot.cursor.js"></script>
        <!-- END: load jqplot -->
        <script language="javascript" type="text/javascript" src="scripts/utils.js"></script>
        <style type="text/css" media="screen">
            .jqplot-axis {
                font-size: 0.85em;
            }
        </style>
        <script type="text/javascript" language="javascript">
			$(document).ready(function() {
				$.jqplot.config.enablePlugins = true;

				// start getting datas
				getLvmStateData();
				getStorageStateData();
				getLvmStatisticsData();
				getStorageStatisticsData();
				
				
			});
			
			//////////// getting datas ////////////
			function getLvmStateData() {
				$.ajax({
					url : 'api/management/get_data/index.php?category=lvm_state&response_object=json', //server script to process data
					type : 'GET',
					success : function(data) {
						loadLvmTable(data);
						loadLoadbalancingState(data);
					},
					error : function(data) {
						console.log(data);
					}
				}); // end of ajax				
			}
			function getStorageStateData() {
				$.ajax({
					url : 'api/management/get_data/index.php?category=storage_state&response_object=json', //server script to process data
					type : 'GET',
					success : function(data) {
						loadStorageTable(data);
						loadBandwidthState(data);
						loadStorageState(data);
					},
					error : function(data) {
						console.log(data);
					}
				}); // end of ajax
			}
			function getLvmStatisticsData() {
				$.ajax({
					url : 'api/management/get_data/index.php?category=lvm_statistics&response_object=json', //server script to process data
					type : 'GET',
					success : function(data) {
						//console.log(data);
						loadLoadbalancingStatistics(data);
					},
					error : function(data) {
						console.log(data);
					}
				}); // end of ajax
			}
			function getStorageStatisticsData() {
				$.ajax({
					url : 'api/management/get_data/index.php?category=storage_statistics&response_object=json', //server script to process data
					type : 'GET',
					success : function(data) {
						loadBandwidthStatistics(data);
						loadStorageStatistics(data);
					},
					error : function(data) {
						console.log(data);
					}
				}); // end of ajax
			}
			
			//////////// load current state table ////////////
			function loadLvmTable(data) {
				var json_data = json_parse(data);
				//console.log(json_data);				
			}
			
			function loadStorageTable(data) {
				
			}
			
			
			//////////// load current state //////////// 
			function loadLoadbalancingState(data) {
				var json_data = json_parse(data);						
				
				var array = new Array();
				for (var index in json_data) {
					var number_of_conn = parseInt(json_data[index]['active_conn']) + parseInt(json_data[index]['inact_conn']);
				  	array.push([json_data[index]['hostname'], number_of_conn]);
				};
				var plot = $.jqplot('chart_lvm_state', [array], {
					title : 'Load Balancing State',
					series : [{
						renderer : $.jqplot.BarRenderer
					}],
					axesDefaults : {
						tickRenderer : $.jqplot.CanvasAxisTickRenderer,
						tickOptions : {
							angle : -30,
							fontSize : '10pt'
						}
					},
					axes : {
						xaxis : {
							renderer : $.jqplot.CategoryAxisRenderer
						}
					}
				});
			}
			
			
			function loadBandwidthState(data) {
				var json_data = json_parse(data);						
				
				var sum_of_rx_use = 0;
				var sum_of_tx_use = 0;
				for (var index in json_data) {
					sum_of_rx_use += parseInt(json_data[index]['rx_use']);
					sum_of_tx_use += parseInt(json_data[index]['tx_use']);
				};
				var array = new Array();
				// 단위 : byte
				array.push(["받는 데이터량(KB / s)", sum_of_rx_use / 3072]);
				array.push(["보내는 데이터량(KB / s)", sum_of_tx_use / 3072]);
				
				var plot = $.jqplot('chart_bandwidth_state', [array], {
					title : 'Bandwidth State (KB / s)',
					series : [{
						renderer : $.jqplot.BarRenderer
					}],
					axesDefaults : {
						tickRenderer : $.jqplot.CanvasAxisTickRenderer,
						tickOptions : {
							angle : -30,
							fontSize : '10pt'
						}
					},
					axes : {
						xaxis : {
							renderer : $.jqplot.CategoryAxisRenderer
						}
					}
				});
			}
			
			
			function loadStorageState(data) {
				var json_data = json_parse(data);
				
				var sum_of_hdd_use = 0;
				var sum_of_hdd_total = 0;
				for (var index in json_data) {
					sum_of_hdd_use += parseInt(json_data[index]['hdd_use']);
					sum_of_hdd_total += parseInt(json_data[index]['hdd_total']);
					// user_space_use
				};
				
				var array = new Array();
				array.push(["현재사용량", sum_of_hdd_use]);
				array.push(["남은용량", sum_of_hdd_total - sum_of_hdd_use]);
				
				var plot = $.jqplot('chart_storage_state', [array], {
					title : 'Storage Usage State',
					seriesDefaults : {
						renderer : jQuery.jqplot.PieRenderer,
						rendererOptions : {
							fill : true,
							showDataLabels : true,
							sliceMargin : 0,
							lineWidth : 5
						}
					},
					legend : {
						show : true,
						location : 'e'
					}
				});
			}
			
			//////////// load statistics ////////////
			function loadLoadbalancingStatistics(data) {
				var json_data = json_parse(data);
				
				var array = new Array();
				array[0] = new Array();
				array[1] = new Array();
				array[2] = new Array();
				
				var max_of_conn = 0;
				
				for (var index in json_data) {
					var position;
					if (json_data[index]["hostname"] == "10.12.17.214") {
						position = 0;
					}
					else if (json_data[index]["hostname"] == "10.12.17.216") {
						position = 1;
					}
					else if (json_data[index]["hostname"] == "10.12.17.218") {
						position = 2;
					}
					
					var sum_of_conn =  parseInt(json_data[index]["active_conn"]) + parseInt(json_data[index]["inact_conn"]);
					if (sum_of_conn > max_of_conn) max_of_conn = sum_of_conn;
					var time = json_data[index]["time"];
					var date = new Date(json_data[index]["time"]);
					var newDate = $.format.date(date, 'yyyy/MM/dd HH:mm:ss');
					//console.log(newDate+" "+sum_of_conn);
					//2009/6/22 1:00
					array[position].push([newDate, sum_of_conn]);
					
				}
				
				var plot = $.jqplot('chart_lvm_statistics', array, {
					title : 'Load Balancing Statistics',
					series : [{
						label : '10.12.17.214',
						neighborThreshold : -1
					},
					{
						label : '10.12.17.216',
						neighborThreshold : -1
					},
					{
						label : '10.12.17.218',
						neighborThreshold : -1
					}],
					axes : {
						xaxis : {
							renderer : $.jqplot.DateAxisRenderer,
							//min : '2009/6/22 1:00',
							tickInterval : "30 minutes",
							tickOptions : {
								formatString : "%H:%M"
							}
						},
						yaxis : {
							//renderer : $.jqplot.LogAxisRenderer,
							// tickOptions : {
								// formatString : '$%.2f'
							// }
							min : 0,
							max : max_of_conn + 30
						}
					},
					legend : {
							show : true,
							location : 'e'
					},
					cursor : {
						zoom : true,
						showTooltip : false,
						clickReset : true
					}
				});
			}
			function loadBandwidthStatistics(data) {
				var json_data = json_parse(data);
				//console.log(json_data);
				
				var dataArray = new Array();
				dataArray[0] = new Array();
				dataArray[1] = new Array();
				dataArray[2] = new Array();						
				
				for (var index in json_data) {
					var position;
					if (json_data[index]["hostname"] == "10.12.17.214") {
						position = 0;
					}
					else if (json_data[index]["hostname"] == "10.12.17.216") {
						position = 1;
					}
					else if (json_data[index]["hostname"] == "10.12.17.218") {
						position = 2;
					}
					
					var time = json_data[index]["time"];
					var rx_use = json_data[index]["rx_use"];
					var tx_use = json_data[index]["tx_use"]; 
					dataArray[position].push([time, rx_use, tx_use]);
				}
				
				// summarize 
				var array = new Array();
				array[0] = new Array();
				array[1] = new Array();
				
				var max = Math.max(dataArray[0].length, dataArray[1].length, dataArray[2].length);
				
				for (var index = 0; index < max; index ++) {
					var sum_of_rx_use = 0;
					var sum_of_tx_use = 0;
					var number_of_data = 0;
					var sum_of_time = 0;
					
					for (var j = 0; j < 3; j++) {
						if(dataArray[j][index] == undefined) continue;
						
						var time = new Date(dataArray[j][index][0]);
						var rx_use = dataArray[j][index][1];
						var tx_use = dataArray[j][index][2];
						
						sum_of_rx_use += parseInt(rx_use) / 3072;
						sum_of_tx_use += parseInt(tx_use) / 3072;
						sum_of_time += time.getTime();
						number_of_data ++;
					}
					
					
					//if (dataArray[1][index] == undefined || dataArray[2][index] == undefined ) console.log("test");
					//console.log(dataArray[0][index]);
					
					var averageDate = new Date(sum_of_time/number_of_data);
					
					
					var newDate = $.format.date(averageDate, 'yyyy/MM/dd HH:mm:ss');
					//console.log(newDate);
					
					// per seconds
					array[0].push([newDate, sum_of_rx_use / 300]); 
					array[1].push([newDate, sum_of_tx_use / 300]);
				}	
				
				//console.log(array);
				
				var plot = $.jqplot('chart_bandwidth_statistics', array, {
					title : 'Bandwidth Statistics (KB / s)',
					series : [{
						label : '받은 데이터량',
						neighborThreshold : -1
					},
					{
						label : '보낸 데이터량',
						neighborThreshold : -1
					}],
					axes : {
						xaxis : {
							renderer : $.jqplot.DateAxisRenderer,
							//min : '2009/6/22 1:00',
							tickInterval : "30 minutes",
							tickOptions : {
								formatString : "%H:%M"
							}
						},
						yaxis : {
							//renderer : $.jqplot.LogAxisRenderer,
							// tickOptions : {
								// formatString : '$%.2f'
							// }
							min : 0,
							//max : max_of_conn + 30
						}
					},
					legend : {
							show : true,
							location : 'e'
					},
					cursor : {
						zoom : true,
						showTooltip : false,
						clickReset : true
					}
				});
			}
			function loadStorageStatistics(data) {
				var json_data = json_parse(data);
				//console.log(json_data);
				
				var dataArray = new Array();
				dataArray[0] = new Array();
				dataArray[1] = new Array();
				dataArray[2] = new Array();						
				
				for (var index in json_data) {
					var position;
					if (json_data[index]["hostname"] == "10.12.17.214") {
						position = 0;
					}
					else if (json_data[index]["hostname"] == "10.12.17.216") {
						position = 1;
					}
					else if (json_data[index]["hostname"] == "10.12.17.218") {
						position = 2;
					}
					
					var time = json_data[index]["time"];
					var hdd_total = json_data[index]["hdd_total"];
					var hdd_use = json_data[index]["hdd_use"]; 
					dataArray[position].push([time, hdd_total, hdd_use]);
				}
				
				// summarize 
				var array = new Array();
				array[0] = new Array();
				array[1] = new Array();
				
				var max = Math.max(dataArray[0].length, dataArray[1].length, dataArray[2].length);
				
				for (var index = 0; index < max; index ++) {
					var sum_of_hdd_total = 0;
					var sum_of_hdd_use = 0;
					var number_of_data = 0;
					var sum_of_time = 0;
					
					for (var j = 0; j < 3; j++) {
						if(dataArray[j][index] == undefined) continue;
						
						var time = new Date(dataArray[j][index][0]);
						var hdd_total = dataArray[j][index][1];
						var hdd_use = dataArray[j][index][2];
						
						sum_of_hdd_total += parseInt(hdd_total) / (1024*1024);
						sum_of_hdd_use += parseInt(hdd_use) / (1024*1024) ;
						sum_of_time += time.getTime();
						number_of_data ++;
					}
					
					var averageDate = new Date(sum_of_time/number_of_data);
					var newDate = $.format.date(averageDate, 'yyyy/MM/dd HH:mm:ss');
					//console.log(newDate);
					
					array[0].push([newDate, sum_of_hdd_total / 1024]);
					array[1].push([newDate, sum_of_hdd_use / 1024]);
				}	
				
				//console.log(array);
				
				var plot = $.jqplot('chart_storage_statistics', array, {
					title : 'Storage Usage Statistics (GB)',
					series : [{
						label : '하드 총용량',
						neighborThreshold : -1
					},
					{
						label : '하드 사용량',
						neighborThreshold : -1
					}],
					axes : {
						xaxis : {
							renderer : $.jqplot.DateAxisRenderer,
							//min : '2009/6/22 1:00',
							tickInterval : "30 minutes",
							tickOptions : {
								formatString : "%H:%M"
							}
						},
						yaxis : {
							//renderer : $.jqplot.LogAxisRenderer,
							// tickOptions : {
								// formatString : '$%.2f'
							// }
							min : 0,
							//max : max_of_conn + 30
						}
					},
					legend : {
							show : true,
							location : 'e'
					},
					cursor : {
						zoom : true,
						showTooltip : false,
						clickReset : true
					}
				});
			}

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
            <table id="lvm_state_table" class="state_table">
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
                    <td><?PHP echo $row['hostname'];?></td>
                    <td><?PHP echo $row['active_conn'];?></td>
                    <td><?PHP echo $row['inact_conn'];?></td>
                </tr>
                <?PHP
					} // end of for statement
                ?>
            </table>
            <!-- LVM State 끝 -->
            <!-- Storage State 시작 -->
            <table id="storage_state_table" class="state_table">
                <caption>
                    Storage State
                </caption>
                <thead>
                    <th> IP address </th>
                    <th> 받은 데이터량(KB) </th>
                    <th> 보낸 데이터량(KB) </th>
                    <th> 하드 총용량(MB) </th>
                    <th> 하드 사용량(MB) </th>
                    <th> 유저 할당량(MB) </th>
                </thead>
                <?PHP
// storage state part
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
                    <td><?PHP echo $row['hostname'];?></td>
                    <td><?PHP echo (int)($row['rx_use']/1024);
                    ?></td>
                    <td><?PHP echo (int)($row['tx_use']/1024);
                    ?></td>
                    <td><?PHP echo (int)($row['hdd_total']/1024/1024);
                    ?></td>
                    <td><?PHP echo (int)($row['hdd_use']/1024/1024);
                    ?></td>
                    <td><?PHP echo (int)($row['user_space_use']/1024/1024);
                    ?></td>
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

        <div id="chart_lvm_state" class="state_chart"></div>
        <div id="chart_bandwidth_state" class="state_chart"></div>
        <div id="chart_storage_state" class="state_chart"></div>
        <div id="chart_lvm_statistics" class="statistics_chart"></div>
        <div id="chart_bandwidth_statistics" class="statistics_chart"></div>
        <div id="chart_storage_statistics" class="statistics_chart"></div>
    </body>
</html>