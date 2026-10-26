<?php

declare(strict_types=1);

use App\Domain\Shared\DomainRuleViolation;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\RedirectResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware()
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontReport(DomainRuleViolation::class);
        $exceptions->render(fn (DomainRuleViolation $exception): RedirectResponse => back()
            ->withInput()
            ->withErrors(['domain' => $exception->getMessage()]));
    })->create();
