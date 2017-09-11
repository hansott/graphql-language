<?php

namespace HansOtt\GraphQL\Query;

final class OperationMutation extends OperationBase implements Operation
{
    public function __construct($name, array $variables = array(), $directives = array(), SelectionSet $selectionSet)
    {
        parent::__construct($name, $variables, $directives, $selectionSet);
    }
}