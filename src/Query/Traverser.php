<?php

namespace HansOtt\GraphQL\Query;

final class Traverser
{
    private $visitor;

    public function __construct(Visitor $visitor)
    {
        $this->visitor = $visitor;
    }

    public function traverse(Document $document)
    {
        $this->visitor->beforeTraverse($document);
        $this->traverseNode($document);
        $this->visitor->afterTraverse($document);
    }

    private function traverseNode(Node $node)
    {
        $this->visitor->enterNode($node);
        $children = $node->getChildren();
        foreach ($children as $child) {
            if ($child instanceof Node) {
                $this->traverseNode($child);
            }
        }

        $this->visitor->leaveNode($node);
    }
}
