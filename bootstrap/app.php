<?php

use Illuminate\Foundation\Application;

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Create a new Laravel application instance which serves as the glue
| for all components and acts as the IoC container.
|
*/

$app = new Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

/*
|--------------------------------------------------------------------------
| Set Public Path (IMPORTANT)
|--------------------------------------------------------------------------
|
| This project is configured to use the PROJECT ROOT as the public path.
| This is required because on shared hosting (Hostinger), the contents of
| Laravel's /public directory are served directly from the web root.
|
| Result:
| - public_path() === base_path()
| - URLs must include /public/ for files inside the public folder
| - build/, assets/, css/, js/ are resolved correctly
|
*/

$app->usePublicPath($app->basePath());

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Bind the HTTP kernel, Console kernel, and Exception handler.
|
*/

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| Return the application instance.
|
*/

return $app;
