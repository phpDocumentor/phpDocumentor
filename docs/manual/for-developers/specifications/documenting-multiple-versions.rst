Documenting multiple versions
=============================

Introduction
------------

With phpDocumentor it is possible to document multiple versions of your
application using a single command.

phpDocumentor will use a :ref:`Version Definition <def-version-definition>` in
the configuration file to populate a temporary directory with the source files
contained in the definition; run phpDocumentor against that and store the
results in a subdirectory of the target location.

By using this functionality can template builders add links to other versions
from within their template without knowing the location's up front.

Definitions
-----------

.. _def-version:

Version
  in the context of phpDocumentor represents a version a single pass of the
  parse phase, transform phase or both, on a given
  :ref:`Version Definition <def-version-definition>`

.. _def-version-definition:

Version Definition
  This represents any set of parameters that identifies a way to retrieve a set
  of file sources. This may be, for example, an absolute or relative path but
  may also be a branch or tag of the current VCS checkout.

.. _def-version-type:

Version Type
  This is a simplified representation of a specific way to retrieve a
  :ref:`Version <def-version>`. Examples of types are: ``Path`` and ``Git``.

Restrictions
------------

Versions may only be specified using the configuration file due to their
complexity. Also note that some :ref:`Version Types <def-version-type>` require
an active VCS checkout at the location of the configuration file.

Theory of operation
-------------------

* Provide configuration option
* Target content is copied to a folder in /tmp
* Documentation is generated in a subfolder of the desired location as indicated
  by a slug of the branch/tag name
* Each index page of the documentation contains references to the other versions.
  This enables the user to easily navigate to them.

Examples::

    <versions>
        <git branch="master" alias="Master branch" />
        <git branch="2.0" alias="2.0 branch" slug="2.0"/>
        <git tag="2.0.0a([\d]+)" alias="2.0.0a$1"/>
        <path tag="/my/location" alias="MyLocation"/>
    </versions>

Body of the version may contain a custom configuration file in case the specified
version did not have one.

During each pass phpDocumentor will use the settings for that specific version
combined with the settings passed on the command line,

.. note:: versions in sub-configurations are ignored

Version Types
-------------

Path
~~~~

Git
~~~


Configuration specification
---------------------------

Performance
-----------

To speed up the generation of documentation will phpDocumentor only write new
output if it has detected changes between the previous version and this one.

.. note::

   A complete new set of documentation is also written after an update of
   phpDocumentor to take advantage of any new features of bug fixes.