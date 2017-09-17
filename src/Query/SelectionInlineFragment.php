<?php

namespace HansOtt\GraphQL\Query;

final class SelectionInlineFragment extends NodeBase implements Selection
{
    public $typeCondition;
    public $directives;
    public $selectionSet;

    /**
     * @param TypeCondition|null $typeCondition
     * @param Directive[] $directives
     * @param SelectionSet $selectionSet
     * @param Location|null $location
     */
    public function __construct(
        TypeCondition $typeCondition = null,
        array $directives = array(),
        SelectionSet $selectionSet,
        Location $location = null
    ) {
        parent::__construct($location);
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
