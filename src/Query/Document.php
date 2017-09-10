<?php

namespace HansOtt\GraphQL\Query;

final class Document
{
    public $definitions;

    /**
     * @param Definition[] $definitions
     */
    public function __construct(array $definitions = array())
    {
        $this->definitions = $definitions;
    }
}
