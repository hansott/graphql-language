<?php

namespace HansOtt\GraphQL\Query;

use ReflectionClass;

final class Token
{
    const T_NAME = 0;
    const T_STRING = 1;
    const T_FLOAT = 2;
    const T_INT = 3;
    const T_TRUE = 4;
    const T_FALSE = 5;
    const T_NULL = 6;
    const T_QUERY = 7;
    const T_MUTATION = 8;
    const T_SUBSCRIPTION = 9;
    const T_FRAGMENT = 10;
    const T_EXCLAMATION = 11;
    const T_DOLLAR = 12;
    const T_PAREN_LEFT = 13;
    const T_PAREN_RIGHT = 14;
    const T_SPREAD = 15;
    const T_COLON = 16;
    const T_EQUAL = 17;
    const T_AT = 18;
    const T_BRACKET_LEFT = 19;
    const T_BRACKET_RIGHT = 20;
    const T_BRACE_LEFT = 21;
    const T_BRACE_RIGHT = 22;
    const T_PIPE = 23;
    const T_COMMA = 24;

    public $type;
    public $value;
    public $location;

    public function __construct($type, $value, Location $location)
    {
        $this->type = (int) $type;
        $this->value = (string) $value;
        $this->location = $location;
    }

    private static function getNames()
    {
        static $typeToName;

        if (is_array($typeToName)) {
            return $typeToName;
        }

        $reflection = new ReflectionClass('HansOtt\\GraphQL\\Query\\Token');
        $constants = $reflection->getConstants();
        $typeToName = array_flip($constants);

        return $typeToName;
    }

    public static function getNameFor($type)
    {
        $typeToName = static::getNames();

        return $typeToName[$type];
    }

    public function getName()
    {
        return static::getNameFor($this->type);
    }
}
