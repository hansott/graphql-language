<?php

namespace HansOtt\GraphQL\Query;

final class SelectionField extends NodeBase implements Selection
{
    public $name;
    public $alias;
    public $arguments;
    public $directives;
    public $selectionSet;

    /**
     * @param string|null $alias
     * @param string $name
     * @param Argument[] $arguments
     * @param Directive[] $directives
     * @param SelectionSet|null $selectionSet
     * @param Location|null $location
     */
    public function __construct(
        $alias = null,
        $name,
        array $arguments = array(),
        array $directives = array(),
        SelectionSet $selectionSet = null,
        Location $location = null
    ) {
        parent::__construct($location);
        $this->alias = $alias ? (string) $alias : null;
        $this->name = (string) $name;
        $this->arguments = $arguments;
        $this->directives = $directives;
        $this->selectionSet = $selectionSet;
    }

    public function getChildren()
    {
        $children = array();

        foreach ($this->arguments as $variable) {
            $children[] = $variable;
        }

        foreach ($this->directives as $directive) {
            $children[] = $directive;
        }

        $children[] = $this->selectionSet;

        return $children;
    }
}
