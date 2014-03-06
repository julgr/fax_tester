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
 }
</style>\n";

// Generate a UUID in PHP cuz we needzzz itsss
$uuid = getGUUID();
$_SESSION['uuid'] = $uuid;
function getGUUID(){
        mt_srand((double)microtime()*10000);
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid,12, 4).$hyphen
            .substr($charid,16, 4).$hyphen
            .substr($charid,20,12);
        return $uuid;
}

echo "<div id='fullview' style='display:block;float:left;width:100%;'>\n";
echo "<table border='0' cellpadding='0' cellspacing='2'>\n";
echo "<tr class='border'>\n";
echo "	<td align=\"left\">\n";
echo "		<br>";

echo "<div id='faxform' style='display:block;position:absolute; width: 350px; top: 0px; left: 0px;border:2px solid #FFFFFF;'>";

echo "<div align='center'>\n";
echo "<table width='100%'  border='0' cellpadding='6' cellspacing='0'>\n";
echo "<tr>\n";
echo "		<td align='left' width='30%'>\n";
echo "			<span class=\"vexpl\"><span class=\"red\"><strong>Test Fax System</strong></span>\n";
echo "		</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "</div>\n";

echo "<form method=\"POST\" enctype=\"multipart/form-data\" name=\"frmUpload\" action=\"\">\n";
echo "<div align='center'>\n";
echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\n";
echo "	<tr>\n";
echo "		<td colspan='2' align='left'>\n";
echo "			Sends a test fax with the below settings. \n";
echo "			<br /><br />\n";
echo "		</td>\n";
echo "	</tr>\n";

echo "<tr>\n";
echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
echo "		Fax Destination:\n";
echo "</td>\n";
echo "<td class='vtable' align='left'>\n";
echo "		<input type=\"text\" name=\"fax_number\" class='formfld' style='' value=\"\">\n";
echo "<br />\n";
echo "\n";
echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
echo "		Outbound Caller-ID:\n";
echo "</td>\n";
echo "<td class='vtable' align='left'>\n";
echo "		<input type=\"text\" name=\"callerid_number\" class='formfld' style='' value=\"\">\n";
echo "<br />\n";
echo "\n";
echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
echo "		Use ECM:\n";
echo "</td>\n";
echo "<td class='vtable' align='left'>\n";
echo "		<input type=\"checkbox\" name=\"ecm\" value=\"\">\n";
echo "<br />\n";
echo "\n";
echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
echo "		Use v17:\n";
echo "</td>\n";
echo "<td class='vtable' align='left'>\n";
echo "		<input type=\"checkbox\" name=\"v17\" value=\"\">\n";
echo "<br />\n";
echo "\n";
echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
echo "		Do not reINVITE:\n";
echo "</td>\n";
echo "<td class='vtable' align='left'>\n";
echo "		<input type=\"checkbox\" name=\"reinvite\" value=\"\">\n";
echo "<br />\n";
echo "\n";
echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
echo "		Create PCAP:\n";
echo "</td>\n";
echo "<td class='vtable' align='left'>\n";
echo "		<input type=\"checkbox\" name=\"pcap\" value=\"\">\n";
echo "<br />\n";
echo "\n";
echo "</td>\n";
echo "</tr>\n";
/*
echo "<tr>\n";
echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
echo "	Fax File to Upload:\n";
echo "</td>\n";
echo "<td class='vtable' align='left'>\n";
echo "	<input name=\"id\" type=\"hidden\" value=\"\$id\">\n";
echo "	<input name=\"type\" type=\"hidden\" value=\"fax_send\">\n";
echo "	<input name=\"fax_file\" type=\"file\" class=\"btn\" id=\"fax_file\" accept=\"image/tiff,application/pdf\">\n";
echo "	<br />\n";
echo "	".$text['description-upload']."\n";
echo "</td>\n";
echo "</tr>\n";
*/

echo "	<tr>\n";
echo "		<td colspan='2' align='right'>\n";
//echo "			<input name=\"submit\" type=\"submit\" class=\"btn\" value=\"Send\">\n";
echo "<input type=\"submit\" value=\"Submit\">";
echo "		</td>\n";
echo "	</tr>";
echo "</table>";
echo "</div>\n";
echo "</form>\n";


echo "</div>\n";



echo "<div id='output' style='display:inline;float:right;width:100%;'>\n";
if (isset($_POST['fax_number'])) {
  echo "Please wait. Your test fax is being sent...\n";
  runscript($_POST);
}
echo "</div>\n";

function runscript($_POST)
{
	
	$destination = $_POST['fax_number'];
	$cid_num = $_POST['callerid_number'];
	
	if ($destination === NULL || $cid_num === NULL) {
	        echo "You need to go back a step and check your destination or caller-ID";
	} else {
	
		$_SESSION['destination'] = $_POST['fax_number'];
		$_SESSION['cid_num'] = $_POST['callerid_number'];
		$uuid = $_SESSION['uuid'];

		$command = "python /usr/share/nginx/scripts/fax_test.py";
        	$command .= " -u $uuid";
        	$command .= " -d $destination";
        	$command .= " -c $cid_num";
        	if (isset($_POST['v17'])){
        	$command .= " -v";
        	}
        	if (isset($_POST['ecm'])){
        	$command .= " -e";
        	}
        	if (isset($_POST['reinvite'])){
        	$command .= " -r";
        	}
        	if (isset($_POST['pcap'])){
        	$command .= " -p";
        	}
        	$command .= " 2>&1 &";
		
        	$pid = popen( $command,"r");
		usleep(500000);
		header('Location: http://173.255.253.86:8181/results.php');
		exit();
	}
}
?>
