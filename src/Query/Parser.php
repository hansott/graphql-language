<?php

namespace HansOtt\GraphQL\Query;

final class Parser
{
    /**
     * @var ScannerTokens
     */
    private $scanner;
    private $lexer;

    public function __construct(Lexer $lexer)
    {
        $this->lexer = $lexer;
    }

    private function error($message)
    {
        $token = $this->scanner->getLastToken();
        if ($this->scanner->eof() === false) {
            $token = $this->scanner->peek();
        }

        throw new ParseError($message . " (line {$token->line}, column {$token->column})");
    }

    private function expect($tokenType)
    {
        $token = $this->scanner->next();

        if ($token->type !== $tokenType) {
            $expectedToken = Token::getNameFor($tokenType);
            $this->error("Expected \"{$expectedToken}\" but instead found \"{$token->getName()}\" with value \"{$token->value}\"");
        }

        return $token;
    }

    private function accept($tokenType)
    {
        $token = $this->scanner->peek();

        if ($token->type !== $tokenType) {
            return false;
        }

        return $this->scanner->next();
    }

    private function is($tokenType)
    {
        if ($this->scanner->eof()) {
            return false;
        }

        $token = $this->scanner->peek();

        return $token->type === $tokenType;
    }

    private function parseList()
    {
        $this->expect(Token::T_BRACKET_LEFT);
        $items = array();

        while (true) {
            if ($this->scanner->eof()) {
                $this->error('Unclosed bracket of list');
            }

            if ($this->accept(Token::T_BRACKET_RIGHT)) {
                break;
            }

            $items[] = $this->parseValue();
            $this->accept(Token::T_COMMA);
        }

        return new ValueList($items);
    }

    private function parseValue()
    {
        if ($string = $this->accept(Token::T_STRING)) {
            return new ValueString($string->value);
        }

        if ($this->accept(Token::T_TRUE)) {
            return new ValueBool(true);
        }

        if ($this->accept(Token::T_FALSE)) {
            return new ValueBool(false);
        }

        if ($this->accept(Token::T_NULL)) {
            return new ValueNull;
        }

        if ($integer = $this->accept(Token::T_INTEGER)) {
            return new ValueInteger($integer->value);
        }

        if ($float = $this->accept(Token::T_FLOAT)) {
            return new ValueFloat($float->value);
        }

        if ($this->is(Token::T_BRACKET_LEFT)) {
            return $this->parseList();
        }

        $token = $this->scanner->peek();
        $this->error("Expected a value but instead found \"{$token->getName()}\" with value \"{$token->value}\"");
    }

    private function parseFieldArgument()
    {
        $name = $this->expect(Token::T_NAME)->value;
        $this->expect(Token::T_COLON);
        $value = $this->parseValue();

        return new SelectionFieldArgument($name, $value);
    }

    private function parseFieldArguments()
    {
        $this->expect(Token::T_PAREN_LEFT);
        $arguments = array();

        while (true) {
            if ($this->scanner->eof()) {
                $this->error('Unclosed brace');
            }

            if ($this->accept(Token::T_PAREN_RIGHT)) {
                break;
            }

            $arguments[] = $this->parseFieldArgument();
            $this->accept(Token::T_COMMA);
        }

        return $arguments;
    }

    private function parseField()
    {
        $name = $this->expect(Token::T_NAME)->value;

        $arguments = array();
        if ($this->is(Token::T_PAREN_LEFT)) {
            $arguments = $this->parseFieldArguments();
        }

        $selectionSet = null;
        if ($this->is(Token::T_BRACE_LEFT)) {
            $selectionSet = $this->parseSelectionSet();
        }

        return new SelectionField(null, $name, $arguments, array(), $selectionSet);
    }

    private function parseFragment()
    {
        $this->expect(Token::T_SPREAD);
        $fragmentName = $this->expect(Token::T_NAME)->value;

        if ($fragmentName !== 'on') {
            return new SelectionFragmentSpread($fragmentName);
        }

        $this->scanner->back();
        $typeCondition = $this->parseTypeCondition();
        $selectionSet = $this->parseSelectionSet();

        return new SelectionInlineFragment($typeCondition, array(), $selectionSet);
    }

