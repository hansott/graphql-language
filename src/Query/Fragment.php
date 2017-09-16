<?php

namespace HansOtt\GraphQL\Query;

final class Fragment implements Definition
{
    public $name;
    public $typeCondition;
    public $directives;
    public $selectionSet;

    /**
     * @param string $name
     * @param TypeCondition $typeCondition
     * @param Directive[] $directives
     * @param SelectionSet $selectionSet
     */
    public function __construct(
        $name,
        TypeCondition $typeCondition,
        array $directives = array(),
        SelectionSet $selectionSet
    ) {
        $this->name = (string) $name;
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
