Plugins
=======

phpDocumentor supports Plugins with which you can expand on the normal behaviour.
The enhancement is actually done by providing hooks into crucial parts of
phpDocumentor's process.

The following things are possible with plugins (and are done by
phpDocumentor itself):

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

* **Composer's autoloader**, autoloader for all classes in a plugin
* **EventDispatcher**, manager that collects dispatched events and distributes
  them to the plugins.
* **\Zend\Config\Config**, the configuration manager containing global settings
  but also definitions which plugins are to be loaded with which options.

These components come pre-installed and ready to use. The only thing that you
need to know is that they are there and what they are used for.

.. NOTE::

    When you want to create your own runner you will have to pass these as
    dependencies to the plugin manager or use the \phpDocumentor\Bootstrap class
    to bootstrap the basics for you.

How does it work
----------------

The minimal setup for a plugin is a `Configuration`_ XML file (called *plugin.xml*)
and a *component*.
A component in this context means either:

* `Listeners`_ (or Observers), which is an object that is able to intercept
  *events* and perform changes on the data of phpDocumentor. Every listener must
  be mentioned in the plugin's configuration as it must be registered.
* `Transformation Writers`_, which can be used by Transformations to perform
  actions during the structure-to-output conversion process.

      An example is the *\phpDocumentor\Plugin\Core\Transformer\Writer\Xsl*; which
      performs the actual creation of a HTML file according to a template.

  Contrary to listeners the `Transformation Writers`_ are **not** registered
  in the configuration. If the plugin is configured correctly then the auto-loader
  will automatically discover it.

Please see the individual chapters for more details on what can be achieved
using each component.

Creating a plugin
-----------------

Creating a plugin is rather simple using the ``plugin:generate`` task of phpDocumentor.
This task enables the user to generate a skeleton plugin with a basic listener.

The following command can be used::

    $ phpdoc plugin:generate -t [PATH] -n [NAME]

After the execution of this command you can find a generated plugin the given
PATH.

.. note::

    Plugins can be placed anywhere, and do not need to reside in the plugins
    folder of phpDocumentor. You can add your custom plugin to your project repository
    and add a relative path to the root of your plugin in your phpDocumentor
    configuration file.

After you have created your plugin you need to edit your *plugin.xml* file
to contain the correct meta-data.

Most important here is the ``class-prefix`` (see `Class prefix`_) field; this
will tell the autoloader which classes can be found in this folder.
Please see  the chapter on `Configuration`_ for details on the configuration file.

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
        <website>http://www.phpdoc.org</website>
        <description>
            This plugin contains all PHPDoc basic behaviours and validators.
        </description>
        <class-prefix>\phpDocumentor\Plugin\Core</class-prefix>
        <listener>Listener</listener>
        <options>
            <option name="Option1">value</name>
        </options>
    </plugin>

As can be seen it contains `Meta data`_ about the plugin itself (*name*, *author*,
*email*, *description*, *website*) but also instructions for phpDocumentor how to
invoke or package it (*class-prefix*, *listener*, *options*).

Meta data
~~~~~~~~~

The following fields may be provided as meta data in the root of the plugin

=========== ==================================================================
Field       Description
=========== ==================================================================
name        The name of the plugin; must be unique within phpDocumentor
version     The version number of this plugin; may be used in the dependencies
author      The name of the author
email       The e-mail address for enquiries about the plugin
website     The home page for this plugin
description A descriptive text about this plugin
=========== ==================================================================

Class prefix
~~~~~~~~~~~~

phpDocumentor uses the
`Composer autoloading <http://getcomposer.org/doc/01-basic-usage.md#autoloading>`_
facilities for plugins.

To map your namespace or class prefix to the plugin's base folder there is a
field named *class-prefix* that should be added to indicate what the ClassMap
or namespace prefix is for the plugin's classes.

For example:

    The configuration file is located in */opt/phpdoc/plugins/mine/plugin.xml*
    and the class names start with ``My_First_Plugin_``. When you have added the
    prefix to the configuration file and you instantiate ``My_First_Plugin_Listener``,
    then phpDocumentor will attempt to locate a file named *Listener.php* in the
    */opt/phpdoc/plugins/mine/My/First_Plugin* folder.

