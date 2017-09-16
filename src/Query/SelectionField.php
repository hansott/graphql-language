<?php

namespace HansOtt\GraphQL\Query;

final class SelectionField implements Selection
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
     */
    public function __construct($alias = null, $name, array $arguments = array(), array $directives = array(), SelectionSet $selectionSet = null)
    {
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
