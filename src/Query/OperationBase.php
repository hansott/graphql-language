<?php

namespace HansOtt\GraphQL\Query;

abstract class OperationBase implements Operation
{
    public $name;
    public $variables;
    public $directives;
    public $selectionSet;

    /**
     * @param string|null $name
     * @param VariableDefinition[] $variables
     * @param Directive[] $directives
     * @param SelectionSet $selectionSet
     */
    public function __construct($name = null, array $variables = array(), $directives = array(), SelectionSet $selectionSet)
    {
        $this->name = $name;
        $this->variables = $variables;
        $this->directives = $directives;
        $this->selectionSet = $selectionSet;
    }

    final public function getChildren()
    {
        $children = array();

        foreach ($this->variables as $variable) {
            $children[] = $variable;
        }

        foreach ($this->directives as $directive) {
            $children[] = $directive;
        }

        $children[] = $this->selectionSet;

        return $children;
    }
}
