<?php

namespace HansOtt\GraphQL\Query;

use PHPUnit\Framework\TestCase;

final class ParserTest extends TestCase
{
    /**
     * @var Parser
     */
    private $parser;

    protected function setUp()
    {
        $lexer = new Lexer;
        $this->parser = new Parser($lexer);
    }

    public function queriesWithSyntaxError()
    {
        return array(
            array('-'),
            array('0.'),
            array('-0.'),
            array('0e'),
            array('0.0e'),
            array('-0.0e'),
            array('0.0ea'),
            array('-0.0ea'),
            array('"'),
        );
    }

    /**
     * @param string $query
     *
     * @dataProvider queriesWithSyntaxError
     *
     * @expectedException \HansOtt\GraphQL\Query\SyntaxError
     */
    public function test_it_throws_an_exception_if_syntax_error($query)
    {
        $this->parser->parse($query);
    }

    public function queriesWithParseError()
    {
        return array(
            array('{'),
            array('{ author'),
            array('{ author(1)'),
            array('{ author(1: "Hans Ott")'),
            array('{ author {'),
            array('{ fragment on { name } }'),
            array('{ author(id: [) }'),
        );
    }

    /**
     * @param string $query
     *
     * @dataProvider queriesWithParseError
     *
     * @expectedException \HansOtt\GraphQL\Query\ParseError
     */
    public function test_it_throws_an_exception_if_parse_error($query)
    {
        $this->parser->parse($query);
    }

    /**
     * @param string $query
     * @param Document $expectedAst
     *
     * @dataProvider validQueries
     */
    public function test_it_parses_a_valid_query($query, Document $expectedAst)
    {
        $actualAst = $this->parser->parse($query);
        $this->assertEquals($expectedAst, $actualAst);
    }

