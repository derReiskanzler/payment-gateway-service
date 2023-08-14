# PHPStan

PHPStan is a tool for static analysis of your code. Static analysis is important to catch any errors or bugs before the code is pushed to production, and to help follow clean code principles.

From [GitHub docs](https://github.com/phpstan/phpstan):
>PHPStan focuses on finding errors in your code without actually running it. It catches whole classes of bugs even before you write tests for the code.

## Installation Steps

To install PHPStan, run the command: `composer install`. The package is already included in `composer.json`.

If you get dependency errors, delete the `vendor` folder as well as `packages.php` and `services.php` located inside `.src/Infrastructure/Boilerplate/Laravel/bootstrap/cache/`; run `composer dump-autoload` and try again

## Configuration

There is a default configuration file in the root folder called `phpstan.neon.dist`. It contains two parameters: `level` and `paths`. The first defines the run level, or how strict rules should PHPStan apply when analysing the code. The second one tells PHPStan which directories to look into.

If you want to use your own configuration, copy the file and rename it to `phpstan.neon`.

```sh
cp phpstan.neon.dist phpstan.neon
```

It will get priority over the default configuration. Your custom configuration should not be added to the repository (it is already ignored by git).

More info on configuring PHPStan can be found in the [GitLab Docs](https://github.com/phpstan/phpstan#configuration).

## Usage

1. Use the command `composer phpstan:analyse` to run PHPStan.
 - If there is no configuration file present and you don't pass any parameters, PHPStan will ask for directories to look into, and they should be entered as follows: `composer phpstan:analyse -- <dir1> <dir2> <dir3>` (and so on).
 - If there is a configuration file present and you pass parameters to the command line, the equivalent parameters in the configuration file will be ignored.
 - If you want to change the run level, you can do so either by changing the level in the config file, or by passing `--level <level>` or `-l <level>`.
 - There are 8 levels, 0 is the loosest, 8 is the strongest level. You can also pass `max` which is currently equivalent to 8. More on levels [in the PHPStan documentation](https://github.com/phpstan/phpstan#rule-levels).
 - Optionally you can pass a custom configuration file using `-c ./<path/to/custom/config.neon>`. This will ignore the default or custom configuration file in the root directory and use the one you provide.
 - Examples:
        - `composer phpstan:analyse -- -l 5 Application/v1 database`
        - `composer phpstan:analyse -- -c myCustomConfig.neon`

2.After the analysis finishes, PHPStan will produce a list of files where it finds issues with line numbers and clear explanation of what needs to be done.

## References of PHPStan

[PHPStan documentation on GitHub](https://github.com/phpstan/phpstan).