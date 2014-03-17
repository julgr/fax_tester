#!/usr/bin/env python


# Do bash shit
# Find the call
# Copy file to /var/tmp/frnocuser
# parse historical file to T.38-only capture
# rm historical file
# return path to T.38-only

import re, os, subprocess, argparse, shutil

#Argument parser
parser = argparse.ArgumentParser(description='Grab a T.38 Capture')
parser.add_argument('--callid', '-c', required=True, help='Call-ID of the call to grab')
args = parser.parse_args()

callid = args.callid

full_capture_path = "/var/tmp/frnocuser/" + callid + "_historical.pcap"
#copy historical capture to frnocuser home
historical_path = subprocess.check_output(["/bin/find_call", str(callid)])
pattern = re.compile(r'\s+')
historical_path = re.sub(pattern, '', historical_path)
if len(historical_path) > 38:
	mergecommand = ["/usr/bin/mergecap", "-w", full_capture_path]
	while historical_path != "":
		mergecommand.append(historical_path[0:38])
		print historical_path[0:38] + " inwhile"
		historical_path = historical_path[38:]
	subprocess.call(mergecommand)
else:
	shutil.copyfile(historical_path, full_capture_path)
parsed_path = "/var/tmp/frnocuser/" + str(callid) + ".pcap"
#filter to only relevant T.38
#output to callid + ".pcap"
subprocess.call(["/bin/t38_capture_parser", str(callid), str(full_capture_path)], cwd="/var/tmp/frnocuser")

#delete historical capture moved to frnocuser home
if os.path.exists(parsed_path):
	os.remove(full_capture_path)
else:
	print "uh oh spaghetti-os!"
