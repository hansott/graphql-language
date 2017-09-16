<?php

namespace HansOtt\GraphQL\Query;

final class ValueObject implements Value
{
    public $fields;

    /**
     * @param ValueObjectField[] $fields
     */
    public function __construct(array $fields = array())
    {
        $this->fields = $fields;
    }

    public function getChildren()
    {
        return $this->fields;
    }
}
