<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">
    <xsl:output indent="yes" method="html"/>

    <xsl:param name="section.dashboard.show" />
    <xsl:template name="sidebar-header">
        <xsl:if test="$section.dashboard.show != 'false'">
            <h3 id="sidebar-dashboard">
                <a href="{$root}content.html" target="content">Dashboard</a>
            </h3>
            <div class="sidebar-dashboard"> </div>
        </xsl:if>
    </xsl:template>

</xsl:stylesheet>