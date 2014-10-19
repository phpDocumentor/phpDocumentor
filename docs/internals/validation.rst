Validation
==========

In phpDocumentor2 you can check your project against a Documentation Standard. By default phpDocumentor2 will verify
your project against a series of pre-defined expectations, or rules, but this is completely configurable using a
configuration file or by providing a command line option describing which standard should be used.

.. important::

   This technical specification is implemented in issue 40_ and may not be completely implemented at this time. Please
   review the issue to check the current status.

Terminology
-----------

This specification uses a number of terms to refer to the roles played by participants in, and objects of, validation
in phpDocumentor.

Documentation Standard
    A Documentation Standard is the description of a series of best practices, expressed as a Ruleset, that a project
    is expected to adhere to and that can be verified in an automated fashion.

Validation
    The process of verifying whether a Structural Element's Documentation adheres to the Documentation Standard that
    was indicated, or if no specific Documentation Standard was provided then phpDocumentor's Default should be used.

Validator
    An object that is responsible for identifying whether a single practice as described in the Documentation Standard
    is adhered to for a given Structural Element Description (:term:`Descriptor`).

Violation
    The result when one of the practices described by a Validator is not adhered to. A single Validator could check
    for several best practices at once and thus return several types of Violations but it is generally recommended to
    have one type of Violation per Validator.

Ruleset
    A named series of Rule definitions that describe how a Documentation Standard should be checked. A Ruleset may also
    define a series of folders that need not be checked.

Active Ruleset
    Only one Ruleset may be active at any given time though it may refer to or include other Rulesets. The Ruleset that
    is to be applied to the provided project is called the Active Ruleset.

Rule
    A definition that describes which violations are to be checked for, and thus indirectly which Validators need to be
    executed, what severity a violation is considered to be and the message that is to be returned should the associated
    Violation occur.

    In addition to the above a Rule can also have a series of properties that can be passed to a Validator. Using these
    options it is possible to influence the behavior of a Validator.

Theory of Operation
-------------------

phpDocumentor is not limited to generating API Documentation it is also capable of checking your in-source documentation
for omissions, mismatching types, or artifacts remaining after a refactoring. In a sense it acts like PHP_CodeSniffer
but then specific to Documentation Standards.

Because there is such a large overlap with PHP_CodeSniffer phpDocumentor has adopted their configuration format for
defining which violations are to be checked. To this end phpDocumentor has adopted the concepts of Rulesets and Rules.
Those familiar with PHP_CodeSniffer might recognise the terms as we reuse these so that it is easier for people to
relate to.

The rest of this chapter is dedicated to describing how we expect the user to interact with the application followed
by the technical description of the system.

Flow from the user's point of view
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

By default phpDocumentor uses its Default Ruleset to verify the project that is being documented so without any user
interaction the person executing phpDocumentor will see each violation output to the command-line during execution and
a report is generated containing all Violations that have been detected. This report is accessible from the menu and
displays per-file which violations have occurred together with the linenumber of the DocBlock where the violation
occurred.

When the user wants to influence the way a project is Validated then they should provide their own Documentation
Standard or, if multiple Standards are included with phpDocumentor, tell phpDocumentor to use one of the pre-defined
Documentation Standards.

A Documentation Standard can be provided by:

1. Adding a Ruleset to the phpDocumentor configuration file.

   For example, using the Default Standard and removing all File DocBlock related checks:

   .. codeblock:: xml

      <?xml version="1.0" encoding="utf-8"?>
      <phpdocumentor>
         ...
         <logging>
           ...
           <ruleset name="MyStandard">
             <rule ref="Default">
               <exclude name="File.*" />
             </rule>
           </ruleset>
         </logging>
         ...
      </phpdocumentor>

2. Creating a separate XML file, for example ``ruleset.xml``, that contains a name, description and series of Rule
   definitions that describe which Violations should be checked for; and linking to this file using the command-line or
   via the configuration file.

   For example, using the Default Standard and removing all File DocBlock related checks in a separate file
   called ``ruleset.xml``:

   .. codeblock:: xml

      <?xml version="1.0" encoding="utf-8"?>
      <ruleset name="MyStandard">
        <rule ref="Default">
          <exclude name="File.*" />
        </rule>
      </ruleset>

   And this file can be called with the following command::

      $ phpdoc --standard=ruleset.xml

.. important::

   Once a user provides their own Ruleset the Default Ruleset will no longer be applied because only one Ruleset may be
   active at any given time. If a user wants to use the Default Ruleset with just minor adjustments then they should add
   a Rule to include the Default Ruleset.

Initializing the validation
~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. uml::

   skinparam activityBorderColor #516f42
   skinparam activityBackgroundColor #a3dc7f
   skinparam shadowing false

   start

   partition "Determine which Ruleset to use" {
       :Use pre-loaded 'Default' Ruleset defined in Service Provider;
       :Check configuration file for a user-defined Ruleset;
       :Check command line option 'standard' for the name of a pre-loaded Ruleset;
       :Check command line option 'standard' for the path of a Ruleset XML configuration file;
   }

   partition "Load Active Ruleset" {
       if (ruleset is not pre-loaded) then (Yes)
           :Unserialize Ruleset XML into a Ruleset object and Rule objects;

           while (Check each Rule)
               if (ref type?) then (Ruleset)
                   if (Ruleset is loaded) then (No)
                       :Load Ruleset;
                   endif

                   :Overwrite Ref property with pointer to Ruleset object;
               endif
           endwhile;
       endif;
   }

   :Only load validators that match the violation codes in the Active Ruleset;

   stop

Validating a Descriptor
~~~~~~~~~~~~~~~~~~~~~~~

.. uml::

   skinparam activityBorderColor #516f42
   skinparam activityBackgroundColor #a3dc7f
   skinparam shadowing false

   start

   stop

Defining a Standard
-------------------

# As a class descending from Ruleset
# Using an XML Ruleset definition in the phpDocumentor configuration
# As an external XML Ruleset file

Appendix: Pre-defined violations
--------------------------------

.. _40: https://github.com/phpDocumentor/phpDocumentor2/issues/40