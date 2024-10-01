<?php

namespace DeviceDetector\Cache;

use Psr\Cache\CacheItemPoolInterface;

class PSR6Bridge implements Cache
{

    private $pool;

    /**
     * PSR6Bridge constructor.
     * @param CacheItemPoolInterface $pool
     */
    public function __construct(CacheItemPoolInterface $pool)
    {
        $this->pool = $pool;
    }


    public function fetch($id)
    {
        $item = $this->pool->getItem($id);
        return $item->isHit() ? $item->get() : false;
    }


    public function contains($id)
    {
        return $this->pool->hasItem($id);
    }


    public function save($id, $data, $lifeTime = 0)
    {
        $item = $this->pool->getItem($id);
        $item->set($data);
        if (func_num_args() > 2) {
            $item->expiresAfter($lifeTime);
        }
        return $this->pool->save($item);
    }


    public function delete($id)
    {
        return $this->pool->deleteItem($id);
    }


    public function flushAll()
    {
        return $this->pool->clear();
    }
}
