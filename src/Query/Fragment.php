<?php

namespace HansOtt\GraphQL\Query;

final class Fragment implements Definition
{
    public $name;
    public $typeCondition;
    public $directives;
    public $selectionSet;

    public function __construct($name, TypeCondition $typeCondition, array $directives = array(), SelectionSet $selectionSet)
    {
        $this->name = (string) $name;
        $this->typeCondition = $typeCondition;
        $this->directives = $directives;
        $this->selectionSet = $selectionSet;
    }
}
