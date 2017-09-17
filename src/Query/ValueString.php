<?php

namespace HansOtt\GraphQL\Query;

final class ValueString extends NodeBase implements Value
{
    public $value;

    public function __construct($value, Location $location = null)
    {
        parent::__construct($location);
        $this->value = (string) $value;
    }

    public function getChildren()
    {
        return array();
    }
}
