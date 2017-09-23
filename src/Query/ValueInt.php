<?php

namespace HansOtt\GraphQL\Query;

final class ValueInt extends NodeBase implements ValueScalar
{
    public $value;

    /**
     * @param string $value
     * @param Location|null $location
     */
    public function __construct($value, Location $location = null)
    {
        parent::__construct($location);
        $this->value = (int) $value;
    }
}
