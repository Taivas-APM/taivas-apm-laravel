<?php

namespace TaivasAPM\Tracking;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Tracker
{
    private $request = null;

    /**
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public function shouldTrack($request)
    {
        return ! Str::startsWith($request->getPathInfo(), '/taivasapm/');
    }

    /**
     * @param \Illuminate\Http\Request $request
     */
    public function start($request)
    {
        $this->getRequest()->setStartedAt(microtime(true) * 1000);
        $this->getRequest()->setUrl($request->getUri());
    }

    public function stop()
    {
        $this->getRequest()->setStoppedAt(microtime(true) * 1000);
        $this->getRequest()->setMaxMemory(memory_get_peak_usage(true));
    }

    public function logDBQueries()
    {
        $queries = DB::getQueryLog();
        $this->getRequest()->setQueries($queries);
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        if ($this->request === null) {
            $this->request = new Request();
        }

        return $this->request;
    }
}
