<?php

namespace HansOtt\GraphQL\Shared;

interface Lexer
{
    /**
     * @param string $query
     *
     * @throws SyntaxError
     *
     * @return Token[]
     */
    public function lex($query);
}
