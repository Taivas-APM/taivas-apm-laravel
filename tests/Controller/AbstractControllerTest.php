<?php

namespace TaivasAPM\Tests\Controller;

use Illuminate\Auth\EloquentUserProvider;
use TaivasAPM\TaivasAPM;
use TaivasAPM\Tests\Controller\Fakes\User;
use TaivasAPM\Tests\IntegrationTest;

abstract class AbstractControllerTest extends IntegrationTest
{
    protected function setUp(): void
    {
        parent::setUp();

        TaivasAPM::auth(function () {
            return true;
        });
    }
}
