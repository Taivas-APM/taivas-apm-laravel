<?php

namespace TaivasAPM\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use TaivasAPM\Analytics\LastRequests;
use TaivasAPM\Analytics\LongestRequests;
use TaivasAPM\Analytics\RecentRequests;

class AnalyticsController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function recentRequests()
    {
        return Response::json([
            'recentRequests' => (new RecentRequests())->getData(),
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function lastRequests()
    {
        return Response::json([
            'lastRequests' => (new LastRequests())->getData(),
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function longestRequests()
    {
        return Response::json([
            'longestRequests' => (new LongestRequests())->getData(),
        ]);
    }
}
