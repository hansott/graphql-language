<?php

namespace HansOtt\GraphQL\Query;

final class ValueList implements Value
{
    private $values;

    /**
     * @param Value[] $values
     */
    public function __construct(array $values)
    {
        $this->values = $values;
    }

    public function getChildren()
    {
        return $this->values;
    }
}
