<?php

namespace HansOtt\GraphQL\Schema;

use PHPUnit\Framework\TestCase;

final class ParserFactoryTest extends TestCase
{
    public function test_it_returns_a_parser()
    {
        $factory = new ParserFactory;
        $parser = $factory->create();
        $this->assertInstanceOf('HansOtt\\GraphQL\\Schema\\Parser', $parser);
    }
}
