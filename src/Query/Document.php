<?php

namespace HansOtt\GraphQL\Query;

final class Document extends NodeBase
{
    public $definitions;

    /**
     * @param Definition[] $definitions
     */
    public function __construct(array $definitions = array())
    {
        $location = null;
        if (empty($definitions) === false) {
            $location = $definitions[0]->getLocation();
        }
        parent::__construct($location);
        $this->definitions = $definitions;
    }

    public function getChildren()
    {
        return $this->definitions;
    }
}
