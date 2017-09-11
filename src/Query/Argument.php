<?php

namespace HansOtt\GraphQL\Query;

final class Argument
{
    public $name;
    public $value;

    public function __construct($name, Value $value)
    {
        $this->name = (string) $name;
        $this->value = $value;
    }
}
