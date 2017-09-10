<?php

namespace HansOtt\GraphQL\Query;

final class ValueEnum implements Value
{
    private $enum;

    public function __construct($enum)
    {
        $this->enum = (string) $enum;
    }
}
