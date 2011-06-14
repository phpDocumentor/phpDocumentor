Introduction
============

This book is about *Symfony Event Dispatcher*, a PHP library part of
the Symfony Components project. Its official website is at
http://components.symfony-project.org/event_dispatcher/.

>**SIDEBAR**
>About the Symfony Components
>
>[Symfony Components](http://components.symfony-project.org/) are
>standalone PHP classes that can be easily used in any
>PHP project. Most of the time, they have been developed as part of the
>[Symfony framework](http://www.symfony-project.org/), and decoupled from the
>main framework later on. You don't need to use the Symfony MVC framework to use
>the components.

What is it?
-----------

Symfony Event Dispatcher is a PHP library that provides a
lightweight implementation of the Observer design pattern.

It's a good way to make your code more flexible. It's also a great
way to make your code easily extensible by others. Third-party code
listens to specific events by registering PHP callbacks and the
dispatcher calls them whenever your code notifies these events.

### Fast

The main goal of Symfony Event Dispatcher is to be as fast as
possible. No need to implement interfaces or extend complex classes,
events are simple strings and the notification code is very light.
Add any number of listeners and notifications without too much
overhead.

### Open-Source

Released under the MIT license, you are free to do whatever you
want, even in a commercial environment. You are also encouraged to
contribute.

### Built on the shoulders of giants

Symfony Event Dispatcher has its roots in the Apple Cocoa
notification center. But instead of being a straight port of the
original implementation, the library has been rethought and
redesigned to take into account the PHP platform specificities.

### Easy to use

There is only one archive to download, and you are ready to go. No
configuration, and no installation. Drop the files in a directory
and start using it today in your projects.

### Documented

Symfony Event Dispatcher is fully documented, with a dedicated online
book, and of course a full API documentation.

### Unit tested

The library is fully unit-tested. With 100% code coverage, the library is
stable and ready to be used in large projects.

Installation
------------

Symfony Event Dispatcher can be installed by downloading the source
code as a
[tar](http://github.com/fabpot/event-dispatcher/tarball/master)
or
[zip](http://github.com/fabpot/event-dispatcher/zipball/master)
archive.

To stay up-to-date, you can also use the official Subversion
[repository](http://svn.symfony-project.com/components/event_dispatcher/).

If you are a Git user, there is an official
[mirror](http://github.com/fabpot/event-dispatcher), which is
updated every 10 minutes.

If you prefer to install the component globally on your machine, you can use
the symfony [PEAR](http://pear.symfony-project.com/) channel server.

Support
-------

Support questions and enhancements can be discussed on the
[mailing-list](http://groups.google.com/group/symfony-components).

If you find a bug, you can create a ticket at the symfony
[trac](http://trac.symfony-project.org/newticket) under the
*event_dispatcher* component.

License
-------

The Symfony Event Dispatcher component is licensed under the *MIT
license*:

>Copyright (c) 2008-2009 Fabien Potencier
>
>Permission is hereby granted, free of charge, to any person obtaining a copy
>of this software and associated documentation files (the "Software"), to deal
>in the Software without restriction, including without limitation the rights
>to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
>copies of the Software, and to permit persons to whom the Software is furnished
>to do so, subject to the following conditions:
>
>The above copyright notice and this permission notice shall be included in all
>copies or substantial portions of the Software.
>
>THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
>IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
>FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
>AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
>LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
>OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
>THE SOFTWARE.
