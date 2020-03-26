<?php

namespace TaivasAPM\Tracking\Analytics;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use TaivasAPM\Tracking\Models\Request;

/**
 * A collection of request statistics grouped by day
 * [
 *     '2020-03-26' => [
 *          'request_duration_avg' => 13,
 *          'db_duration_avg' => 3,
 *          ...
 *      ],
 *      ...
 * ].
 */
class RequestHistory
{
    private $days = 30;
    private $requestId;

    public function __construct($requestId)
    {
        $this->requestId = $requestId;
    }

    public function getData()
    {
        $defaultFields = [
            'started_at_date' => 0,
            'request_duration_avg' => 0,
            'db_duration_avg' => 0,
            'memory_peak_avg' => 0,
            'request_sum' => 0,
        ];
        $baseRequest = Request::findOrFail($this->requestId);
        /** @var Collection $data */
        $data = Request::where('started_at', '>=', Carbon::now()->subDays($this->days))
            ->where('url', $baseRequest->url)
            ->select([DB::raw('date(started_at) as started_at_date, AVG(request_duration) as request_duration_avg, AVG(db_duration) as db_duration_avg, AVG(memory_peak) / 1024 / 1024 as memory_peak_avg, SUM(1) as request_sum')])
            ->groupBy('started_at_date')
            ->orderBy('started_at_date')
            ->get()
            ->keyBy('started_at_date');

        $now = Carbon::now()->subDays($this->days);
        for ($i = 0; $i < $this->days - 1; $i++) {
            $date = $now->format('Y-m-d');
            if (! $data->has($date)) {
                $entry = $defaultFields;
                $entry['started_at_date'] = $date;
                $data->put($date, $entry);
            }
            $now->addDay();
        }
        $data = $data->sortBy('started_at_date');

        return $data;
    }

    /**
     * @param int $days
     */
    public function setDays(int $days): void
    {
        $this->days = $days;
    }
}
