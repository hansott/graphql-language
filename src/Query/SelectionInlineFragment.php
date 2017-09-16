<?php

namespace HansOtt\GraphQL\Query;

final class SelectionInlineFragment implements Selection
{
    public $typeCondition;
    public $directives;
    public $selectionSet;

    /**
     * @param TypeCondition|null $typeCondition
     * @param Directive[] $directives
     * @param SelectionSet $selectionSet
     */
    public function __construct(TypeCondition $typeCondition = null, array $directives = array(), SelectionSet $selectionSet)
    {
        $this->typeCondition = $typeCondition;
        $this->directives = $directives;
        $this->selectionSet = $selectionSet;
    }

    public function getChildren()
    {
        $children = array($this->typeCondition);

        foreach ($this->directives as $directive) {
            $children[] = $directive;
        }

        $children[] = $this->selectionSet;

        return $children;
    }
}
