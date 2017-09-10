<?php

namespace HansOtt\GraphQL\Query;

final class SelectionSet
{
    public $selections;

    /**
     * @param Selection[] $selections
     */
    public function __construct(array $selections)
    {
        $this->selections = $selections;
    }
}
