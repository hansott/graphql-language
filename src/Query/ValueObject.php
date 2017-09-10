<?php

namespace HansOtt\GraphQL\Query;

final class ValueObject
{
    private $properties;

    /**
     * @param Value[] $properties
     */
    public function __construct(array $properties)
    {
        $this->properties = $properties;
    }
}
