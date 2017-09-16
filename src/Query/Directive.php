<?php

namespace HansOtt\GraphQL\Query;

final class Directive implements Node
{
    public $name;
    public $arguments;

    /**
     * @param string $name
     * @param Argument[] $arguments
     */
    public function __construct($name, array $arguments = array())
    {
        $this->name = (string) $name;
        $this->arguments = $arguments;
    }

    public function getChildren()
    {
        return $this->arguments;
    }
}
