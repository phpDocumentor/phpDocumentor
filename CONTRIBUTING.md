Contributing to phpDocumentor
=============================

Introduction
------------

phpDocumentor aims to be a high quality Documentation Generation Application (DGA) but at the same time wants to give
contributors freedom when submitting fixes or improvements.

As such we want to *encourage* but not obligate you, the contributor, to follow these guidelines. The only exception to
this are the guidelines regarding *GitHub Usage & Branching* to prevent `merge-hell`.

Having said that: we really appreciate it when you apply the guidelines in part or wholly as that will save us time
which, in turn, we can spend on bugfixes and new features.

GitHub Usage & Branching
------------------------

Once you decide you want to contribute to phpDocumentor (which we really appreciate!) you can fork the project from
http://github.com/phpDocumentor/phpDocumentor2.

Please do *not* develop your contribution on your master branch but create a separate feature branch, that is based off
the `develop` branch, for each feature that you want to contribute.

> Not doing so means that if you decide to work on two separate features and place a pull request for one of them, that
> the changes of the other issue that you are working on is also submitted. Even if it is not completely finished.

To get more information about the usage of Git, please refer to the [Pro Git book][PROGIT] written
by Scott Chacon and/or [this help page of GitHub][GITHUB_HELP_PR].

Setting Up Your Development Environment
---------------------------------------

There are two methods to setup your development environment,

- using [Vagrant][VAGRANT], which sets up a virtual machine for you and automatically installs all dependencies or
- Installing all dependencies on your local machine

Which method you choose is up to you, using Vagrant keeps your machine clean but requires a bit more setup and
it does not work on Windows machines (because Ansible has no Windows version).

> If you'd really want to you can run Ansible using Cygwin but we cannot provide support for that. More information
> can be found here: https://servercheck.in/blog/running-ansible-within-windows

### Using Vagrant

The easiest way to get started is by using [Vagrant][VAGRANT]. Only one command is necessary after installing
[Vagrant][VAGRANT_INSTALL] (1.5 or higher), [VirtualBox][VIRTUALBOX_INSTALL] and [Ansible][ANSIBLE_INSTALL]
(1.6 or higher) on your local machine.

Just run the following command from the phpDocumentor folder:

    $ vagrant up

At this point a Virtual Machine, running Ubuntu Precise Pangolin 64-bit, is downloaded from the internet and several
packages are installed that are necessary to run phpDocumentor and its automated tests.

To access the Virtual Machine you can run the following command from the phpDocumentor folder:

    $ vagrant ssh

Once done you see a welcome message that explains some of the things that you can do (which are repeated in the
following chapters) and you are taken to the `/vagrant` folder. The `/vagrant` folder is a shared folder between
your local machine and the virtual machine and points to the folder where you put phpDocumentor.

At this stage there is just one more step to do, and that is run composer to install all dependencies that phpDocumentor
uses:

    $ composer install

Composer is installed globally in the virtual machine during provisioning, there are no steps required to get it.

### On Your Local Machine

If you don't want to use Vagrant to setup phpDocumentor, or use Windows, you can always setup the necessary dependencies
to get started.

You need the following:

- Composer
- Git
- Curl and PHP's Curl extension
- PHP's XSL extension and
- PHP's intl extension

Coding Standards
----------------

phpDocumentor uses the [PSR-2 Coding Standard][PSR2] as defined by the
[PHP Framework Interoperability Group (PHP-FIG)][PHP_FIG].

It is recommended to check your code using phpCodeSniffer using the *PSR2* standard using the following command:

    $ ./bin/phpcs --standard=PSR2 [file(s)]

With this command you can specify a file or folder to limit which files it will check or omit that argument altogether,
in which case the current directory is checked.

Unit testing
------------

phpDocumentor aims to be have at least 90% Code Coverage using unit tests using PHPUnit. It is appreciated to include
unit tests in your pull requests as they also help understand what the contributed code exactly does.

In order to run the unit tests you can execute the following command from your phpDocumentor folder:

    ./bin/phpunit [file(s)]

With this command you can specify a file or folder to limit which files it will check or omit that argument altogether,
in which case all tests are ran.

Profiling phpDocumentor
-----------------------

> By default the requirements for profiling phpDocumentor are not installed in the Virtual Machine, please run the
> following command to install the required libraries and modules:
>
>     $ ansible-playbook --tags=profiling -i ".vagrant/provisioners/ansible/inventory/vagrant_ansible_inventory"
>     --private-key=~/.vagrant.d/insecure_private_key -u vagrant ansible/playbook.yml

If you want to see profiling output for phpDocumentor the following manual steps need to be done

1. Open your hosts file locally on your machine, `/etc/hosts` on MacOsX and Linux, and add the following entry:

       192.168.255.2 profiling.phpdocumentor.local

   Once you have done this you should be able to browse to http://profiling.phpdocumentor.local and see the xhgui web
   interface. As you haven't done any profiling runs just yet there will be no info on this screen.
2. Log into your vagrant virtual machine using the `vagrant ssh` command.
3. Xhgui by default will only profile 1 out of 100 requests. To make it profile every request edit the following file
   on the virtual machine: `/var/www/xhgui/config/config.default.php` and change the code located on line **27** to

   ``` php
   // Profile 1 in 100 requests.
   'profiler.enable' => function() {
       return true; //rand(0, 100) === 42;
   },
   ```

4. You need to let phpDocumentor know that you want to switch profiling on. To do this you need to create two
   environment variables using the commands below:

   ``` bash
   export PHPDOC_PROFILE="on"
   export XHGUI_PATH="/var/www/xhgui"
   ```

5. On the virtual machine you can profile phpDocumentor using the docs of phpDocumentor itself. To do that run the
   command:

   ``` bash
   /vagrant/bin/phpdoc run -d /vagrant/vendor/phpdocumentor
   ```
   You should see the text *PROFILING ENABLED* on screen when you run phpDocumentor.

6. Now browse to http://profiling.phpdocumentor.local URL and you should see profiling output. If you click on the
   date you should be able to see the profile output for phpDocumentor. Clicking on the URL does work as it expects a
   URL and we are profiling a command line application.

[PROGIT]:             http://git-scm.com/book
[GITHUB_HELP_PR]:     https://help.github.com/articles/using-pull-requests
[VAGRANT]:            http://vagrantup.com
[ANSIBLE_INSTALL]:    http://docs.ansible.com/intro_installation.html
[VAGRANT_INSTALL]:    http://docs.vagrantup.com/v2/installation/index.html
[VIRTUALBOX_INSTALL]: https://www.virtualbox.org/manual/ch02.html
[PSR2]:               https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
[PHP_FIG]:            http://www.php-fig.org/