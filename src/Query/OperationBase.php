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
     * @param array $variables
     * @param array $directives
     * @param SelectionSet $selectionSet
     */
    public function __construct($name = null, array $variables = array(), $directives = array(), SelectionSet $selectionSet)
    {
        $this->name = $name;
        $this->variables = $variables;
        $this->directives = $directives;
        $this->selectionSet = $selectionSet;
    }
}
