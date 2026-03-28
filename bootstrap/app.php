<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // Trust all proxies — para gumana ang ngrok/cloudflare tunnel
        $middleware->trustProxies(at: '*');

        // ALIASES
        $middleware->alias([
            'voter.timeout' => \App\Http\Middleware\CheckSessionTimeout::class,
            'admin.2fa'     => \App\Http\Middleware\Voter2FAMiddleware::class,
            'admin2fa'      => \App\Http\Middleware\Admin2FAMiddleware::class,
        ]);

        // FORCE REDIRECT TO CORRECT LOGIN
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('vote/*') || $request->is('voter/*') || $request->is('election/*')) {
                return route('voter.login');
            }
            return route('login');
        });

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();