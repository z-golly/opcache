<?php


namespace Golly\Opcache\Http\Controllers;

use Golly\Opcache\OpcacheService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Class OpcacheController
 * @package Golly\OpcacheService\Http\Controllers
 */
class OpcacheController extends Controller
{
    /**
     * Reset the opcache.
     *
     * @return JsonResponse
     */
    public function reset()
    {
        return response()->json(['result' => OpcacheService::reset()]);
    }

    /**
     * Get config values.
     *
     * @return JsonResponse
     */
    public function config()
    {
        return response()->json(['result' => OpcacheService::config()]);
    }

    /**
     * Get status info.
     *
     * @return JsonResponse
     */
    public function status()
    {
        return response()->json(['result' => OpcacheService::status()]);
    }

    /**
     * Compile.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function compile(Request $request)
    {
        return response()->json(['result' => OpcacheService::compile($request->get('force'))]);
    }
}
