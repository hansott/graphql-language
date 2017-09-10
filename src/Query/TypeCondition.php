<?php

namespace HansOtt\GraphQL\Query;

final class TypeCondition
{
    public $namedType;

    public function __construct($namedType)
    {
        $this->namedType = (string) $namedType;
    }
}
