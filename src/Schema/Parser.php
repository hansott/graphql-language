<?php

namespace HansOtt\GraphQL\Schema;

use HansOtt\GraphQL\Shared\ScannerTokens;
use HansOtt\GraphQL\Shared\ScannerGeneric;
use HansOtt\GraphQL\Shared\Parser as ParserShared;

final class Parser extends ParserShared
{
    private $objects;
    private $enums;
    private $unions;
    private $interfaces;
    private $inputObjects;

    protected function getNameFor($tokenType)
    {
        return Token::getNameFor($tokenType);
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

    private function maybeParseNullType(Type $type)
    {
        if ($this->accept(Token::T_EXCLAMATION)) {
            return new TypeNonNull($type, $type->getLocation());
        }

        return $type;
    }

    private function parseType()
    {
        if ($this->is(Token::T_BRACKET_LEFT)) {
            return $this->maybeParseNullType(
                $this->parseListType()
            );
        }

        if ($this->is(Token::T_NAME)) {
            return $this->maybeParseNullType(
                $this->parseNamedType()
            );
        }

        $message = 'Expected a type';

        if ($this->scanner->eof()) {
            throw $this->getParseError($message . ' but instead reached end');
        }

        $token = $this->scanner->peek();
        throw $this->getParseError($message . " but instead found \"{$token->getName()}\" with value \"{$token->value}\"");
    }

    private function parseObject()
    {
        $location = $this->expect(Token::T_BRACE_LEFT)->location;
        $fields = array();
        while ($this->scanner->eof() === false) {
            if ($this->accept(Token::T_BRACE_RIGHT)) {
                return new ValueObject($fields, $location);
            }

            $nameToken = $this->expect(Token::T_NAME);
            $this->expect(Token::T_COLON);
            $fields[] = new ValueObjectField($nameToken->value, $this->parseValue(), $nameToken->location);
            $this->accept(Token::T_COMMA);
        }

        throw $this->getParseError('Unclosed brace of object value');
    }

    private function parseList()
    {
        $location = $this->expect(Token::T_BRACKET_LEFT)->location;
        $items = array();
        while ($this->scanner->eof() === false) {
            if ($this->accept(Token::T_BRACKET_RIGHT)) {
                return new ValueList($items, $location);
            }

            $items[] = $this->parseValue();
            $this->accept(Token::T_COMMA);
        }

        throw $this->getParseError('Unclosed bracket of list');

    }

    private function parseValue()
    {
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

    private function parseFieldArguments()
    {
        if ($this->accept(Token::T_PAREN_LEFT) === false) {
            return array();
        }

        $arguments = array();
        while ($this->scanner->eof() === false) {
            if ($this->accept(Token::T_PAREN_RIGHT)) {
                return $arguments;
            }

            $nameToken = $this->expect(Token::T_NAME);
            $this->expect(Token::T_COLON);
            $type = $this->parseType();
            $defaultValue = null;

            if ($this->accept(Token::T_EQUAL)) {
                $defaultValue = $this->parseValue();
            }

            $arguments[] = new Argument(
                $nameToken->value,
                $type,
                $defaultValue,
                $nameToken->location
            );

            $this->accept(Token::T_COMMA);
        }

        return $arguments;
    }

    private function parseField($forInputObject)
    {
        $nameToken = $this->expect(Token::T_NAME);

        $arguments = array();
        if ($forInputObject === false) {
            $arguments = $this->parseFieldArguments();
        }

        $this->expect(Token::T_COLON);
        $type = $this->parseType();

        return new Field(
            $nameToken->value,
            $type,
            $arguments,
            $nameToken->location
        );
    }

    private function parseObjectDeclaration()
    {
        $location = $this->expect(Token::T_NAME)->location;
        $name = $this->expect(Token::T_NAME)->value;

        $interface = null;
        if ($implementsToken = $this->accept(Token::T_NAME)) {
            if ($implementsToken->value !== 'implements') {
                throw $this->getParseError("Expected \"implements\" but instead found \"{$implementsToken->value}\"");
            }
            $interface = $this->expect(Token::T_NAME)->value;
        }

        $this->expect(Token::T_BRACE_LEFT);
        $fields = array();
        while ($this->scanner->eof() === false) {
            if ($this->accept(Token::T_BRACE_RIGHT)) {
                return new DeclarationObject(
                    $name,
                    $fields,
                    $interface,
                    $location
                );
            }

            $fields[] = $this->parseField(false);
            $this->accept(Token::T_COMMA);
        }

        throw $this->getParseError('Expected an object field but instead reached end');
    }

    private function parseInputObjectDeclaration()
    {
        $location = $this->expect(Token::T_NAME)->location;
        $name = $this->expect(Token::T_NAME)->value;
        $this->expect(Token::T_BRACE_LEFT);
        $fields = array();
        while ($this->scanner->eof() === false) {
            if ($this->accept(Token::T_BRACE_RIGHT)) {
                return new DeclarationInputObject(
                    $name,
                    $fields,
                    $location
                );
            }

            $fields[] = $this->parseField(true);
            $this->accept(Token::T_COMMA);
        }

        throw $this->getParseError('Expected an input object field but instead reached end');
    }

    private function parseEnumDeclaration()
    {
        $location = $this->expect(Token::T_NAME)->location;
        $name = $this->expect(Token::T_NAME)->value;
        $this->expect(Token::T_BRACE_LEFT);
        $values = array();
        while ($this->scanner->eof() === false) {
            if ($this->accept(Token::T_BRACE_RIGHT)) {
                return new DeclarationEnum(
                    $name,
                    $values,
                    $location
                );
            }

            $values[] = $this->expect(Token::T_NAME)->value;
            $this->accept(Token::T_COMMA);
        }

        throw $this->getParseError('Expected an enumeration value but instead reached end');
    }

    private function parseInterfaceDeclaration()
    {
        $location = $this->expect(Token::T_NAME)->location;
        $name = $this->expect(Token::T_NAME)->value;
        $this->expect(Token::T_BRACE_LEFT);
        $fields = array();
        while ($this->scanner->eof() === false) {
            if ($this->accept(Token::T_BRACE_RIGHT)) {
                return new DeclarationInterface(
                    $name,
                    $fields,
                    $location
                );
            }

            $fields[] = $this->parseField(false);
            $this->accept(Token::T_COMMA);
        }

        throw $this->getParseError('Expected an interface field but instead reached end');
    }

    private function parseUnionDeclaration()
    {
        $location = $this->expect(Token::T_NAME)->location;
        $name = $this->expect(Token::T_NAME)->value;
        $this->expect(Token::T_EQUAL);
        $members = array();
        while ($this->scanner->eof() === false) {
            if ($this->is(Token::T_NAME) === false) {
                return new DeclarationUnion(
                    $name,
                    $members,
                    $location
                );
            }

            $members[] = $this->expect(Token::T_NAME)->value;

            if ($this->accept(Token::T_PIPE) === false) {
                return new DeclarationUnion(
                    $name,
                    $members,
                    $location
                );
            }
        }

        throw $this->getParseError('Expected an interface field but instead reached end');
    }

    private function parseDeclaration()
    {
        $message = 'Expected a type declaration';

        if ($this->scanner->eof()) {
            throw $this->getParseError("{$message} but instead reached end");
        }

        $token = $this->scanner->peek();
        switch ($token->value) {
            case 'type':
                return $this->parseObjectDeclaration();
            case 'input':
                return $this->parseInputObjectDeclaration();
            case 'enum':
                return $this->parseEnumDeclaration();
            case 'interface':
                return $this->parseInterfaceDeclaration();
            case 'union':
                return $this->parseUnionDeclaration();
        }

        throw $this->getParseError("{$message} but instead found {$token->getName()} with value \"{$token->value}\"");
    }

    private function parseSchema()
    {
        $declarations = array();
        while ($this->scanner->eof() === false) {
            $declarations[] = $this->parseDeclaration();
        }

        return new Schema($declarations);
    }

    /**
     * @param string $schema
     *
     * @return Schema
     */
    public function parse($schema)
    {
        $tokens = $this->lexer->lex($schema);
        $scanner = new ScannerGeneric($tokens);
        $this->scanner = new ScannerTokens($scanner);
        $this->objects = array();
        $this->enums = array();
        $this->unions = array();
        $this->interfaces = array();
        $this->inputObjects = array();
        $declaration = $this->parseSchema();

        return $declaration;
    }
}
