Extending an existing template
==============================

Root templates and overriding
-----------------------------

To ease overriding templates all root templates (those directly invoked by a
transformation) contain the xsl:includes for every 'child-template' file.
When creating your own templates; keep this in mind. Anyone wanting to extend
your template will be thankful for it.

Every root template will result in a HTML file upon transformation and is
advised to have at least the following:

1. an include of the chrome.xsl file of the Abstract template
2. a template named 'content'.

Example::

    <xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
      <xsl:output indent="yes" method="html" />
      <xsl:include href="chrome.xsl" />

      <xsl:template name="content">
        My content
      </xsl:template>

    </xsl:stylesheet>

The chrome.xsl file is responsible for the basic layout and HTML chrome. It will
invoke the xsl:template named *content* in the body.
