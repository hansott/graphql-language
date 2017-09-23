<?php

namespace HansOtt\GraphQL\Schema;

final class Argument extends NodeBase
{
    public $name;
    public $type;
    public $defaultValue;

    /**
     * @param string $name
     * @param Type $type
     * @param Value|null $defaultValue
     * @param Location|null $location
     */
    public function __construct($name, Type $type, Value $defaultValue = null, Location $location = null)
    {
        parent::__construct($location);
        $this->name = (string) $name;
        $this->type = $type;
        $this->defaultValue = $defaultValue;
    }

    public function getChildren()
    {
        return array($this->type, $this->defaultValue);
    }
}
