<?php

namespace HansOtt\GraphQL\Query;

final class Lexer
{
    /**
     * @var ScannerWithLocation
     */
    private $scanner;
    private $tokens;

    private function emit($type, $value)
    {
        $line = $this->scanner->getLine();
        $column = $this->scanner->getColumn();
        $this->tokens[] = new Token($type, $value, $line, $column);
    }

    private function error($message)
    {
        $line = $this->scanner->getLine();
        $column = $this->scanner->getColumn();
        throw new SyntaxError($message . " (line {$line}, column {$column})");
    }

    private function name()
    {
        $name = $this->scanner->next();

        if ($this->scanner->eof()) {
            $this->emit(Token::T_NAME, $name);
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

        $this->emit($type, $name);
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
        $next = $this->scanner->peek();

        if ($next !== '.') {
            $this->error("Expected \".\" but instead found \"{$next}\"");
        }

        $points .= $this->scanner->next();
        $next = $this->scanner->peek();

        if ($next !== '.') {
            $this->error("Expected \".\" but instead found \"{$next}\"");
        }

        $points .= $this->scanner->next();
        $this->emit(Token::T_SPREAD, $points);
    }

    private function str()
    {
        $this->scanner->next();
        $string = '';
        $previousChar = false;

        while (true) {
            if ($this->scanner->eof()) {
                $this->error('Unclosed quote');
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
        $this->emit(Token::T_STRING, $string);
    }

    private function integerPart()
    {
        $number = $this->scanner->next();
        if ($number === '-') {
            if ($this->scanner->eof()) {
                $this->error('Expected a digit but instead reached end');
            }
            $next = $this->scanner->peek();
            if (ctype_digit($next) === false) {
                $this->error("Expected a digit but instead found \"{$next}\"");
            }
        }

        $next = $this->scanner->peek();
        if ($next === '0') {
            $number .= $this->scanner->next();
            return $number;
        }

        $next = $this->scanner->peek();
        while ($this->scanner->eof() === false && ctype_digit($next)) {
            $number .= $this->scanner->next();
            $next = $this->scanner->peek();
        }

        return $number;
    }

    private function fractionalPart()
    {
        $part = $this->scanner->next();

        if ($this->scanner->eof()) {
            $this->error('Expected a digit but instead reached end');
        }

        $next = $this->scanner->peek();
        if (ctype_digit($next) === false) {
            $this->error("Expected a digit but instead found \"{$next}\"");
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
            $this->error('Expected a digit but instead reached end');
        }

        $next = $this->scanner->peek();
        if ($next === '+' || $next === '-') {
            $part .= $this->scanner->next();
        }

        $next = $this->scanner->peek();
        if (ctype_digit($next) === false) {
            $this->error("Expected a digit but instead found \"{$next}\"");
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
        $integerPart = $this->integerPart();
        if ($this->scanner->eof()) {
            $this->emit(Token::T_INT, $integerPart);
            return;
        }

        $next = $this->scanner->peek();
        if ($next !== '.' && $next !== 'e' && $next !== 'E') {
            $this->emit(Token::T_INT, $integerPart);
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

        $this->emit(Token::T_FLOAT, $number);
    }

    /**
     * @param string $query
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
                $this->emit($punctuators[$next], $this->scanner->next());
                continue;
            }

            $this->error("Unknown character: \"{$next}\"");
        }

        return $this->tokens;
    }
}
