<?php

namespace HansOtt\GraphQL\Query;

final class Argument implements Node
{
    public $name;
    public $value;

    public function __construct($name, Value $value)
    {
        $this->name = (string) $name;
        $this->value = $value;
    }

    public function getChildren()
    {
        return array($this->value);
    }
}
