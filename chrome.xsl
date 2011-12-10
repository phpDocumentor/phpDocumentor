<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output indent="yes" method="html"/>
    <xsl:include href="chrome/head.xsl"/>
    <xsl:include href="helpers.xsl"/>

    <xsl:template name="title">
        <xsl:choose>
            <xsl:when test="$title != ''">
                <xsl:value-of select="$title"/>
            </xsl:when>
            <xsl:otherwise>DocBlox Documentation</xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="/">
        <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <title><xsl:call-template name="title" /></title>
                <xsl:call-template name="chrome-head"/>
            </head>
            <body>
                <xsl:call-template name="content"/>
            </body>
        </html>
    </xsl:template>

</xsl:stylesheet>