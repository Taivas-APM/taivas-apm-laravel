<?php

namespace TaivasAPM\Tracking\Analytics;

use Illuminate\Database\Eloquent\Collection;
use TaivasAPM\Tracking\Models\Request;

class LastRequests {
    private $amount = 10;

    public function getData() {
        /** @var Collection $data */
        $data = Request::select([
                'id',
                'url',
                'started_at',
                'request_duration',
                'db_duration',
                'memory_peak',
            ])
            ->orderByDesc('started_at')
            ->take($this->amount)
            ->get();

        return $data;
    }

    /**
     * @param int $amount
     * @return LastRequests
     */
    public function setAmount(int $amount)
    {
        $this->amount = $amount;
        return $this;
    }
}
