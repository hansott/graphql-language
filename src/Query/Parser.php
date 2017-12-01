<?php

namespace HansOtt\GraphQL\Query;

use HansOtt\GraphQL\Shared\ScannerTokens;
use HansOtt\GraphQL\Shared\ScannerGeneric;
use HansOtt\GraphQL\Shared\Parser as ParserShared;

final class Parser extends ParserShared
{
    protected function getNameFor($tokenType)
    {
        return Token::getNameFor($tokenType);
    }

    private function parseObject()
    {
        $location = $this->expect(Token::T_BRACE_LEFT)->location;
        $fields = array();

        while (true) {
            if ($this->scanner->eof()) {
                throw $this->getParseError('Unclosed brace of object value');
            }

            if ($this->accept(Token::T_BRACE_RIGHT)) {
                break;
            }

            $nameToken = $this->expect(Token::T_NAME);
            $this->expect(Token::T_COLON);
            $fields[] = new ValueObjectField($nameToken->value, $this->parseValue(), $nameToken->location);
            $this->accept(Token::T_COMMA);
        }

        return new ValueObject($fields, $location);
    }

    private function parseList()
    {
        $location = $this->expect(Token::T_BRACKET_LEFT)->location;
        $items = array();

        while (true) {
            if ($this->scanner->eof()) {
                throw $this->getParseError('Unclosed bracket of list');
            }

            if ($this->accept(Token::T_BRACKET_RIGHT)) {
                break;
            }

            $items[] = $this->parseValue();
            $this->accept(Token::T_COMMA);
        }

        return new ValueList($items, $location);
    }

    private function parseVariable()
    {
        $location = $this->expect(Token::T_DOLLAR)->location;
        $name = $this->expect(Token::T_NAME)->value;

        return new ValueVariable($name, $location);
    }

    private function parseValue()
    {
        if ($this->is(Token::T_DOLLAR)) {
            return $this->parseVariable();
        }

        if ($string = $this->accept(Token::T_STRING)) {
            return new ValueString($string->value, $string->location);
        }

        if ($true = $this->accept(Token::T_TRUE)) {
            return new ValueBoolean(true, $true->location);
        }

        if ($false = $this->accept(Token::T_FALSE)) {
            return new ValueBoolean(false, $false->location);
        }

        if ($null = $this->accept(Token::T_NULL)) {
            return new ValueNull($null->location);
        }

        if ($int = $this->accept(Token::T_INT)) {
            return new ValueInt($int->value, $int->location);
        }

        if ($float = $this->accept(Token::T_FLOAT)) {
            return new ValueFloat($float->value, $float->location);
        }

        if ($name = $this->accept(Token::T_NAME)) {
            return new ValueEnum($name->value, $name->location);
        }

        if ($this->is(Token::T_BRACKET_LEFT)) {
            return $this->parseList();
        }

        if ($this->is(Token::T_BRACE_LEFT)) {
            return $this->parseObject();
        }

        $message = 'Expected a value';

        if ($this->scanner->eof()) {
            throw $this->getParseError($message . ' but instead reached end');
        }

        $token = $this->scanner->peek();
        throw $this->getParseError($message . " but instead found \"{$token->getName()}\" with value \"{$token->value}\"");
    }

    private function parseArgument()
    {
        $nameToken = $this->expect(Token::T_NAME);
        $this->expect(Token::T_COLON);
        $value = $this->parseValue();

        return new Argument($nameToken->value, $value, $nameToken->location);
    }

    private function parseArgumentList()
    {
        $arguments = array();

        if ($this->is(Token::T_PAREN_LEFT) === false) {
            return $arguments;
        }

        $this->expect(Token::T_PAREN_LEFT);

        while (true) {
            if ($this->scanner->eof()) {
                throw $this->getParseError('Unclosed brace of argument list');
            }

            if ($this->accept(Token::T_PAREN_RIGHT)) {
                break;
            }

            $arguments[] = $this->parseArgument();
            $this->accept(Token::T_COMMA);
        }

        return $arguments;
    }

