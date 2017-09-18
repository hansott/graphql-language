<?php

namespace HansOtt\GraphQL\Query;

final class ValueObjectField extends NodeBase
{
    public $name;
    public $value;

    /**
     * @param string $name
     * @param Value $value
     * @param Location|null $location
     */
    public function __construct($name, Value $value, Location $location = null)
    {
        parent::__construct($location);
        $this->name = (string) $name;
        $this->value = $value;
    }

    public function getChildren()
    {
        return array($this->value);
    }
}
