Extending an existing template
==============================

To extend an existing template, create a template as explained in the previous
sections, but then copy the template.xml of the template you want to extend
over the template.xml.

Then configure what template to inherit from. Look for the lines declaring the
dependencies, and add the template you are extending.

For example, to extend the new-black theme, add it to the dependencies::

    <dependencies>
        <template name="abstract" version="1.0.3" />
        <template name="new-black" version="1.0.3" />
    </dependencies>

Now you can change any of the transformation lines if you want to skip
something or do it differently. All paths are relative to the data directory
of phpDocumentor. To change specific parts of the template, copy that part
(css, js, xsl) into the template folder or add an additional one that does
something more. Then add a transformation entry to run that piece.

..

    Note that for now, the template.xml file does not inherit information from
    the parent template.xml. This might be added in the future to only need to
    define the differences.

If you just want to customize the welcome page, you still need your own
template. The only line you need to change in template.xml then would be the
one for content.html::

    <transformation query="" writer="xsl" source="templates/your-template/content.xsl" artifact="content.html"/>

Copying asset files from your source directory is not supported (yet). If you
use images in your phpdoc, you need to include them in the template and define
a FileIO copy task for them.


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
