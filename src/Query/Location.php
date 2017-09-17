<?php

namespace HansOtt\GraphQL\Query;

final class Location
{
    public $line;
    public $column;

    public function __construct($line, $column)
    {
        $this->line = (int) $line;
        $this->column = (int) $column;
    }
}
