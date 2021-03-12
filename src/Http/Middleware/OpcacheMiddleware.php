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
        return in_array($this->getRequestIp($request), [
            $this->getServerIp(),
            '127.0.0.1',
            '::1'
        ]);
    }

    /**
     * Get ip from different request headers.
     *
     * @param Request $request
     * @return array|string|null
     */
    protected function getRequestIp(Request $request)
    {
        if ($request->server('HTTP_CF_CONNECTING_IP')) {
            // cloudflare
            return $request->server('HTTP_CF_CONNECTING_IP');
        }

        if ($request->server('X_FORWARDED_FOR')) {
            // forwarded proxy
            return $request->server('X_FORWARDED_FOR');
        }

        if ($request->server('REMOTE_ADDR')) {
            // remote header
            return $request->server('REMOTE_ADDR');
        }

        return $request->ip();
    }

    /**
     * Get the server ip.
     *
     * @return string
     */
    protected function getServerIp()
    {
        if (isset($_SERVER['SERVER_ADDR'])) {
            return $_SERVER['SERVER_ADDR'];
        }

        if (isset($_SERVER['LOCAL_ADDR'])) {
            return $_SERVER['LOCAL_ADDR'];
        }

        return '127.0.0.1';
    }

}
