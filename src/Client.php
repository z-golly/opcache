<?php


namespace Golly\Opcache;


use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

/**
 * Trait Client
 * @package Golly\Opcache
 */
trait Client
{

    /**
     * @param string $path
     * @param array $params
     * @return Response
     */
    public function get(string $path, array $params = [])
    {
        return Http::withHeaders(config('opcache.headers', []))
            ->baseUrl(config('app.url'))
            ->withOptions(['verify' => config('opcache.verify')])
            ->get($path, array_merge([
                'key' => Crypt::encrypt('opcache')
            ], $params));
    }
}
