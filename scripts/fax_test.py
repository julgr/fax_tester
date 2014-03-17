#!/usr/bin/env python

import subprocess, ESL, string, sys, uuid, re, time, argparse
from optparse import OptionParser
from ESL import *

#Argument parser
parser = argparse.ArgumentParser(description='Send a test fax')
parser.add_argument('--destination', '-d', help='Destination for fax (E.164 number, SIP URI, etc.)')
parser.add_argument('--callerid', '-c', help='Value to use as caller-ID')
parser.add_argument('--uuid', '-u', help='UUID/Call-ID for Call')
parser.add_argument('--ecm', '-e', action='store_true', help='Activate Error Correction')
parser.add_argument('--v17', '-v', action='store_true', help='Send as T.38 v17')
parser.add_argument('--pcap', '-p', action='store_true', help='Return filtered T.38 capture')
parser.add_argument('--reinvite', '-r', action='store_true', help='Wait for Receiving Fax Terminal to Send T.38 reINVITE')
args = parser.parse_args()

v17 = "false"
ecm = "false"
returncap = "false"
cid_num = ""
fax = ""
#uuid = ""
sendreinvite = "true"

if args.destination: fax = args.destination
if args.callerid: cid_num = args.callerid
if args.uuid: 
	uuid = args.uuid
	callid = uuid
else:
	uuid = uuid.uuid1()
	callid = str(uuid)
if args.ecm: ecm = "true"
if args.v17: v17 = "true"
if args.pcap: returncap = True
if args.reinvite: sendreinvite = "false" 

gateway = "sofia/gateway/flowroute/"
prefix = "88720639*"
#prefix = "27562314*"
profile = "sofia/external/"
command_string = ""

# Need regex to check if fax destination is not a SIP URI
pstn_pattern = r'^\d*$'
pstnpattern_re = re.compile(pstn_pattern)
if pstnpattern_re.search(fax):
	pstn = True
else:
	pstn = False

con = ESL.ESLconnection("127.0.0.1", "8021", "EZnI6D1bKUjI");

if con.connected:
	starttime = time.time() 
	if pstn == True:
		print "Fax started with Call-ID: " + callid
		command_string = "api bgapi originate {api_hangup_hook=\'lua /usr/share/nginx/scripts/hangup.lua\',origination_uuid=" + callid + ",fax_disable_v17=" + v17 + ",fax_use_ecm=" + ecm + ",fax_ident=" + cid_num + ",fax_header=TESTFAX,origination_caller_id_number=" + cid_num + ",fax_verbose=true,fax_enable_t38=true,ignore_early_media=true,fax_enable_t38_request=" + sendreinvite + ",absolute_codec_string=PCMU}" + gateway + prefix + fax + " &txfax(/usr/local/freeswitch/fax/testfax.tiff)"
		#command_string = "api bgapi originate {api_hangup_hook=\'system /bin/grep " + callid + " /usr/local/freeswitch/log/freeswitch.log > /var/tmp/fslog/" + callid + ".html\',origination_uuid=" + callid + ",fax_disable_v17=" + v17 + ",fax_use_ecm=" + ecm + ",fax_ident=" + cid_num + ",fax_header=TESTFAX,origination_caller_id_number=" + cid_num + ",fax_verbose=true,fax_enable_t38=true,ignore_early_media=true,fax_enable_t38_request=" + sendreinvite + ",absolute_codec_string=PCMU}" + gateway + prefix + fax + " &txfax(/usr/local/freeswitch/fax/testfax.tiff)"
		e = con.sendRecv(command_string)
		status = True
		callstatus = "true"
	else:
		print "Fax started with Call-ID: " + callid
		command_string = "api bgapi originate {api_hangup_hook=\'system /bin/grep " + callid + " /usr/local/freeswitch/log/freeswitch.log > /var/tmp/fslog/" + callid + ".log\',origination_uuid=" + callid + ",fax_disable_v17=" + v17 + ",fax_use_ecm=" + ecm + ",fax_ident=" + cid_num + ",fax_header=TESTFAX,origination_caller_id_number=" + cid_num + ",fax_verbose=true,fax_enable_t38=true,ignore_early_media=true,fax_enable_t38_request=" + sendreinvite + ",absolute_codec_string=PCMU}" + profile + fax + " &txfax(/usr/local/freeswitch/fax/testfax.tiff)"
		e = con.sendRecv(command_string)
		status = True
		callstatus = "true"
else:
	print "Not Connected"
	status = False
	sys.exit(2)

if status == True:
	# Need to wait for the call before we can execute the subprocess
	#Using a while loop to pol fs_cli will be good here
	returnstatus = "api uuid_exists " + callid
	while callstatus == "true":
		time.sleep(1)
		f = con.sendRecv(returnstatus)
		callstatus = f.getBody()
		#print "Your call is still in progress..."

	endtime = time.time()
	print "The fax ended after " + str(endtime-starttime) + " seconds"

	if returncap == True:
		subprocess.call(["python", "return_capture.py", "-c", callid], cwd="/usr/share/nginx/scripts")
		print "Use the below link to download the capture file:"
		print "<a href=http://173.255.253.86:81/" + callid + ".pcap>" + callid + "</a>\n"	
else:
	print "The fax didn't happen. Could not connect to test machine."
