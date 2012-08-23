<?PHP

global $PHP_SELF;
$thisfilename = basename(__FILE__);
$temp_filename = realpath(__FILE__);
if (!$temp_filename)
	$temp_filename = __FILE__;
//$osdir=eregi_replace($thisfilename,"",$temp_filename);
$osdir = preg_replace("/" . $thisfilename . "/", "", $temp_filename);
unset($temp_filename);

$virdir = preg_replace("/" . $thisfilename . "/", "", $PHP_SELF);
//preg_replace($pattern, $replacement, $subject)

echo "현재 디렉토리의 절대경로 : " . $osdir . "<br />";
echo "현재 디렉토리의 상대 경로 주소 : " . $virdir . "<br />";

if ($_GET['sdir']) $sdir = $_GET['sdir'];
else $sdir = ".";

// check path contains ..
if(strstr($sdir, "..") == true) {
	echo "잘못된 접근입니다.";
}

echo "<br />";
echo "basename:".basename($sdir)."<br/>";
echo "realpath:".realpath($sdir)."<br/>";

// open this directory
$myDirectory = opendir($sdir);

// get each entry
while ($entryName = readdir($myDirectory)) {
	$dirArray[] = $entryName;
}

// close directory
closedir($myDirectory);

//	count elements in array
$indexCount = count($dirArray);
Print("$indexCount files<br>\n");

// sort 'em
sort($dirArray);

// print 'em
print("<TABLE border=1 cellpadding=5 cellspacing=0 class=whitelinks>\n");
print("<TR><TH>Filename</TH><th>Filetype</th><th>Filesize</th></TR>\n");
// loop through the array of files and print them all
for ($index = 0; $index < $indexCount; $index++) {
	if (substr("$dirArray[$index]", 0, 1) != ".") {// don't list hidden files
		print("<tr>");
		print("<td>");
		if (filetype($sdir."/".$dirArray[$index]) == "dir")
			print("<a href=\".?sdir=".$sdir."/".$dirArray[$index]."\">$dirArray[$index]</a>");
		else
			print("<a href=\"$dirArray[$index]\">$dirArray[$index]</a>");
		print("</td>");
		print("<td>");
		print(filetype($sdir."/".$dirArray[$index]));
		print("</td>");
		print("<td>");
		print(filesize($sdir."/".$dirArray[$index]));
		print("</td>");
		print("</tr>\n");
	}
}
print("</TABLE>\n");
?>