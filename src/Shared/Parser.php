<?php

namespace HansOtt\GraphQL\Shared;

abstract class Parser
{
    /**
     * @var ScannerTokens
     */
    protected $scanner;
    protected $lexer;

    final public function __construct(Lexer $lexer)
    {
        $this->lexer = $lexer;
    }

    abstract protected function getNameFor($tokenType);

    final protected function getParseError($message)
    {
        $token = $this->scanner->getLastToken();
        if ($this->scanner->eof() === false) {
            $token = $this->scanner->peek();
        }

        return new ParseError($message . " (line {$token->location->line}, column {$token->location->column})");
    }

    final protected function expect($tokenType)
    {
        $token = $this->scanner->next();

        if ($token->type !== $tokenType) {
            $expectedToken = $this->getNameFor($tokenType);
            throw $this->getParseError("Expected \"{$expectedToken}\" but instead found \"{$token->getName()}\" with value \"{$token->value}\"");
        }

        return $token;
    }

    final protected function accept($tokenType)
    {
        $token = $this->scanner->peek();

        if ($token->type !== $tokenType) {
            return false;
        }

        return $this->scanner->next();
    }

    final protected function is($tokenType)
    {
        if ($this->scanner->eof()) {
            return false;
        }

        $token = $this->scanner->peek();

        return $token->type === $tokenType;
    }
}
