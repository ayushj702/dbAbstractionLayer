<?php

namespace src;

use src\Database;
use \PDO;
use \PDOException; //abstraction class
use src\Condition;

class Query{
    private $connection;
    private $tablename;
    private $fields;
    private $operation;
    private $conditions = [];
    private static array $cache = [];
    private $joinConditions = [];



    public function __construct(Database $db){
        $this->connection = $db->getConnection();
    }

    private function buildQuery(): string {
        $fields = $this->getFields();
        $whereCondition = $this->getCondition();
        $joinCondition = $this->getJoin();
        if ($this->operation === 'DELETE') {
            $query = "$this->operation FROM $this->tablename $whereCondition";
        }
        elseif($this->operation != 'UPDATE'){
            $query = "$this->operation $fields FROM $this->tablename $joinCondition $whereCondition";
        }else{
            //handle update query
        }
        return $query;
    }

    public function select(string $tableOrQuery): self {
        if($tableOrQuery instanceof Query) {
            $this->tablename = '(' . $tableOrQuery->__toString() . ')';
        } else {
            $this->tablename = $tableOrQuery;
        }
        $this->operation = 'SELECT';
        return $this;
    }

    public function delete(string $table): self {
        $this->tablename = $table;
        $this->operation = 'DELETE';
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

    public function join(string $table, string $clause, string $type = 'INNER'): self {
        $this->joinConditions[] = "$type JOIN $table ON $clause";
        return $this;
    }

    public function getJoin(): string {
        return implode(' ', $this->joinConditions);
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
