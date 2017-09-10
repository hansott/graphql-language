<?php

namespace HansOtt\GraphQL\Query;

final class ValueVariable implements Value
{
    private $name;
    private $defaultValue;
    private $type;

    public function __construct($name, Type $type, Value $defaultValue = null)
    {
        $this->name = (string) $name;
        $this->type = $type;
        $this->defaultValue = $defaultValue;
    }
}
