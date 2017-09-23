<?php

namespace HansOtt\GraphQL\Schema;

final class DeclarationInputObject extends NodeBase implements Declaration
{
    public $name;
    public $fields;

    /**
     * @param string $name
     * @param array $fields
     * @param Location|null $location
     */
    public function __construct($name, array $fields, Location $location = null)
    {
        parent::__construct($location);
        $this->name = $name;
        $this->fields = $fields;
    }

    public function getChildren()
    {
        return $this->fields;
    }
}
