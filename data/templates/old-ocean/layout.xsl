<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:date="http://exslt.org/dates-and-times"
    extension-element-prefixes="date">
    <xsl:output indent="yes" method="html" />

    <xsl:template name="string-replace-all">
        <xsl:param name="text" />
        <xsl:param name="replace" />
        <xsl:param name="by" />
        <xsl:choose>
            <xsl:when test="contains($text, $replace)">
                <xsl:value-of select="substring-before($text,$replace)" />
                <xsl:value-of select="$by" />
                <xsl:call-template name="string-replace-all">
                    <xsl:with-param name="text"
                        select="substring-after($text,$replace)" />
                    <xsl:with-param name="replace" select="$replace" />
                    <xsl:with-param name="by" select="$by" />
                </xsl:call-template>
            </xsl:when>
            <xsl:otherwise>
                <xsl:value-of select="$text" />
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template name="remove-leading-dot">
        <xsl:param name="text" />
        <xsl:choose>
            <xsl:when test="substring($text, 1, 1) = '.'">
                <xsl:value-of select="substring-after($text, '.')" />
            </xsl:when>
            <xsl:otherwise>
                <xsl:value-of select="$text" />
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template name="createLink">
        <xsl:param name="value"/>
        <xsl:variable name="stage1">
            <xsl:call-template name="string-replace-all">
                <xsl:with-param name="text" select="$value"/>
                <xsl:with-param name="replace" select="'\'"/>
                <xsl:with-param name="by" select="'.'"/>
            </xsl:call-template>
        </xsl:variable>

        <xsl:variable name="stage2">
            <xsl:call-template name="string-replace-all">
                <xsl:with-param name="text" select="$stage1" />
                <xsl:with-param name="replace" select="'/'" />
                <xsl:with-param name="by" select="'.'" />
            </xsl:call-template>
        </xsl:variable>

        <xsl:call-template name="remove-leading-dot">
            <xsl:with-param name="text" select="$stage2" />
        </xsl:call-template>
    </xsl:template>

</xsl:stylesheet>