<?php

namespace HansOtt\GraphQL\Query;

final class ValueFloat implements Value
{
    public $value;

    public function __construct($value)
    {
        $this->value = (float) $value;
    }

    public function getChildren()
    {
        return array();
    }
}
