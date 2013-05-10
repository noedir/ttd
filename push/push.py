#!/usr/bin/env python
# -*- coding: utf-8 -*-
'''
	Sample code for Apple Push Notification Service in Python

	Author: Jacky Tsoi <jacky@tsoi.me>
	Date: December 15, 2011
'''

import ssl
try:
    import json
except ImportError:
    import simplejson as json
import socket
import struct
import binascii
import sys
import sqlite3 as lite
from threading import Thread

con = None

try:
	con = lite.connect('push.db')
	cur = con.cursor()
	cur.execute('SELECT * FROM enviapush')
except lite.Error, e:
	sys.exit(1)

def send_push_message(token,payload):
	# the certificate file generated from Provisioning Portal
	certfile = 'apns-dev.pem'
	
	# APNS server address (use 'gateway.push.apple.com' for production server)
	apns_address = ('gateway.sandbox.push.apple.com', 2195)

	# create socket and connect to APNS server using SSL
	s = socket.socket()
	sock = ssl.wrap_socket(s, ssl_version=ssl.PROTOCOL_SSLv3, certfile=certfile)
	sock.connect(apns_address)

	# generate APNS notification packet
	token = binascii.unhexlify(token)
	fmt = "!cH32sH{0:d}s".format(len(payload))
	cmd = '\x00'
	msg = struct.pack(fmt, cmd, len(token), token, len(payload), payload)
	sock.write(msg)
	sock.close()

#if __name__ == '__main__':
count = 0

con = None

try:
        con = lite.connect('push.db')
        cur = con.cursor()
        cur.execute('SELECT * FROM enviapush')
except lite.Error, e:
        sys.exit(1)

for x in cur.fetchall():
	
	payload = {"aps": {"alert" : x[2], "sound": "default", "counts": x[3]}}
	#th=Thread( target=send_push_message, args=("064e0cf07193b1baa888a5f589f3801f3348a847166dc3360d29dd3e4eb322f5",json.dumps(payload)))
	th=Thread( target=send_push_message, args=(x[1],json.dumps(payload)))
	if(count < 2):
		th.start()
		th.join()
	count = count + 1
	print "Foi enviado"

cur.execute('DELETE FROM enviapush')
con.commit()
cur.close()
