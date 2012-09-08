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
				/*
				var goog = [["2009/6/22 2:12", 425.32], ["2009/6/22 9:11", 420.09], ["2009/6/22 9:13", 424.84], ["2009/6/22 9:14", 444.32], ["2009/6/22 10:15", 417.23], ["2009/6/22 12:15", 393.5]];
				var goog2 = [["2009/6/22 2:12", 525.32], ["2009/6/22 9:11", 520.09], ["2009/6/22 9:13", 524.84], ["2009/6/22 9:14", 544.32], ["2009/6/22 10:15", 617.23], ["2009/6/22 12:15", 593.5]];
				var plot = $.jqplot('chart', [goog, goog2], {
					title : 'Storage Statistics',
					series : [{
						label : 'Storage Statistics',
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
*/
				loadLoadbalancingState();
				loadBandwidthState();
				loadStorageState();
				loadLoadbalancingStatistics();

			});
			
			//////////// current state //////////// 
			function loadLoadbalancingState() {
				$.ajax({
					url : 'api/management/get_data/index.php?category=lvm_state&response_object=json', //server script to process data
					type : 'GET',
					success : function(data) {
						var json_data = json_parse(data);						
						
						var array = new Array();
						for (var index in json_data) {
							var number_of_conn = json_data[index]['active_conn'] + json_data[index]['inact_conn'];
						  	array.push([json_data[index]['hostname'], number_of_conn]);
						};
						var plot = $.jqplot('chart_lvm_state', [array], {
							title : 'LB State',
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
					},
					error : function(data) {
						console.log(data);
					}
				}); // end of ajax
			}
			
			
			function loadBandwidthState() {
				$.ajax({
					url : 'api/management/get_data/index.php?category=storage_state&response_object=json', //server script to process data
					type : 'GET',
					success : function(data) {
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
							title : 'Bandwidth State',
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
					},
					error : function(data) {
						console.log(data);
					}
				}); // end of ajax
			}
			
			
			function loadStorageState() {
				$.ajax({
					url : 'api/management/get_data/index.php?category=storage_state&response_object=json', //server script to process data
					type : 'GET',
					success : function(data) {
						var json_data = json_parse(data);
						
						var sum_of_hdd_use = 0;
						var sum_of_hdd_total = 0;
						for (var index in json_data) {
							sum_of_hdd_use += parseInt(json_data[index]['hdd_use']);
							sum_of_hdd_total += parseInt(json_data[index]['hdd_total']);
							// user_space_use
						};
						
						var array = new Array();
						console.log(sum_of_hdd_use);
						console.log(sum_of_hdd_total);
						array.push(["현재사용량", sum_of_hdd_use]);
						array.push(["남은용량", sum_of_hdd_total - sum_of_hdd_use]);
						
						//var array = [['Heavy Industry', 12], ['Retail', 9], ['Light Industry', 14], ['Out of home', 16], ['Commuting', 7], ['Orientation', 9]];
						var plot = $.jqplot('chart_storage_state', [array], {
							seriesDefaults : {
								renderer : jQuery.jqplot.PieRenderer,
								rendererOptions : {
									// Turn off filling of slices.
									fill : true,
									showDataLabels : true,
									// Add a margin to seperate the slices.
									sliceMargin : 0,
									// stroke the slices with a little thicker line.
									lineWidth : 5
								}
							},
							legend : {
								show : true,
								location : 'e'
							}
						});
						
						
					},
					error : function(data) {
						console.log(data);
					}
				}); // end of ajax
			}
			
			//////////// statistics ////////////
			function loadLoadbalancingStatistics() {
				$.ajax({
					url : 'api/management/get_data/index.php?category=lvm_statistics&response_object=json', //server script to process data
					type : 'GET',
					success : function(data) {
						var json_data = json_parse(data);
						
						var array = new Array();
						array[0] = new Array();
						array[1] = new Array();
						array[2] = new Array();
						
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
							
							var sum_of_conn = json_data[index]["active_conn"] + json_data[index]["inact_conn"];
							var time = json_data[index]["time"];
							//array[position].push([time.getDate(), sum_of_conn]);
							//console.log(time);
							var date = new Date(json_data[index]["time"]);
							//dateFormat(now, "dddd, mmmm dS, yyyy, h:MM:ss TT");
							//var newDate = (date.getYear() + 1900) + "/" + (date.getMonth() + 1) + "/" + date.getDate() + " " + date.getHours() + ":" + date.getMinutes();
							//console.log(date.getDate() + 1);
							//console.log(newDate);
							array[position].push([json_data[index]["index"], sum_of_conn]);
							
							//array[position].push([index, sum_of_conn]);
						}
						
						console.log(array);
						
						var plot = $.jqplot('chart_lvm_statistics', array, {
							title : 'Storage Statistics',
							// series : [{
								// label : 'Storage Statistics',
								// neighborThreshold : -1
							// }],
							axes : {
								// xaxis : {
									// renderer : $.jqplot.DateAxisRenderer,
									// min : '2009/6/22 1:00',
									// tickInterval : "1 hours",
									// tickOptions : {
										// formatString : "%H:%M"
									// }
								// },
								// yaxis : {
									// renderer : $.jqplot.LogAxisRenderer,
									// tickOptions : {
										// formatString : '$%.2f'
									// }
								// }
							},
							cursor : {
								zoom : false,
								showTooltip : false,
								clickReset : true
							}
						});
						
						
					},
					error : function(data) {
						console.log(data);
					}
				}); // end of ajax				
			}
			function loadBandwidthStatistics() {
				
			}
			function loadStorageStatistics() {
				
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
            <table class="state_table">
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

        <div id="chart_lvm_state" class="chart"></div>
        <div id="chart_bandwidth_state" class="chart"></div>
        <div id="chart_storage_state" class="chart"></div>
        <div id="chart_lvm_statistics" class="chart"></div>
    </body>
</html>