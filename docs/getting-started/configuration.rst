Configuration
=============

phpDocumentor works perfectly fine without a configuration file; but by using it you can make generating your
documentation easier.

phpDocumentor uses an XML-based configuration file with quite a few options; in this document we will go into a few
common uses and how to set this up.

.. code-block:: xml

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

And next
--------

You can get more information on the phpDocumentor configuration file by reading the `XSD`_.

.. _XSD: https://github.com/phpDocumentor/phpDocumentor/blob/master/data/xsd/phpdoc.xsd
