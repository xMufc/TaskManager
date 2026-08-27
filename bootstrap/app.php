<?php

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
        $exceptions->render(function (InvalidStatusTransitionException $e, $request) {
            if ($request->header('X-Inertia')) {
                return back()->withErrors(['status' => $e->getMessage()]);
            }

            return response()->json(['message' => $e->getMessage()], 422);
        });
        $exceptions->render(function (TaskNotFoundException $e, $request) {
            if ($request->header('X-Inertia')) {
                return back()->withErrors(['task' => $e->getMessage()]);
            }

            return response()->json(['message' => $e->getMessage()], 404);
        });
    })->create();
