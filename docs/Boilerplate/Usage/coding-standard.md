# Coding Standard

This project follows the [Symfony Coding Standard].

We ensure Compliance with that Standard via the [PHP-CS-Fixer] and its [Symfony Ruleset].

## Fixing

You can **fix** your code by running `composer cs-fix` (or `composer cs-f`).

This will fix all detected violations in place.

## Checking

You can **check** your code by running `composer cs-check` (or `composer cs-c`).

This will dry-run the fixer and display a diff of all needed fixes in your shell, but won't change any files.

An equivalent command is used in the gitlab-ci.

## Config

Checkout the [PHP-CS-Fixer Config Documentation].

[--- References ---]: .
[Symfony Coding Standard]: https://symfony.com/doc/6.0/contributing/code/standards.html
[Symfony Ruleset]: https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/master/doc/ruleSets/Symfony.rst
[PHP-CS-Fixer]: https://github.com/FriendsOfPHP/PHP-CS-Fixer
[PHP-CS-Fixer Config Documentation]: https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/master/doc/config.rst
