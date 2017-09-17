<?php

namespace HansOtt\GraphQL\Query;

final class TypeList extends NodeBase implements Type
{
    public $types;

    /**
     * @param Type[] $types
     * @param Location|null $location
     */
    public function __construct(array $types, Location $location = null)
    {
        parent::__construct($location);
        $this->types = $types;
    }

    public function getChildren()
    {
        return $this->types;
    }
}
