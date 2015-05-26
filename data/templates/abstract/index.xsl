<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">
    <xsl:output indent="yes" method="html"/>
    <xsl:include href="chrome.xsl"/>
    <xsl:include href="chrome/header.xsl"/>
    <xsl:include href="chrome/menu.xsl"/>
    <xsl:include href="chrome/footer.xsl"/>

    <xsl:template match="/">
        <xsl:call-template name="root">
            <xsl:with-param name="pageId" select="'frameset'" />
        </xsl:call-template>
    </xsl:template>
    
    <xsl:template name="content">
        <table id="page">
            <thead>
                <tr><td colspan="2" id="db-header">
                    <xsl:call-template name="page-header" />
                </td></tr>
                <tr><td colspan="2" id="db-menu">
                    <xsl:call-template name="page-menu" />
                </td></tr>
            </thead>
            <tfoot>
                <tr><td colspan="2" id="db-footer">
                    <xsl:call-template name="page-footer"/>
                </td></tr>
            </tfoot>
            <tbody>
                <tr>
                    <td id="sidebar">
                        <iframe name="nav" id="nav" src="{$root}nav.html"/>
                    </td>
                    <td id="contents">
                        <iframe name="content" id="content" src="{$root}content.html"/>
                    </td>
                </tr>
            </tbody>
        </table>
    </xsl:template>

</xsl:stylesheet>