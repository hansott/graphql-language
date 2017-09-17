<?php

namespace HansOtt\GraphQL\Query;

final class Argument extends NodeBase
{
    public $name;
    public $value;

    public function __construct($name, Value $value, Location $location = null)
    {
        parent::__construct($location);
        $this->name = (string) $name;
        $this->value = $value;
    }

    public function getChildren()
    {
        return array($this->value);
    }
}
