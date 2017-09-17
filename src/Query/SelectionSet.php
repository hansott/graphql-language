<?php

namespace HansOtt\GraphQL\Query;

final class SelectionSet extends NodeBase implements Node
{
    public $selections;

    /**
     * @param Selection[] $selections
     * @param Location|null $location
     */
    public function __construct(array $selections, Location $location = null)
    {
        parent::__construct($location);
        $this->selections = $selections;
    }

    public function getChildren()
    {
        return $this->selections;
    }
}
