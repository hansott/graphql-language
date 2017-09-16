<?php

namespace HansOtt\GraphQL\Query;

final class TypeCondition implements Node
{
    public $namedType;

    public function __construct(TypeNamed $namedType)
    {
        $this->namedType = $namedType;
    }

    public function getChildren()
    {
        return array($this->namedType);
    }
}
