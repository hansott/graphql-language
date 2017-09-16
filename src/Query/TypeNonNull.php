<?php

namespace HansOtt\GraphQL\Query;

final class TypeNonNull implements Type
{
    public $type;

    public function __construct(Type $type)
    {
        $this->type = $type;
    }

    public function getChildren()
    {
        return array($this->type);
    }
}