.. attention::

    The directory structure is based on
    `PSR-0 <https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md>`_.
    This means that if you do not use a namespace that underscores are interpreted
    as directory separators.

    Example:

        You have a class ``My_First_Plugin_Listener`` that is to be autoloaded, then
        the file's path is: ``[plugin.xml folder]/My/First/Plugin/Listener.php``.

    Example2:

        Your class is called ``\My\Plugin\Custom_Listener`` (thus with the
        namespace ``\My\Plugin`` then the file's path is:
        ``[plugin.xml folder]/My/Plugin/Listener.php``.

When no class_prefix is given then ``\phpDocumentor\Plugin\<ucfirst(name)>`` is assumed.

Listener
~~~~~~~~

To listen in on events from phpDocumentor the plugin needs to register a listener
class using an equally named field. Multiple listeners may be registered by adding
this field multiple times.

.. note::

    The class prefix (if provided) should **not** be added to the Listener for
    brevity.

Options
~~~~~~~

Here you can provide a set of *default* options for your plugin; the user
has the ability to override these options from the phpDocumentor configuration file.

    Example: the phpDocumentor core plugin has an option to switch off Graph
    generation; the default here can be set to make graphs but the user could
    again disable that.

Listeners
---------

Basic concept
~~~~~~~~~~~~~

With listeners can a plugin author extend the functionality of phpDocumentor without
making changes to its core. Listeners provide an implementation of the Event
Dispatcher via phpDocumentor's EventDispatcher class.

From within phpDocumentor events are dispatched to the Event Dispatcher (which is
available in a plugin as ``$this->getEventDispatcher()``); which in turn triggers
any listener methods that are connected to that event.

The process
~~~~~~~~~~~

In order to understand how listeners work it is important that you know a little
about how phpDocumentor works, and where which events are triggered.

Below is a complete step-by-step description of the phpDocumentor flow with emphasize
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
2. phpDocumentor invokes the Bootstrapper; which initializes the autoloader,
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
   \phpDocumentor\Parser\Parser class and starts the parsing process.
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
   by default this is the Intermediate XML Structure of phpDocumentor itself

       Each encountered tag in this process will trigger a
       `reflection.docblock.tag.export`_ event where the final contents can be
       rewritten.

10. Steps 8 and 9 will repeat until all files have been processed.
11. The **run** task will take back control and initiate the **transform** task
12. The **transform** task instantiates an object of class \phpDocumentor\Transformer\Transformer
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

Connecting to events
~~~~~~~~~~~~~~~~~~~~

Any event in phpDocumentor can be connected to a public class method using one of two
actions:

1. Annotations
2. Manual

The method which will receive the given event must always have one argument of
type sfEvent.

Example:

.. code-block:: php
   :linenos:

    public function applyBehaviours(sfEvent $data)
    {
        ...
    }

This argument can contain parameters (accessible as array) which you can
influence from within your method; please note that any object is passed by
reference and any change you make will also happen in the further handling
by phpDocumentor.

This way you can filter or influence the process without having to change
anything in phpDocumentor' core.
Which arguments are supported per event type can be found in their respective
chapter below.

Annotations
###########

Methods in `Listeners`_ can have a special annotation `@phpdoc-event` in their
DocBlock. In this annotation is mentioned which event triggers the given method.

Example:

.. code-block:: php
   :linenos:

    /**
     * My first listener.
     *
     * @phpdoc-event transformer.transform.pre
     *
     * @param sfEvent $data
     *
     * @return void
     */
    public function applyBehaviours(sfEvent $data)
    {
        $xml = $data['source'];
        ...
    }

In this example you can see how the class method **applyBehaviours** is being
connected to the event `transformer.transform.pre`_ and how we get the
parameter **source** from the event.

.. NOTE::

    You can have multiple methods which consume the same event. phpDocumentor will
    execute them all in order of appearance in the listener.

Manual connecting
#################

Another way to connect is to manually indicate to the EventDispatcher that you
want to link a method to an event. This is useful when you want to link an event
to a method contained in a different object.

A **configure** method is available where you can execute such actions or
perform other initializations.

Example:

.. code-block:: php
   :linenos:

    protected function configure()
    {
        $this->logger = new phpDocumentor_Core_Log(phpDocumentor_Core_Log::FILE_STDOUT);

        // connect the log method of the $this->logger object to the event
        // system.log
        $this->event_dispatcher->addListener('system.log', array($this->logger, 'log'));
    }

Supported events
~~~~~~~~~~~~~~~~

system.log.threshold
####################

This event is triggered any time phpDocumentor wants to change which priority of
messages need to logged; it is comparable to the *error_reporting* method of
PHP.

system.log
##########

This event is triggered any time phpDocumentor logs an action.

At certain places in the code a logging event is triggered by invoking the method
``$this->log()`` (which is defined in the Layer Superclass of each component.).

This method has **two** arguments:

========= ============================================================
Name      Description
========= ============================================================
message   The message that needs to be logged.
priority  The priority or urgency of the logging, ranging from 0 to 7
          where the lowest number is the most crucial error or logging
========= ============================================================

Typical uses for this event is grabbing the logging events and sending them to
a collector or outputting them.

system.debug
############

This event is triggered any time phpDocumentor logs an action.

At certain places in the code a logging event is triggered by invoking the method
``$this->log()`` (which is defined in the Layer Superclass of each component.).

This method has **two** arguments:

========= ============================================================
Name      Description
========= ============================================================
message   The message that needs to be logged.
priority  The priority or urgency of the logging, ranging from 0 to 7
          where the lowest number is the most crucial error or logging
========= ============================================================

Typical uses for this event is grabbing the logging events and sending them to
a collector or outputting them.

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
