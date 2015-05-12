<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template name="output-tokens">
        <xsl:param name="list" />
        <xsl:variable name="newlist" select="concat(normalize-space($list), ' ')" />
        <xsl:variable name="first" select="substring-before($newlist, ' ')" />
        <xsl:variable name="remaining" select="substring-after($newlist, ' ')" />

        <id>
            <xsl:value-of select="$first" />
        </id>

        <xsl:if test="$remaining">
            <xsl:call-template name="output-tokens">
                    <xsl:with-param name="list" select="$remaining" />
            </xsl:call-template>
        </xsl:if>
    </xsl:template>

    <xsl:template match="/">
        <xsl:call-template name="output-tokens">
            <xsl:with-param name="list" select="//description"/>
        </xsl:call-template>
    </xsl:template>

</xsl:stylesheet>