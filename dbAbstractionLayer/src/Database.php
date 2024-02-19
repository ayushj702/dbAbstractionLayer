<?php

namespace dbAbstractionLayer\src;

use dbAbstractionLayer\src\Query;
use \PDO;
use \PDOException; //abstraction class
use dbAbstractionLayer\src\ApplicationSetting;

class Database{
    private ApplicationSetting $setting;
    private $connection;
    private array $cache = [];

    public function __construct(string $selection = 'default')
    {
        $this->setting = new ApplicationSetting();
    }

    public function __destruct(){
        //$this->connection->close(); ->error
        //$this->connection->null; ->warning
    }

    public function getConnection() {
        if(!$this->connection) {
            try {
                $dsn = "mysql:host={$this->setting->host};dbname={$this->setting->databaseName}";
                $this->connection = new PDO($dsn, $this->setting->username, $this->setting->password);
                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                echo "Connection failed: " . $e->getMessage();
                return false;
            }
        }
        return $this->connection;

    }

    public function getQuery(): Query {
        return new Query($this);
    }

    public function cacheQuery(string $query, array $result): void {
        $this->cache[$query] = $result;
    }
}
