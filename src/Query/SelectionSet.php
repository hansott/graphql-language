<?php

namespace HansOtt\GraphQL\Query;

final class SelectionSet implements Node
{
    public $selections;

    /**
     * @param Selection[] $selections
     */
    public function __construct(array $selections)
    {
        $this->selections = $selections;
    }

    public function getChildren()
    {
        return $this->selections;
    }
}
