<?php

namespace HansOtt\GraphQL\Query;

use Exception;

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

        $document = $this->traverseNode($document);
        if (!$document instanceof Document) {
            throw new Exception('Expected a document but instead found ' . get_class($document));
        }

        $this->visitor->afterTraverse($document);

        return $document;
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

        return $node;
    }
}
