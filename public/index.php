<?php

use Slim\Factory\AppFactory;
use Dotenv\Dotenv;
use App\Middleware\CorsMiddleware;
use App\Middleware\JsonBodyParserMiddleware;
use App\Middleware\ExceptionMiddleware;

require __DIR__ . '/../vendor/autoload.php';

// Cargar variables de entorno
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

$app = AppFactory::create();

// -------------------------------------------------------------------------
// MIDDLEWARES (LIFO: El último en añadirse corre PRIMERO en la petición)
// -------------------------------------------------------------------------

// IMPORTANTE: NO usamos $app->addErrorMiddleware(...) de Slim porque retorna HTML por defecto.
// En su lugar, confiamos en nuestro propio ExceptionMiddleware (más abajo) para devolver JSON.

// 4. Routing Middleware
$app->addRoutingMiddleware();

// 3. JSON Parser
$app->add(new JsonBodyParserMiddleware());

// 2. CORS
$app->add(new CorsMiddleware());

// 1. Exception Handling (OUTERMOST) - Para atrapar errores de rutas (404/405)
$app->add(new ExceptionMiddleware());

// -------------------------------------------------------------------------
// IMPORTACIÓN DE RUTAS
// -------------------------------------------------------------------------
$routes = require __DIR__ . '/../src/routes.php';
$routes($app);

$app->run();
