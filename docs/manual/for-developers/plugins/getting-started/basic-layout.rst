Basic Layout
============

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