<?php

namespace TaivasAPM\Http\Controllers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use TaivasAPM\TaivasAPM;

class AuthController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = [
            config('taivasapm.api.auth.identifier') => $request->get('email'),
            'password' => $request->get('password'),
        ];

        if(!config('taivasapm.secret')) {
            return app()->abort(403, 'No secret is set. Please set the JWT_SECRET environment variable (see config/taivasapm.php).');
        }

        $auth = auth(TaivasAPM::getGuard());
        if (! $auth->attempt($credentials)) {
            return app()->abort(403, 'Your credentials are invalid');
        }

        $user = $auth->user();
        if (! TaivasAPM::check($user)) {
            return app()->abort(403, 'You are not allowed to enter Taivas');
        }

        return Response::json([
            'token' => $this->getToken($user),
        ]);
    }

    /**
     * @param Authenticatable $user
     * @return string
     */
    private function getToken($user)
    {
        $time = time();
        $signer = new Sha256();
        $token = (new Builder())
            ->issuedBy('https://taivas.io') // Configures the issuer (iss claim)
            ->identifiedBy(Str::random(32)) // Configures the id (jti claim)
            ->issuedAt($time) // Configures the time that the token was issue (iat claim)
            ->canOnlyBeUsedAfter($time) // Configures the time that the token can be used (nbf claim)
            ->expiresAt($time + config('taivasapm.api.auth.lifetime')) // Configures the expiration time of the token (exp claim)
            ->relatedTo($user->getAuthIdentifier()) // Sets the user this token is fore (sub claim)
            ->getToken($signer, new Key(config('taivasapm.secret')));

        return $token->__toString();
    }
}
