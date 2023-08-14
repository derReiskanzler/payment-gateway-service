# BAT - Backend Code Review Checklist

* Stay nice and kind!
* Ask all the questions that come to your mind. Don’t be shy, take the opportunity to learn something new!
* Accept all comments in the most positive and open way. Remember, there are no personal comments. Be open to listening and considering the views of others even if you have strong opinions. Explain your choices thoroughly, exhibiting little bias.

Focus on:

* Security
* Readability (Understandability and Simplicity ([KISS](https://en.wikipedia.org/wiki/KISS_principle)))
* Maintainability
* Extendability

[Check the general code review best practices](https://gitlab.smartexpose.com/allmyhomes/technology/-/blob/4c056fcf20b16c8105402bb639161ccfe8eb1c25/general/clean-code/CODE-REVIEW-BEST-PRACTICES.md#code-review-best-practices)
## General

* [ ] The code performs the task it has been written to complete, in an efficient and concise way. The implementation is efficient.

## Security

* [ ] Passwords, API keys, Session Tokens, and Authorization facilities have been implemented in a secure way.
* [ ] Is the data input checked (correct type/length/format/range/etc.) and encoded?

## Licenses

* [ ] if new composer packages are used, the License must be checked to comply with following conditions (check the licenses at <https://tldrlegal.com/license>):
  * always:
    * `allowed for Commercial Use`
  * in production dependencies (= NOT require-dev):
    * `allowed to sublicense`
  * if we need to make changes:
    * `allowed to modify`

## No Trickery of Quality Tools

* [ ] no `phpcs ignore`, `@codeCoverageIgnore` or similar exclusions, except there is really a GOOD reason for it. *("But it's not written by us" is NO good reason!)*

## Zoom out (Air control)

* [ ] Moving to the right architectural direction?
* [ ] Can the code be simplified by using external dependencies (not reinventing the wheel)?
* [ ] Is business logic properly separated from integration?

## Zoom in (Ground control)

### Testing

* [ ] All code is covered by tests, write unit tests for your core application/domain and integration tests for your infrastructure layer.
* [ ] Are the tests covering all the edge cases?
* [ ] Are the tests covering the error cases and not solely the happy path (main control flow)?

### SOLID

* [ ] Single-responsibility principle

> A class should only have a single responsibility, that is, only changes to one part of the software's specification should be able to affect the specification of the class.

* [ ] Open–closed principle

> "Software entities ... should be open for extension, but closed for modification."

* [ ] Liskov substitution principle

> "Objects in a program should be replaceable with instances of their subtypes without altering the correctness of that program."

* [ ] Interface segregation principle

> "Many client-specific interfaces are better than one general-purpose interface."

* [ ] Dependency inversion principle

> One should "depend upon abstractions, **[not]** concretions."

### PHP Files

* [ ] `strict_types` - every .php file ought to enable strict types like so:

```php
<?php

declare(strict_types=1);
```

To achieve this, the easiest would be to update your IDE templates.

### Class/Object Design

* [ ] `final` keyword by default to protect the class' design and prevent abuse. The option to extend a class should be one of its explicit use cases, hence we'd expect at least one subclass if the `final` keyword is absent. To achieve this, the easiest would be to update your IDE templates.
* [ ] The `constructors` do not do anything meaningful. They ought to accept arguments and assign them to properties - nothing more. An object will get all its dependencies injected, hence there is no call to a service locator. They may do some assertions (check pre-conditions) on the arguments in case the PHP type system cannot do it, e.g.:

```php
    private string $id;

    public function __construct(string $id)
    {
        Assertion::uuid($id);
        $this->id = $id;
    }
```

* [ ] `protected properties` - If a property is not marked "private", it's used by at least one subclass.
* [ ] `abstract classes` - An abstract class won't use methods that aren't part of its published interface.

### Services

* [ ] Services are stateless and immutable, hence it is not allowed to setData and save it in a property. You ought to pass it directly to the function that needs it.

### Value Objects

* [ ] Use Value Objects to extend the PHP type system and express your domain semantically. You ought to avoid passing arrays around.
* [ ] Value Objects are immutable.

### Traits

* [ ] can be understood as capabilities to provide a standard implementation for a given interface.
* [ ] shall not depend on properties and methods they do not provide.

### Type Declarations

* [ ] All properties and function arguments have a type hint where possible.
* [ ] All return values have a return type declaration.
* [ ] Generators, arrays, and the like have a PhpDoc to describe the inner types, e.g.:

```php
/**
* @param int $number
* @return Generator<int>
*/
function generate(int $number): Generator
```

### Comments

* [ ] Challenge code comments. Do you they really document anything meaningful? In our opinion good code does not need any comments because good code is self-documenting, comments tend to outdate rapidly and simply lie. They reduce the readability by blowing up the code base and add more stuff to read.

### Misc

* [ ] No magic parameters, like magic numbers or booleans passed to any function.
* [ ] Correct level of abstraction?
* [ ] Names for variables, methods, classes are descriptive and concise.
* [ ] Obsolete code has been deleted and deprecated code has been marked as deprecated.
* [ ] No code duplication or redundant code has been added.

## Changelog

* [ ] The purpose of the change is clearly mentioned in `CHANGELOG.md`.
* [ ] each change references its ticket(s) in format `(**[<number>](https://allmyhomes.atlassian.net/browse/<number>)**)`
* [ ] all changes are categorized either as `Added`, `Changed`, `Deprecated`, `Fixed` or `Removed`
* [ ] all changes are below the version tag `[UNRELEASED]` and **NOT** below a tag for the next version

## Api-Contract

* [ ] all changes to endpoints are reflected in the api-contract
* [ ] the version number is **NOT** adjusted, this will be done during the release
