<?php
/**
 * @var Router $router
 */

use Golly\Opcache\Http\Controllers\OpcacheController;
use Illuminate\Routing\Router;

$router->get('opcache/status', [OpcacheController::class, 'status'])->name('opcache.status');
$router->get('opcache/reset', [OpcacheController::class, 'reset'])->name('opcache.reset');
$router->get('opcache/compile', [OpcacheController::class, 'compile'])->name('opcache.compile');
$router->get('opcache/config', [OpcacheController::class, 'config'])->name('opcache.config');
