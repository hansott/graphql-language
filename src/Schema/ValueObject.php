<?php

namespace HansOtt\GraphQL\Schema;

final class ValueObject extends NodeBase implements Value
{
    public $fields;

    /**
     * @param ValueObjectField[] $fields
     * @param Location|null $location
     */
    public function __construct(array $fields = array(), Location $location = null)
    {
        parent::__construct($location);
        $this->fields = $fields;
    }

    public function getChildren()
    {
        return $this->fields;
    }
}
