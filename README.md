# GraphQL Parser

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

This package can be used to parse a GraphQL query into an abstract syntax tree. The parser is able to parse the full [spec](https://facebook.github.io/graphql/).

## Install

Via Composer

``` bash
$ composer require hansott/graphql-parser
```

## Usage

``` php
use HansOtt\GraphQL\Query\ParseError;
use HansOtt\GraphQL\Query\SyntaxError;
use HansOtt\GraphQL\Query\ParserFactory;

$factory = new ParserFactory;
$parser = $factory->create();

$query = <<<'QUERY'
    {
        author(id: 1) {
            name
        }
    }
QUERY;

try {
    $document = $parser->parse($query);
    var_dump($document); // Instance of HansOtt\GraphQL\Query\Document
} catch (SyntaxError $e) {
    echo "Syntax error in query: {$e->getMessage()}" . PHP_EOL;
} catch (ParseError $e) {
    echo "Failed to parse query: {$e->getMessage()}" . PHP_EOL;
}
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email hansott@hotmail.be instead of using the issue tracker.

## Credits

- [Hans Ott][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/hansott/graphql-parser.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/hansott/graphql-parser/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/hansott/graphql-parser.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/hansott/graphql-parser.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/hansott/graphql-parser.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/hansott/graphql-parser
[link-travis]: https://travis-ci.org/hansott/graphql-parser
[link-scrutinizer]: https://scrutinizer-ci.com/g/hansott/graphql-parser/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/hansott/graphql-parser
[link-downloads]: https://packagist.org/packages/hansott/graphql-parser
[link-author]: https://github.com/hansott
[link-contributors]: ../../contributors
