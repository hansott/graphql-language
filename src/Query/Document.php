<?php

namespace HansOtt\GraphQL\Query;

final class Document extends NodeBase
{
    public $definitions;

    /**
     * @param Definition[] $definitions
     * @param Location|null $location
     */
    public function __construct(array $definitions = array(), Location $location = null)
    {
        parent::__construct($location);
        $this->definitions = $definitions;
    }

    public function getChildren()
    {
        return $this->definitions;
    }
}
