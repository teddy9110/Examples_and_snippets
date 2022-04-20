<?php

namespace Rhf\Modules\System\Services;

use Generator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redis;

abstract class CacheService
{
    protected string $hashKey;

    public function __construct(string $hashKey)
    {
        $this->hashKey = $hashKey;
    }

    /**
     * Get hash key value.
     *
     * @return string
     */
    public function getHashKey(): string
    {
        return $this->hashKey;
    }

    /**
     * Get the cache object of the passed in key
     *
     * @param string $key
     * @return mixed
     */
    public function getCache(string $key)
    {
        return Redis::hget($this->hashKey, $key);
    }

    /**
     * Set the cache of an item using a defined key
     * Values of the cache
     * Expiration of the cache in seconds
     *
     * @param string $key
     * @param $values
     * @param integer $expiration
     * @return void
     */
    public function setCache(string $key, $values, int $expiration = null)
    {
        $value = '';

        if ($values instanceof Collection) {
            $value = json_encode($values);
        }

        if (is_array($values)) {
            $value = json_encode(collect($values));
        }

        if (is_string($values)) {
            $value = $values;
        }

        if (!is_null($expiration)) {
            $cached = Redis::hset($this->hashKey, $key, $value);
            Redis::expire($this->hashKey, $expiration);
            return $cached;
        }

        return Redis::hset($this->hashKey, $key, $value);
    }

    /**
     * Delete cached item based on key
     *
     * @param string $key
     * @return void
     */
    public function deleteCache(string $key)
    {
        if (!is_null($this->getCache($key))) {
            return Redis::hdel($this->hashKey, $key);
        }
    }

    /**
     * Find keys via wildcard search and returns an array
     *
     * @return array $matchingKeys
     */
    public function findKeys(): array
    {
        $matchingKeys = Redis::hkeys($this->hashKey);
        return array_values($matchingKeys);
    }

    /**
     * DO NOT USE, TOO SLOW
     *
     * Correct implementation of SCAN. Keyspace without an iterator will only check a limited
     * amount of keys. With an iterator however, it can take a very long time to find the keys.
     * For example in a 44k key long database, it times out the request.
     *
     * @param string $pattern
     * @return Generator
     */
    private function scanKeysByPattern(string $pattern): Generator
    {
        $cursor = 0;
        do {
            list($cursor, $keys) = Redis::scan($cursor, 'match', $pattern);
            foreach ($keys as $key) {
                yield $key;
            }
        } while ($cursor);
    }
}
