<?php
require_once 'FnordmetricApiRedis.php';

# External connection to Redis server (use phpredis lib)
$redis = new Redis();
$redis->connect('127.0.0.1:6379');

# Instantiate the class, set connect to redis server
$fnord = new FnordmetricApiRedis($redis);

# Send a simple event
$fnord->event('new_registration');

# Send a event with extra data
$data = array('referer' => 'facebook');
$fnord->event('new_registration', $data);

# Send a event with extra data and session token
$sessiontoken = 'rtERydysuTY';
$fnord->event('_set_name', array("name" => "Goodman"), $sessiontoken);
$fnord->event('new_registration', array('referer' => 'twitter'), $sessiontoken);

?>