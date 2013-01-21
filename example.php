<?php
require_once 'FnordmetricApiRedis.php';

# Instantiate the class, create new connect to redis server on 127.0.0.1:6379 and select database 10
$fnord = new FnordmetricApiRedis('new', '127.0.0.1', 6379, 10);

# Send a simple event
$fnord->event('mail_send');

# Send a event with extra data
$data = array('colour' => 'rainbow', 'name' => 'Luna');
$fnord->event('mail_send', $data);

?>