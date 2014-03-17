#!/usr/bin/python
# print fax results and scrubbed logs to html-ready file for display in fax tester
import re
import sys

line_break = "=============================================================================="
status_header = ""
callid = sys.argv[1]
fax_success = sys.argv[2]
fax_result_code = sys.argv[3]
fax_result_text = sys.argv[4]
calllog = ""

if fax_success == '1': 
	status_header = "#####SUCCESSFUL FAX TRANSMISSION#####"
else:
	status_header = "!!!!!FAILED FAX TRANSMISSION!!!!!"
header ="<br><br><div id='fax_results' style='font-size:20px'>" + line_break + "<br>" + status_header+ "<br>FAX RESULT CODE: " + fax_result_code + "<br> FAX RESULT TEXT: " + fax_result_text + "<br>" + line_break + "<br></div>"	
f = open("/usr/local/freeswitch/log/freeswitch.log", "r")
fslog = f.read()
f.close()

for line in fslog.splitlines():
	if callid in line:
		calllog += line + "<br>"

cleanlog = re.sub(callid + ' ', '', calllog)
htmllog = re.sub('\n', '<br>', cleanlog) 

print cleanlog


f = open('/var/tmp/fslog/' + callid + '.html', 'w+')
f.write(header + htmllog)
f.close()
