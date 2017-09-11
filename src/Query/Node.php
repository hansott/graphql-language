<?php

namespace HansOtt\GraphQL\Query;

interface Node
{
    /**
     * @return array
     */
    public function getChildren();
}
