<?php

use Slim\Factory\AppFactory;
use Dotenv\Dotenv;
use App\Middleware\CorsMiddleware;
use App\Middleware\ExceptionMiddleware;
use App\Controllers\SwaggerController;

require __DIR__ . '/../vendor/autoload.php';

// Soporte para archivos estáticos (Swagger UI index.html, css, js)
if (PHP_SAPI === 'cli-server') {
    $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $file = realpath(__DIR__ . $url);
    if ($file && str_starts_with($file, __DIR__) && is_file($file)) {
        return false;
    }
}

// Cargar variables de entorno
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

$app = AppFactory::create();

// 1. Manejo Global de Excepciones
$app->add(new ExceptionMiddleware());

// 2. CORS Middleware
$app->add(new CorsMiddleware());

// 3. Routing Middleware
$app->addRoutingMiddleware();

// 4. Error Middleware nativo de Slim
$app->addErrorMiddleware(false, true, true);

// -------------------------------------------------------------------------
// RUTAS DE DOCUMENTACIÓN
// -------------------------------------------------------------------------
$swaggerController = new SwaggerController();

$app->get('/swagger.json', [$swaggerController, 'generateJson']);
$app->get('/swagger.yaml', [$swaggerController, 'generateYaml']);

// Redirigir la raíz y /docs a la interfaz de Swagger
$app->get('/', function ($request, $response) {
    return $response->withHeader('Location', '/swagger/index.html')->withStatus(302);
});

$app->get('/docs', function ($request, $response) {
    return $response->withHeader('Location', '/swagger/index.html')->withStatus(302);
});

// Manejo de peticiones OPTIONS (CORS Preflight)
$app->options('/{routes:.+}', function ($request, $response) {
    return $response;
});


$app->run();
