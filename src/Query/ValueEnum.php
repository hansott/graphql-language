<?php

namespace HansOtt\GraphQL\Query;

final class ValueEnum extends NodeBase implements Value
{
    public $value;

    public function __construct($value, Location $location = null)
    {
        parent::__construct($location);
        $this->value = (string) $value;
    }
}
