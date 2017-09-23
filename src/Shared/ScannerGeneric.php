<?php

namespace HansOtt\GraphQL\Shared;

final class ScannerGeneric implements Scanner
{
    private $items;
    private $position = -1;

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    public function peek()
    {
        if (isset($this->items[$this->position + 1]) === false) {
            throw new ScannerReachedEnd('Unable to peek because scanner reached end');
        }

        return $this->items[$this->position + 1];
    }

    public function next()
    {
        if (isset($this->items[$this->position + 1]) === false) {
            $nextPosition = $this->position + 1;
            throw new ScannerReachedEnd("Tried to move to position {$nextPosition} but reached end");
        }

        $this->position++;

        return $this->items[$this->position];
    }

    public function eof()
    {
        return isset($this->items[$this->position + 1]) === false;
    }

    public function back()
    {
        if (isset($this->items[$this->position - 1]) === false) {
            $nextPosition = $this->position - 1;
            throw new ScannerReachedEnd("Tried to move to position {$nextPosition} but reached end");
        }

        $this->position--;

        return $this->items[$this->position];
    }
}
