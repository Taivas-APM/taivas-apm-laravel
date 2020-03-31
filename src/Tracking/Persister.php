<?php

namespace TaivasAPM\Tracking;

use Illuminate\Contracts\Redis\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use TaivasAPM\LuaScripts;

class Persister
{
    /**
     * The Redis database instance.
     *
     * @var Factory
     */
    private $redis;

    public function __construct($redis)
    {
        $this->redis = $redis;
    }

    public function persist(Request $request)
    {
        $data = $this->formatData($request);
        switch (config('taivasapm.tracking.persistence_driver')) {
            case 'sync':
                $this->persistImmediately($data);
                break;
            case 'redis':
                $this->queueViaRedis($data);
                break;
        }
    }

    private function formatData(Request $request)
    {
        return [
            'url' => $request->getUrl(),
            'started_at' => Carbon::createFromFormat('U.v', $request->getStartedAt() / 1000)->format('Y-m-d H:i:s'),
            'stopped_at' => Carbon::createFromFormat('U.v', $request->getStoppedAt() / 1000)->format('Y-m-d H:i:s'),
            'request_duration' => intval($request->getRequestDuration()),
            'db_duration' => intval($request->getDBDuration()),
            'db_count' => $request->getDBCount(),
            'db_queries' => $request->getSanitizedDBQueries(),
            'memory_peak' => $request->getMaxMemory(),
        ];
    }

    private function queueViaRedis($requestData)
    {
        $this->redis->connection()->rpush('taivasapm:queue', serialize($requestData));
    }

    private function persistImmediately($data)
    {
        $this->persistItems(collect([$data]));
    }

    public function persistQueuedJobs()
    {
        $listKey = 'taivasapm:queue';
        $connection = $this->redis->connection();
        $listLength = $connection->llen($listKey);
        $chunkSize = 100;
        for ($i = 0; $i < ceil($listLength / $chunkSize); $i++) {
            $items = $connection->eval(LuaScripts::lpopMany(), 1, $listKey, $chunkSize);
            $items = collect($items);
            $listLength = $connection->llen($listKey);
            $items->transform(function ($item) {
                return unserialize($item);
            });
            $this->persistItems($items);
        }
    }

    private function persistItems(Collection $items)
    {
        $now = date('Y-m-d H:i:s');
        $items->transform(function ($item) use ($now) {
            $item['created_at'] = $now;
            $item['updated_at'] = $now;

            return $item;
        });
        \TaivasAPM\Models\Request::insert($items->toArray());
    }
}
