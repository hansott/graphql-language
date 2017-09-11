<?php

namespace HansOtt\GraphQL\Query;

interface Node
{
    /**
     * @return Node[]
     */
    public function getChildren();
}
