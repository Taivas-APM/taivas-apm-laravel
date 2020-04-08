<?php

namespace TaivasAPM\Tests\Controller;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RequestControllerTest extends AbstractControllerTest
{
    use RefreshDatabase;

    public function test_the_request_list_api_returns_correct_results()
    {
        $this->persistARequest();

        $response = $this->withAuthentication()
                    ->get('/taivas/requests');

        $response->assertJsonStructure([
            'requests' => [
                0 => [
                    'url',
                    'id',
                    'request_duration_avg',
                    'db_duration_avg',
                    'memory_peak_avg',
                    'request_sum',
                ],
            ],
        ]);
    }

    public function test_the_request_details_api_returns_correct_results()
    {
        $this->persistARequest();

        $response = $this->withAuthentication()
            ->get('/taivas/requests/1');

        $response->assertJsonStructure([
            'request' => [
                'id',
                'url',
                'started_at',
                'stopped_at',
                'request_duration',
                'db_duration',
                'db_count',
                'db_queries',
                'memory_peak',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function test_the_request_history_api_returns_correct_results()
    {
        $this->persistARequest();

        $response = $this->withAuthentication()
            ->get('/taivas/requests/1/history');

        $today = Carbon::now()->format('Y-m-d');
        $response->assertJsonStructure([
            'recentRequests' => [
                $today => [
                    'started_at_date',
                    'request_duration_avg',
                    'db_duration_avg',
                    'memory_peak_avg',
                    'request_sum',
                ],
            ],
        ]);
    }
}
