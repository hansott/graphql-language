<?php

namespace HansOtt\GraphQL\Query;

final class ValueInt extends NodeBase implements ValueScalar
{
    public $value;

    public function __construct($value, Location $location = null)
    {
        parent::__construct($location);
        $this->value = (int) $value;
    }

    public function getChildren()
    {
        return array();
    }
}
