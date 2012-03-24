<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output indent="yes" method="html" />

    <!-- Property display name -->
    <xsl:template match="property/name">
        <xsl:value-of select="../docblock/description" />
        <xsl:if test="not(../docblock/description) or ../docblock/description = ''">
            <xsl:value-of select="../docblock/tag[@name='var']/@description" />
            <xsl:if test="not(../docblock/tag[@name='var']/@description) or ../docblock/tag[@name='var']/@description = ''">
                <xsl:value-of select="." />
            </xsl:if>
        </xsl:if>
    </xsl:template>

    <xsl:template match="property/name" mode="signature">
        <xsl:param name="exclude-link" />

        <xsl:variable name="name" select="."/>
        <pre>
            <xsl:value-of select="$name"/>
            <xsl:text>&#160;</xsl:text>
            <xsl:apply-templates select="../docblock/tag[@name='var']" mode="signature">
                <xsl:with-param name="exclude-link" select="$exclude-link"/>
            </xsl:apply-templates>
        </pre>
    </xsl:template>

</xsl:stylesheet>