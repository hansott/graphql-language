<?php

namespace HansOtt\GraphQL\Query;

use RuntimeException;

final class ScannerWithLocation implements Scanner
{
    private $scanner;
    private $line = 1;
    private $column = 1;

    public function __construct(Scanner $scanner)
    {
        $this->scanner = $scanner;
    }

    public function getLine()
    {
        return $this->line;
    }

    public function getColumn()
    {
        return $this->column;
    }

    public function peek()
    {
        return $this->scanner->peek();
    }

    public function next()
    {
        $current = $this->scanner->next();
        if ($current === "\n" || $current === "\r") {
            $this->line++;
            $this->column = 1;
        } else {
            $this->column++;
        }

        return $current;
    }

    public function eof()
    {
        return $this->scanner->eof();
    }

    public function back()
    {
        throw new RuntimeException('Not implemented');
    }
}