    private function parseSelection()
    {
        if ($this->is(Token::T_SPREAD)) {
            return $this->parseFragment();
        }

        if ($this->is(Token::T_NAME)) {
            return $this->parseField();
        }

        $tokenNameName = Token::getNameFor(Token::T_NAME);
        $tokenSpreadName = Token::getNameFor(Token::T_SPREAD);
        $message = "Expected \"{$tokenNameName}\" (field) or \"{$tokenSpreadName}\" (fragment spread / inline fragment)";

        if ($this->scanner->eof()) {
            $this->error($message . ' but instead reached end');
        }

        $token = $this->scanner->peek();
        $this->error($message . " but instead found \"{$token->getName()}\" with value \"{$token->value}\"");
    }

    private function parseSelectionSet()
    {
        $this->expect(Token::T_BRACE_LEFT);

        $selections = array();
        while (true) {
            if ($this->scanner->eof()) {
                $this->error('Unclosed brace');
            }

            if ($this->accept(Token::T_BRACE_RIGHT)) {
                break;
            }

            $selections[] = $this->parseSelection();
        }

        return new SelectionSet($selections);
    }

    private function parseTypeCondition()
    {
        $on = $this->expect(Token::T_NAME)->value;
        if ($on !== 'on') {
            $tokenNameName = Token::getNameFor(Token::T_NAME);
            $this->error("Expected \"on\" but instead found \"{$tokenNameName}\" with value \"{$on}\"");
        }

        $namedType = $this->expect(Token::T_NAME)->value;

        return new TypeCondition($namedType);
    }

    private function parseDefinition()
    {
        if ($this->is(Token::T_FRAGMENT)) {
            $this->expect(Token::T_FRAGMENT);

            $name = $this->expect(Token::T_NAME)->value;
            if ($name === 'on') {
                $this->error('A fragment cannot be named "on"');
            }

            $typeCondition = $this->parseTypeCondition();
            $selectionSet = $this->parseSelectionSet();

            return new DefinitionFragment($name, $typeCondition, array(), $selectionSet);
        }

        if ($this->is(Token::T_MUTATION)) {
            $this->expect(Token::T_MUTATION);
            $name = $this->expect(Token::T_NAME)->value;
            $selectionSet = $this->parseSelectionSet();

            return new DefinitionOperationMutation($name, array(), array(), $selectionSet);
        }

        if ($this->is(Token::T_SUBSCRIPTION)) {
            $this->expect(Token::T_SUBSCRIPTION);
            $name = $this->expect(Token::T_NAME)->value;
            $selectionSet = $this->parseSelectionSet();

            return new DefinitionOperationSubscription($name, array(), array(), $selectionSet);
        }

        if ($this->is(Token::T_QUERY)) {
            $this->expect(Token::T_QUERY);

            $name = null;
            if ($this->is(Token::T_NAME)) {
                $name = $this->expect(Token::T_NAME)->value;
            }

            $selectionSet = $this->parseSelectionSet();

            return new DefinitionOperationQuery($name, array(), array(), $selectionSet);
        }

        if ($this->is(Token::T_BRACE_LEFT)) {
            $selectionSet = $this->parseSelectionSet();

            return new DefinitionOperationQuery(null, array(), array(), $selectionSet);
        }

        $queryTokenName = Token::getNameFor(Token::T_QUERY);
        $braceLeftTokenName = Token::getNameFor(Token::T_BRACE_LEFT);
        $mutationTokenName = Token::getNameFor(Token::T_MUTATION);
        $subscriptionTokenName = Token::getNameFor(Token::T_SUBSCRIPTION);
        $message = "Expected \"{$queryTokenName}\", \"{$braceLeftTokenName}\" (query shorthand),"
            . " \"{$mutationTokenName}\" or \"{$subscriptionTokenName}\\";

        if ($this->scanner->eof()) {
            $this->error($message . ' but instead reached end');
        }

        $token = $this->scanner->peek();
        $this->error(
            $message . " but instead found \"{$token->getName()}\" with value \"{$token->value}\""
        );
    }

    private function parseDocument()
    {
        $definitions = array();
        while ($this->scanner->eof() === false) {
            $definition = $this->parseDefinition();
            if (empty($definitions) === false && $definition instanceof DefinitionOperationQuery && $definition->name === null) {
                throw new ParseError('You need to specify a name for your query if there are more then one queries');
            }
            $definitions[] = $definition;
        }

        return new Document($definitions);
    }

    public function parse($query)
    {
        $tokens = $this->lexer->lex($query);
        $scanner = new ScannerGeneric($tokens);
        $this->scanner = new ScannerTokens($scanner);

        return $this->parseDocument();
    }
}
