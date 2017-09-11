<?php

namespace HansOtt\GraphQL\Query;

final class ValueBoolean implements Value
{
    private $isTrue;

    public function __construct($isTrue)
    {
        $this->isTrue = (bool) $isTrue;
    }
}
