<?php

namespace dbAbstractionLayer\src;

use dbAbstractionLayer\src\Database;
use \PDO;
use \PDOException; //abstraction class
use dbAbstractionLayer\src\Condition;

class Query{
    private $connection;
    private $tablename;
    private $fields;
    private $operation;
    private $conditions = [];
    private static array $cache = [];
    

    public function __construct(Database $db){
        $this->connection = $db->getConnection();
    }

    private function buildQuery(): string {
        $fields = $this->getFields();
        $condition = $this->getCondition();
        if($this->operation != 'UPDATE'){
            $query = "$this->operation $fields FROM $this->tablename $condition";
        }else{
            //
        }

        return $query;
    }

    public function select(string $tablename): self {
        $this->tablename = $tablename;
        $this->operation = 'SELECT';
        return $this;
    }

    public function fields(array $fields): self {
        $this->fields=$fields;
        return $this;
    }
    
    public function getFields(): string{

        return ($this->fields === '*') ? '*' : implode(',', $this->fields);
    }
    //or condition, condition groups, the groups need to be implemented (condition group class->build groups for condition, or or and group)
    public function condition(string $column, mixed $value, string $operator = '='): self {
        $this->condition[] = new Condition($column, $value, $operator = '=');
        return $this;
    }

    public function getCondition(): string {
        $conditions = [];
        foreach ($this->conditions as $condition) {
            $conditions[] = $condition->buildCondition();
        }
        return !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
    }

    

    public function __toString(): string {
        return $this->buildQuery();
    }

    public function execute(bool $useCache = true): array {
        $query = $this->buildQuery();

        if ($useCache && isset(self::$cache[$query])) {
            // If cached result exists, return it
            return self::$cache[$query];
        }

        if ($this->validateQuery($query)) {
            try {
                $stmt = $this->connection->prepare($query);
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC); //pdo fetch 

                if ($useCache) {
                    self::$cache[$query] = $results;
                }
    
                return $results;
            } 
            catch (PDOException $e) {
                throw new Exception("Query execution failed: " . $e->getMessage());
            }
        } else {
            throw new Exception("Query validation failed");
        }
    }

    function validateQuery(string $query): bool {
        //validation logic
        $query = trim($query);
        if(empty($query)){
            return false;
        }
        return true; //for testing
    }
}
