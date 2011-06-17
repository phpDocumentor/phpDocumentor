Contributor's Guidelines
========================

Introduction
------------

DocBlox aims to be a high quality Documentation Generation Application (DGA) but at the same time wants to
give contributors freedom when submitting fixes or improvements.

As such we want to _encourage_ but not obligate you, the contributor, to follow these guidelines.
The only exception to this are the guidelines regarding Github usage and branching to prevent merge-hell.

Having said that: I do really appreciate it when you apply the guidelines in part or wholly.

Github Usage & Branching
------------------------

Coding Standards
----------------

### @category, @packages and @subpackages

DocBlox tries to standardize the way it uses the @package and @subpackage to create a consistent and maintainable API
Documentation. This will also reduce confusion in the naming of the File-level packages and Class level packages.

**@category**

The category is always `DocBlox`; including capitol D and B.

**@package**

The package represents the component in which this class or file is placed. A list of components is added as present
at time of writing.

* Core
* Parser
* Transformer
* Reflection
* Tasks
* Tokens

**@subpackage**

There are no subpackages pre-defined and using one is not required. When a class or file belongs to a specific subset
of functionality within a package (i,e, Writers, Behaviours, etc.) it is however encouraged to do so.

The following is requested when creating a subpackage:

* Start a subpackage with a capitol letter
* Try to use single word subpackages (i.e. Writers, Behaviours, etc)
* Use singular for a concept (i.e. Parser) and plural for a collection and its basics (i.e. Writers)
* If you must use multiple words to describe a single package, separate them with an underscore (i.e. Unit_tests)

Unit testing
------------