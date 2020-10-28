1. Input and output formats
===========================

Date: 2020-10-28
Status: Proposal

Context
-------

Users provide and consume information from phpDocumentor in various formats. Currently, output formats are separate
templates and input formats are hardcoded into the application.

In order to make it easier to support multiple input and output formats, we should adjust the way phpDocumentor deals
with these by making them configurable and maintained from a single template.

Examples of possible input formats are:

- RestructuredText
- Markdown
- Textile

Examples of possible output formats are:

- HTML
- LaTeX
- PDF
- XML
- CHM
- JSON
- RestructuredText

PHP Code is explicitly not mentioned as an input format, even though you could consider it, because it doesn't follow
the same structure as hand-written documentation.

Decision
--------

Provide:

1. the concept of Input and Output formats in phpDocumentor itself by promoting the 'Guides' models
2. Change the Twig writer to select the correct twig files based on extension, instead of selecting a different template

Model Input and Output formats
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

TBW

Change the Twig writer
~~~~~~~~~~~~~~~~~~~~~~

TBW

Consequences
------------

TBW

Potential risks
---------------

TBW
