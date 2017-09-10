<?php

namespace HansOtt\GraphQL\Query;

final class SelectionFieldArgument
{
    public $name;
    public $value;

    public function __construct($name, Value $value)
    {
        $this->name = $name;
        $this->value = $value;
    }
}
