<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output indent="yes" method="html" />
    <xsl:include href="layout.xsl" />
    <xsl:include href="elements/common.xsl" />
    <xsl:include href="elements/function.xsl" />
    <xsl:include href="elements/constant.xsl" />
    <xsl:include href="elements/property.xsl" />
    <xsl:include href="elements/class.xsl" />

    <!-- Body text that is inserted into the layout -->
    <xsl:template match="/project" mode="contents">
        <xsl:variable
            name="element"
            select="/project/file/class[full_name=$full_name]|/project/file/interface[full_name=$full_name]|/project/file/trait[full_name=$full_name]"
        />

        <div class="row">
            <div class="span4"><xsl:apply-templates select="$element" mode="sidebar" /></div>
            <div class="span8"><xsl:apply-templates select="$element" mode="contents"/></div>
        </div>
    </xsl:template>

    <xsl:template match="/" mode="title">
        <xsl:text> &#187; </xsl:text>
        <xsl:value-of select="$full_name" />
    </xsl:template>

</xsl:stylesheet>
