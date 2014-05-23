<?php
namespace Proxy\CacheAdapter;

class Memcache implements Cacheable
{
    protected $cache;

    protected $cacheTime;

    public function __construct($server, $port)
    {
        if (!extension_loaded('memcached')) {
            throw new \RuntimeException("ext/memcached is required");
        }
        $this->cache = new \Memcached();
        if (!$this->cache->addServer($server, $port)) {
            throw new \InvalidArgumentException("Memcache server $server:$port is invalid");
        }
    }

    public function get($item)
    {
        return $this->cache->get($item);
    }

    public function set($item, $value)
    {
        $this->cache->set($item, $value, null, $this->cacheTime);
    }

    public function has($item)
    {
        return $this->cache->get($item) !== false;
    }

    public function setCacheTime($time)
    {
        $this->cacheTime = (int)$time;
        return $this;
    }

    public function getCacheTime()
    {
        return $this->cacheTime;
    }
}