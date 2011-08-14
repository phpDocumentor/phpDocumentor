Contributor's Guidelines
========================

Introduction
------------

DocBlox aims to be a high quality Documentation Generation
Application (DGA) but at the same time wants to give contributors
freedom when submitting fixes or improvements.

As such we want to *encourage* but not obligate you, the
contributor, to follow these guidelines. The only exception to this
are the guidelines regarding *Github usage and branching* to
prevent ``merge-hell``.

Having said that: I do really appreciate it when you apply the
guidelines in part or wholly.

Github Usage & Branching
------------------------

Once you decide you want to contribute to DocBlox (which we really
appreciate!) you can fork the project at
http://github.com/mvriel/docblox.

Please do *not* develop your contribution on your master branch but
create a separate branch for each feature that you want to
contribute.

Not doing so means that if you decide to work on two separate
features and place a pull request for one of them, that the changes
of the other issue that you are working on is also submitted. Even
if it is not completely finished.

To get more information about the usage of Git, please refer to the
ProGit online book written by Scott Chacon and/or this help page of
Github: @PAGE@

Coding Standards
----------------

PEAR Coding Standards
~~~~~~~~~~~~~~~~~~~~~

DocBlox uses the coding standards as defined by PEAR, which can be
found at http://pear.php.net/codingstandards.

It is adviced to check your code using \_PHP*CodeSniffer*; it
includes support for the PEAR coding standard by default. In the
root of the project is a script file called ``phpcs`` which
provides an example of usage.

Amendments
~~~~~~~~~~

@category, @packages and @subpackages
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

DocBlox tries to standardize the way it uses the @package and
@subpackage to create a consistent and maintainable API
Documentation. This will also reduce confusion in the naming of the
File-level packages and Class level packages.

**@category**

The category is always ``DocBlox``; including capital D and B.

**@package**

The package represents the component in which this class or file is
placed. A list of components is added as present at time of
writing.


-  Core
-  Parser
-  Transformer
-  Reflection
-  Tasks
-  GraphViz

**@subpackage**

There are no subpackages pre-defined and using one is not required.
When a class or file belongs to a specific subset of functionality
within a package (i,e, Writers, Behaviours, etc.) it is however
encouraged to do so.

The following is requested when creating a subpackage:


-  Start a subpackage with a capital letter
-  Try to use single word subpackages (i.e. Writers, Behaviours,
   etc)
-  Use singular for a concept (i.e. Parser) and plural for a
   collection and its basics (i.e. Writers)
-  If you must use multiple words to describe a single package,
   separate them with an underscore (i.e. Unit\_tests)

Unit testing
------------

DocBlox aims to be have at least 90% Code Coverage using unit tests
using PHPUnit. It is appreciated to include unit tests in your pull
requests as they also help understand what the contributed code
exactly does.


