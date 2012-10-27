Xsl
===

Introduction
------------

The Xsl writer uses an XSLT transformation to generate text output from the AST
(structure.xml) given a XSL template. The output for this writer can be any
text-based format including, but not limited to, XML, HTML and even JSON.

Parameters
----------

The Xsl writer understands the 'source', 'artefact' and 'query' attributes of a
transformation.

source
    Identifies the source XSL template to use for the translation to a
    plain-text file. Must be a relative path that is relative to the
    data folder of phpDocumentor

    .. important::

       The root folder mentioned above may change before 2.0.0b1 because this
       does not work correctly in some setups.

artefact
    Represents that destination path, relative to the target directory, where
    the output should be stored. The 'artefact' may contain a variable between
    braces if the query attribute is used; see the query attribute for examples.

query
    If provided, an XPath query is executed and the result is piped into the XSL
    template; instead of the entire AST. The name of the node(s) resulting from
    this query can, and should, be injected in the 'artefact' attribute using
    the following notation: `{$name}`; where 'name' should be replaced with the
    actual nodename.

Examples:

Transforms the AST using the 'index.xsl' file in the responsive template and
renders it as 'index.html' in the folder that was provided as target location
with the transformer.

.. code-block:: xml

    <transformation writer="xsl" source="templates/responsive/index.xsl"
        artifact="index.html"
    />

Transforms the AST using the 'class.xsl'

.. code-block:: xml

    <transformation writer="xsl" query="//class/full_name|//interface/full_name"
        source="templates/responsive/class.xsl" artifact="classes/{$full_name}"
    />
