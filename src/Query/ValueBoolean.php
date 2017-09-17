<?php

namespace HansOtt\GraphQL\Query;

final class ValueBoolean extends NodeBase implements Value
{
    private $isTrue;

    public function __construct($isTrue, Location $location = null)
    {
        parent::__construct($location);
        $this->isTrue = (bool) $isTrue;
    }

    public function getChildren()
    {
        return array();
    }
}
