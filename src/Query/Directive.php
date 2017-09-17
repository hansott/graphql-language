<?php

namespace HansOtt\GraphQL\Query;

final class Directive extends NodeBase
{
    public $name;
    public $arguments;

    /**
     * @param string $name
     * @param Argument[] $arguments
     * @param Location|null $location
     */
    public function __construct($name, array $arguments = array(), Location $location = null)
    {
        parent::__construct($location);
        $this->name = (string) $name;
        $this->arguments = $arguments;
    }

    public function getChildren()
    {
        return $this->arguments;
    }
}
