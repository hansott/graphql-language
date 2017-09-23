<?php

namespace HansOtt\GraphQL\Schema;

use HansOtt\GraphQL\Shared\Token as TokenShared;

final class Token extends TokenShared
{
    const T_NAME = 0;
    const T_STRING = 1;
    const T_FLOAT = 2;
    const T_INT = 3;
    const T_TRUE = 4;
    const T_FALSE = 5;
    const T_NULL = 6;
    const T_EXCLAMATION = 7;
    const T_PAREN_LEFT = 8;
    const T_PAREN_RIGHT = 9;
    const T_COLON = 10;
    const T_EQUAL = 11;
    const T_BRACKET_LEFT = 12;
    const T_BRACKET_RIGHT = 13;
    const T_BRACE_LEFT = 14;
    const T_BRACE_RIGHT = 15;
    const T_PIPE = 16;
    const T_COMMA = 17;
}
