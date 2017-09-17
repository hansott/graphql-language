<?php

namespace HansOtt\GraphQL\Query;

final class OperationMutation extends OperationBase
{
    public function __construct(
        $name,
        array $variables = array(),
        $directives = array(),
        SelectionSet $selectionSet,
        Location $location = null
    ) {
        parent::__construct($name, $variables, $directives, $selectionSet, $location);
    }
}
