<?php

namespace DeviceDetector\Cache;

use Psr\SimpleCache\CacheInterface;

class PSR16Bridge implements Cache
{


    private $cache;

    /**
     * PSR16Bridge constructor.
     * @param CacheInterface $cache
     */
    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }


    public function fetch($id)
    {
        return $this->cache->get($id, false);
    }


    public function contains($id)
    {
        return $this->cache->has($id);
    }


    public function save($id, $data, $lifeTime = 0)
    {
        return $this->cache->set($id, $data, func_num_args() < 3 ? null : $lifeTime);
    }


    public function delete($id)
    {
        return $this->cache->delete($id);
    }


    public function flushAll()
    {
        return $this->cache->clear();
    }
}
