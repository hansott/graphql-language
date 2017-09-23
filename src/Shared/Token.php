<?php

namespace HansOtt\GraphQL\Shared;

use ReflectionClass;

abstract class Token
{
    public $type;
    public $value;
    public $location;

    final public function __construct($type, $value, Location $location)
    {
        $this->type = (int) $type;
        $this->value = (string) $value;
        $this->location = $location;
    }

    final private static function getNames()
    {
        static $typeToName;

        if (is_array($typeToName)) {
            return $typeToName;
        }

        $tokenClassName = get_called_class();
        $reflection = new ReflectionClass($tokenClassName);
        $constants = $reflection->getConstants();
        $typeToName = array_flip($constants);

        return $typeToName;
    }

    final public static function getNameFor($type)
    {
        $typeToName = static::getNames();

        return $typeToName[$type];
    }

    final public function getName()
    {
        return static::getNameFor($this->type);
    }
}
