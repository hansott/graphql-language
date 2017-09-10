<?php

namespace HansOtt\GraphQL\Query;

final class SelectionInlineFragment
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
}
