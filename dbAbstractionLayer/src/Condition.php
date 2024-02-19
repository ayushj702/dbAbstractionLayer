<?php

namespace dbAbstractionLayer\src;

class Condition{

    public $column;
    public $value;
    public $operator;

    public function __construct(string $column, mixed $value, string $operator='='){
        $this->column = $column;
        $this->value = $value;
        $this->operator = $operator;
    }

    public function buildCondition(): string {
        return "$this->column $this->operator :$this->value";
            }

}