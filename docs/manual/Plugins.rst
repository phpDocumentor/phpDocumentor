Plugins
=======

DocBlox supports Plugins with which you can expand on the normal behaviour. The
enhancement is actually done by providing hooks into crucial parts of DocBlox'
process.

The following things are possible with plugins (and are done by DocBlox itself):

* Additional validators with which to check your source code
* Defining (or redefining) the way tags are interpreted
* Adding behaviour to the data
* Post-processing of transformed files
* Enhancing logging

The following sections will provide a high-level overview of plugins. The above
are short examples of use; a full explanation can be found in the `Recipes`_
chapter.

Dependencies
------------

Plugins make use of the following components:

* **ZendX_StandardAutoloader**, autoloader for all classes in a plugin
* **sfEventDispatcher**, manager that collects dispatched events and distributes
  them to the plugins.
* **DocBlox_Core_Config**, the configuration manager containing global settings
  but also definitions which plugins are to be loaded with which options.

These components come pre-installed  and ready to use. The only thing that you
need to know is that they are there and what they are used for.

    **Please note**: *when you want to create your own runner you will have
    to pass these as dependencies to the plugin manager or use the
    DocBlox_Bootstrap class to bootstrap the basics for you.*

How does it work
----------------

The minimal setup for a plugin is a `Configuration`_ XML file (called *plugin.xml*)
and a *component*.
A component in this context means either:

* `Listeners`_ (or Observer), which is an object that is able to intercept *events*
  and perform changes on the data of DocBlox.
* `Transformation Writers`_, which can be used by Transformations to perform
  actions during the structure-to-output conversion process.

      An example is the *DocBlox_Plugin_Core_Transformer_Writer_Xsl*; which
      performs the actual creation of a HTML file according to a template.

Please see the individual chapters for more details on what can be achieved
using each component.

Configuration
-------------

The configuration of a plugin is governed by a file called *plugin.xml*; which
must always be located in the root of the plugin.

An example of such a file is given here:

.. code-block:: xml

    <?xml version="1.0" encoding="UTF-8" ?>

    <plugin>
        <name>PHPDoc</name>
        <version>1.0</version>
        <author>Mike van Riel</author>
        <email>mike.vanriel@naenius.com</email>
        <website>http://www.docbloc-project.org</website>
        <description>
            This plugin contains all PHPDoc basic behaviours and validators.
        </description>
        <class-prefix>DocBlox_Plugin_Core</class-prefix>
        <listener>Listener</listener>
        <dependencies>
            <docblox>
                <min-version>0.15.0</min-version>
            </docblox>
            <plugin>
                <name>Core</name>
                <min-version>1.0</min-version>
            </plugin>
        </dependencies>
        <options>
            <option name="Option1">value</name>
        </options>
    </plugin>

As can be seen it contains meta data about the plugin itself (*name*, *author*,
*email*, *description*, *website*) but also instructions for DocBlox how to
invoke or package it (*class-prefix*, *listener*, *dependencies*, *options*).

Meta data
~~~~~~~~~

The following fields may be provided as meta data in the root of the plugin

=========== ==================================================================
Field       Description
=========== ==================================================================
name        The name of the plugin; must be unique within DocBlox
version     The version number of this plugin; may be used in the dependencies
author      The name of the author
email       The e-mail address for enquiries about the plugin
website     The website where more information may be shared
description A descriptive text about this plugin
=========== ==================================================================

Class prefix
~~~~~~~~~~~~

