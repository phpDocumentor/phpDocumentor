Configuration
=============

phpDocumentor works perfectly fine without a configuration file; but by using it you can make generating your
documentation easier. The configuration file is in XML format with quite a few options; in this document we will go
into a few common uses and how to set this up.

The configuration file is loaded from the current working directory and is named ``phpdoc.dist.xml``. The easiest
solution is to place the configuration file in the root of your project and commit it to your version control system.
So you keep your settings in sync with your project.

A simple configuration file looks like this:

.. code-block:: xml
    :caption: phpdoc.dist.xml

    <?xml version="1.0" encoding="UTF-8" ?>
    <phpdocumentor
            configVersion="3"
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns="https://www.phpdoc.org"
    >
        <title>phpDocumentor</title>
        <paths>
            <output>docs/.build</output>
        </paths>

    </phpdocumentor>

Read more about configuration: :doc:`../references/configuration`.

And next
--------

You can get more information on the phpDocumentor configuration file by reading the `XSD`_.

.. _XSD: https://github.com/phpDocumentor/phpDocumentor/blob/master/data/xsd/phpdoc.xsd
