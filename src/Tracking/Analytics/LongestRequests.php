<?php

namespace TaivasAPM\Tracking\Analytics;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use TaivasAPM\Tracking\Models\Request;

class LongestRequests
{
    private $days = 1;

    public function getData()
    {
        /** @var Collection $data */
        $data = Request::where('started_at', '>=', Carbon::now()->subDays($this->days))
            ->select([
                'id',
                'url',
                DB::raw('AVG(request_duration) as request_duration_avg, AVG(db_duration) as db_duration_avg, AVG(memory_peak) as memory_peak_avg, SUM(1) as request_sum'),
            ])
            ->groupBy('url')
            ->orderByDesc('request_duration_avg')
            ->take(10)
            ->get();

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
