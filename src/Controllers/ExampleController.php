<?php

namespace App\Controllers;

use App\DTO\Example\CreateExampleDTO;
use App\DTO\Example\UpdateExampleDTO;
use App\Services\ExampleService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Examples", description: "Operaciones de la entidad base Ejemplo")]
class ExampleController extends BaseController
{
    private ExampleService $service;

    public function __construct()
    {
        $this->service = new ExampleService();
    }

    #[OA\Get(
        path: "/examples",
        summary: "Obtener todos los ejemplos",
        tags: ["Examples"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Lista de ejemplos",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiResponse")
            )
        ]
    )]
    public function index(Request $request, Response $response): Response
    {
        $apiResponse = $this->service->getAll();
        return $this->responseData($response, $apiResponse);
    }

    #[OA\Get(
        path: "/examples/{id}",
        summary: "Obtener un ejemplo por ID",
        tags: ["Examples"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "ID del ejemplo", schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Ejemplo encontrado", content: new OA\JsonContent(ref: "#/components/schemas/ApiResponse")),
            new OA\Response(response: 404, description: "Ejemplo no encontrado", content: new OA\JsonContent(ref: "#/components/schemas/ApiResponse"))
        ]
    )]
    public function show(Request $request, Response $response, array $args): Response
    {
        $id = (int)$args['id'];
        $apiResponse = $this->service->getById($id);
        return $this->responseData($response, $apiResponse);
    }

    #[OA\Post(
        path: "/examples",
        summary: "Crear nuevo ejemplo",
        tags: ["Examples"],
        security: [["BearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/CreateExampleDTO")
        ),
        responses: [
            new OA\Response(response: 201, description: "Ejemplo creado", content: new OA\JsonContent(ref: "#/components/schemas/ApiResponse")),
            new OA\Response(response: 400, description: "Error de validación", content: new OA\JsonContent(ref: "#/components/schemas/ApiResponse"))
        ]
    )]
    public function create(Request $request, Response $response): Response
    {
        $data = (array)($request->getParsedBody() ?? json_decode((string)$request->getBody(), true) ?? []);
        $dto = new CreateExampleDTO($data);

        $validationResponse = $this->validateDto($dto);
        if ($validationResponse) {
            return $this->responseData($response, $validationResponse);
        }

        $user = $request->getAttribute('user');
        $username = $user['user'] ?? 'system';

        $apiResponse = $this->service->create($dto, $username);
        return $this->responseData($response, $apiResponse);
    }

    #[OA\Put(
        path: "/examples/{id}",
        summary: "Actualizar un ejemplo",
        tags: ["Examples"],
        security: [["BearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "ID del ejemplo", schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/UpdateExampleDTO")
        ),
        responses: [
            new OA\Response(response: 200, description: "Ejemplo actualizado", content: new OA\JsonContent(ref: "#/components/schemas/ApiResponse")),
            new OA\Response(response: 404, description: "Ejemplo no encontrado", content: new OA\JsonContent(ref: "#/components/schemas/ApiResponse"))
        ]
    )]
    public function update(Request $request, Response $response, array $args): Response
    {
        $id = (int)$args['id'];
        $data = (array)($request->getParsedBody() ?? json_decode((string)$request->getBody(), true) ?? []);
        $dto = new UpdateExampleDTO($data);

        $validationResponse = $this->validateDto($dto);
        if ($validationResponse) {
            return $this->responseData($response, $validationResponse);
        }

        $user = $request->getAttribute('user');
        $username = $user['user'] ?? 'system';

        $apiResponse = $this->service->update($id, $dto, $username);
        return $this->responseData($response, $apiResponse);
    }

    #[OA\Delete(
        path: "/examples/{id}",
        summary: "Eliminar un ejemplo",
        tags: ["Examples"],
        security: [["BearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "ID del ejemplo", schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Ejemplo eliminado", content: new OA\JsonContent(ref: "#/components/schemas/ApiResponse")),
            new OA\Response(response: 404, description: "Ejemplo no encontrado", content: new OA\JsonContent(ref: "#/components/schemas/ApiResponse"))
        ]
    )]
    public function delete(Request $request, Response $response, array $args): Response
    {
        $id = (int)$args['id'];
        $user = $request->getAttribute('user');
        $username = $user['user'] ?? 'system';

        $apiResponse = $this->service->delete($id, $username);
        return $this->responseData($response, $apiResponse);
    }
}
