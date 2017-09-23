<?php

namespace HansOtt\GraphQL\Query;

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
}
