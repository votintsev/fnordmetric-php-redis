<?php

/**
 * Fnordmetric PHP API use REDIS
 * Very simple class to send events to Fnordmetric use Redis
 * @author Nikolay Votintsev <nikolay@votintsev.ru>
 */
class FnordmetricApiRedis
{
    /**
     * Redis client
     */
    private $redis = false;

    /**
     * Timeout on key event (on second).
     * Events that aren't processed after this time get dropped
     */
    private $expire = 60;

    /**
     * Prefix for key on Redis
     */
    private $redis_prefix = 'fnordmetric';

    /**
     * Construct, set connect to Redis server
     * @param mixed $redis if you want new connect use string 'host:port',
     * if you want put external connect, set object external connect. Use phpredis.
     */
    public function __construct($redis = '127.0.0.1:6379')
    {
        if (is_string($redis)) {
            $redis = $this->connect($redis);
        }

        $this->setRedis($redis);
    }

    /**
     * Send event to fnordmetric
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
     * Set timeout on key event in Redis
     * @param integer $seconds
     */
    public function setExpire($seconds)
    {
        $this->expire = $seconds;
    }

    /**
     * Set a prefix for key on Redis
     * @param string $prefix
     */
    public function setEventKeyPrefix($prefix)
    {
        $this->event_key_prefix = $prefix;
    }

    /**
     * Connect to Redis server
     * @param string $url
     */
    private function connect($url)
    {
        $redis = new Redis();
        $redis->connect($url);

        return $redis;
    }

    /**
     * Set in $redis connect to Redis client
     * @param type $redis
     */
    private function setRedis(Redis $redis)
    {
        $this->redis = $redis;
    }

    /**
     * Write event in Redis
     * @param array $data
     */
    private function write($data)
    {
        $event_id = uniqid();
        $key = $this->redis_prefix. '-event-' .$event_id;

        $this->redis->hincrby($this->redis_prefix. '-stats', 'events_received', 1);
        $this->redis->setex($key, $this->expire, $data);
        $this->redis->lpush($this->redis_prefix .'-queue', $event_id);
    }
}