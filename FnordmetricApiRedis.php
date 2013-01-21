<?php
require_once 'FnordmetricApiRedisException.php';

/**
 * 
 */
class FnordmetricApiRedis
{
    /**
     * Redis client
     * @var Redis
     */
    private $redis = false;

    /**
     * 
     * @param type $redis_init
     * @param mixed $db
     * @param  $db
     */
    public function __construct($redis, $host = '127.0.0.1', $port = 6379, $db = false)
    {
        if ($redis == 'new') {
            $redis = $this->connect($host, $port, $db);
        }

        $this->setRedis($redis);
    }

    /**
     * Connect to Redis server
     * @param string $host
     * @param integer $port
     * @param integer $db
     */
    private function connect($host = '127.0.0.1', $port = 6379, $db = false)
    {
        $redis = new Redis();
        $redis->connect($host.':'.$port);
        if ($db !== false) {
            $this->redis->select($db);
        }

        return $redis;
    }

    /**
     * Set in $redis Redis client
     * @param type $redis
     */
    private function setRedis($redis)
    {
        $this->redis = $redis;
    }

    /**
     * 
     * @param string $name 
     * @param array $data
     * @param type $session
     */
    public function event($name, array $data = array(), $session = false)
    {
        $data['_type'] = $name;

        if ($session) {
            $data['_session'] = $session;
        }

        $data = json_encode($data);
        $this->write($data);
    }

    /**
     * Write event in Redis
     * @param array $data
     */
    private function write(array $data)
    {
        $event_id = uniqid();
        $key = 'fnordmetric-event-'.$event_id;
        $this->redis->setex($key, 60, $data);
        $this->redis->lPush('fnordmetric-queue', $event_id);
    }
}