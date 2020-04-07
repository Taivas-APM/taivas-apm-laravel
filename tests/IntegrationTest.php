<?php

namespace TaivasAPM\Tests;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use Mockery;
use Orchestra\Testbench\TestCase;
use TaivasAPM\Http\Middleware\Authenticate;
use TaivasAPM\Models\Request;
use TaivasAPM\TaivasAPM;
use TaivasAPM\TaivasAPMServiceProvider;
use TaivasAPM\Tests\Controller\Fakes\User;
use TaivasAPM\Tracking\Persister;

abstract class IntegrationTest extends TestCase
{


    /**
     * Setup the test case.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        Redis::flushall();
    }

    /**
     * Tear down the test case.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        Redis::flushall();
        TaivasAPM::$authUsing = null;
    }

    /**
     * Configure the environment.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('queue.default', 'redis');
        $app['config']->set('auth.providers.users.model', User::class);
        $userProvider = Mockery::mock(Authenticate::class);
        $userProvider
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getUser')
            ->andReturn(new User());
        $userProvider->makePartial();
        $app->instance(Authenticate::class, $userProvider);
    }

    protected function persistARequest()
    {
        $request = new \TaivasAPM\Tracking\Request();
        $request->setQueries([]);
        $request->setMaxMemory(1928734);
        $request->setUrl('/test');
        $request->setStartedAt(Carbon::now()->format('U.v'));
        $request->setStoppedAt(Carbon::now()->addSeconds(1)->addMilliseconds(14)->format('U.v'));
        /** @var Persister $persister */
        $persister = $this->app['taivas.persister'];
        $persister->persist($request);
    }

    protected function withAuthentication()
    {
        $time = time();
        $signer = new Sha256();
        $token = (new Builder())
            ->issuedBy('https://taivas.io') // Configures the issuer (iss claim)
            ->identifiedBy(Str::random(32)) // Configures the id (jti claim)
            ->issuedAt($time) // Configures the time that the token was issue (iat claim)
            ->canOnlyBeUsedAfter($time) // Configures the time that the token can be used (nbf claim)
            ->expiresAt($time + config('taivasapm.api.auth.lifetime')) // Configures the expiration time of the token (exp claim)
            ->relatedTo(1) // Sets the user this token is fore (sub claim)
            ->getToken($signer, new Key(config('taivasapm.secret')));

        $token = $token->__toString();
        $this->defaultHeaders = ['Authorization' => 'Bearer ' . $token];
        return $this;
    }

    protected function getPackageProviders($app)
    {
        return [
            TaivasAPMServiceProvider::class,
        ];
    }
}
