<?php

namespace HansOtt\GraphQL\Schema;

final class TypeNamed extends NodeBase implements Type
{
    public $name;

    /**
     * @param string $name
     * @param Location|null $location
     */
    public function __construct($name, Location $location = null)
    {
        parent::__construct($location);
        $this->name = (string) $name;
    }
}
