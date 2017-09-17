<?php

namespace HansOtt\GraphQL\Query;

final class SelectionFragmentSpread extends NodeBase implements Selection
{
    public $fragmentName;

    public function __construct($fragmentName, Location $location = null)
    {
        parent::__construct($location);
        $this->fragmentName = (string) $fragmentName;
    }

    public function getChildren()
    {
        return array();
    }
}
