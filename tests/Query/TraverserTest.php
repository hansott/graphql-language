<?php

namespace HansOtt\GraphQL\Query;

use PHPUnit\Framework\TestCase;

final class TraverserTest extends TestCase
{
    public function test_it_traverses_a_query_document()
    {
        $episodeVariable = new ValueVariable('episode');
        $episodeType = new TypeNamed('Episode');
        $variableDefinitionEpisode = new VariableDefinition(
            $episodeVariable,
            $episodeType
        );

        $withFriendsVariable = new ValueVariable('withFriends');
        $withFriendsBooleanType = new TypeNamed('Boolean');
        $withFriendsBooleanNotNullType = new TypeNonNull(
            $withFriendsBooleanType
        );

        $variableDefinitionWithFriends = new VariableDefinition(
            $withFriendsVariable,
            $withFriendsBooleanNotNullType
        );

        $heroArgumentEpisode = new Argument('episode', $episodeVariable);
        $nameSelectionField = new SelectionField(null, 'name');
        $directiveArgument = new Argument('if', $withFriendsVariable);
        $includeDirective = new Directive(
            'include',
            array($directiveArgument)
        );

        $friendsChildSelectionField = new SelectionSet(
            array($nameSelectionField)
        );

        $friendsSelectionField = new SelectionField(
            null,
            'friends',
            array(),
            array($includeDirective),
            $friendsChildSelectionField
        );

        $heroChildSelectionSet = new SelectionSet(array($nameSelectionField, $friendsSelectionField));

        $heroSelectionField = new SelectionField(
            null,
            'hero',
            array($heroArgumentEpisode),
            array(),
            $heroChildSelectionSet
        );

        $topLevelSelectionSet = new SelectionSet(array($heroSelectionField));

        $query = new OperationQuery(
            'Hero',
            array($variableDefinitionEpisode, $variableDefinitionWithFriends),
            array(),
            $topLevelSelectionSet
        );

        $document = new Document(array($query));

        $visitor = $this->getMockBuilder('HansOtt\\GraphQL\\Query\\Visitor')->getMock();

        $visitor
            ->expects($this->once())
            ->method('beforeTraverse')
            ->with($this->equalTo($document));

        $visitor
            ->expects($this->once())
            ->method('afterTraverse')
            ->with($this->equalTo($document));

        $visitor
            ->expects($this->atLeastOnce())
            ->method('enterNode')
            ->withConsecutive(
                array($this->equalTo($document)),
                array($this->equalTo($query)),
                array($this->equalTo($variableDefinitionEpisode)),
                array($this->equalTo($episodeVariable)),
                array($this->equalTo($episodeType)),
                array($this->equalTo($variableDefinitionWithFriends)),
                array($this->equalTo($withFriendsVariable)),
                array($this->equalTo($withFriendsBooleanNotNullType)),
                array($this->equalTo($withFriendsBooleanType)),
                array($this->equalTo($topLevelSelectionSet)),
                array($this->equalTo($heroSelectionField)),
                array($this->equalTo($heroArgumentEpisode)),
                array($this->equalTo($episodeVariable)),
                array($this->equalTo($heroChildSelectionSet)),
                array($this->equalTo($nameSelectionField)),
                array($this->equalTo($friendsSelectionField)),
                array($this->equalTo($includeDirective)),
                array($this->equalTo($directiveArgument)),
                array($this->equalTo($withFriendsVariable)),
                array($this->equalTo($friendsChildSelectionField)),
                array($this->equalTo($nameSelectionField))
            );

        $visitor
            ->expects($this->atLeastOnce())
            ->method('leaveNode')
            ->withConsecutive(
                array($this->equalTo($episodeVariable)),
                array($this->equalTo($episodeType)),
                array($this->equalTo($variableDefinitionEpisode)),
                array($this->equalTo($withFriendsVariable)),
                array($this->equalTo($withFriendsBooleanType)),
                array($this->equalTo($withFriendsBooleanNotNullType)),
                array($this->equalTo($variableDefinitionWithFriends)),
                array($this->equalTo($episodeVariable)),
                array($this->equalTo($heroArgumentEpisode)),
                array($this->equalTo($nameSelectionField)),
                array($this->equalTo($withFriendsVariable)),
                array($this->equalTo($directiveArgument)),
                array($this->equalTo($includeDirective)),
                array($this->equalTo($nameSelectionField)),
                array($this->equalTo($friendsChildSelectionField)),
                array($this->equalTo($friendsSelectionField)),
                array($this->equalTo($heroChildSelectionSet)),
                array($this->equalTo($heroSelectionField)),
                array($this->equalTo($topLevelSelectionSet)),
                array($this->equalTo($query)),
                array($this->equalTo($document))
            );

        $countingVisitor = new VisitorCounting;
        $visitors = new VisitorMany(
            array(
                $countingVisitor,
                $visitor,
            )
        );

        $traverser = new Traverser($visitors);
        $traverser->traverse($document);

        $this->assertEquals(
            21,
            $countingVisitor->getCount()
        );
    }
}
