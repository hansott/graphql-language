<?php

namespace HansOtt\GraphQL\Query;

final class ParserFactory
{
    public function create()
    {
        $lexer = new Lexer;

        return new Parser($lexer);
    }
}
