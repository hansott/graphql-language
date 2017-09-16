<?php

namespace HansOtt\GraphQL\Query;

final class ValueNull implements Value
{
    public function getChildren()
    {
        return array();
    }
}
