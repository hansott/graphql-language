<?php

namespace HansOtt\GraphQL\Query;

final class DefinitionOperationSubscription extends DefinitionOperationBase implements DefinitionOperation
{
    public function __construct($name, array $variables = array(), $directives = array(), SelectionSet $selectionSet)
    {
        parent::__construct($name, $variables, $directives, $selectionSet);
    }
}
