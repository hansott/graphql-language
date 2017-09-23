<?php

namespace HansOtt\GraphQL\Schema;

final class Field extends NodeBase
{
    public $name;
    public $type;
    public $arguments;

    /**
     * @param string $name
     * @param Type $type
     * @param Argument[] $arguments
     * @param Location|null $location
     */
    public function __construct($name, Type $type, array $arguments = array(), Location $location = null)
    {
        parent::__construct($location);
        $this->name = (string) $name;
        $this->type = $type;
        $this->arguments = $arguments;
    }

    public function getChildren()
    {
        return array_merge(
            array($this->type),
            $this->arguments
        );
    }
}
