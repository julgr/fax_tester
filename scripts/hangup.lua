--hangup.lua
--freeswitch hangup hook
--just call the python script
callid = env:getHeader("sip_call_id")
fax_success = env:getHeader("fax_success")
fax_result_code = env:getHeader("fax_result_code")
fax_result_text = env:getHeader("fax_result_text")

command = "/usr/bin/python /usr/share/nginx/scripts/htmllog.py " .. callid .. " " .. fax_success .. " " .. fax_result_code .. " \"" .. fax_result_text .. "\""
freeswitch.consoleLog("info", "Your command is "..command.." \n")
os.execute(command)
