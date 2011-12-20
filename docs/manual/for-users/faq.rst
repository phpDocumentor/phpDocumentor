Frequently asked questions
==========================

I get the following error: preg_match(): Compilation failed: support for \P, \p, and \X has not been compiled
-------------------------------------------------------------------------------------------------------------

    I get the following error: preg_match(): Compilation failed: support for
    \P, \p, and \X has not been compiled at offset XX. What does that mean?.

\P, \p and \X are Unicode character selectors of preg_match, like \w but then
on steroids. To ensure multi-lingual support DocBlox converts all source files
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