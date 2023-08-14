# Lang

In this folder the defaults of laravel collide with the
(although widely ignored...) standards we set a while back regarding localization.

Unfortunately using the proper standard as agreed on isn't compatible
with most libraries in the php-world, so we have to use the closest approximation:

## POSIX

You shall use posix-locales (xx_XX) whenever possible.
Right now this has no impact, but who know, maybe we'll grow internationally important enough ;)

e.g. you should have the folder `en_US` and maybe `de_DE`.  (don't delete the `en`-link!)

## en / de

In some cases libraries just expect `en` to be there, regardless of your config.
Also, some logic maybe relies on `de` to be there...
To allow easy compatibility we symlinked the `en`-folder to `en_US` and `de` to `de_DE`