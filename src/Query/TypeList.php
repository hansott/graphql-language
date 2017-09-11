<?php

namespace HansOtt\GraphQL\Query;

final class TypeList implements Type
{
    public $types;

    /**
     * @param Type[] $types
     */
    public function __construct(array $types)
    {
        $this->types = $types;
    }
}
