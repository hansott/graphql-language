<?php

namespace HansOtt\GraphQL\Query;

final class ValueString implements Value
{
    public $value;

    public function __construct($value)
    {
        $this->value = (string) $value;
    }
}
