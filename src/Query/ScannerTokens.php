<?php

namespace HansOtt\GraphQL\Query;

final class ScannerTokens implements Scanner
{
    private $scanner;
    private $lastToken;

    public function __construct(Scanner $scanner)
    {
        $this->scanner = $scanner;
    }

    public function getLastToken()
    {
        return $this->lastToken;
    }

    /**
     * @throws ScannerReachedEnd
     *
     * @return Token
     */
    public function peek()
    {
        return $this->scanner->peek();
    }

    /**
     * @throws ScannerReachedEnd
     *
     * @return Token
     */
    public function next()
    {
        return $this->lastToken = $this->scanner->next();
    }

    public function eof()
    {
        return $this->scanner->eof();
    }

    /**
     * @throws ScannerReachedEnd
     *
     * @return Token
     */
    public function back()
    {
        return $this->lastToken = $this->scanner->back();
    }
}
