<?php

namespace HansOtt\GraphQL\Schema;

use PHPUnit\Framework\TestCase;

final class ParserTest extends TestCase
{
    /**
     * @var Parser
     */
    private $parser;

    protected function setUp()
    {
        $this->parser = new Parser(new Lexer);
    }

    public function invalidSchemas()
    {
        return array(
            array(
                '
                    input Location {
                        x(argument: Int!): Float,
                        y: Float,
                    }
                '
            ),
        );
    }

    public function validSchemasForCrashTest()
    {
        return array(
            array('union U = U | U'),
        );
    }

    /**
     * @dataProvider validSchemasForCrashTest
     *
     * @param string $schema
     */
    public function test_it_does_not_crash_for_valid_schema($schema)
    {
        $schema = $this->parser->parse($schema);
        $this->assertInstanceOf('HansOtt\\GraphQL\\Schema\\Schema', $schema);
    }

    /**
     * @dataProvider invalidSchemas
     * @expectedException \HansOtt\GraphQL\Shared\ParseError
     *
     * @param string $schema
     */
    public function test_it_throws_exception_if_invalid_schema($schema)
    {
        $this->parser->parse($schema);
    }

    public function validSchemas()
    {
        return array(
            array(
                'type Query { name: String }',
                new Schema(
                    array(
                        new DeclarationObject(
                            'Query',
                            array(
                                new Field(
                                    'name',
                                    new TypeNamed('String', new Location(1, 20)),
                                    array(),
                                    new Location(1, 14)
                                ),
                            ),
                            null,
                            new Location(1, 1)
                        ),
                    )
                )
            ),
            array(
                '
                    input Location {
                        x: Float,
                        y: Float,
                    }
                    enum UserStatus {
                        ACTIVE,
                        EXPIRED,
                    }
                    interface UserInterface {
                        status: UserStatus!
                        name(formatted: Boolean = false): String
                        email(arg1: Type, arg2: Type): String!
                        distanceTo(location: Location!): Float!
                    }
                    type HistoryViewer {
                        achievements: [String!]!
                    }
                    type HistoryAdmin {
                        logins: [Login!]!
                    }
                    union History = HistoryAdmin | HistoryViewer
                    type User implements User {
                        status: UserStatus!
                        name(formatted: Boolean = false): String
                        email(arg1: Type, arg2: Type): String!
                        distanceTo(location: Location = {x: 1.1, y: 1.1}): Float!
                        history: History!
                    }
                    type Query {
                        name: User!
                    }
                ',
                new Schema(
                    array(
                        new DeclarationInputObject(
                            'Location',
                            array(
                                new Field(
                                    'x',
                                    new TypeNamed('Float', new Location(3, 29)),
                                    array(),
                                    new Location(3, 26)
                                ),
                                new Field(
                                    'y',
                                    new TypeNamed('Float', new Location(4, 29)),
                                    array(),
                                    new Location(4, 26)
                                ),
                            ),
                            new Location(2, 22)
                        ),
                        new DeclarationEnum(
                            'UserStatus',
                            array('ACTIVE', 'EXPIRED'),
                            new Location(6, 22)
                        ),
                        new DeclarationInterface(
                            'UserInterface',
                            array(
                                new Field(
                                    'status',
                                    new TypeNonNull(
                                        new TypeNamed('UserStatus', new Location(11, 34)),
                                        new Location(11, 34)
                                    ),
                                    array(),
                                    new Location(11, 26)
                                ),
                                new Field(
                                    'name',
                                    new TypeNamed('String', new Location(12, 60)),
                                    array(
                                        new Argument(
                                            'formatted',
                                            new TypeNamed('Boolean', new Location(12, 42)),
                                            new ValueBoolean(false, new Location(12, 52)),
                                            new Location(12, 31)
                                        ),
                                    ),
                                    new Location(12, 26)
                                ),
                                new Field(
                                    'email',
                                    new TypeNonNull(
                                        new TypeNamed('String', new Location(13, 57)),
                                        new Location(13, 57)
                                    ),
                                    array(
                                        new Argument(
                                            'arg1',
                                            new TypeNamed('Type', new Location(13, 38)),
                                            null,
                                            new Location(13, 32)
                                        ),
                                        new Argument(
                                            'arg2',
                                            new TypeNamed('Type', new Location(13, 50)),
                                            null,
                                            new Location(13, 44)
                                        ),
                                    ),
                                    new Location(13, 26)
                                ),
                                new Field(
                                    'distanceTo',
                                    new TypeNonNull(
                                        new TypeNamed('Float', new Location(14, 59)),
                                        new Location(14, 59)
                                    ),
                                    array(
                                        new Argument(
                                            'location',
                                            new TypeNonNull(
                                                new TypeNamed('Location', new Location(14, 47)),
                                                new Location(14, 47)
                                            ),
                                            null,
                                            new Location(14, 37)
                                        ),
                                    ),
                                    new Location(14, 26)
                                ),
                            ),
                            new Location(10, 22)
                        ),
                        new DeclarationObject(
                            'HistoryViewer',
                            array(
                                new Field(
                                    'achievements',
                                    new TypeNonNull(
                                        new TypeList(
                                            new TypeNonNull(
                                                new TypeNamed('String', new Location(17, 41)),
                                                new Location(17, 41)
                                            ),
                                            new Location(17, 40)
                                        ),
                                        new Location(17, 40)
                                    ),
                                    array(),
                                    new Location(17, 26)
                                ),
                            ),
                            null,
                            new Location(16, 22)
                        ),
                        new DeclarationObject(
                            'HistoryAdmin',
                            array(
                                new Field(
                                    'logins',
                                    new TypeNonNull(
                                        new TypeList(
                                            new TypeNonNull(
                                                new TypeNamed('Login', new Location(20, 35)),
                                                new Location(20, 35)
                                            ),
                                            new Location(20, 34)
                                        ),
                                        new Location(20, 34)
                                    ),
                                    array(),
                                    new Location(20, 26)
                                ),
                            ),
                            null,
                            new Location(19, 22)
                        ),
                        new DeclarationUnion(
                            'History',
                            array('HistoryAdmin', 'HistoryViewer'),
                            new Location(22, 22)
                        ),
                        new DeclarationObject(
                            'User',
                            array(
                                new Field(
                                    'status',
                                    new TypeNonNull(
                                        new TypeNamed('UserStatus', new Location(24, 34)),
                                        new Location(24, 34)
                                    ),
                                    array(),
                                    new Location(24, 26)
                                ),
                                new Field(
                                    'name',
                                    new TypeNamed('String', new Location(25, 60)),
                                    array(
                                        new Argument(
                                            'formatted',
                                            new TypeNamed('Boolean', new Location(25, 42)),
                                            new ValueBoolean(false, new Location(25, 52)),
                                            new Location(25, 31)
                                        ),
                                    ),
                                    new Location(25, 26)
                                ),
                                new Field(
                                    'email',
                                    new TypeNonNull(
                                        new TypeNamed('String', new Location(26, 57)),
                                        new Location(26, 57)
                                    ),
                                    array(
                                        new Argument(
                                            'arg1',
                                            new TypeNamed('Type', new Location(26, 38)),
                                            null,
                                            new Location(26, 32)
                                        ),
                                        new Argument(
                                            'arg2',
                                            new TypeNamed('Type', new Location(26, 50)),
                                            null,
                                            new Location(26, 44)
                                        ),
                                    ),
                                    new Location(26, 26)
                                ),
                                new Field(
                                    'distanceTo',
                                    new TypeNonNull(
                                        new TypeNamed('Float', new Location(27, 77)),
                                        new Location(27, 77)
                                    ),
                                    array(
                                        new Argument(
                                            'location',
                                            new TypeNamed('Location', new Location(27, 47)),
                                            new ValueObject(
                                                array(
                                                    new ValueObjectField(
                                                        'x',
                                                        new ValueFloat(1.1, new Location(27, 62)),
                                                        new Location(27, 59)
                                                    ),
                                                    new ValueObjectField(
                                                        'y',
                                                        new ValueFloat(1.1, new Location(27, 70)),
                                                        new Location(27, 67)
                                                    ),
                                                ),
                                                new Location(27, 58)
                                            ),
                                            new Location(27, 37)
                                        ),
                                    ),
                                    new Location(27, 26)
                                ),
                                new Field(
                                    'history',
                                    new TypeNonNull(
                                        new TypeNamed('History', new Location(28, 35)),
                                        new Location(28, 35)
                                    ),
                                    array(),
                                    new Location(28, 26)
                                )
                            ),
                            'User',
                            new Location(23, 22)
                        ),
                        new DeclarationObject(
                            'Query',
                            array(
                                new Field(
                                    'name',
                                    new TypeNonNull(
                                        new TypeNamed('User', new Location(31, 32)),
                                        new Location(31, 32)
                                    ),
                                    array(),
                                    new Location(31, 26)
                                ),
                            ),
                            null,
                            new Location(30, 22)
                        ),
                    )
                )
            ),
            array(
                '
                    type Query {
                        name(
                            enum: Enum = ACTIVE,
                            int: Int = 1,
                            string: String = "String",
                            list: [String] = ["\\" \\n \\t \\r \\ud83d\\ude00"]
                        ): String
                    }
                ',
                new Schema(
                    array(
                        new DeclarationObject(
                            'Query',
                            array(
                                new Field(
                                    'name',
                                    new TypeNamed('String', new Location(8, 29)),
                                    array(
                                        new Argument(
                                            'enum',
                                            new TypeNamed('Enum', new Location(4, 36)),
                                            new ValueEnum('ACTIVE', new Location(4, 43)),
                                            new Location(4, 30)
                                        ),
                                        new Argument(
                                            'int',
                                            new TypeNamed('Int', new Location(5, 35)),
                                            new ValueInt(1, new Location(5, 41)),
                                            new Location(5, 30)
                                        ),
                                        new Argument(
                                            'string',
                                            new TypeNamed('String', new Location(6, 38)),
                                            new ValueString('String', new Location(6, 47)),
                                            new Location(6, 30)
                                        ),
                                        new Argument(
                                            'list',
                                            new TypeList(
                                                new TypeNamed('String', new Location(7, 37)),
                                                new Location(7, 36)
                                            ),
                                            new ValueList(
                                                array(
                                                    new ValueString("\" \n \t \r 😀", new Location(7, 48)),
                                                ),
                                                new Location(7, 47)
                                            ),
                                            new Location(7, 30)
                                        ),
                                    ),
                                    new Location(3, 26)
                                ),
                            ),
                            null,
                            new Location(2, 22)
                        )
                    )
                )
            ),
        );
    }

    /**
     * @dataProvider validSchemas
     *
     * @param string $schema
     * @param Schema $expectedDeclaration
     */
    public function test_it_parses_a_valid_schema($schema, Schema $expectedDeclaration)
    {
        $declaration = $this->parser->parse($schema);
        $this->assertEquals($expectedDeclaration, $declaration);
    }
}
