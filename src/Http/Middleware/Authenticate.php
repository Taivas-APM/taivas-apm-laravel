<?php

namespace TaivasAPM\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\ValidationData;
use TaivasAPM\TaivasAPM;

class Authenticate
{
    /**
     * Handle the incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return Response|void
     */
    public function handle($request, $next)
    {
        $token = $this->getToken($request);
        if (! $token) {
            return abort(403, 'Could not get token from the request');
        }

        $isValid = $this->tokenIsValid($token);

        if (! $isValid) {
            return abort(403, 'Your token is not valid');
        }

        $user = $this->getUser($token);

        if (! $user) {
            return abort(403, 'Could not get your user object');
        }

        return TaivasAPM::check($user) ? $next($request) : abort(403, 'This user is not allowed to enter Taivas');
    }

    /**
     * @param Request $request
     * @return Token|null
     */
    private function getToken($request)
    {
        $header = $request->header('Authorization');
        if ($header && preg_match('/Bearer\s*(\S+)\b/i', $header, $matches)) {
            if ($token = $matches[1]) {
                return (new Parser())->parse((string) $token);
            }
        }
    }

    /**
     * @param Token $token
     * @return bool
     */
    private function tokenIsValid($token)
    {
        $validation = new ValidationData();
        $validation->setIssuer('https://taivas.io');

        if (! $token->validate($validation)) {
            return false;
        }

        $signer = new Sha256();
        if (! $token->verify($signer, config('taivasapm.secret'))) {
            return false;
        }

        return true;
    }

    /**
     * @param Token $token
     * @return Authenticatable|null
     */
    private function getUser($token)
    {
        $providerName = config('auth.guards.'.TaivasAPM::getGuard().'.provider');
        /** @var AuthManager $authManager */
        $authManager = app('auth');
        $userProvider = $authManager->createUserProvider($providerName);
        $user = $userProvider->retrieveById($token->getClaim('sub'));

        return $user;
    }
}
