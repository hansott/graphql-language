<?php

namespace HansOtt\GraphQL\Schema;

final class DeclarationInterface extends NodeBase implements Declaration
{
    public $name;
    public $fields;

    /**
     * @param string $name
     * @param Field[] $fields
     * @param Location|null $location
     */
    public function __construct($name, array $fields, Location $location = null)
    {
        parent::__construct($location);
        $this->name = (string) $name;
        $this->fields = $fields;
    }

    public function getChildren()
    {
        return $this->fields;
    }
}
