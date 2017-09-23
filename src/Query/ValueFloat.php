<?php

namespace HansOtt\GraphQL\Query;

final class ValueFloat extends NodeBase implements ValueScalar
{
    public $value;

    public function __construct($value, Location $location = null)
    {
        parent::__construct($location);
        $this->value = (float) $value;
    }
}
