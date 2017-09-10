<?php

namespace HansOtt\GraphQL\Query;

final class ValueBool implements Value
{
    private $isTrue;

    public function __construct($isTrue)
    {
        $this->isTrue = (bool) $isTrue;
    }
}
