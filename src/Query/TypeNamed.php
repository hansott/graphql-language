<?php

namespace HansOtt\GraphQL\Query;

final class TypeNamed implements Type
{
    public $name;

    public function __construct($name)
    {
        $this->name = (string) $name;
    }
}
