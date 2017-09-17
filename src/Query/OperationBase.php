<?php

namespace HansOtt\GraphQL\Query;

abstract class OperationBase extends NodeBase implements Operation
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
     * @param Location|null $location
     */
    public function __construct(
        $name = null,
        array $variables = array(),
        $directives = array(),
        SelectionSet $selectionSet,
        Location $location = null
    ) {
        parent::__construct($location);
        $this->name = $name ? (string) $name : null;
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
