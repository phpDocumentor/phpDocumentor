Profiling
=========

Improving the performance of phpDocumentor isn't something done by gazing in a crystal
ball. This is a process of carefully profiling the application and making sure
everything performs well.

As such phpDocumentor has made profiling a first-class citizen by including parts of
the XHProf files and adding a special 'hidden' command-line argument `--profile`.

Requirements
------------

.. WARNING::

    These instructions are only for Linux users. No profiling support is
    provided for Windows installations. However, contributions are extremely
    appreciated!

In order to profile phpDocumentor you need:

* the `XHProf extension <http://pecl.php.net/package/xhprof>`_ installed and
* have the XHProf UI available via your local apache setup
* have a world-readable folder *xhprof* in your /tmp folder

A nice guide that provides comprehensive installation instruction can be found
here: http://techportal.ibuildings.com/2009/12/01/profiling-with-xhprof/

Running the profiler
--------------------

phpDocumentor can profile its execution by having `--profile` as command parameter.

.. NOTE::

    Please note that for this to work the parent folder of XHProf must be in
    your path or this must be executed from the *src* folder in the phpDocumentor
    source.

When executed in such fashion phpDocumentor will run as expected and in the end generate
an XHProf report in */tmp/xhprof*. For convenience will phpDocumentor output the
complete page that needs to be invoked in XHProf UI.

Example::

    $ mvriel@chronos:/usr/share/php/phpDocumentor/src$ phpdoc --profile
    ...
    Profile can be found at: index.php?run=4ef0eab24bcaf&source=phpdoc
