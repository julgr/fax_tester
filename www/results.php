<?php

session_start();

header('Content-Type: text/html; charset=utf-8');
echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
echo "<style type='text/css'>
 body{
 background:#000;
 color: #FFFFFF;
 font-family:'Lucida Console',sans-serif !important;
 font-size: 12px;
 link: #00FF00;
 }
</style>\n";

echo "<div id='fullview' style='display:block;float:left;width:100%;'>\n";
echo "<table border='0' cellpadding='0' cellspacing='2'>\n";
echo "<tr class='border'>\n";
echo "  <td align=\"left\">\n";
echo "          <br>";

$uuid = $_SESSION['uuid'];
$nuuid = $_SESSION['call_id'];
$inbound = $_SESSION['inbound'];
$outbound = $_SESSION['outbound'];

// Need to check if UUID exisits. If not, then don't do below since the call never setup.
$logpath = "/var/tmp/fslog/$uuid.html";
@$logfile=fopen("$logpath","r");
if( $inbound === TRUE || $outbound === TRUE )
	{
	echo "<br><br>Your capture can be downloaded at the below link:\n";
	sleep(2);
	echo "<br><br><a href=http://173.255.253.86:81/$nuuid.pcap> $nuuid.pcap</a>\n";
	}
elseif($logfile)
	{
	echo "<div id='download_link' style='padding:3px;link:#00FF00;font-size:18px;display:block;position:absolute;width:100%px;height:21px;background:#000000;top:5px;left:5px;'> Download Capture: $uuid.pcap<a href=http://173.255.253.86:81/$uuid.pcap><img src=whitedown.png vspace=3 height=20px/></a>\n";
	echo " \n";
	echo "</div>\n";
	echo "<div id='log_output' style='font-size:10px;'>\n";
	$output = file_get_contents($logpath);
	echo $output;
	echo "</div>\n";
	fclose($logfile);
	//while (!feof($logfile))
	//	{
	//	$line = fgets($logfile, 4096);
	//	//$output = explode($uuid, $line);
	//	$output = $line;
	//	for ($i=1; $i<count($output); $i++)
	//		{
	//		print $output[$i];
	//		print "<br>";
	//		}
	//	}
	//	echo "</div>\n";
	//	fclose($logfile);
	}
else
	{
	echo "<br><br>Please wait while your test fax is being sent...\n";
	echo "<meta http-equiv='refresh' content='5;url=http://173.255.253.86:8181/results.php'>\n";
	}
echo "</div>\n";
?>
