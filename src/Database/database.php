<?php

namespace App\Database;

use PDO;

class Database
{
    private static $pdo;

    public static function getInstance()
    {
        if (self::$pdo === null) {
            // Create the PDO instance here
            $dbPath = __DIR__ . '/../../database.db';
            self::$pdo = new PDO("sqlite:$dbPath");
            self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return self::$pdo;
    }
}