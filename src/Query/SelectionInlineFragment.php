<?php

namespace HansOtt\GraphQL\Query;

final class SelectionInlineFragment implements Selection
{
    public $typeCondition;
    public $directives;
    public $selectionSet;

    public function __construct(TypeCondition $typeCondition = null, array $directives = array(), SelectionSet $selectionSet)
    {
        $this->typeCondition = $typeCondition;
        $this->directives = $directives;
        $this->selectionSet = $selectionSet;
    }

    public function getChildren()
    {
        return array($this->selectionSet);
    }
}
