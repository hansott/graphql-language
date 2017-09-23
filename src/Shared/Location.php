<?php

namespace HansOtt\GraphQL\Shared;

abstract class Location
{
    public $line;
    public $column;

    public function __construct($line, $column)
    {
        $this->line = (int) $line;
        $this->column = (int) $column;
    }
}
