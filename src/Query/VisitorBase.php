<?php

namespace HansOtt\GraphQL\Query;

abstract class VisitorBase implements Visitor
{
    public function beforeTraverse(Document $document)
    {
    }

    public function enterNode(Node $node)
    {
    }

    public function leaveNode(Node $node)
    {
    }

    public function afterTraverse(Document $document)
    {
    }
}
