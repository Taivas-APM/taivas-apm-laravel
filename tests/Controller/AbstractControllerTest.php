<?php

namespace TaivasAPM\Tests\Controller;

use TaivasAPM\TaivasAPM;
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
