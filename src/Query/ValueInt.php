<?php

namespace HansOtt\GraphQL\Query;

final class ValueInt implements Value
{
    public $value;

    public function __construct($value)
    {
        $this->value = (int) $value;
    }
}
