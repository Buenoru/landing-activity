<?php

declare(strict_types=1);

namespace App\Service;

use App\Message\Activity;
use Generator;
use Redis;

class ActivityService
{
    private const PREFIX = 'activity';

    public function __construct(
        private RedisClient $client
    ) {
    }

    public function addOrIncrement(Activity $activity)
    {
        $key = sprintf('%s:%s', self::PREFIX, $activity->getUrl());
        $date = $activity->getDateTime()->format('c');
        if ($this->client->hSetNx($key, 'lastVisited', $date)) {
            $this->client->hSet($key, 'count', 1);
        } else {
            $this->client->hSet($key, 'lastVisited', $date);
            $this->client->hIncrBy($key, 'count', 1);
        }
    }

    /**
     * @return Generator<array>
     */
    public function getList(): Generator
    {
        $this->client->setOption(Redis::OPT_SCAN, Redis::SCAN_RETRY);
        $it = null;
        while ($keys = $this->client->scan($it, 'activity:*')) {
            foreach ($keys as $key) {
                $url = str_replace(self::PREFIX . ':', '', $key);
                yield ['url' => $url, ...$this->client->hGetAll($key)];
            }
        }
    }
}
