Setting up a plugin
===================

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