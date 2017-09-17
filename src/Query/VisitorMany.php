<?php

namespace HansOtt\GraphQL\Query;

final class VisitorMany implements Visitor
{
    private $visitors;

    /**
     * @param Visitor[] $visitors
     */
    public function __construct(array $visitors)
    {
        $this->visitors = $visitors;
    }

    public function beforeTraverse(Document $document)
    {
        foreach ($this->visitors as $visitor) {
            $visitor->beforeTraverse($document);
        }
    }

    public function enterNode(Node $node)
    {
        foreach ($this->visitors as $visitor) {
            $visitor->enterNode($node);
        }
    }

    public function leaveNode(Node $node)
    {
        foreach ($this->visitors as $visitor) {
            $visitor->leaveNode($node);
        }
    }

    public function afterTraverse(Document $document)
    {
        foreach ($this->visitors as $visitor) {
            $visitor->afterTraverse($document);
        }
    }
}
