<?php

namespace HansOtt\GraphQL;

use PHPUnit\Framework\TestCase;

final class ParserTest extends TestCase
{
    public function getParseTests()
    {
        $files = glob(__DIR__.'/parsing/*');
        $cases = array();
        foreach ($files as $file) {
            $contents = file_get_contents($file);
            $parts = explode('----', $contents);
            if (count($parts) !== 2) {
                $this->fail("{$file} is not a valid test case");
            }
            list ($graphql, $expectedOutput) = $parts;
            $pathInfo = pathinfo($file);
            $cases[] = array($graphql, trim($expectedOutput), $pathInfo['filename']);
        }

        return $cases;
    }

    /**
     * @dataProvider getParseTests
     *
     * @param string $graphql
     * @param string $expectedOutput
     * @param string $message
     */
    public function test_it_parses_graphql($graphql, $expectedOutput, $message)
    {
        $graphql = escapeshellarg($graphql);
        $output = shell_exec(__DIR__."/../bin/graphql-parser {$graphql}");
        $this->assertEquals($expectedOutput, trim($output), $message);
    }
}
