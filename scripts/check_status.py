#!/usr/bin/env python

import subprocess, ESL, string, sys, uuid, re, time, argparse
from optparse import OptionParser
from ESL import *

#Argument parser
parser = argparse.ArgumentParser(description='Check status of given call-ID')
parser.add_argument('--uuid', '-u', required=True, help='UUID/Call-ID for Call')
args = parser.parse_args()

uuid = args.uuid
callid = uuid

con = ESL.ESLconnection("127.0.0.1", "8021", "EZnI6D1bKUjI");

if con.connected:
	returnstatus = "api uuid_exists " + callid
	f = con.sendRecv(returnstatus)
	callstatus = f.getBody()
	returnfaxstatus = "api uuid_getvar " + callid + " fax_result_code"
	f = con.sendRecv(returnfaxstatus)
	faxstatus = f.getBody()
else:
	print "No ESL connection..."

if callstatus == "true": 
	print "true"
else:
	print "false"
