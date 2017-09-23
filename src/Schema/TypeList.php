<?php

namespace HansOtt\GraphQL\Schema;

final class TypeList extends NodeBase implements Type
{
    public $type;

    /**
     * @param Type $type
     * @param Location|null $location
     */
    public function __construct(Type $type, Location $location = null)
    {
        parent::__construct($location);
        $this->type = $type;
    }

    public function getChildren()
    {
        return array($this->type);
    }
}
