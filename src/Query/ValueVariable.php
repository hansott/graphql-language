<?php

namespace HansOtt\GraphQL\Query;

final class ValueVariable implements Value
{
    public $name;

    public function __construct($name)
    {
        $this->name = (string) $name;
    }

    public function getChildren()
    {
        return array();
    }
}
