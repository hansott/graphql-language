<?php

namespace HansOtt\GraphQL\Schema;

final class DeclarationEnum extends NodeBase implements Declaration
{
    public $name;
    public $possibleValues;

    /**
     * @param string $name
     * @param string[] $possibleValues
     * @param Location|null $location
     */
    public function __construct($name, array $possibleValues, Location $location = null)
    {
        parent::__construct($location);
        $this->name = $name;
        $this->possibleValues = $possibleValues;
    }
}
