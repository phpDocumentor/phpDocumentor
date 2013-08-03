Troubleshooting
===============

I want to install phpDocumentor without internet access
-------------------------------------------------------

At http://pear.phpdoc.org you can find compressed archives that can be used to do an offline install using PEAR, or
when you do not have PEAR on the designated machine you can uncompress the archive in your installation path.

The binary's path is ``<installation_path>/bin/phpdoc.php``.

I get the following error: preg_match(): Compilation failed: support for \P, \p, and \X has not been compiled
-------------------------------------------------------------------------------------------------------------

    I get the following error: preg_match(): Compilation failed: support for
    \P, \p, and \X has not been compiled at offset XX. What does that mean?.

\P, \p and \X are Unicode character selectors of preg_match, like \w but then
on steroids. To ensure multi-lingual support phpDocumentor converts all source
files into UTF-8. preg_match() uses the PCRE library, which is (almost) always
bundled with your PHP version.

It is here that an error may occur as there are packages out there (mostly on
CentOS Linux) that have an incorrectly build PCRE library.

To enable full UTF-8 support with PCRE you need 2 options: UTF-8 support and
Unicode properties enabled; the latter is most probably not supported in your
setup.
The easiest fix is to upgrade your PCRE library to the latest version or
follow the instructions in the following blog post:
http://chrisjean.com/2009/01/31/unicode-support-on-centos-52-with-php-and-pcre/

I get a segfault during transformation when using Zend Server CE 5.0
--------------------------------------------------------------------

Some versions of Zend Server CE 5.0 have an incorrectly build version of the
libxslt library. You can identify whether this is the case for you using the
following commands and checking if the output matches:

    $ php -i | grep libxslt
    libxslt Version => 1.1.24 libxslt compiled against libxml Version => 2.7.3 libexslt Version => 1.1.23
    $ php -i | grep libxml
    libxml Version => 2.6.32 libxml libxml2 Version => 2.6.32 libxslt compiled against libxml Version => 2.7.3

As can be seen in this command and response is that the libxslt library is built
against a newer version of the library than is available on the platform.

The only solution to resolve this situation is to update your libxml library to
at least the version as specified in the libxslt line, to update your Zend
Server to the latest version and/or contact Zend Support for a solution.

I get an error during transformation: Specified DOMDocument lacks documentElement, cannot transform
----------------------------------------------------------------------------------------------------

Your source files do not have UTF-8 encoding, add ``--encoding`` parameter to the command with the correct encoding
See also: :doc:`Run command <../commands/project_run>`
