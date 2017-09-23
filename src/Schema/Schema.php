<?php

namespace HansOtt\GraphQL\Schema;

final class Schema extends NodeBase
{
    public $declarations;

    /**
     * @param Declaration[] $declarations
     */
    public function __construct(array $declarations = array())
    {
        $location = null;
        if (empty($declarations) === false) {
            $location = $declarations[0]->getLocation();
        }
        parent::__construct($location);
        $this->declarations = $declarations;
    }

    public function getChildren()
    {
        return $this->declarations;
    }
}
