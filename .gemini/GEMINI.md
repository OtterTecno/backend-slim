# Guía de Desarrollo - Plant API (Gemini Context)

Este documento sirve como guía de referencia para la Inteligencia Artificial (y desarrolladores) para entender la arquitectura del proyecto y cómo extenderlo siguiendo los patrones establecidos.

## 🚀 Arquitectura y Tecnologías
- **Framework**: Slim 4 (PHP 8.2+)
- **Puertos**:
  - `8080`: API Principal (Entry point: `public/index.php`)
  - `8081`: Documentación Swagger (Entry point: `public/swagger.php`)
- **Autenticación**: JWT (HS256) con `firebase/php-jwt`.
- **Validación**: `symfony/validator`.
- **Base de Datos**: PDO con MySQL/MariaDB.
- **Documentación**: OpenAPI 3.0 mediante Atributos de PHP 8 (`zircote/swagger-php`).

---

## � Librerías y Dependencias Principales
El proyecto usa las siguientes dependencias clave (ver `composer.json`):
- `slim/slim`: Micro-framework base para el API.
- `slim/psr7`, `nyholm/psr7`, `guzzlehttp/psr7`, `laminas/laminas-diactoros`: Implementaciones y soporte para peticiones HTTP PSR-7.
- `vlucas/phpdotenv`: Manejo de variables de entorno mediante `.env`.
- `symfony/validator`: Validación de datos en los DTOs.
- `firebase/php-jwt`: Generación y validación de JSON Web Tokens (JWT).
- `zircote/swagger-php`: Generación de documentación OpenAPI/Swagger vía atributos de PHP 8.
- `monolog/monolog`: Sistema de logs avanzado (stderr y archivos rotativos).
- `ext-pdo` y `ext-pdo_mysql`: Extensiones nativas requeridas para la conexión a la base de datos MySQL/MariaDB.

---

## �📁 Estructura del Proyecto (`src/`)
1. **`Config/`**: Bases de datos y configuraciones como el Logger.
2. **`Controllers/`**: Manejan la petición HTTP. No contienen lógica de negocio.
3. **`Services/`**: Capa de lógica de negocio pura.
4. **`Repositories/`**: Capa de persistencia (SQL).
5. **`Entities/`**: Objetos del dominio que representan tablas de la DB.
6. **`DTO/`**: 
   - `Common/ApiResponse`: Respuesta estándar del API.
   - Otros DTOs: Validan la entrada de datos del usuario.
7. **`Middleware/`**: CORS, Validación de Token, Manejo de Excepciones.

---

## 🗄️ Base de Datos (`database/`)
- Contiene los scripts SQL iniciales de la base de datos (ej. `init.sql`).

---


## 🛠️ Cómo Crear un Nuevo Endpoint (Paso a Paso)

### 1. Definir la Entidad (`src/Entities/`)
Crea una clase con los atributos de la tabla y añade Atributos OpenAPI (`#[OA\Schema]`, `#[OA\Property]`).
```php
#[OA\Schema(schema: "MiEntidad")]
class MiEntidad { ... }
```

### 2. Crear el DTO de Entrada (`src/DTO/`)
Usa Atributos de Symfony Validator para validar los datos.
```php
class CreateEntidadDTO {
    #[Assert\NotBlank]
    public ?string $nombre;
    ...
}
```

### 3. Crear el Repositorio (`src/Repositories/`)
Implementa las consultas SQL mediante PDO.
```php
class MiEntidadRepository {
    public function getAll(): array { ... }
}
```

### 4. Crear el Servicio (`src/Services/`)
Orquestar la lógica. Debe retornar siempre un objeto `App\DTO\Common\ApiResponse`.
```php
class MiEntidadService {
    public function run(): ApiResponse {
        $data = $this->repository->getAll();
        return ApiResponse::success($data);
    }
}
```

### 5. Crear el Controlador (`src/Controllers/`)
Debe extender de `BaseController`. Usa Atributos de OpenAPI para documentar el endpoint.
```php
class MiEntidadController extends BaseController {
    #[OA\Get(path: "/ruta", ...)]
    public function index(Request $req, Response $res): Response {
        $apiResponse = (new MiEntidadService())->run();
        return $this->responseData($res, $apiResponse);
    }
}
```

### 6. Registrar la Ruta (`src/routes.php`)
**Importante**: Usa closures para instanciar los controladores bajo demanda (Lazy Loading).
```php
$group->get('/mi-ruta', function($req, $res) {
    return (new MiEntidadController())->index($req, $res);
});
```

---

## 📜 Reglas de Oro y Convenciones
- **Documentación Viva (Swagger)**: SIEMPRE que se cree o modifique un endpoint, una entidad o un DTO, SE DEBE añadir o actualizar su correspondiente anotación OpenAPI (`#[OA\Get]`, `#[OA\Schema]`, etc.) para mantener la documentación del API al 100%.
- **Base de Datos y SQL**: SIEMPRE añade el atributo `COMMENT '...'` a todas las columnas y tablas cuando crees o edites scripts SQL para que la estructura esté documentada directamente en el motor de base de datos.
- **Auditoría y Borrado Lógico en Tablas**: TODAS las tablas deben incluir obligatoriamente las siguientes columnas al final para manejar el borrado lógico y auditoría:
  ```sql
  `activo` tinyint(1) NULL DEFAULT 1 COMMENT 'Estado del registro: 1 Activo, 0 Inactivo',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'Fecha de creación',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha de actualización',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Fecha de eliminación logica',
  `created_by` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Usuario creador',
  `updated_by` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Usuario modificador',
  `deleted_by` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Usuario eliminador',
  ```
- **Respuestas**: Todas las respuestas JSON deben usar la clase `ApiResponse`.
- **Errores**: No uses `try-catch` en los controladores a menos que sea necesario. El `ExceptionMiddleware` capturará y formateará cualquier error automáticamente.
- **Logs**: Usa `AppLogger::info()` o `AppLogger::error()` para eventos críticos. El log capturará automáticamente el Método y la URI.
- **Git**: No subir archivos `.env` (usa `.env-example`).

## 🧪 Comandos Útiles
- `composer start`: Inicia el API en el puerto 8080.
- `composer swagger`: Inicia la documentación en el puerto 8081.
