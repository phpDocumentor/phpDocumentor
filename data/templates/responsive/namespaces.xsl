<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output indent="yes" method="html" />
    <xsl:include href="layout.xsl" />
    <xsl:include href="elements/common.xsl" />
    <xsl:include href="elements/namespace.xsl" />
    <xsl:include href="elements/class.xsl" />
    <xsl:include href="elements/constant.xsl" />
    <xsl:include href="elements/function.xsl" />

    <xsl:template match="/project" mode="contents">
        <xsl:variable name="element" select="//namespace[@full_name = $full_name]" />

        <xsl:variable name="parent_name">
            <xsl:if test="local-name($element/..) = 'namespace'">
                <xsl:value-of select="concat($element/../@full_name, '\')"/>
            </xsl:if>
        </xsl:variable>

        <div class="row">
            <div class="span4">
                <xsl:apply-templates select="$element" mode="sidebar-nav">
                    <xsl:with-param name="parent_name" select="$parent_name"/>
                </xsl:apply-templates>
            </div>

            <div class="span8 namespace-contents">
                <xsl:apply-templates select="$element" mode="contents">
                    <xsl:with-param name="parent_name" select="$parent_name"/>
                </xsl:apply-templates>
            </div>
        </div>
    </xsl:template>

    <xsl:template match="/" mode="title">
        <xsl:text> &#187; </xsl:text>
        <xsl:value-of select="$full_name" />
    </xsl:template>

</xsl:stylesheet>