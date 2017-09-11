<?php

namespace HansOtt\GraphQL\Query;

use PHPUnit\Framework\TestCase;

final class ParserFactoryTest extends TestCase
{
    public function test_it_returns_a_parser()
    {
        $factory = new ParserFactory;
        $parser = $factory->create();
        $this->assertInstanceOf('HansOtt\\GraphQL\\Query\\Parser', $parser);
    }
}
