<?php

namespace App\Repositories;

use App\Config\Database;
use App\Config\AppLogger;
use PDO;
use Exception;

abstract class BaseRepository
{
    protected PDO $db;

    public function __construct()
    {
        $db = (new Database())->getConnection();
        if (!$db) {
            AppLogger::error(static::class . ": Could not connect to database");
            throw new Exception(static::class . ": Could not connect to database");
        }
        $this->db = $db;
    }
}
