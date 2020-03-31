<?php

namespace TaivasAPM\Analytics;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use TaivasAPM\Models\Request;

/**
 * All requests from the last x days grouped by request url.
 */
class RequestsByUrl
{
    private $days = 1;

    public function getData()
    {
        $data = Request::where('started_at', '>=', Carbon::now()->subDays($this->days))
            ->select(['url', DB::raw('MAX(id) as id, AVG(request_duration) as request_duration_avg, AVG(db_duration) as db_duration_avg, AVG(memory_peak) / 1024 / 1024 as memory_peak_avg, SUM(1) as request_sum')])
            ->groupBy('url')
            ->orderBy('request_sum', 'DESC')
            ->get();

        $data->transform(function ($entry) {
            $entry->request_duration_avg = (float) $entry->request_duration_avg;
            $entry->db_duration_avg = (float) $entry->db_duration_avg;
            $entry->memory_peak_avg = (float) $entry->memory_peak_avg;
            $entry->request_sum = (int) $entry->request_sum;

            return $entry;
        });

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