    public function validQueries()
    {
        return array(
            array(
                '',
                new Document
            ),
            array(
                '{ author }',
                new Document(
                    array(
                        new OperationQuery(
                            null,
                            array(),
                            array(),
                            new SelectionSet(
                                array(
                                    new SelectionField(
                                        null,
                                        'author',
                                        array(),
                                        array(),
                                        null,
                                        new Location(1, 3)
                                    ),
                                ),
                                new Location(1, 1)
                            ),
                            new Location(1, 1)
                        ),
                    )
                )
            ),
            array(
                '{ author { name } }',
                new Document(
                    array(
                        new OperationQuery(
                            null,
                            array(),
                            array(),
                            new SelectionSet(
                                array(
                                    new SelectionField(
                                        null,
                                        'author',
                                        array(),
                                        array(),
                                        new SelectionSet(
                                            array(
                                                new SelectionField(
                                                    null,
                                                    'name',
                                                    array(),
                                                    array(),
                                                    null,
                                                    new Location(1, 12)
                                                ),
                                            ),
                                            new Location(1, 10)
                                        ),
                                        new Location(1, 3)
                                    ),
                                ),
                                new Location(1, 1)
                            ),
                            new Location(1, 1)
                        ),
                    )
                )
            ),
            array(
                '{ author() { name() } }',
                new Document(
                    array(
                        new OperationQuery(
                            null,
                            array(),
                            array(),
                            new SelectionSet(
                                array(
                                    new SelectionField(
                                        null,
                                        'author',
                                        array(),
                                        array(),
                                        new SelectionSet(
                                            array(
                                                new SelectionField(
                                                    null,
                                                    'name',
                                                    array(),
                                                    array(),
                                                    null,
                                                    new Location(1, 14)
                                                ),
                                            ),
                                            new Location(1, 12)
                                        ),
                                        new Location(1, 3)
                                    ),
                                ),
                                new Location(1, 1)
                            ),
                            new Location(1, 1)
                        ),
                    )
                )
            ),
            array(
                '{ author(id: 1) }',
                new Document(
                    array(
                        new OperationQuery(
                            null,
                            array(),
                            array(),
                            new SelectionSet(
                                array(
                                    new SelectionField(
                                        null,
                                        'author',
                                        array(
                                            new Argument(
                                                'id',
                                                new ValueInt(1, new Location(1, 14)),
                                                new Location(1, 10)
                                            ),
                                        ),
                                        array(),
                                        null,
                                        new Location(1, 3)
                                    ),
                                ),
                                new Location(1, 1)
                            ),
                            new Location(1, 1)
                        ),
                    )
                )
            ),
            array(
                '{ author(id: 1, name: "Hans Ott") }',
                new Document(
                    array(
                        new OperationQuery(
                            null,
                            array(),
                            array(),
                            new SelectionSet(
                                array(
                                    new SelectionField(
                                        null,
                                        'author',
                                        array(
                                            new Argument(
                                                'id',
                                                new ValueInt(1, new Location(1, 14)),
                                                new Location(1, 10)
                                            ),
                                            new Argument(
                                                'name',
                                                new ValueString("Hans Ott", new Location(1, 23)),
                                                new Location(1, 17)
                                            ),
                                        ),
                                        array(),
                                        null,
                                        new Location(1, 3)
                                    ),
                                ),
                                new Location(1, 1)
                            ),
                            new Location(1, 1)
                        ),
                    )
                )
            ),
            array(
                'query { author(id: 1, name: "Hans Ott") }',
                new Document(
                    array(
                        new OperationQuery(
                            null,
                            array(),
                            array(),
                            new SelectionSet(
                                array(
                                    new SelectionField(
                                        null,
                                        'author',
                                        array(
                                            new Argument(
                                                'id',
                                                new ValueInt(1, new Location(1, 20)),
                                                new Location(1, 16)
                                            ),
                                            new Argument(
                                                'name',
                                                new ValueString("Hans Ott", new Location(1, 29)),
                                                new Location(1, 23)
                                            ),
                                        ),
                                        array(),
                                        null,
                                        new Location(1, 9)
                                    ),
                                ),
                                new Location(1, 7)
                            ),
                            new Location(1, 1)
                        ),
                    )
                )
            ),
            array(
                'query name { author(id: 1, name: "Hans Ott") }',
                new Document(
                    array(
                        new OperationQuery(
                            'name',
                            array(),
                            array(),
                            new SelectionSet(
                                array(
                                    new SelectionField(
                                        null,
                                        'author',
                                        array(
                                            new Argument(
                                                'id',
                                                new ValueInt(1, new Location(1, 25)),
                                                new Location(1, 21)
                                            ),
                                            new Argument(
                                                'name',
                                                new ValueString("Hans Ott", new Location(1, 34)),
                                                new Location(1, 28)
                                            ),
                                        ),
                                        array(),
                                        null,
                                        new Location(1, 14)
                                    ),
                                ),
                                new Location(1, 12)
                            ),
                            new Location(1, 1)
                        ),
                    )
                )
            ),
            array(
                '{ author { ...authorFragment } }'
                . ' fragment authorFragment on Author { name }',
                new Document(
                    array(
                        new OperationQuery(
                            null,
                            array(),
                            array(),
                            new SelectionSet(
                                array(
                                    new SelectionField(
                                        null,
                                        'author',
                                        array(),
                                        array(),
                                        new SelectionSet(
                                            array(
                                                new SelectionFragmentSpread(
                                                    'authorFragment',
                                                    new Location(1, 12)
                                                ),
                                            ),
                                            new Location(1, 10)
                                        ),
                                        new Location(1, 3)
                                    ),
                                ),
                                new Location(1, 1)
                            ),
                            new Location(1, 1)
                        ),
                        new Fragment(
                            'authorFragment',
                            new TypeCondition(
                                new TypeNamed('Author', new Location(1, 61)),
                                new Location(1, 58)
                            ),
                            array(),
                            new SelectionSet(
                                array(
                                    new SelectionField(
                                        null,
                                        'name',
                                        array(),
                                        array(),
                                        null,
                                        new Location(1, 70)
                                    ),
                                ),
                                new Location(1, 68)
                            ),
                            new Location(1, 34)
                        )
                    )
                )
            ),
            array(
                '{ handle { name ... on Page { likes } ... on User { email } } }',
                new Document(
                    array(
                        new OperationQuery(
                            null,
                            array(),
                            array(),
                            new SelectionSet(
                                array(
                                    new SelectionField(
                                        null,
                                        'handle',
                                        array(),
                                        array(),
                                        new SelectionSet(
                                            array(
                                                new SelectionField(
                                                    null,
                                                    'name',
                                                    array(),
                                                    array(),
                                                    null,
                                                    new Location(1, 12)
                                                ),
                                                new SelectionInlineFragment(
                                                    new TypeCondition(
                                                        new TypeNamed('Page', new Location(1, 24)),
                                                        new Location(1, 21)
                                                    ),
                                                    array(),
                                                    new SelectionSet(
                                                        array(
                                                            new SelectionField(
                                                                null,
                                                                'likes',
                                                                array(),
                                                                array(),
                                                                null,
                                                                new Location(1, 31)
                                                            ),
                                                        ),
                                                        new Location(1, 29)
                                                    ),
                                                    new Location(1, 17)
                                                ),
                                                new SelectionInlineFragment(
                                                    new TypeCondition(
                                                        new TypeNamed('User', new Location(1, 46)),
                                                        new Location(1, 43)
                                                    ),
                                                    array(),
                                                    new SelectionSet(
                                                        array(
                                                            new SelectionField(
                                                                null,
                                                                'email',
                                                                array(),
                                                                array(),
                                                                null,
                                                                new Location(1, 53)
                                                            ),
                                                        ),
                                                        new Location(1, 51)
                                                    ),
                                                    new Location(1, 39)
                                                ),
                                            ),
                                            new Location(1, 10)
                                        ),
                                        new Location(1, 3)
                                    ),
                                ),
                                new Location(1, 1)
                            ),
                            new Location(1, 1)
                        ),
                    )
                )
            ),
            array(
                '{ author(name: "\\" \\n \\t \\r \\ud83d\\ude00") }',
                new Document(
                    array(
                        new OperationQuery(
                            null,
                            array(),
                            array(),
                            new SelectionSet(
                                array(
                                    new SelectionField(
                                        null,
                                        'author',
                                        array(
                                            new Argument(
                                                'name',
                                                new ValueString("\" \n \t \r 😀", new Location(1, 16)),
                                                new Location(1, 10)
                                            ),
                                        ),
                                        array(),
                                        null,
                                        new Location(1, 3)
                                    ),
                                ),
                                new Location(1, 1)
                            ),
                            new Location(1, 1)
                        ),
                    )
                )
            ),
            array(
                '{ author(id: [1, $name, PUBLISHED, "string", 1.0, null, true, false, ["nested list"], { nestedObject: { nestedObject: {} } }]) }',
                new Document(
                    array(
                        new OperationQuery(
                            null,
                            array(),
                            array(),
                            new SelectionSet(
                                array(
                                    new SelectionField(
                                        null,
                                        'author',
                                        array(
                                            new Argument(
                                                'id',
                                                new ValueList(
                                                    array(
                                                        new ValueInt(1, new Location(1, 15)),
                                                        new ValueVariable('name', new Location(1, 18)),
                                                        new ValueEnum('PUBLISHED', new Location(1, 25)),
                                                        new ValueString('string', new Location(1, 36)),
                                                        new ValueFloat(1.0, new Location(1, 46)),
                                                        new ValueNull(new Location(1, 51)),
                                                        new ValueBoolean(true, new Location(1, 57)),
                                                        new ValueBoolean(false, new Location(1, 63)),
                                                        new ValueList(
                                                            array(
                                                                new ValueString(
                                                                    'nested list',
                                                                    new Location(1, 71)
                                                                ),
                                                            ),
                                                            new Location(1, 70)
                                                        ),
                                                        new ValueObject(
                                                            array(
                                                                new ValueObjectField(
                                                                    'nestedObject',
                                                                    new ValueObject(
                                                                        array(
                                                                            new ValueObjectField(
                                                                                'nestedObject',
                                                                                new ValueObject(
                                                                                    array(),
                                                                                    new Location(1, 119)
                                                                                ),
                                                                                new Location(1, 105)
                                                                            )
                                                                        ),
                                                                        new Location(1, 103)
                                                                    ),
                                                                    new Location(1, 89)
                                                                ),
                                                            ),
                                                            new Location(1, 87)
                                                        )
                                                    ),
                                                    new Location(1, 14)
                                                ),
                                                new Location(1, 10)
                                            ),
                                        ),
                                        array(),
                                        null,
                                        new Location(1, 3)
                                    ),
                                ),
                                new Location(1, 1)
                            ),
                            new Location(1, 1)
                        ),
                    )
                )
            ),
            array(
                'query getZuckProfile($devicePicSize: Int = "Default") { user(id: 4) { id name profilePic(size: $devicePicSize) } }',
                new Document(
                    array(
                        new OperationQuery(
                            'getZuckProfile',
                            array(
                                new VariableDefinition(
                                    new ValueVariable('devicePicSize', new Location(1, 22)),
                                    new TypeNamed('Int', new Location(1, 38)),
                                    new ValueString('Default', new Location(1, 44)),
                                    new Location(1, 22)
                                ),
                            ),
                            array(),
                            new SelectionSet(
                                array(
                                    new SelectionField(
                                        null,
                                        'user',
                                        array(
                                            new Argument(
                                                'id',
                                                new ValueInt(4, new Location(1, 66)),
                                                new Location(1, 62)
                                            )
                                        ),
                                        array(),
                                        new SelectionSet(
                                            array(
                                                new SelectionField(
                                                    null,
                                                    'id',
                                                    array(),
                                                    array(),
                                                    null,
                                                    new Location(1, 71)
                                                ),
                                                new SelectionField(
                                                    null,
                                                    'name',
                                                    array(),
                                                    array(),
                                                    null,
                                                    new Location(1, 74)
                                                ),
                                                new SelectionField(
                                                    null,
                                                    'profilePic',
                                                    array(
                                                        new Argument(
                                                            'size',
                                                            new ValueVariable(
                                                                'devicePicSize',
                                                                new Location(1, 96)
                                                            ),
                                                            new Location(1, 90)
                                                        )
                                                    ),
                                                    array(),
                                                    null,
                                                    new Location(1, 79)
                                                ),
                                            ),
                                            new Location(1, 69)
                                        ),
                                        new Location(1, 57)
                                    )
                                ),
                                new Location(1, 55)
                            ),
                            new Location(1, 1)
                        ),
                    )
                )
            ),
            array(
                'query myQuery($someTest: Boolean) { alias: experimentalField @skip(if: $someTest) }',
                new Document(
                    array(
                        new OperationQuery(
                            'myQuery',
                            array(
                                new VariableDefinition(
                                    new ValueVariable('someTest', new Location(1, 15)),
                                    new TypeNamed('Boolean', new Location(1, 26)),
                                    null,
                                    new Location(1, 15)
                                ),
                            ),
                            array(),
                            new SelectionSet(
                                array(
                                    new SelectionField(
                                        'alias',
                                        'experimentalField',
                                        array(),
                                        array(
                                            new Directive(
                                                'skip',
                                                array(
                                                    new Argument(
                                                        'if',
                                                        new ValueVariable('someTest', new Location(1, 72)),
                                                        new Location(1, 68)
                                                    ),
                                                ),
                                                new Location(1, 62)
                                            ),
                                        ),
                                        null,
                                        new Location(1, 37)
                                    )
                                ),
                                new Location(1, 35)
                            ),
                            new Location(1, 1)
                        )
                    )
                )
            ),
            array(
                'query Hero($episode: Episode, $withFriends: Boolean!) { hero(episode: $episode) { name friends @include(if: $withFriends) { name } } }',
                new Document(
                    array(
                        new OperationQuery(
                            'Hero',
                            array(
                                new VariableDefinition(
                                    new ValueVariable('episode', new Location(1, 12)),
                                    new TypeNamed('Episode', new Location(1, 22)),
                                    null,
                                    new Location(1, 12)
                                ),
                                new VariableDefinition(
                                    new ValueVariable('withFriends', new Location(1, 31)),
                                    new TypeNonNull(
                                        new TypeNamed('Boolean', new Location(1, 45)),
                                        new Location(1, 45)
                                    ),
                                    null,
                                    new Location(1, 31)
                                ),
                            ),
                            array(),
                            new SelectionSet(
                                array(
                                    new SelectionField(
                                        null,
                                        'hero',
                                        array(
                                            new Argument(
                                                'episode',
                                                new ValueVariable('episode', new Location(1, 71)),
                                                new Location(1, 62)
                                            ),
                                        ),
                                        array(),
                                        new SelectionSet(
                                            array(
                                                new SelectionField(
                                                    null,
                                                    'name',
                                                    array(),
                                                    array(),
                                                    null,
                                                    new Location(1, 83)
                                                ),
                                                new SelectionField(
                                                    null,
                                                    'friends',
                                                    array(),
                                                    array(
                                                        new Directive(
                                                            'include',
                                                            array(
                                                                new Argument(
                                                                    'if',
                                                                    new ValueVariable('withFriends', new Location(1, 109)),
                                                                    new Location(1, 105)
                                                                ),
                                                            ),
                                                            new Location(1, 96)
                                                        ),
                                                    ),
                                                    new SelectionSet(
                                                        array(
                                                            new SelectionField(
                                                                null,
                                                                'name',
                                                                array(),
                                                                array(),
                                                                null,
                                                                new Location(1, 125)
                                                            ),
                                                        ),
                                                        new Location(1, 123)
                                                    ),
                                                    new Location(1, 88)
                                                )
                                            ),
                                            new Location(1, 81)
                                        ),
                                        new Location(1, 57)
                                    )
                                ),
                                new Location(1, 55)
                            ),
                            new Location(1, 1)
                        )
                    )
                )
            )
        );
    }
}
