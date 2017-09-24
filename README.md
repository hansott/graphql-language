# GraphQL Language

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

A GraphQL parser written in PHP. The parser is able to parse the full [spec](https://facebook.github.io/graphql/). This package is compatible with PHP 5.3+. I'm still actively working on this project. Expect things to break. 🙈

## Install

Via Composer

``` bash
$ composer require hansott/graphql-language
```

## Usage

### Parsing a query to a [HansOtt\GraphQL\Query\Document](src/Query/Document.php)

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

### Traversing a [HansOtt\GraphQL\Query\Document](src/Query/Document.php)

```php
use HansOtt\GraphQL\Query\Node;
use HansOtt\GraphQL\Query\Traverser;
use HansOtt\GraphQL\Query\VisitorBase;
use HansOtt\GraphQL\Query\OperationQuery;

final class VisitorQueryFinder extends VisitorBase // Or implement HansOtt\GraphQL\Query\Visitor
{
    /**
     * @var OperationQuery[]
     */
    private $queries = [];

    public function enterNode(Node $node)
    {
        if ($node instanceof OperationQuery) {
            $this->queries[] = $node;
        }
    }

    public function getQueries()
    {
        return $this->queries;
    }
}

$document = $parser->parse($query);
$finder = new VisitorQueryFinder;

$traverser = new Traverser($finder);
$traverser->traverse($document);
var_dump($finder->getQueries());

// Or if you need multiple visitors
// use HansOtt\GraphQL\Query\VisitorMany

$visitors = new VisitorMany([$finder, ...]);
$traverser = new Traverser($visitors);
$traverser->traverse($document);
var_dump($finder->getQueries());
```

### Parsing a schema declaration to a [HansOtt\GraphQL\Schema\Schema](src/Schema/Schema.php)

```php
use HansOtt\GraphQL\Schema\ParseError;
use HansOtt\GraphQL\Schema\SyntaxError;
use HansOtt\GraphQL\Schema\ParserFactory;

$factory = new ParserFactory;
$parser = $factory->create();

$schema = <<<'SCHEMA'
    enum DogCommand { SIT, DOWN, HEEL }
    
    type Dog implements Pet {
        name: String!
        nickname: String
        barkVolume: Int
        doesKnowCommand(dogCommand: DogCommand!): Boolean!
        isHousetrained(atOtherHomes: Boolean): Boolean!
        owner: Human
    }
    
    interface Sentient {
        name: String!
    }
    
    interface Pet {
        name: String!
    }
    
    type Alien implements Sentient {
        name: String!
        homePlanet: String
    }
    
    type Human implements Sentient {
        name: String!
    }
    
    enum CatCommand { JUMP }
    
    type Cat implements Pet {
        name: String!
        nickname: String
        doesKnowCommand(catCommand: CatCommand!): Boolean!
        meowVolume: Int
    }
    
    union CatOrDog = Cat | Dog
    union DogOrHuman = Dog | Human
    union HumanOrAlien = Human | Alien
    
    type QueryRoot {
        dog: Dog
    }
SCHEMA;

try {
    $schema = $parser->parse($schema);
    var_dump($schema); // Instance of HansOtt\GraphQL\Schema\Schema
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

[ico-version]: https://img.shields.io/packagist/v/hansott/graphql-language.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/hansott/graphql-language/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/hansott/graphql-language.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/hansott/graphql-language.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/hansott/graphql-language.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/hansott/graphql-language
[link-travis]: https://travis-ci.org/hansott/graphql-language
[link-scrutinizer]: https://scrutinizer-ci.com/g/hansott/graphql-language/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/hansott/graphql-language
[link-downloads]: https://packagist.org/packages/hansott/graphql-language
[link-author]: https://github.com/hansott
[link-contributors]: ../../contributors
