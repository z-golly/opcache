<?php


namespace Golly\Opcache;


use Symfony\Component\Finder\Finder;
use Throwable;

/**
 * Class OpcacheService
 * @package Golly\OpcacheService
 */
class OpcacheService
{

    /**
     * @return bool
     */
    public static function reset()
    {
        $result = false;
        if (function_exists('opcache_reset')) {
            $result = opcache_reset();
        }

        return $result;
    }

    /**
     * @return array
     */
    public static function config()
    {
        $result = [];
        if (function_exists('opcache_get_configuration')) {
            $result = opcache_get_configuration();
        }

        return $result;
    }

    /**
     * @return array
     */
    public static function status()
    {
        $result = [];
        if (function_exists('opcache_get_status')) {
            $result = opcache_get_status(false);
        }
        return $result;
    }


    /**
     * @param false $force
     * @return array
     */
    public static function compile($force = false)
    {
        // 仅作为针对 “不可重定义类”错误的一种解决方案
        if (!ini_get('opcache.dups_fix') && !$force) {
            return ['message' => 'opcache.dups_fix must be enabled, or run with --force'];
        }

        $count = $compiled = 0;
        $notCompiledFiles = [];
        if (function_exists('opcache_compile_file')) {
            // Get files in these paths
            $files = Finder::create()
                ->in(config('opcache.directories'))
                ->name('*.php')
                ->ignoreUnreadableDirs()
                ->notContains('#!/usr/bin/env php')
                ->exclude(config('opcache.exclude'))
                ->files()
                ->followLinks();
            $count = $files->count();
            // optimized files
            foreach ($files as $file) {
                try {
                    if (!opcache_is_script_cached($file)) {
                        opcache_compile_file($file);
                    }
                    $compiled++;
                } catch (Throwable $e) {
                    $notCompiledFiles[] = $file . ':' . $e->getMessage();
                }
            }
        }

        return [
            'files_count' => $count,
            'compiled_count' => $compiled,
            'not_compiled_files' => $notCompiledFiles
        ];
    }
}
