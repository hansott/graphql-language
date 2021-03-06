<?php

namespace HansOtt\GraphQL\Schema;

final class ValueBoolean extends NodeBase implements ValueScalar
{
    private $isTrue;

    public function __construct($isTrue, Location $location = null)
    {
        parent::__construct($location);
        $this->isTrue = (bool) $isTrue;
    }
}
