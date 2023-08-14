How to update your service to the newest Boilerplate version?
========

#### This applies to the `Laravel Boilerplate`.

Good to know before going further:

- Add the new remote - `git remote add <remote-name> <remote-url>`
- Show remotes - `git remote -v`
- Pull data from the remote tag - `git pull <remote-name> tags/<tag-name>`

____

There are the steps you need to take to update the Boilerplate:
1. **Choose a released tag you want to update to**
   - for example `X.Y.Z`


2. **Create a new branch**
   - `git checkout -b <branch-name>`
	

3. **Add a new remote for the Boilerplate**
   - In case you don't have boilerplate remote yet, run `git remote add boilerplate git@gitlab.smartexpose.com:allmyhomes/laravel-api-boilerplate.git`


4. **Fetch remote**
   - `git fetch boilerplate --tags`


5. **Pull/Merge**
      - **a.** `git pull boilerplate tags/X.Y.Z`
      - **b.** `git checkout --theirs -- composer.json composer.lock`
      - **c.** or merge via IDE


6. **Resolve merge conflicts**
    - [Resolve merge conflicts using command line](https://help.github.com/en/articles/resolving-a-merge-conflict-using-the-command-line)
    - [Resolve merge conflicts using PHPStorm](https://www.jetbrains.com/help/phpstorm/resolving-conflicts.html)


7. **Re-apply your composer.json and composer.lock changes**
   - As we know what we changed in composer.json and composer.lock, it should be easy to re-apply the changes we did in our branch


8. **Run the tests**
   - Login into shell, update your dependencies  & run your tests


9. **Commit**
   - `git add .`
   - `git commit -m "<commit-message>"`


10. **Push**
    - `git push --set-upstream origin <branch-name>` (same `<branch-name>` as in step **1.**)
	
	
	
## Example

```sh
> $ git checkout -b feature/SRE-1890-update-boilerplate

# Skip it in case you added boilerplate remote before
> $ git remote add boilerplate git@gitlab.smartexpose.com:allmyhomes/laravel-api-boilerplate.git

> $ git fetch boilerplate --tags

> $ git pull boilerplate tags/4.0.0

# Overwrite our changes with the changes in the boilerplate in composer.json and composer.lock
> $ git checkout --theirs -- composer.json composer.lock 

> $ git status
# RESOLVE CONFLICTS IF THERE ARE SOME

# Here, we need to re-apply our changes in composer.json and composer.lock by using any merging tool

# Log in into shell
# Remove composer.lock file in case you've got the merge conflicts there
> $ composer install

# Make sure your application is still working with the newest Boilerplate
> $ composer test

# Add & commit your changes
> $ git add .

> $ git commit -m "chore: boilerplate update 4.0.0"

> $ git push --set-upstream origin feature/SRE-1890-update-boilerplate
```

## Useful Links

- Handling Composer Conflicts
   - <https://naderman.de/slippy/slides/2018-01-26-composer-lock-demystified.pdf>
   - <https://jmstewart00.github.io/stewartblog/jekyll/update/2018/09/22/composer-conflicts.html>
