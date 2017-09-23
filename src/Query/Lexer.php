<?php

namespace HansOtt\GraphQL\Query;

use HansOtt\GraphQL\Shared\ScannerGeneric;
use HansOtt\GraphQL\Shared\ScannerWithLocation;
use HansOtt\GraphQL\Shared\Lexer as LexerShared;

final class Lexer implements LexerShared
{
    /**
     * @var ScannerWithLocation
     */
    private $scanner;
    private $tokens;

    private function emit($type, $value, $location)
    {
        $this->tokens[] = new Token($type, $value, $location);
    }

    private function getLocation()
    {
        $line = $this->scanner->getLine();
        $column = $this->scanner->getColumn();

        return new Location($line, $column);
    }

    private function getError($message)
    {
        $line = $this->scanner->getLine();
        $column = $this->scanner->getColumn();

        return new SyntaxError($message . " (line {$line}, column {$column})");
    }

    private function name()
    {
        $name = $this->scanner->next();
        $location = $this->getLocation();

        if ($this->scanner->eof()) {
            $this->emit(Token::T_NAME, $name, $location);
            return;
        }

        $next = $this->scanner->peek();
        while ($next === '_' || ctype_alpha($next) || ctype_digit($next)) {
            $name .= $this->scanner->next();
            if ($this->scanner->eof()) {
                break;
            }
            $next = $this->scanner->peek();
        }

        $type = Token::T_NAME;
        if ($name === 'query') {
            $type = Token::T_QUERY;
        } elseif ($name === 'mutation') {
            $type = Token::T_MUTATION;
        } elseif ($name === 'subscription') {
            $type = Token::T_SUBSCRIPTION;
        } elseif ($name === 'fragment') {
            $type = Token::T_FRAGMENT;
        } elseif ($name === 'true') {
            $type = Token::T_TRUE;
        } elseif ($name === 'false') {
            $type = Token::T_FALSE;
        } elseif ($name === 'null') {
            $type = Token::T_NULL;
        }

        $this->emit($type, $name, $location);
    }

    private function comment()
    {
        $this->scanner->next();
        $next = $this->scanner->peek();
        while ($this->scanner->eof() === false && $next !== "\n" && $next !== "\r") {
            $this->scanner->next();
            $next = $this->scanner->peek();
        }
    }

    private function spread()
    {
        $points = $this->scanner->next();
        $location = $this->getLocation();
        $next = $this->scanner->peek();

        if ($next !== '.') {
            throw $this->getError("Expected \".\" but instead found \"{$next}\"");
        }

        $points .= $this->scanner->next();
        $next = $this->scanner->peek();

        if ($next !== '.') {
            throw $this->getError("Expected \".\" but instead found \"{$next}\"");
        }

        $points .= $this->scanner->next();
        $this->emit(Token::T_SPREAD, $points, $location);
    }

    private function str()
    {
        $this->scanner->next();
        $location = $this->getLocation();
        $string = '';
        $previousChar = false;

        while (true) {
            if ($this->scanner->eof()) {
                throw $this->getError('Unclosed quote');
            }
            $next = $this->scanner->peek();
            if ($previousChar !== '\\' && $next === '"') {
                $this->scanner->next();
                break;
            }
            $previousChar = $this->scanner->next();
            $string .= $previousChar;
        }

        $string = json_decode('"' . $string . '"');
        $this->emit(Token::T_STRING, $string, $location);
    }

    private function integerPart()
    {
        $number = $this->scanner->next();
        $location = $this->getLocation();
        if ($number === '-') {
            if ($this->scanner->eof()) {
                throw $this->getError('Expected a digit but instead reached end');
            }
            $next = $this->scanner->peek();
            if (ctype_digit($next) === false) {
                throw $this->getError("Expected a digit but instead found \"{$next}\"");
            }
        }

        $next = $this->scanner->peek();
        if ($next === '0') {
            $number .= $this->scanner->next();
            return array($number, $location);
        }

        $next = $this->scanner->peek();
        while ($this->scanner->eof() === false && ctype_digit($next)) {
            $number .= $this->scanner->next();
            $next = $this->scanner->peek();
        }

        return array($number, $location);
    }

