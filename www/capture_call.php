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

echo "<div id='fullview' style='display:block;float:left;width:100%;'>\n";
echo "<table border='0' cellpadding='0' cellspacing='2'>\n";
echo "<tr class='border'>\n";
echo "	<td align=\"left\">\n";
echo "		<br>";

echo "<div id='proxyform' style='display:block;position:absolute; width: 450px; top: 0px; left: 0px;border:2px solid #FFFFFF;'>";

echo "<div align='center'>\n";
echo "<table width='100%'  border='0' cellpadding='6' cellspacing='0'>\n";
echo "<tr>\n";
echo "		<td align='left' width='30%'>\n";
echo "			<span class=\"vexpl\"><span class=\"red\"><strong>Call Capture System</strong></span>\n";
echo "		</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "</div>\n";

echo "<form method=\"POST\" enctype=\"multipart/form-data\" name=\"frmUpload\" action=\"\">\n";
echo "<div align='center'>\n";
echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\n";
echo "	<tr>\n";
echo "		<td colspan='2' align='left'>\n";
echo "			Retrieve a proxied call by Call-ID. \n";
echo "			<br /><br />\n";
echo "		</td>\n";
echo "	</tr>\n";

echo "<tr>\n";
echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
echo "		Call-ID:\n";
echo "</td>\n";
echo "<td class='vtable' align='left'>\n";
echo "		<input type=\"text\" name=\"call_id\" class='formfld' style='' value=\"\">\n";
echo "<br />\n";
echo "\n";
echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
echo "		Inbound:\n";
echo "</td>\n";
echo "<td class='vtable' align='left'>\n";
echo "		<input type=\"checkbox\" name=\"inbound\" value=\"\">\n";
echo "<br />\n";
echo "\n";
echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
echo "		Outbound:\n";
echo "</td>\n";
echo "<td class='vtable' align='left'>\n";
echo "		<input type=\"checkbox\" name=\"outbound\" value=\"\">\n";
echo "<br />\n";
echo "\n";
echo "</td>\n";
echo "</tr>\n";

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
if (isset($_POST['call_id'])) {
  runscript($_POST);
}
echo "</div>\n";

function runscript($_POST)
{
	
	$call_id = $_POST['call_id'];
	
	//if ($call_id === NULL || ($inbound === NULL && $outbound === NULL)) {
	if ($call_id == NULL) {
	        echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>";
		echo '<span style="color: red;" />Please check your parameters (call-ID, inbound, outbound)</span>';
	} else {
	        echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>";
  		echo "Finding and retrieving your call...\n";
		$_SESSION['call_id'] = $_POST['call_id'];
		$uuid = $_SESSION['call_id'];

		$command = "python /usr/share/nginx/scripts/return_capture.py";
        	$command .= " -c $uuid";
        	if (isset($_POST['inbound'])){
        	//$_SESSION['inbound'] = $_POST['inbound'];
		$_SESSION['inbound'] = TRUE;
        	}
        	if (isset($_POST['outbound'])){
        	//$_SESSION['outbound'] = $_POST['outbound'];
		$_SESSION['outbound'] = TRUE;
        	}
        	$command .= " 2>&1 &";
		
        	$pid = popen( $command,"r");
		usleep(500000);
		header('Location: http://173.255.253.86:8181/results.php');
		exit();
	}
}
?>
