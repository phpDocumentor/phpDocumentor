<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">
    <xsl:output indent="yes" method="html"/>
    <xsl:include href="../sidebar/sections/charts.xsl"/>
    <xsl:include href="../sidebar/sections/reports.xsl"/>

    <xsl:template name="page-menu">
        <ul>
            <xsl:call-template name="sidebar-section-charts"/>
            <xsl:call-template name="sidebar-section-reports"/>
        </ul>
    </xsl:template>

</xsl:stylesheet>