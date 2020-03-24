<?php

namespace TaivasAPM\Tracking\Analytics;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use TaivasAPM\Tracking\Models\Request;
use Illuminate\Support\Carbon;

class RecentRequests {
    private $days = 30;

    public function getData() {
        $defaultFields = [
            'started_at_date' => 0,
            'request_duration_avg' => 0,
            'db_duration_avg' => 0,
            'memory_peak_avg' => 0,
            'request_sum' => 0,
        ];
        /** @var Collection $data */
        $data = Request::where('started_at', '>=', Carbon::now()->subDays($this->days))
            ->select([DB::raw('date(started_at) as started_at_date, AVG(request_duration) as request_duration_avg, AVG(db_duration) as db_duration_avg, AVG(memory_peak) / 1024 / 1024 as memory_peak_avg, SUM(1) as request_sum')])
            ->groupBy('started_at_date')
            ->orderBy('started_at_date')
            ->get()
            ->keyBy('started_at_date');

        $now = Carbon::now()->subDays($this->days);
        for($i = 0;$i < $this->days - 1; $i++) {
            $date = $now->format("Y-m-d");
            if(!$data->has($date)) {
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
