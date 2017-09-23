<?php

namespace HansOtt\GraphQL\Schema;

final class DeclarationUnion extends NodeBase implements Declaration
{
    public $name;
    public $members;

    /**
     * @param string $name
     * @param string[] $members
     * @param Location|null $location
     */
    public function __construct($name, array $members, Location $location = null)
    {
        parent::__construct($location);
        $this->name = (string) $name;
        $this->members = $members;
    }
}
