Frequently asked questions
==========================

I get the following error: preg_match(): Compilation failed: support for \P, \p, and \X has not been compiled
-------------------------------------------------------------------------------------------------------------

    I get the following error: preg_match(): Compilation failed: support for
    \P, \p, and \X has not been compiled at offset XX. What does that mean?.

\P, \p and \X are Unicode character selectors of preg_match, like \w but then
on steroids. To ensure multi-lingual support phpDocumentor converts all source files
into UTF-8. preg_match() uses the PCRE library, which is (almost) always
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