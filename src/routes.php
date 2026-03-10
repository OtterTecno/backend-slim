<?php

use App\Controllers\AuthController;
use App\Controllers\ExampleController;
use App\Middleware\JwtAuthMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {

    // 1. RUTA DE SALUD (Uso libre / Público)
    $app->get('/', function ($request, $response) {
        $data = [
            'success' => true,
            'message' => 'Base API is running',
            'version' => '1.0.0',
            'timestamp' => date('Y-m-d H:i:s')
        ];
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    });

    // 2. LOGIN (Público)
    $app->post('/login', function ($request, $response) {
        $controller = new AuthController();
        return $controller->login($request, $response);
    });

    // 3. RUTAS PÚBLICAS DE EJEMPLO (Uso libre)
    $app->get('/examples', function ($req, $res) {
        return (new ExampleController())->index($req, $res);
    });
    $app->get('/examples/{id}', function ($req, $res, $args) {
        return (new ExampleController())->show($req, $res, $args);
    });

    // 4. RUTAS PROTEGIDAS DE EJEMPLO (Con JWT)
    $app->group('', function (RouteCollectorProxy $group) {

        $group->post('/examples', function ($req, $res) {
            return (new ExampleController())->create($req, $res);
        });

        $group->put('/examples/{id}', function ($req, $res, $args) {
            return (new ExampleController())->update($req, $res, $args);
        });

        $group->delete('/examples/{id}', function ($req, $res, $args) {
            return (new ExampleController())->delete($req, $res, $args);
        });
    })->add(new JwtAuthMiddleware());

    // 5. MANEJO DE OPTIONS (CORS)
    $app->options('/{routes:.+}', function ($request, $response) {
        return $response;
    });
};
