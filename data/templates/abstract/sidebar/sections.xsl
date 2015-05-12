<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">
    <xsl:output indent="yes" method="html" />
    <xsl:include href="sections/api.xsl"/>
    <xsl:include href="sections/charts.xsl"/>
    <xsl:include href="sections/files.xsl"/>
    <xsl:include href="sections/namespaces.xsl"/>
    <xsl:include href="sections/packages.xsl"/>
    <xsl:include href="sections/reports.xsl"/>

    <xsl:param name="section.api.show" />
    <xsl:param name="section.packages.show" />
    <xsl:param name="section.namespaces.show" />
    <xsl:param name="section.files.show" />
    <xsl:param name="section.files.show-elements" />
    <xsl:param name="section.charts.show" />
    <xsl:param name="section.reports.show" />

    <xsl:template name="sidebar-sections">

        <xsl:if test="$section.api.show != 'false'">
            <xsl:if test="count(/project/file/*/docblock/tag[@name='api']|/project/file/class/*/docblock/tag[@name='api']|/project/file/interface/*/docblock/tag[@name='api']) > 0">
            <h3 id="sidebar-api">API</h3>
            <div class="sidebar-section">
                <xsl:call-template name="sidebar-section-api"/>
            </div>
            </xsl:if>
        </xsl:if>

        <xsl:if test="$section.packages.show != 'false'">
            <xsl:if test="count(/project/package) > 0">
            <h3 id="sidebar-packages">Packages</h3>
            <div class="sidebar-section sidebar-tree">
                <xsl:call-template name="sidebar-section-packages"/>
            </div>
            </xsl:if>
        </xsl:if>


        <xsl:if test="$section.namespaces.show != 'false'">
            <xsl:if test="count(/project/namespace[@name != 'default']) > 0">
            <h3 id="sidebar-namespaces">Namespaces</h3>
            <div class="sidebar-section sidebar-tree">
                <xsl:call-template name="sidebar-section-namespaces"/>
            </div>
            </xsl:if>
        </xsl:if>

        <xsl:if test="$section.files.show != 'false'">
        <h3 id="sidebar-files">Files</h3>
        <div class="sidebar-section">
            <xsl:call-template name="sidebar-section-files"/>
        </div>
        </xsl:if>

        <xsl:if test="$section.charts.show != 'false'">
        <h3 id="sidebar-charts">Charts</h3>
        <div class="sidebar-section">
            <ul style="list-style-image: url('css/phpdoc/images/icons/chart15x12.png');">
                <xsl:call-template name="sidebar-section-charts"/>
            </ul>
        </div>
        </xsl:if>

        <xsl:if test="$section.reports.show != 'false'">
            <h3 id="sidebar-reports">Reports</h3>
            <div class="sidebar-section">
                <ul style="list-style-image: url('css/phpdoc/images/icons/reports9x12.png');">
                    <xsl:call-template name="sidebar-section-reports"/>
                </ul>
            </div>
        </xsl:if>
    </xsl:template>

</xsl:stylesheet>
