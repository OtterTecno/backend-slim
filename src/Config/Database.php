<?php

namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private string $host;
    private string $db_name;
    private string $username;
    private string $password;
    private string $port;
    public ?PDO $conn = null;

    public function __construct()
    {
        $this->host = $_ENV['DB_HOST'] ?? 'localhost';
        $this->db_name = $_ENV['DB_DATABASE'] ?? '';
        $this->username = $_ENV['DB_USERNAME'] ?? 'root';
        $this->password = $_ENV['DB_PASSWORD'] ?? '';
        $this->port = $_ENV['DB_PORT'] ?? '3306';
    }

    public function getConnection(): ?PDO
    {
        if ($this->conn !== null) {
            return $this->conn;
        }

        try {
            // Verificamos si los drivers de PDO están instalados
            if (!in_array('mysql', PDO::getAvailableDrivers())) {
                AppLogger::error("CRITICAL: El driver PDO MySQL no está instalado en este sistema PHP.");
                error_log("CRITICAL: El driver PDO MySQL no está instalado en este sistema PHP.");
                return null;
            }

            $dsn = "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->username, $this->password);

            // Configuración de errores y modo de obtención por defecto
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (\Throwable $exception) {
            // Loguear tanto en el log de archivos como en la consola del servidor
            $errorMessage = "Error de conexión a Base de Datos: " . $exception->getMessage();
            AppLogger::error($errorMessage);
            error_log($errorMessage);
            return null;
        }


        return $this->conn;
    }
}
