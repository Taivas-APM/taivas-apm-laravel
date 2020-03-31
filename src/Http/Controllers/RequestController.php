<?php

namespace TaivasAPM\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use TaivasAPM\Analytics\RequestHistory;
use TaivasAPM\Analytics\RequestsByUrl;
use TaivasAPM\Models\Request;

class RequestController extends Controller
{
    public function index()
    {
        return Response::json([
            'requests' => (new RequestsByUrl())->getData(),
        ]);
    }

    /**
     * @param $requestId
     * @return JsonResponse
     */
    public function show($requestId)
    {
        $request = Request::findOrFail($requestId);

        return Response::json([
            'request' => $request,
        ]);
    }

    /**
     * @param $requestId
     * @return JsonResponse
     */
    public function history($requestId)
    {
        return Response::json([
            'recentRequests' => (new RequestHistory($requestId))->getData(),
        ]);
    }
}
