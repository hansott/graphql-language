<?php

namespace HansOtt\GraphQL\Shared;

abstract class NodeBase implements Node
{
    public $location;

    public function __construct(Location $location = null)
    {
        $this->location = $location;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function getChildren()
    {
        return array();
    }
}
