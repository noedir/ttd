<?php

// Put your device token here (without spaces):
//$deviceToken = '064e0cf07193b1baa888a5f589f3801f3348a847166dc3360d29dd3e4eb322f5';

//token ipod preto
//$deviceToken = '94b08e42aee29d453e4768176fef7fc98f940e97cdcc208f0ce938ee0d3b36ec';

//token ipod branco
//$deviceToken = '064e0cf07193b1baa888a5f589f3801f3348a847166dc3360d29dd3e4eb322f5';

//token iphone Daniel
$deviceToken = '6a92e1672bcae29c1c34f953133e23487adb798c688057dd2dd82874ddcf90b4';

// Put your private key's passphrase here:
$passphrase = '200402';

// Put your alert message here:
$message = 'VAMOS QUE VAMOS....';

////////////////////////////////////////////////////////////////////////////////

$ctx = stream_context_create();
stream_context_set_option($ctx, 'ssl', 'local_cert', 'apns-dev.pem');
stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

// Open a connection to the APNS server
$fp = stream_socket_client(
	'ssl://gateway.sandbox.push.apple.com:2195', $err,
	$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

if (!$fp)
	exit("Failed to connect: $err $errstr" . PHP_EOL);

echo 'Connected to APNS' . PHP_EOL;

// Create the payload body
$body['aps'] = array(
	'alert' => $message,
	'sound' => 'default'
	);

// Encode the payload as JSON
$payload = json_encode($body);

// Build the binary notification
$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

// Send it to the server
$result = fwrite($fp, $msg, strlen($msg));

if (!$result)
	echo 'Message not delivered' . PHP_EOL;
else
	echo 'Message successfully delivered' . PHP_EOL;

// Close the connection to the server
fclose($fp);
