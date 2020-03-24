<?php

namespace TaivasAPM\Tracking;

use Illuminate\Support\Carbon;

class Persister {
    public function persist(Request $request) {
        $requestModel = new \TaivasAPM\Tracking\Models\Request();
        $requestModel->url = $request->getUrl();
        $requestModel->started_at = Carbon::createFromFormat('U.v', $request->getStartedAt() / 1000);
        $requestModel->stopped_at = Carbon::createFromFormat('U.v', $request->getStoppedAt() / 1000);
        $requestModel->request_duration = intval($request->getRequestDuration());
        $requestModel->db_duration = intval($request->getDBDuration());
        $requestModel->db_count = $request->getDBCount();
        $requestModel->db_queries = $request->getSanitizedDBQueries();
        $requestModel->memory_peak = $request->getMaxMemory();
        $requestModel->save();
    }
}
