<?php


namespace Golly\Opcache\Http\Middleware;


use Closure;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\UnauthorizedException;

/**
 * Class OpcacheMiddleware
 * @package Golly\Opcache\Http\Middleware
 */
class OpcacheMiddleware
{

    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$this->isEquelKey($request) || !$this->isAllowedIp($request)) {
            throw new UnauthorizedException('This action is unauthorized.');
        }

        return $next($request);
    }


    /**
     * Check if the request is allowed.
     *
     * @param Request $request
     * @return bool
     */
    protected function isEquelKey(Request $request)
    {
        try {
            $decrypted = Crypt::decrypt($request->get('key'));
        } catch (DecryptException $e) {
            $decrypted = '';
        }

        return $decrypted == 'opcache';
    }

    /**
     * @param Request $request
     * @return bool
     */
    protected function isAllowedIp(Request $request)
    {
        return true;
    }

}