    private function parseField()
    {
        $nameToken = $this->expect(Token::T_NAME);
        $name = $nameToken->value;

        $alias = null;
        if ($this->is(Token::T_COLON)) {
            $this->expect(Token::T_COLON);
            $aliasToken = $this->expect(Token::T_NAME);
            $alias = $name;
            $name = $aliasToken->value;
        }

        $arguments = $this->parseArgumentList();
        $directives = $this->parseDirectiveList();

        $selectionSet = null;
        if ($this->is(Token::T_BRACE_LEFT)) {
            $selectionSet = $this->parseSelectionSet();
        }

        return new SelectionField($alias, $name, $arguments, $directives, $selectionSet, $nameToken->location);
    }

    private function parseFragment()
    {
        $location = $this->expect(Token::T_SPREAD)->location;
        $fragmentName = $this->expect(Token::T_NAME)->value;

        if ($fragmentName !== 'on') {
            return new SelectionFragmentSpread($fragmentName, $location);
        }

        $this->scanner->back();
        $typeCondition = $this->parseTypeCondition();
        $directives = $this->parseDirectiveList();
        $selectionSet = $this->parseSelectionSet();

        return new SelectionInlineFragment($typeCondition, $directives, $selectionSet, $location);
    }

    private function parseSelection()
    {
        if ($this->is(Token::T_SPREAD)) {
            return $this->parseFragment();
        }

        if ($this->is(Token::T_NAME)) {
            return $this->parseField();
        }

        $message = 'Expected a field, a fragment spread or an inline fragment';

        if ($this->scanner->eof()) {
            throw $this->getParseError($message . ' but instead reached end');
        }

        $token = $this->scanner->peek();
        throw $this->getParseError($message . " but instead found \"{$token->getName()}\" with value \"{$token->value}\"");
    }

    private function parseSelectionSet()
    {
        $location = $this->expect(Token::T_BRACE_LEFT)->location;

        /** @var Selection[] $selections */
        $selections = array();
        while (true) {
            if ($this->scanner->eof()) {
                throw $this->getParseError('Unclosed brace of selection set');
            }

            if ($this->accept(Token::T_BRACE_RIGHT)) {
                break;
            }

            $selections[] = $this->parseSelection();
        }

        return new SelectionSet($selections, $location);
    }

    private function parseTypeCondition()
    {
        $nameToken = $this->expect(Token::T_NAME);
        $on = $nameToken->value;
        if ($on !== 'on') {
            $tokenNameName = Token::getNameFor(Token::T_NAME);
            throw $this->getParseError("Expected a type condition but instead found \"{$tokenNameName}\" with value \"{$on}\"");
        }

        $type = $this->parseNamedType();

        return new TypeCondition($type, $nameToken->location);
    }

    private function parseListType()
    {
        $location = $this->expect(Token::T_BRACKET_LEFT)->location;
        $type = $this->parseType();
        $this->accept(Token::T_BRACKET_RIGHT);

        return new TypeList($type, $location);
    }

    private function parseNamedType()
    {
        $nameToken = $this->expect(Token::T_NAME);
        $type = new TypeNamed($nameToken->value, $nameToken->location);

        return $type;
    }

    private function parseType()
    {
        $type = null;
        if ($this->is(Token::T_BRACKET_LEFT)) {
            $type = $this->parseListType();
        } elseif ($this->is(Token::T_NAME)) {
            $type = $this->parseNamedType();
        }

        if ($type !== null) {
            if ($this->accept(Token::T_EXCLAMATION)) {
                return new TypeNonNull($type, $type->location);
            }
            return $type;
        }

        $message = 'Expected a type';

        if ($this->scanner->eof()) {
            throw $this->getParseError($message . ' but instead reached end');
        }

        $token = $this->scanner->peek();
        throw $this->getParseError($message . " but instead found \"{$token->getName()}\" with value \"{$token->value}\"");
    }

    private function parseDirective()
    {
        $location = $this->expect(Token::T_AT)->location;
        $name = $this->expect(Token::T_NAME)->value;
        $arguments = $this->parseArgumentList();

        return new Directive($name, $arguments, $location);
    }

    private function parseDirectiveList()
    {
        $directives = array();
        while ($this->is(Token::T_AT)) {
            $directives[] = $this->parseDirective();
        }

        return $directives;
    }

