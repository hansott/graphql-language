<?php

namespace HansOtt\GraphQL\Query;

final class TypeNonNull extends NodeBase implements Type
{
    public $type;

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
