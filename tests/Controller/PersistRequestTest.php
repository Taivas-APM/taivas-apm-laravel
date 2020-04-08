<?php

namespace TaivasAPM\Tests\Controller;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use TaivasAPM\Models\Request;

class PersistRequestTest extends AbstractControllerTest
{
    use RefreshDatabase;
    public function test_request_data_is_persisted()
    {
        Route::get('/test', function() {
            return 1;
        });

        $this->get('/test');
        $this->get('/test');

        $this->assertEquals(2, Request::count());
    }
}
