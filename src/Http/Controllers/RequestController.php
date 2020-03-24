<?php

namespace TaivasAPM\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use TaivasAPM\Tracking\Analytics\RequestHistory;
use TaivasAPM\Tracking\Models\Request;

class RequestController extends Controller
{
    /**
     * @param $requestId
     * @return JsonResponse
     */
    public function show($requestId) {
        $request = Request::findOrFail($requestId);
        return Response::json([
            'request' => $request
        ]);
    }

    /**
     * @param $requestId
     * @return JsonResponse
     */
    public function history($requestId) {
        return Response::json([
            'recentRequests' => (new RequestHistory($requestId))->getData(),
        ]);
    }
}
