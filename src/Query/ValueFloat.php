<?php

namespace HansOtt\GraphQL\Query;

final class ValueFloat extends NodeBase implements Value
{
    public $value;

    public function __construct($value, Location $location = null)
    {
        parent::__construct($location);
        $this->value = (float) $value;
    }

    public function getChildren()
    {
        return array();
    }
}
