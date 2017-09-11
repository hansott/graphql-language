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
                                    new SelectionField(null, 'author'),
                                )
                            )
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
                                                new SelectionField(null, 'name'),
                                            )
                                        )
                                    ),
                                )
                            )
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
                                                new SelectionField(null, 'name'),
                                            )
                                        )
                                    ),
                                )
                            )
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
                                            new Argument('id', new ValueInt(1)),
                                        )
                                    ),
                                )
                            )
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
                                            new Argument('id', new ValueInt(1)),
                                            new Argument('name', new ValueString("Hans Ott")),
                                        )
                                    ),
                                )
                            )
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
                                            new Argument('id', new ValueInt(1)),
                                            new Argument('name', new ValueString("Hans Ott")),
                                        )
                                    ),
                                )
                            )
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
                                            new Argument('id', new ValueInt(1)),
                                            new Argument('name', new ValueString("Hans Ott")),
                                        )
                                    ),
                                )
                            )
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
                                                new SelectionFragmentSpread('authorFragment'),
                                            )
                                        )
                                    ),
                                )
                            )
                        ),
                        new Fragment(
                            'authorFragment',
                            new TypeCondition(
                                new TypeNamed('Author')
                            ),
                            array(),
                            new SelectionSet(
                                array(
                                    new SelectionField(null, 'name'),
                                )
                            )
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
                                                new SelectionField(null, 'name'),
                                                new SelectionInlineFragment(
                                                    new TypeCondition(
                                                        new TypeNamed('Page')
                                                    ),
                                                    array(),
                                                    new SelectionSet(
                                                        array(
                                                            new SelectionField(null, 'likes'),
                                                        )
                                                    )
                                                ),
                                                new SelectionInlineFragment(
                                                    new TypeCondition(
                                                        new TypeNamed('User')
                                                    ),
                                                    array(),
                                                    new SelectionSet(
                                                        array(
                                                            new SelectionField(null, 'email'),
                                                        )
                                                    )
                                                ),
                                            )
                                        )
                                    ),
                                )
                            )
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
                                            new Argument('name', new ValueString("\" \n \t \r 😀")),
                                        )
                                    ),
                                )
                            )
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
                                                        new ValueInt(1),
                                                        new ValueVariable('name'),
                                                        new ValueEnum('PUBLISHED'),
                                                        new ValueString('string'),
                                                        new ValueFloat(1.0),
                                                        new ValueNull,
                                                        new ValueBoolean(true),
                                                        new ValueBoolean(false),
                                                        new ValueList(
                                                            array(
                                                                new ValueString('nested list'),
                                                            )
                                                        ),
                                                        new ValueObject(
                                                            array(
                                                                'nestedObject' => new ValueObject(
                                                                    array(
                                                                        'nestedObject' => new ValueObject(array()),
                                                                    )
                                                                )
                                                            )
                                                        )
                                                    )
                                                )
                                            ),
                                        )
                                    ),
                                )
                            )
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
                                    new ValueVariable('devicePicSize'),
                                    new TypeNamed('Int'),
                                    new ValueString('Default')
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
                                                new ValueInt(4)
                                            )
                                        ),
                                        array(),
                                        new SelectionSet(
                                            array(
                                                new SelectionField(null, 'id'),
                                                new SelectionField(null, 'name'),
                                                new SelectionField(
                                                    null,
                                                    'profilePic',
                                                    array(
                                                        new Argument(
                                                            'size',
                                                            new ValueVariable('devicePicSize')
                                                        )
                                                    )
                                                ),
                                            )
                                        )
                                    )
                                )
                            )
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
                                    new ValueVariable('someTest'),
                                    new TypeNamed('Boolean')
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
                                                    new Argument('if', new ValueVariable('someTest')),
                                                )
                                            )
                                        )
                                    )
                                )
                            )
                        )
                    )
                )
            ),
        );
    }
}
