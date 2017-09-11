<?php

namespace HansOtt\GraphQL\Query;

final class VariableDefinition
{
    public $variable;
    public $type;
    public $defaultValue;

    public function __construct(ValueVariable $variable, Type $type, Value $defaultValue = null)
    {
        $this->variable = $variable;
        $this->type = $type;
        $this->defaultValue = $defaultValue;
    }
}
