<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output indent="yes" method="html"/>

    <xsl:template name="page-header">
        <h1>
            <xsl:if test="not($title)">
                <img src="images/icon48x48.png" id="sidebar-logo" alt="Logo"/> DocBlox
            </xsl:if>
            <xsl:if test="$title">
                <xsl:value-of select="$title" disable-output-escaping="yes"/>
            </xsl:if>
        </h1>
    </xsl:template>

</xsl:stylesheet>