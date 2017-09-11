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
            array('{ name } { name }'),
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
                        new DefinitionOperationQuery(
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
                        new DefinitionOperationQuery(
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
                        new DefinitionOperationQuery(
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
                        new DefinitionOperationQuery(
                            null,
                            array(),
                            array(),
                            new SelectionSet(
                                array(
                                    new SelectionField(
                                        null,
                                        'author',
                                        array(
                                            new SelectionFieldArgument('id', new ValueInt(1)),
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
                        new DefinitionOperationQuery(
                            null,
                            array(),
                            array(),
                            new SelectionSet(
                                array(
                                    new SelectionField(
                                        null,
                                        'author',
                                        array(
                                            new SelectionFieldArgument('id', new ValueInt(1)),
                                            new SelectionFieldArgument('name', new ValueString("Hans Ott")),
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
                        new DefinitionOperationQuery(
                            null,
                            array(),
                            array(),
                            new SelectionSet(
                                array(
                                    new SelectionField(
                                        null,
                                        'author',
                                        array(
                                            new SelectionFieldArgument('id', new ValueInt(1)),
                                            new SelectionFieldArgument('name', new ValueString("Hans Ott")),
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
                        new DefinitionOperationQuery(
                            'name',
                            array(),
                            array(),
                            new SelectionSet(
                                array(
                                    new SelectionField(
                                        null,
                                        'author',
                                        array(
                                            new SelectionFieldArgument('id', new ValueInt(1)),
                                            new SelectionFieldArgument('name', new ValueString("Hans Ott")),
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
                        new DefinitionOperationQuery(
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
                        new DefinitionFragment(
                            'authorFragment',
                            new TypeCondition('Author'),
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
                        new DefinitionOperationQuery(
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
                                                    new TypeCondition('Page'),
                                                    array(),
                                                    new SelectionSet(
                                                        array(
                                                            new SelectionField(null, 'likes'),
                                                        )
                                                    )
                                                ),
                                                new SelectionInlineFragment(
                                                    new TypeCondition('User'),
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
                        new DefinitionOperationQuery(
                            null,
                            array(),
                            array(),
                            new SelectionSet(
                                array(
                                    new SelectionField(
                                        null,
                                        'author',
                                        array(
                                            new SelectionFieldArgument('name', new ValueString("\" \n \t \r 😀")),
                                        )
                                    ),
                                )
                            )
                        ),
                    )
                )
            ),
            array(
                '{ author(id: [1, PUBLISHED, "string", 1.0, null, true, false, ["nested list"], { nestedObject: { nestedObject: {} } }]) }',
                new Document(
                    array(
                        new DefinitionOperationQuery(
                            null,
                            array(),
                            array(),
                            new SelectionSet(
                                array(
                                    new SelectionField(
                                        null,
                                        'author',
                                        array(
                                            new SelectionFieldArgument(
                                                'id',
                                                new ValueList(
                                                    array(
                                                        new ValueInt(1),
                                                        new ValueEnum('PUBLISHED'),
                                                        new ValueString('string'),
                                                        new ValueFloat(1.0),
                                                        new ValueNull,
                                                        new ValueBool(true),
                                                        new ValueBool(false),
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
        );
    }
}
