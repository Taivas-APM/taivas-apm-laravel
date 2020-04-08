<?php

namespace TaivasAPM\Tests\Controller;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AnalyticsControllerTest extends AbstractControllerTest
{
    use RefreshDatabase;

    public function test_the_recent_requests_api_returns_correct_results()
    {
        $this->persistARequest();

        $response = $this->withAuthentication()
            ->get('/taivas/analytics/recent-requests');

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

    public function test_the_last_requests_api_returns_correct_results()
    {
        $this->persistARequest();

        $response = $this->withAuthentication()
            ->get('/taivas/analytics/last-requests');

        $response->assertJsonStructure([
            'lastRequests' => [
                [
                    'id',
                    'url',
                    'started_at',
                    'request_duration',
                    'db_duration',
                    'memory_peak',
                ],
            ],
        ]);
    }

    public function test_the_longest_requests_api_returns_correct_results()
    {
        $this->persistARequest();

        $response = $this->withAuthentication()
            ->get('/taivas/analytics/longest-requests');

        $response->assertJsonStructure([
            'longestRequests' => [
                [
                    'id',
                    'url',
                    'request_duration_avg',
                    'db_duration_avg',
                    'memory_peak_avg',
                    'request_sum',
                ],
            ],
        ]);
    }
}
