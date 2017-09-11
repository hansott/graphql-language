<?php

namespace HansOtt\GraphQL\Query;

final class TypeCondition
{
    public $namedType;

    public function __construct(TypeNamed $namedType)
    {
        $this->namedType = $namedType;
    }
}
