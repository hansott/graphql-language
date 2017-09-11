<?php

namespace HansOtt\GraphQL\Query;

final class ValueEnum implements Value
{
    public $value;

    public function __construct($value)
    {
        $this->value = (string) $value;
    }
}
