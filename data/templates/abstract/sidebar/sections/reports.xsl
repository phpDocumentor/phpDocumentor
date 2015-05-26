<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">
    <xsl:output indent="yes" method="html"/>

    <xsl:template name="sidebar-section-reports">
        <li><a href="{$root}report_markers.html" target="content">Markers (TODO/FIXME)</a></li>
        <li><a href="{$root}report_parse_markers.html" target="content">Parsing errors</a></li>
        <li><a href="{$root}report_deprecated.html" target="content">Deprecated elements</a></li>
    </xsl:template>

</xsl:stylesheet>