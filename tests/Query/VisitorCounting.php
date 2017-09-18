<?php

namespace HansOtt\GraphQL\Query;

final class VisitorCounting extends VisitorBase
{
    private $count = 0;

    public function getCount()
    {
        return $this->count;
    }

    public function enterNode(Node $node)
    {
        $this->count++;
    }
}
