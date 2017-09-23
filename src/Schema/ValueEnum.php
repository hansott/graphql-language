<?php

namespace HansOtt\GraphQL\Schema;

final class ValueEnum extends NodeBase implements Value
{
    public $value;

    /**
     * @param string $value
     * @param Location|null $location
     */
    public function __construct($value, Location $location = null)
    {
        parent::__construct($location);
        $this->value = (string) $value;
    }
}
