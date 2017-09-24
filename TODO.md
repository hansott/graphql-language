# TODO

## Version 2.0.0

* [ ] Follow https://github.com/facebook/graphql/pull/90 and update schema parser (To build type declaration tools)
* [ ] Remove duplicate code in parser and lexer (To get a better rating)
* [ ] Add more tests (To catch the edge cases)
* [ ] Better abstractions (Interfaces and abstract classes)
* [ ] Hide public properties on AST and provide getters for it (Immutability)
* [ ] Better file structure (Too much classes in one directory)
* [ ] Better traverser, or at least find a use case for it, maybe replace with utility functions (The getters will help for this too) (Use case: Rate limiter, detect amount of possible nodes in query, like GitHub's GraphQL API)
