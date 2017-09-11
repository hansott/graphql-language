<?php

namespace HansOtt\GraphQL\Query;

interface Visitor
{
    public function beforeTraverse(Document $document);
    public function enterNode(Node $node);
    public function leaveNode(Node $node);
    public function afterTraverse(Document $document);
}
