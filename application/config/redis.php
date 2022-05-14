<?php
$config['socket_type'] = 'tcp'; //`tcp` or `unix`
$config['socket'] = '/var/run/redis.sock'; // in case of `unix` socket type
$config['host'] = '127.0.0.1'; //change this to match your amazon redis cluster node endpoint
$config['password'] = NULL;
$config['port'] = 3033;
$config['timeout'] = 0;