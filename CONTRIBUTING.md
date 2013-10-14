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

Profiling phpDocumentor
-----------------------

The phpDocumentor vagrant setup installs the various components so that profiling of phpDocumentor can be done.

If you want to see profiling output for phpDocumentor the following manual steps need to be done

1. Open your hosts file locally on your machine and add the following entry
``` bash
192.168.255.2 profiling.phpdocumentor.local
```
Once you have done this you should be able to browse to http://profiling.phpdocumentor.local and see the xhgui. As you haven't done any profiling runs just yet
there will be no info on this screen.

2. ssh into the vagrant virtual machine with vagrant ssh.
3. xhgui by default will only profile 1 out of 100 requests. To make it profile every request edit the following file on the guest machine
   /var/www/xhgui/external/header.php
   and remove the code below located on line 45
``` php
// Obtain the answer to life, the universe, and your application one time out of a hundred
if (rand(0, 100) !== 42) {
    return;
}
```

4. You need to let phpDocumentor know that you want to switch profiling on. Todo this you need to create two environment variables using the command below
``` bash
   export PHPDOC_PROFILE="on"
   export XHGUI_PATH="/var/www/xhgui"
```
5. On the guest machine you can profile phpDocumentor using the docs of phpDocumentor itself. To do that
``` bash
/vagrant/bin/phpdoc.php run -d /vagrant/vendor/phpdocumentor
```
You should see PROFILING ENABLED when you run phpDocumentor.

6. Now browse to http://profiling.phpdocumentor.local url and you should see profiling output. If you click on the date you should be able to see the profile output for phpDocumentor. Clicking on the url does work as it expects a url and we are profiling a command line application.


