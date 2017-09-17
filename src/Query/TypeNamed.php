<?php

namespace HansOtt\GraphQL\Query;

final class TypeNamed extends NodeBase implements Type
{
    public $name;

    public function __construct($name, Location $location = null)
    {
        parent::__construct($location);
        $this->name = (string) $name;
    }

    public function getChildren()
    {
        return array();
    }
}