    private function parseVariableDefinition()
    {
        $variable = $this->parseVariable();
        $this->expect(Token::T_COLON);
        $type = $this->parseType();
        $defaultValue = null;

        if ($this->accept(Token::T_EQUAL)) {
            $defaultValue = $this->parseValue();
        }

        return new VariableDefinition($variable, $type, $defaultValue, $variable->location);
    }

    private function parseVariableDefinitionList()
    {
        $definitions = array();

        if ($this->is(Token::T_PAREN_LEFT) === false) {
            return $definitions;
        }

        $this->expect(Token::T_PAREN_LEFT);

        while (true) {
            if ($this->scanner->eof()) {
                throw $this->getParseError('Unclosed parenthesis of variable definition list');
            }

            if ($this->accept(Token::T_PAREN_RIGHT)) {
                break;
            }

            $definitions[] = $this->parseVariableDefinition();
            $this->accept(Token::T_COMMA);
        }

        return $definitions;
    }

    private function parseDefinition()
    {
        if ($this->is(Token::T_FRAGMENT)) {
            $location = $this->expect(Token::T_FRAGMENT)->location;

            $name = $this->expect(Token::T_NAME)->value;
            if ($name === 'on') {
                throw $this->getParseError('A fragment cannot be named "on"');
            }

            $typeCondition = $this->parseTypeCondition();
            $directives = $this->parseDirectiveList();
            $selectionSet = $this->parseSelectionSet();

            return new Fragment($name, $typeCondition, $directives, $selectionSet, $location);
        }

        if ($this->is(Token::T_MUTATION)) {
            $location = $this->expect(Token::T_MUTATION)->location;

            $name = null;
            if ($this->is(Token::T_NAME)) {
                $name = $this->expect(Token::T_NAME)->value;
            }

            $variables = $this->parseVariableDefinitionList();
            $directives = $this->parseDirectiveList();
            $selectionSet = $this->parseSelectionSet();

            return new OperationMutation($name, $variables, $directives, $selectionSet, $location);
        }

        if ($this->is(Token::T_SUBSCRIPTION)) {
            $location = $this->expect(Token::T_SUBSCRIPTION)->location;

            $name = null;
            if ($this->is(Token::T_NAME)) {
                $name = $this->expect(Token::T_NAME)->value;
            }

            $variables = $this->parseVariableDefinitionList();
            $directives = $this->parseDirectiveList();
            $selectionSet = $this->parseSelectionSet();

            return new OperationSubscription($name, $variables, $directives, $selectionSet, $location);
        }

        if ($this->is(Token::T_QUERY)) {
            $location = $this->expect(Token::T_QUERY)->location;

            $name = null;
            if ($this->is(Token::T_NAME)) {
                $name = $this->expect(Token::T_NAME)->value;
            }

            $variables = $this->parseVariableDefinitionList();
            $directives = $this->parseDirectiveList();
            $selectionSet = $this->parseSelectionSet();

            return new OperationQuery($name, $variables, $directives, $selectionSet, $location);
        }

        if ($this->is(Token::T_BRACE_LEFT)) {
            $selectionSet = $this->parseSelectionSet();

            return new OperationQuery(null, array(), array(), $selectionSet, $selectionSet->location);
        }

        $message = 'Expected a query, a query shorthand, a mutation or a subscription operation';

        if ($this->scanner->eof()) {
            throw $this->getParseError($message . ' but instead reached end');
        }

        $token = $this->scanner->peek();
        throw $this->getParseError($message . " but instead found \"{$token->getName()}\" with value \"{$token->value}\"");
    }

    private function parseDocument()
    {
        /** @var Definition[] $definitions */
        $definitions = array();
        while ($this->scanner->eof() === false) {
            $definitions[] = $this->parseDefinition();
        }

        return new Document($definitions);
    }

    /**
     * @param string $query
     *
     * @return Document
     */
    public function parse($query)
    {
        $tokens = $this->lexer->lex($query);
        $scanner = new ScannerGeneric($tokens);
        $this->scanner = new ScannerTokens($scanner);
        $document = $this->parseDocument();

        return $document;
    }
}
