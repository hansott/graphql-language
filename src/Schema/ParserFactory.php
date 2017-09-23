<?php

namespace HansOtt\GraphQL\Schema;

final class ParserFactory
{
    public function create()
    {
        $lexer = new Lexer;

        return new Parser($lexer);
    }
}
