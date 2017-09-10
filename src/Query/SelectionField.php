<?php

namespace HansOtt\GraphQL\Query;

final class SelectionField implements Selection
{
    public $name;
    public $alias;
    public $arguments;
    public $directives;
    public $selectionSet;

    public function __construct($alias = null, $name, array $arguments = array(), array $directives = array(), SelectionSet $selectionSet = null)
    {
        $this->alias = $alias;
        $this->name = (string) $name;
        $this->arguments = $arguments;
        $this->directives = $directives;
        $this->selectionSet = $selectionSet;
    }
}
