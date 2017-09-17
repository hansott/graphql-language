<?php

namespace HansOtt\GraphQL\Query;

final class TypeCondition extends NodeBase implements Node
{
    public $namedType;

    public function __construct(TypeNamed $namedType, Location $location = null)
    {
        parent::__construct($location);
        $this->namedType = $namedType;
    }

    public function getChildren()
    {
        return array($this->namedType);
    }
}
