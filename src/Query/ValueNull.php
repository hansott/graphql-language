<?php

namespace HansOtt\GraphQL\Query;

final class ValueNull extends NodeBase implements Value
{
    public function getChildren()
    {
        return array();
    }
}
