<?php

namespace HansOtt\GraphQL\Schema;

final class DeclarationObject extends NodeBase implements Declaration
{
    public $name;
    public $fields;
    public $interface;

    /**
     * @param string $name
     * @param Field[] $fields
     * @param string $interface
     * @param Location|null $location
     */
    public function __construct($name, array $fields, $interface = null, Location $location = null)
    {
        parent::__construct($location);
        $this->name = (string) $name;
        $this->fields = $fields;
        $this->interface = $interface;
    }

    public function getChildren()
    {
        return $this->fields;
    }
}