DocBlox provide autoloading facilities for its plugins but also believes a
plugin should be free to be named in whatever way they like.
To accomplish this a field named *class-prefix* may be added to indicate what
the prefix is for the classes that are to be located in the folder where the
configuration file is found.

    For example: the configuration file is located in
    */opt/docblox/plugins/mine/plugin.xml* and the class names start with
    `My_First_Plugin_`. When you have added the prefix to the configuration file
    and you try to instantiate My_First_Plugin_Listener, then DocBlox will try
    to locate a file named *Listener.php* in the */opt/docblox/plugins/mine/*
    folder.

When no class_prefix is given then `DocBlox_Plugin_<ucfirst(name)>` is assumed.

Listener
~~~~~~~~

To listen in on events from DocBlox the plugin needs to register a listener class
using an equally named field. Multiple listeners may be registered using this
field.

    Please note that the class prefix should **not** be added to the Listener,
    this is assumed from the class prefix and is done to better support
    namespaces in the future.

Dependencies
~~~~~~~~~~~~

Here you can specify which minimal version of DocBlox is required and if
this plugin depends on other plugins which minimal version they should have.

Options
~~~~~~~

Here you can provide a set of *default* options for your plugin; the user
has the ability to override these options from the DocBlox configuration file.

    Example: the DocBlox core plugin has an option to switch off Graph
    generation; the default here can be set to make graphs but the user could
    again disable that.

Listeners
---------

The process
~~~~~~~~~~~

In order to understand how listeners work it is important that you know how
DocBlox works and where which events are triggered.

Below is a complete step-by-step description of the DocBlox flow with emphasize
on the invocation of plugins.

.. uml::

    scale 0.6

    (*) --> "2. Bootstrap"
    "2. Bootstrap" --> "3. Load plugins"
    "3. Load plugins" --> "4. Execute 'run'"
    "4. Execute 'run'" --> "5. Execute 'parse'"
    "5. Execute 'parse'" -> "6. Collect files"
    "5. Execute 'parse'" --> "11. Return to 'run'"
    "6. Collect files" --> "7. Invokes Parser"
    "7. Invokes Parser" --> "8. Analyze sourcefile"
    note bottom: reflection.docblock-extraction.post
    "8. Analyze sourcefile" --> "9. Store structure"
    note bottom: reflection.docblock.tag.export
    if "Files left to analyze" then
      -->[true] "8. Analyze sourcefile"
    else
      -->[false] "10. Continue"
    endif
    "10. Continue" -left-> "11. Return to 'run'"
    "11. Return to 'run'" --> "11b. Execute 'transform'"
    "11b. Execute 'transform'" -> "12. Starts transformation process"
    "12. Starts transformation process" --> "13. Apply behaviours"
    note left: transformer.transform.pre
    "13. Apply behaviours" --> "14. Execute the transformations"
    "14. Execute the transformations" --> "15. Call post-processing"
    note bottom: transformer.transform.post
    "15. Call post-processing" -left-> "16. Return to 'run'"
    "16. Return to 'run'" --> (*)

    "11b. Execute 'transform'" --> "16. Return to 'run'"

1. The user calls on the CLI commandtool to **run** the generation process
2. DocBlox invokes the Bootstrapper; which initializes the autoloader,
   configuration, Event Dispatcher and Plugin Manager
3. The Plugin Manager scans the configuration and instantiates any found plugin
   definition.

       From this point on; anytime a log is sent to the screen a `system.log`_
       event is dispatched. any plugin that is listening to this event will
       deal with it at that moment.

   ..

       The above also applies any time a debug message is discovered; this will
       trigger the `system.debug`_ message

4. A TaskRunner is started and passes all parameters and configuration to
   the **run** task.
5. The **run** task starts the **parse** task
6. The **parse** task creates a File collection, which collects all files that
   are to be parsed (or ignored) from the given arguments and configuration.
7. The **parse** task then sends the File collection to an instance of the
   DocBlox_Parser class and starts the parsing process.
8. A File is taken from the collection and is processed by the Static
   Reflection component

       Anytime an error is discovered during parsing will the `parser.log`_ event
       be triggered.

   ..

       Each time a docblock is discovered that precedes a parsable element (such
       as a class, function or property) is the `reflection.docblock-extraction.post`_
       event dispatched. This allows the user to examine the docblock or even alter
       the docblock definition.

9. After a file is processed it's contents are written to the parser output format,
   by default this is the Intermediate XML Structure of DocBlox itself

       Each encountered tag in this process will trigger a
       `reflection.docblock.tag.export`_ event where the final contents can be
       rewritten.

10. Steps 8 and 9 will repeat until all files have been processed.
11. The **run** task will take back control and initiate the **transform** task
12. The **transform** task instantiates an object of class DocBlox_Transformer
    and start the transformation from temporary structure to the intended
    output format, such as HTML.
13. Right before the actual transformation will the `transformer.transform.pre`_
    be invoked where the plugin author has a chance to influence the system as a
    whole (a.k.a. add behaviour).
14. The actual writers are invoked and the collected data is transformed to
    the intended output format; such as HTML.
15. After the transformation has been invoked will the
    `transformer.transform.post`_ event be triggered so that post processing is
    possible.

Supported hooks
~~~~~~~~~~~~~~~

system.log
##########

This event is triggered any time DocBlox logs an action.

At certain places in the code a logging event is triggered by invoking the method
``$this->log()`` (which is defined in the Layer Superclass of each component.).

This method has **two** arguments

system.debug
############

parser.log
##########

reflection.docblock-extraction.post
###################################

reflection.docblock.tag.export
##############################

transformer.transform.pre
#########################

transformer.transform.post
##########################


Recipes
~~~~~~~

Adding a docblock validation
############################

Streaming parser errors to a file
#################################

Removing a all tags of a specific type
######################################

Transformation Writers
----------------------