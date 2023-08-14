# How to update Boilerplate

Steps to migrate your Service to the newest boilerplate version:

- Run `php artisan cache:clear`
- Remove the files located in `src/Infrastructure/Boilerplate/Laravel/bootstrap/cache/` (except `.gitignore`)
- Merge this branch
  - in case of merge conflicts in `composer.lock` file, simply delete it running `rm composer.lock` command
  - NEVER try to merge the `composer.lock`
- Run `composer update`
- Run your tests
  - If you see `Class 'Allmyhomes\NestedModules\ServiceProvider' not found` error, delete `Allmyhomes\NestedModules\ServiceProvider::class` from `src/Infrastructure/Boilerplate/Laravel/config/app.php`

After successful merging & deploying application, don't forget to double test your application on Dev environment!
