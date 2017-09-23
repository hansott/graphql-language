<?php

namespace HansOtt\GraphQL\Shared;

interface Node
{
    /**
     * @return Node[]
     */
    public function getChildren();

    /**
     * @return Location
     */
    public function getLocation();
}
