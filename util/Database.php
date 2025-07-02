<?php
namespace Analogous\Util;

/**
 * Database.php
 *
 * Provides methods to interact with the database.
 *
 * @author Convobis Project
 */
class Database
{

    private static $instance = null;
    private $connection;

    private function __construct()
    {
        $this->connect();
        $this->checkAndBuildTables();
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function connect()
    {
        $type = ConfigHelper::getConfigValue("database.type", "sqlite", true);

        switch ($type) {
            case "sqlite":
                $file = ConfigHelper::getConfigValue("database.file", "database.db", true);
                $this->connection = new \PDO("sqlite:" . $file);
                break;
            case "mysql":
                $host = ConfigHelper::getConfigValue("database.host", "localhost", true);
                $port = ConfigHelper::getConfigValue("database.port", 3306, true);
                $dbname = ConfigHelper::getConfigValue("database.name", "my_database", true);
                $username = ConfigHelper::getConfigValue("database.username", "root", true);
                $password = ConfigHelper::getConfigValue("database.password", "", true);
                $this->connection = new \PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
                break;
            default:
                throw new \Exception("Unsupported database type: " . $type);
        }
    }

    public function checkAndBuildTables()
    {
        $schemaFile = __DIR__ . '/../schema.sql';
        if (!file_exists($schemaFile)) {
            throw new \Exception("Schema file not found: " . $schemaFile);
        }
        $schema = file_get_contents($schemaFile);
        $statements = explode(";", $schema);
        foreach ($statements as $statement) {
            $trimmed = trim($statement);
            if (!empty($trimmed)) {
                $this->connection->exec($trimmed);
            }
        }
    }

    public function getConnection()
    {
        return $this->connection;
    }

}