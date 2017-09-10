<?php

namespace HansOtt\GraphQL\Query;

final class SelectionFragmentSpread implements Selection
{
    public $fragmentName;

    public function __construct($fragmentName)
    {
        $this->fragmentName = (string) $fragmentName;
    }
}
