<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">
    <xsl:import href="../abstract/sidebar.xsl" />
    <xsl:include href="../abstract/chrome.xsl" />
    <xsl:output indent="yes" method="html" />

    <xsl:param name="section.dashboard.show" />
    <xsl:template name="sidebar-header">
        <xsl:if test="not($title)">
            <img src="images/logo.png" id="sidebar-logo" alt="Logo" />
        </xsl:if>
        <h1>
            <xsl:if test="$title">
                <xsl:value-of select="$title" disable-output-escaping="yes" />
            </xsl:if>
        </h1>
        <div style="clear: both"></div>
        <xsl:if test="$section.dashboard.show != 'false'">
            <h3 id="sidebar-dashboard">
                <a href="{$root}content.html" target="content"
                   class="ui-state-default ui-helper-reset ui-accordion-header">Dashboard</a>
            </h3>
        </xsl:if>
    </xsl:template>

    <xsl:template name="content">
        <script type="text/javascript" src="{$root}js/jquery.cookie.js"></script>
        <script type="text/javascript" src="{$root}js/jquery.treeview.js"></script>
        <script type="text/javascript" src="{$root}js/sidebar.js"></script>

        <div id="sidebar">
            <div id="sidebar-header">
                <xsl:call-template name="sidebar-header" />
            </div>

            <div id="sidebar-nav">
                <xsl:call-template name="sidebar-sections" />
            </div>

            <div id="sidebar-footer">
                <xsl:call-template name="sidebar-footer" />
            </div>
        </div>
    </xsl:template>

</xsl:stylesheet>