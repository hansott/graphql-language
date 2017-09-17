<?php

namespace HansOtt\GraphQL\Query;

final class VariableDefinition extends NodeBase
{
    public $variable;
    public $type;
    public $defaultValue;

    public function __construct(ValueVariable $variable, Type $type, Value $defaultValue = null, Location $location = null)
    {
        parent::__construct($location);
        $this->variable = $variable;
        $this->type = $type;
        $this->defaultValue = $defaultValue;
    }

    public function getChildren()
    {
        return array($this->variable, $this->type, $this->defaultValue);
    }
}
