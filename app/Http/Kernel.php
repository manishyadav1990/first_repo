<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \App\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
       // \App\Http\Middleware\VerifyCsrfToken::class,
        'LucaDegasperi\OAuth2Server\Middleware\OAuthExceptionHandlerMiddleware',
            'Barryvdh\Cors\HandleCors'
    ];
    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'oauth' => 'LucaDegasperi\OAuth2Server\Middleware\OAuthMiddleware',
        'oauth-owner' => 'LucaDegasperi\OAuth2Server\Middleware\OAuthOwnerMiddleware',
        'check-authorization-params' => 'LucaDegasperi\OAuth2Server\Middleware\CheckAuthCodeRequestMiddleware',
        'subdomain' => \App\Http\Middleware\SubdomainSetup::class,
        'setcookie' => \App\Http\Middleware\SetCookieInResponse::class
    ];
}
