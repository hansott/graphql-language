<?php

namespace HansOtt\GraphQL\Query;

final class Document implements Node
{
    public $definitions;

    /**
     * @param Definition[] $definitions
     */
    public function __construct(array $definitions = array())
    {
        $this->definitions = $definitions;
    }

    public function getChildren()
    {
        return $this->definitions;
    }
}
