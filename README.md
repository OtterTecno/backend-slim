# PHP API Skeleton by Ottertecno

🚀 Este proyecto base o "skeleton" ha sido desarrollado y estandarizado por **Ottertecno**. Su propósito es permitirte crear aplicaciones y servicios en PHP de una manera fácil, ágil y sin complicaciones, proveyendo una estructura sumamente liviana pero orientada al más alto rendimiento.

> **⚠️ IMPORTANTE PARA DESARROLLADORES Y AI:**  
> Todas las reglas de arquitectura, patrones de diseño, convenciones de base de datos (borrado lógico, auditoría) y pasos para crear nuevos endpoints están documentados en **[`.gemini/GEMINI.md`](./.gemini/GEMINI.md)**. Debes leer y seguir ese archivo **estrictamente** al hacer cambios en este proyecto.

API backend construida con PHP (Slim Framework 4).

## Requisitos previos

- [Docker](https://www.docker.com/products/docker-desktop)
- [Docker Compose](https://docs.docker.com/compose/install/)

## Instalación y ejecución

1. **Clonar el repositorio** (si aún no lo has hecho) y entrar a la carpeta del proyecto:
   ```bash
   cd back-plantas
   ```

2. **Configurar las variables de entorno**:
   Copia el archivo de ejemplo para crear tu propio `.env`.
   ```bash
   cp .env-example .env
   ```
   *Nota: Si utilizas Docker, asegúrate de cambiar `DB_HOST=localhost` por `DB_HOST=db` dentro de tu archivo `.env` para que el contenedor de la aplicación pueda conectarse al contenedor de la base de datos.*

3. **Levantar los contenedores**:
   Ejecuta el siguiente comando para descargar las imágenes e iniciar la aplicación y la base de datos en segundo plano:
   ```bash
   docker-compose up -d
   ```

4. **Instalar dependencias de PHP (Composer)**:
   Una vez que el contenedor esté corriendo, instala las librerías necesarias con:
   ```bash
   docker-compose exec app composer install
   ```

5. **Ejecutar documentación (Swagger)**:
   Para visualizar la documentación interactiva de la API, levanta el servicio de Swagger con:
   ```bash
   docker-compose exec app composer swagger
   ```

## Ejecución en desarrollo local (Sin Docker)

Si prefieres no usar Docker, puedes ejecutar la aplicación directamente con PHP CLI usando los scripts de Composer:

1. **Instalar dependencias de PHP**:
   ```bash
   composer install
   ```

2. **Iniciar la API** (Puerto 8080):
   ```bash
   composer start
   ```

3. **Iniciar documentación Swagger** (Puerto 8081):
   ```bash
   composer swagger
   ```

*Nota: Asegúrate de configurar la conexión a la base de datos de manera local (ej. `DB_HOST=localhost`) en tu archivo `.env`.*

## Acceso a la aplicación

- **API / Web**: La aplicación estará disponible respondiendo en [http://localhost:8080](http://localhost:8080)
- **Documentación Swagger**: La documentación interactiva de la API estará en [http://localhost:8081](http://localhost:8081)
- **Base de Datos**: MySQL está expuesto de forma local en el puerto `3306` con la base de datos `repo_plantas`, usuario `root` y contraseña `root`.

## Endpoints Disponibles y Pruebas

El skeleton incluye una entidad `Example` completamente funcional para que puedas realizar pruebas y ver cómo interactúa la arquitectura.

### Servicios Públicos (Sin Autenticación)
- `GET /` : Endpoint de Salud (Health check) que retorna si el API está corriendo.
- `GET /examples` : Lista todos los registros de ejemplo activos.
- `GET /examples/{id}` : Devuelve el detalle de un registro específico.
- `POST /login` : Genera un token JWT de acceso.
  - **Payload de prueba**: `{"username": "admin", "password": "admin"}`

### Servicios Protegidos (Con JWT)
Para consumir estos servicios, primero necesitas llamar a `/login` y obtener tu variable `token`. Luego, debes colocar el token en las cabeceras (`Headers`) de tus peticiones HTTP así: `Authorization: Bearer <tu_token>`.

- `POST /examples` : Crea un nuevo registro.
body: {
    "name":"asd"
}

- `PUT /examples/{id}` : Actualiza un registro existente de forma parcial o total.
- `DELETE /examples/{id}` : Realiza un borrado lógico del registro especificado (marca `activo` en 0 y llena `deleted_at`).

*Tip: Puedes interactuar visualmente con estos endpoints a través del panel propio de **Swagger API** en tu navegador en [http://localhost:8081](http://localhost:8081) una vez lo tengas en ejecución.*

## Estructura de contenedores (docker-compose)

El proyecto incluye dos servicios principales:
- `app`: Utiliza una imagen conjunta de **PHP 8.2 y NGINX** (`webdevops/php-nginx:8.2`). El document root está configurado en el directorio `/public`.
- `db`: Utiliza la imagen oficial de **MySQL 8.0**, inicializando la base de datos especificada en tus las variables de entorno.
