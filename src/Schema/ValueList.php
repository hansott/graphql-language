<?php

namespace HansOtt\GraphQL\Schema;

final class ValueList extends NodeBase implements Value
{
    private $values;

    /**
     * @param Value[] $values
     * @param Location|null $location
     */
    public function __construct(array $values, Location $location = null)
    {
        parent::__construct($location);
        $this->values = $values;
    }

    public function getChildren()
    {
        return $this->values;
    }
}
