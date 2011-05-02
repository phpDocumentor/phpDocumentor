<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:import href="../default2/nav.xsl"/>
    <xsl:output indent="yes" method="html" />
    <xsl:include href="../default2/chrome.xsl" />
    <xsl:include href="../default2/menubar.xsl" />

    <xsl:template match="/project">
        <div class="section">
            <xsl:call-template name="menubar"/>
        </div>
        <xsl:apply-imports />
    </xsl:template>

</xsl:stylesheet>