    private function fractionalPart()
    {
        $part = $this->scanner->next();

        if ($this->scanner->eof()) {
            throw $this->getError('Expected a digit but instead reached end');
        }

        $next = $this->scanner->peek();
        if (ctype_digit($next) === false) {
            throw $this->getError("Expected a digit but instead found \"{$next}\"");
        }

        $next = $this->scanner->peek();
        while ($this->scanner->eof() === false && ctype_digit($next)) {
            $part .= $this->scanner->next();
            $next = $this->scanner->peek();
        }

        return $part;
    }

    private function exponentPart()
    {
        $part = $this->scanner->next();

        if ($this->scanner->eof()) {
            throw $this->getError('Expected a digit but instead reached end');
        }

        $next = $this->scanner->peek();
        if ($next === '+' || $next === '-') {
            $part .= $this->scanner->next();
        }

        $next = $this->scanner->peek();
        if (ctype_digit($next) === false) {
            throw $this->getError("Expected a digit but instead found \"{$next}\"");
        }

        $next = $this->scanner->peek();
        while ($this->scanner->eof() === false && ctype_digit($next)) {
            $part .= $this->scanner->next();
            $next = $this->scanner->peek();
        }

        return $part;
    }

    private function number()
    {
        list ($integerPart, $location) = $this->integerPart();
        if ($this->scanner->eof()) {
            $this->emit(Token::T_INT, $integerPart, $location);
            return;
        }

        $next = $this->scanner->peek();
        if ($next !== '.' && $next !== 'e' && $next !== 'E') {
            $this->emit(Token::T_INT, $integerPart, $location);
            return;
        }

        $number = $integerPart;
        if ($next === '.') {
            $number .= $this->fractionalPart();
        }

        $next = $this->scanner->peek();
        if ($next === 'e' || $next === 'E') {
            $number .= $this->exponentPart();
        }

        $this->emit(Token::T_FLOAT, $number, $location);
    }

    /**
     * @param string $query
     *
     * @throws SyntaxError
     *
     * @return Token[]
     */
    public function lex($query)
    {
        $flags = PREG_SPLIT_NO_EMPTY;
        $chars = preg_split('//u', $query, -1, $flags);
        $scanner = new ScannerGeneric($chars);
        $this->scanner = new ScannerWithLocation($scanner);
        $this->tokens = array();
        $punctuators = array(
            '!' => Token::T_EXCLAMATION,
            '$' => Token::T_DOLLAR,
            '(' => Token::T_PAREN_LEFT,
            ')' => Token::T_PAREN_RIGHT,
            '{' => Token::T_BRACE_LEFT,
            '}' => Token::T_BRACE_RIGHT,
            ':' => Token::T_COLON,
            ',' => Token::T_COMMA,
            '[' => Token::T_BRACKET_LEFT,
            ']' => Token::T_BRACKET_RIGHT,
            '=' => Token::T_EQUAL,
            '@' => Token::T_AT,
        );

        while ($this->scanner->eof() === false) {
            $next = $this->scanner->peek();

            if (ctype_space($next)) {
                $this->scanner->next();
                continue;
            }

            if ($next === '#') {
                $this->comment();
                continue;
            }

            if ($next === '_' || ctype_alpha($next)) {
                $this->name();
                continue;
            }

            if ($next === '.') {
                $this->spread();
                continue;
            }

            if ($next === '"') {
                $this->str();
                continue;
            }

            if ($next === '-' || ctype_digit($next)) {
                $this->number();
                continue;
            }

            if (isset($punctuators[$next])) {
                $this->emit($punctuators[$next], $this->scanner->next(), $this->getLocation());
                continue;
            }

            throw $this->getError("Unknown character: \"{$next}\"");
        }

        return $this->tokens;
    }
}
