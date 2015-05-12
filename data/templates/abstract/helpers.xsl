<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">
    <xsl:output indent="yes" method="html"/>

    <!-- Concatenate items with a given separator: http://symphony-cms.com/download/xslt-utilities/view/22517/-->
    <xsl:template name="implode">
        <xsl:param name="items"/>
        <xsl:param name="separator" select="', '"/>

        <xsl:for-each select="$items">
            <xsl:if test="position() &gt; 1">
                <xsl:value-of select="$separator"/>
            </xsl:if>

            <xsl:value-of select="."/>
        </xsl:for-each>
    </xsl:template>

</xsl:stylesheet>