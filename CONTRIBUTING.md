Contributing to phpDocumentor
=============================

Introduction
------------

phpDocumentor aims to be a high quality Documentation Generation Application (DGA) but at the same time wants to give
contributors freedom when submitting fixes or improvements.

As such we want to *encourage* but not obligate you, the contributor, to follow these guidelines. The only exception to
this are the guidelines regarding *Github usage and branching* to prevent `merge-hell`.

Having said that: we really appreciate it when you apply the guidelines in part or wholly as that will save us time
which we can spend on bugfixes and new features.

Github Usage & Branching
------------------------

Once you decide you want to contribute to phpDocumentor (which we really appreciate!) you can fork the project from
http://github.com/phpDocumentor/phpDocumentor2.

Please do *not* develop your contribution on your master branch but create a separate feature branch, that is based off
the `develop` branch, for each feature that you want to contribute.

   Not doing so means that if you decide to work on two separate features and place a pull request for one of them, that
   the changes of the other issue that you are working on is also submitted. Even if it is not completely finished.

To get more information about the usage of Git, please refer to the [ProGit online book](http://progit.org/book) written
by Scott Chacon and/or [this help page of Github](https://help.github.com/articles/using-pull-requests).

Coding Standards
----------------

phpDocumentor uses the
[PSR-2 Coding Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)
as defined by the [PHP Framework Interoperability Group (PHP-FIG)](http://www.php-fig.org/).

It is advised to check your code using phpCodeSniffer; the 'PSR2' standard is included by default in the most
recent versions.

Example:

``` bash
$ phpcs --standard=PSR2 [file(s)]
```

Unit testing
------------

phpDocumentor aims to be have at least 90% Code Coverage using unit tests using PHPUnit. It is appreciated to include
unit tests in your pull requests as they also help understand what the contributed code exactly does.
